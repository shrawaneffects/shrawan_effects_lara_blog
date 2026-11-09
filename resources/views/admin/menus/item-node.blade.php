@php
    $depth = $depth ?? 0;
@endphp
<div class="menu-item-node" data-id="{{ $item->id }}" data-depth="{{ $depth }}">
    <div class="card border rounded-3 p-3 bg-body shadow-sm menu-item-card {{ !$item->is_active ? 'is-hidden' : '' }}">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <!-- Left: Selection Checkbox, Drag Handle, Indent/Outdent & Item Info -->
            <div class="d-flex align-items-center gap-2 flex-grow-1 flex-wrap">
                <!-- Checkbox for Bulk Actions -->
                <div class="form-check mb-0 me-1">
                    <input class="form-check-input item-select-checkbox" type="checkbox" value="{{ $item->id }}" id="chk-{{ $item->id }}">
                </div>

                <!-- Drag Handle -->
                <div class="drag-handle text-muted px-1 py-1 fs-5" title="Click & Drag to reorder or nest">
                    <i class="bi bi-grip-vertical"></i>
                </div>

                <!-- Hierarchy Controls (Outdent & Indent) -->
                <div class="btn-group btn-group-sm me-1" role="group" aria-label="Hierarchy Controls">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-2 py-0 btn-outdent" 
                            title="Shift Left (Outdent / Move Up a Level)" 
                            onclick="outdentItem({{ $item->id }})" 
                            {{ $depth === 0 ? 'disabled' : '' }}>
                        <i class="bi bi-arrow-left"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm px-2 py-0 btn-indent" 
                            title="Shift Right (Indent / Make Sub-item of above)" 
                            onclick="indentItem({{ $item->id }})">
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </div>

                <!-- Item Icon & Title -->
                <div class="d-flex align-items-center gap-2">
                    @if($item->icon)
                        <i class="{{ $item->icon }} {{ $item->is_active ? 'text-primary' : 'text-muted' }}"></i>
                    @endif
                    <span class="fw-bold item-title {{ !$item->is_active ? 'text-muted text-decoration-line-through' : '' }}">{{ $item->title }}</span>
                </div>

                <!-- URL preview -->
                <span class="text-muted small font-monospace d-none d-md-inline-block text-truncate" style="max-width: 170px;" title="{{ $item->url }}">
                    {{ $item->url }}
                </span>

                <!-- Hierarchy Level Badge -->
                <span class="depth-badge badge {{ $depth === 0 ? 'bg-primary-subtle text-primary border border-primary-subtle' : ($depth === 1 ? 'bg-info-subtle text-info border border-info-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle') }} small">
                    @if($depth === 0)
                        <i class="bi bi-hdd-stack me-1"></i> Top Level
                    @elseif($depth === 1)
                        <i class="bi bi-arrow-return-right me-1"></i> Dropdown Item
                    @else
                        <i class="bi bi-diagram-2 me-1"></i> Sub-Dropdown (Tier {{ $depth + 1 }})
                    @endif
                </span>

                @if($item->target === '_blank')
                    <span class="badge bg-secondary-subtle text-secondary small d-none d-sm-inline" title="Opens in new tab">
                        <i class="bi bi-box-arrow-up-right me-1"></i> New tab
                    </span>
                @endif

                @if(!$item->is_active)
                    <span class="badge bg-secondary text-white small">
                        <i class="bi bi-eye-slash me-1"></i> Hidden
                    </span>
                @endif
            </div>

            <!-- Right: Visibility, Edit & Delete Controls -->
            <div class="d-flex align-items-center gap-1">
                <!-- Quick One-Click Visibility Toggle -->
                <form action="{{ route('admin.menus.toggle-visibility', $item->id) }}" method="POST" class="d-inline">
                    @csrf
                    @if($item->is_active)
                        <button type="submit" class="btn btn-outline-success btn-sm rounded-pill px-2 py-1" title="Click to hide from website">
                            <i class="bi bi-eye me-1"></i> Visible
                        </button>
                    @else
                        <button type="submit" class="btn btn-outline-secondary btn-sm rounded-pill px-2 py-1" title="Click to show on website">
                            <i class="bi bi-eye-slash me-1"></i> Hidden
                        </button>
                    @endif
                </form>

                <!-- Edit Settings Collapse Toggle -->
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-2 py-1" data-bs-toggle="collapse" data-bs-target="#editItem-{{ $item->id }}" title="Edit item settings">
                    <i class="bi bi-gear"></i>
                </button>

                <!-- Delete Item Button -->
                <form action="{{ route('admin.menus.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove {{ addslashes($item->title) }} from menu? Any child items will also be removed.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-2 py-1" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Collapsible In-Place Edit Form -->
        <div class="collapse mt-3 pt-3 border-top" id="editItem-{{ $item->id }}">
            <form action="{{ route('admin.menus.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Navigation Label <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control form-control-sm rounded-pill" value="{{ $item->title }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">URL Path <span class="text-danger">*</span></label>
                        <input type="text" name="url" class="form-control form-control-sm rounded-pill" value="{{ $item->url }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Parent Menu (Dropdown Hierarchy)</label>
                        <select name="parent_id" class="form-select form-select-sm rounded-pill">
                            <option value="">-- None (Top Level Menu) --</option>
                            @foreach($allLocationItems as $opt)
                                @if($opt->id != $item->id)
                                    <option value="{{ $opt->id }}" {{ $item->parent_id == $opt->id ? 'selected' : '' }}>
                                        {{ $opt->parent_id ? '  ↳ ' : '' }}{{ $opt->title }} ({{ $opt->parent_id ? 'Dropdown Sub-item' : 'Top Level' }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Icon Class <span class="text-muted">(Bootstrap Icons)</span></label>
                        <input type="text" name="icon" class="form-control form-control-sm rounded-pill" value="{{ $item->icon }}" placeholder="bi bi-star">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Open Behavior</label>
                        <select name="target" class="form-select form-select-sm rounded-pill">
                            <option value="_self" {{ $item->target === '_self' ? 'selected' : '' }}>Same Tab (_self)</option>
                            <option value="_blank" {{ $item->target === '_blank' ? 'selected' : '' }}>New Tab (_blank)</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-center mt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activeSwitch-{{ $item->id }}" {{ $item->is_active ? 'checked' : '' }}>
                            <label class="form-check-label small fw-semibold" for="activeSwitch-{{ $item->id }}">Visible / Active</label>
                        </div>
                    </div>
                    <div class="col-12 text-end mt-2">
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">
                            <i class="bi bi-check-lg me-1"></i> Update Item
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Nested Dropzone for Child Menu Items (Dropdown inside Dropdown) -->
    <div class="menu-children-container nested-sortable mt-2 ms-4 border-start border-2 border-primary-subtle ps-2 py-1" data-parent-id="{{ $item->id }}">
        @if($item->children && $item->children->count() > 0)
            @foreach($item->children as $child)
                @include('admin.menus.item-node', ['item' => $child, 'depth' => $depth + 1, 'allLocationItems' => $allLocationItems])
            @endforeach
        @endif
    </div>
</div>
