@extends('layouts.admin')

@section('title', 'Menu Builder & Drag & Drop Arrangement')
@section('page_title', 'Menu Management')

@push('styles')
    <style>
        .menu-item-card {
            transition: all 0.2s ease;
            border-left: 4px solid var(--bs-primary);
        }
        .menu-item-card.is-hidden {
            border-left-color: var(--bs-secondary);
            background-color: var(--bs-tertiary-bg) !important;
            opacity: 0.8;
        }
        .menu-item-card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
        }
        .drag-handle {
            cursor: grab;
            user-select: none;
        }
        .drag-handle:active {
            cursor: grabbing;
        }
        .sortable-ghost {
            opacity: 0.4;
            background: rgba(var(--bs-primary-rgb), 0.1) !important;
            border: 2px dashed var(--bs-primary) !important;
        }
        .sortable-chosen {
            background-color: var(--bs-body-bg);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .bulk-action-bar {
            position: sticky;
            top: 70px;
            z-index: 1020;
            backdrop-filter: blur(10px);
        }
    </style>
@endpush

@section('content')
    <!-- Header Controls -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1">Navigation Menu Builder</h4>
            <p class="text-body-secondary small mb-0">Drag and drop menu items to rearrange the site navigation bar and footer quick links.</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-warning rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#resetMenuModal">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset to Defaults
            </button>
            <button type="button" id="saveOrderBtn" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="bi bi-check2-circle me-1"></i> Save Arrangement
            </button>
        </div>
    </div>

    <!-- Location Switcher Tabs -->
    <div class="card border-0 shadow-sm rounded-4 bg-body mb-4 p-2">
        <ul class="nav nav-pills nav-fill">
            <li class="nav-item">
                <a class="nav-link rounded-pill fw-semibold py-2 {{ $location === 'header' ? 'active' : '' }}" href="{{ route('admin.menus.index', ['location' => 'header']) }}">
                    <i class="bi bi-layout-text-window-reverse me-2"></i> Header Navigation Menu
                    <span class="badge {{ $location === 'header' ? 'bg-light text-primary' : 'bg-primary-subtle text-primary' }} ms-2">{{ $location === 'header' ? $menuItems->count() : \App\Models\MenuItem::location('header')->count() }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link rounded-pill fw-semibold py-2 {{ $location === 'footer' ? 'active' : '' }}" href="{{ route('admin.menus.index', ['location' => 'footer']) }}">
                    <i class="bi bi-layout-text-sidebar-reverse me-2"></i> Footer Navigation Menu
                    <span class="badge {{ $location === 'footer' ? 'bg-light text-primary' : 'bg-primary-subtle text-primary' }} ms-2">{{ $location === 'footer' ? $menuItems->count() : \App\Models\MenuItem::location('footer')->count() }}</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Live Toast Alert -->
    <div id="saveToast" class="alert alert-success d-none align-items-center rounded-4 shadow-sm py-2 px-3 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
        <div id="saveToastText" class="small fw-semibold">Menu arrangement saved successfully!</div>
    </div>

    <!-- Selection & Bulk Actions Bar -->
    <div id="bulkActionBar" class="card border-0 shadow-lg rounded-4 bg-primary text-white p-3 mb-4 d-none bulk-action-bar">
        <form id="bulkActionForm" action="{{ route('admin.menus.bulk-action') }}" method="POST" class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            @csrf
            <input type="hidden" name="location" value="{{ $location }}">
            <input type="hidden" name="action" id="bulkActionInput" value="hide">
            <div id="bulkSelectedInputsContainer"></div>

            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check2-square fs-5"></i>
                <span class="fw-bold"><span id="selectedCount">0</span> items selected</span>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-warning btn-sm rounded-pill px-3 shadow-sm" onclick="submitBulkAction('hide')">
                    <i class="bi bi-eye-slash-fill me-1"></i> Hide Selected
                </button>
                <button type="button" class="btn btn-light text-primary btn-sm rounded-pill px-3 shadow-sm" onclick="submitBulkAction('show')">
                    <i class="bi bi-eye-fill me-1"></i> Show / Unhide Selected
                </button>
                <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm" onclick="submitBulkAction('delete')">
                    <i class="bi bi-trash-fill me-1"></i> Delete Selected
                </button>
                <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-2" onclick="deselectAllItems()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </form>
    </div>

    <div class="row g-4">
        <!-- Left Column: Add Menu Items Accordion -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 bg-body p-4 mb-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-plus-circle-fill text-primary me-2"></i> Add Menu Items</h5>

                <div class="accordion accordion-flush" id="menuAddAccordion">
                    <!-- 1. Quick Standard Links -->
                    <div class="accordion-item bg-transparent border-0 mb-2">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded-3 bg-body-tertiary fw-semibold py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStandard">
                                <i class="bi bi-lightning-charge text-warning me-2"></i> Standard Links
                            </button>
                        </h2>
                        <div id="collapseStandard" class="accordion-collapse collapse" data-bs-parent="#menuAddAccordion">
                            <div class="accordion-body px-1 py-3">
                                <form action="{{ route('admin.menus.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="type" value="standard">
                                    <input type="hidden" name="location" value="{{ $location }}">

                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="standards[]" value="home" id="stdHome" checked>
                                        <label class="form-check-label small" for="stdHome">Home (/)</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="standards[]" value="articles" id="stdArticles" checked>
                                        <label class="form-check-label small" for="stdArticles">All Articles (/blog)</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="standards[]" value="faq" id="stdFaq">
                                        <label class="form-check-label small" for="stdFaq">FAQ (/faq)</label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" name="standards[]" value="contact" id="stdContact">
                                        <label class="form-check-label small" for="stdContact">Contact Us (/contact)</label>
                                    </div>

                                    <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill w-100">
                                        <i class="bi bi-plus-lg me-1"></i> Add to {{ ucfirst($location) }} Menu
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Dynamic Pages -->
                    <div class="accordion-item bg-transparent border-0 mb-2">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded-3 bg-body-tertiary fw-semibold py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePages">
                                <i class="bi bi-file-earmark-richtext text-info me-2"></i> Custom Pages ({{ $pages->count() }})
                            </button>
                        </h2>
                        <div id="collapsePages" class="accordion-collapse collapse" data-bs-parent="#menuAddAccordion">
                            <div class="accordion-body px-1 py-3">
                                @if($pages->count() > 0)
                                    <form action="{{ route('admin.menus.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="type" value="pages">
                                        <input type="hidden" name="location" value="{{ $location }}">

                                        <div class="mb-3" style="max-height: 180px; overflow-y: auto;">
                                            @foreach($pages as $page)
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="pages[]" value="{{ $page->id }}" id="page-{{ $page->id }}">
                                                    <label class="form-check-label small text-truncate d-block" for="page-{{ $page->id }}" title="{{ $page->title }}">
                                                        {{ $page->title }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>

                                        <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill w-100">
                                            <i class="bi bi-plus-lg me-1"></i> Add Selected Pages
                                        </button>
                                    </form>
                                @else
                                    <p class="text-muted small mb-2">No pages found.</p>
                                    <a href="{{ route('admin.pages.create') }}" class="btn btn-sm btn-link text-decoration-none p-0">+ Create a page</a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- 3. Blog Categories -->
                    <div class="accordion-item bg-transparent border-0 mb-2">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded-3 bg-body-tertiary fw-semibold py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCategories">
                                <i class="bi bi-folder2 text-success me-2"></i> Categories ({{ $categories->count() }})
                            </button>
                        </h2>
                        <div id="collapseCategories" class="accordion-collapse collapse" data-bs-parent="#menuAddAccordion">
                            <div class="accordion-body px-1 py-3">
                                @if($categories->count() > 0)
                                    <form action="{{ route('admin.menus.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="type" value="categories">
                                        <input type="hidden" name="location" value="{{ $location }}">

                                        <div class="mb-3" style="max-height: 180px; overflow-y: auto;">
                                            @foreach($categories as $cat)
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="categories[]" value="{{ $cat->id }}" id="cat-{{ $cat->id }}">
                                                    <label class="form-check-label small text-truncate d-block" for="cat-{{ $cat->id }}">
                                                        <span class="d-inline-block rounded-circle me-1" style="width: 8px; height: 8px; background-color: {{ $cat->color }};"></span>
                                                        {{ $cat->name }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>

                                        <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill w-100">
                                            <i class="bi bi-plus-lg me-1"></i> Add Selected Categories
                                        </button>
                                    </form>
                                @else
                                    <p class="text-muted small mb-0">No categories found.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- 4. Custom Link -->
                    <div class="accordion-item bg-transparent border-0">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed rounded-3 bg-body-tertiary fw-semibold py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCustom">
                                <i class="bi bi-link-45deg text-primary me-2"></i> Custom Link
                            </button>
                        </h2>
                        <div id="collapseCustom" class="accordion-collapse collapse" data-bs-parent="#menuAddAccordion">
                            <div class="accordion-body px-1 py-3">
                                <form action="{{ route('admin.menus.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="type" value="custom">
                                    <input type="hidden" name="location" value="{{ $location }}">

                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold">Link Text <span class="text-danger">*</span></label>
                                        <input type="text" name="title" class="form-control form-control-sm rounded-pill" placeholder="e.g. GitHub, Documentation" required>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold">URL Path <span class="text-danger">*</span></label>
                                        <input type="text" name="url" class="form-control form-control-sm rounded-pill" placeholder="/shrawan-effects or https://..." required>
                                        <small class="text-muted d-block mt-1" style="font-size:0.75rem;">Enter clean text URL (e.g. <kbd>/shrawan-effects</kbd>) or full external URL.</small>
                                    </div>

                                    <div class="mb-2">
                                        <label class="form-label small fw-semibold">Icon Class <span class="text-muted">(optional)</span></label>
                                        <input type="text" name="icon" class="form-control form-control-sm rounded-pill" placeholder="bi bi-star">
                                    </div>

                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" name="target" value="_blank" id="customTarget">
                                        <label class="form-check-label small" for="customTarget">Open in new tab (_blank)</label>
                                    </div>

                                    <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill w-100">
                                        <i class="bi bi-plus-lg me-1"></i> Add Custom Link
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Drag and Drop Canvas -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" id="selectAllCheckbox" title="Select all menu items">
                            <label class="form-check-label small fw-semibold text-muted" for="selectAllCheckbox">Select All</label>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0"><i class="bi bi-arrows-move text-primary me-2"></i> Menu Arrangement</h5>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1">
                            {{ $menuItems->count() }} Total
                        </span>
                        <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1">
                            {{ $menuItems->where('is_active', true)->count() }} Visible
                        </span>
                        @if($menuItems->where('is_active', false)->count() > 0)
                            <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1">
                                {{ $menuItems->where('is_active', false)->count() }} Hidden
                            </span>
                        @endif
                    </div>
                </div>

                <p class="text-body-secondary small mb-3">
                    Drag items to reorder them, click the <strong>Eye (<i class="bi bi-eye"></i>)</strong> button to quickly hide/unhide, or select multiple items to hide them together.
                </p>

                @if($menuItems->count() > 0)
                    <div id="menuSortableList" class="d-flex flex-column gap-2 mb-3">
                        @foreach($menuItems as $item)
                            <div class="card border rounded-3 p-3 bg-body shadow-sm menu-item-card {{ !$item->is_active ? 'is-hidden' : '' }}" data-id="{{ $item->id }}">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <!-- Selection Checkbox, Drag Handle & Item Info -->
                                    <div class="d-flex align-items-center gap-2 flex-grow-1">
                                        <!-- Item Selection Checkbox -->
                                        <div class="form-check mb-0 me-1">
                                            <input class="form-check-input item-select-checkbox" type="checkbox" value="{{ $item->id }}" id="chk-{{ $item->id }}">
                                        </div>

                                        <div class="drag-handle text-muted px-1 py-1 fs-5" title="Click & Drag to reorder">
                                            <i class="bi bi-grip-vertical"></i>
                                        </div>

                                        <div class="d-flex align-items-center gap-2">
                                            @if($item->icon)
                                                <i class="{{ $item->icon }} {{ $item->is_active ? 'text-primary' : 'text-muted' }}"></i>
                                            @endif
                                            <span class="fw-bold item-title {{ !$item->is_active ? 'text-muted text-decoration-line-through' : '' }}">{{ $item->title }}</span>
                                        </div>

                                        <span class="text-muted small font-monospace d-none d-md-inline-block text-truncate" style="max-width: 180px;">
                                            {{ $item->url }}
                                        </span>

                                        @if($item->target === '_blank')
                                            <span class="badge bg-secondary-subtle text-secondary small d-none d-sm-inline" title="Opens in new tab">
                                                <i class="bi bi-box-arrow-up-right me-1"></i> New tab
                                            </span>
                                        @endif

                                        @if(!$item->is_active)
                                            <span class="badge bg-secondary text-white small">
                                                <i class="bi bi-eye-slash me-1"></i> Hidden from site
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Quick Visibility Toggle, Edit & Delete Controls -->
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

                                        <!-- Edit Modal Toggle -->
                                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-2 py-1" data-bs-toggle="collapse" data-bs-target="#editItem-{{ $item->id }}" title="Edit item settings">
                                            <i class="bi bi-gear"></i>
                                        </button>

                                        <!-- Delete Item Button -->
                                        <form action="{{ route('admin.menus.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove {{ addslashes($item->title) }} from menu?');">
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
                                            <div class="col-md-6">
                                                <label class="form-label small fw-semibold">Navigation Label</label>
                                                <input type="text" name="title" class="form-control form-control-sm rounded-pill" value="{{ $item->title }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-semibold">URL</label>
                                                <input type="text" name="url" class="form-control form-control-sm rounded-pill" value="{{ $item->url }}" required>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-semibold">Icon Class</label>
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
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-diagram-3 fs-1 d-block mb-3 opacity-50"></i>
                        <h6 class="fw-bold">No Menu Items Configured</h6>
                        <p class="small mb-3">Add links from the left panel or click "Reset to Defaults" to populate the standard layout.</p>
                        <button type="button" class="btn btn-primary rounded-pill btn-sm px-4" data-bs-toggle="modal" data-bs-target="#resetMenuModal">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Populate Standard Menu
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Reset to Default Menu Confirmation Modal -->
    <div class="modal fade" id="resetMenuModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-warning">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Reset {{ ucfirst($location) }} Menu
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to reset the <strong>{{ ucfirst($location) }} Navigation Menu</strong> to default layout? This will replace your current custom arrangement with standard items.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.menus.reset-defaults') }}" method="POST">
                        @csrf
                        <input type="hidden" name="location" value="{{ $location }}">
                        <button type="submit" class="btn btn-warning rounded-pill px-4">Confirm Reset</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- SortableJS library for Drag and Drop -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const menuListEl = document.getElementById('menuSortableList');
            const saveBtn = document.getElementById('saveOrderBtn');
            const toastEl = document.getElementById('saveToast');
            const toastText = document.getElementById('saveToastText');
            const selectAllChk = document.getElementById('selectAllCheckbox');
            const bulkBar = document.getElementById('bulkActionBar');
            const selectedCountSpan = document.getElementById('selectedCount');

            // Bulk selection handling
            function updateBulkBar() {
                const checkedBoxes = document.querySelectorAll('.item-select-checkbox:checked');
                const count = checkedBoxes.length;

                if (count > 0) {
                    bulkBar.classList.remove('d-none');
                    selectedCountSpan.textContent = count;
                } else {
                    bulkBar.classList.add('d-none');
                    if (selectAllChk) selectAllChk.checked = false;
                }
            }

            if (selectAllChk) {
                selectAllChk.addEventListener('change', function () {
                    const checkboxes = document.querySelectorAll('.item-select-checkbox');
                    checkboxes.forEach(cb => {
                        cb.checked = selectAllChk.checked;
                    });
                    updateBulkBar();
                });
            }

            document.querySelectorAll('.item-select-checkbox').forEach(cb => {
                cb.addEventListener('change', updateBulkBar);
            });

            window.deselectAllItems = function () {
                document.querySelectorAll('.item-select-checkbox').forEach(cb => {
                    cb.checked = false;
                });
                if (selectAllChk) selectAllChk.checked = false;
                updateBulkBar();
            };

            window.submitBulkAction = function (action) {
                const checkedBoxes = document.querySelectorAll('.item-select-checkbox:checked');
                if (checkedBoxes.length === 0) {
                    alert('Please select at least one menu item.');
                    return;
                }

                if (action === 'delete' && !confirm('Are you sure you want to delete the selected menu items?')) {
                    return;
                }

                const container = document.getElementById('bulkSelectedInputsContainer');
                container.innerHTML = '';

                checkedBoxes.forEach(cb => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'ids[]';
                    input.value = cb.value;
                    container.appendChild(input);
                });

                document.getElementById('bulkActionInput').value = action;
                document.getElementById('bulkActionForm').submit();
            };

            function showToast(message, isSuccess = true) {
                toastEl.classList.remove('d-none', 'alert-success', 'alert-danger');
                toastEl.classList.add(isSuccess ? 'alert-success' : 'alert-danger', 'd-flex');
                toastText.textContent = message;
                setTimeout(() => {
                    toastEl.classList.add('d-none');
                    toastEl.classList.remove('d-flex');
                }, 3500);
            }

            function saveMenuArrangement() {
                if (!menuListEl) return;
                
                const items = [];
                const cards = menuListEl.querySelectorAll('.menu-item-card');
                cards.forEach((card, index) => {
                    items.push({
                        id: card.getAttribute('data-id'),
                        order: index + 1,
                        parent_id: null
                    });
                });

                if (saveBtn) {
                    saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Saving...';
                    saveBtn.disabled = true;
                }

                fetch("{{ route('admin.menus.reorder') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        location: "{{ $location }}",
                        items: items
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message || 'Menu order saved successfully!', true);
                    } else {
                        showToast('Failed to save menu order.', false);
                    }
                })
                .catch(err => {
                    console.error('Error reordering menu items:', err);
                    showToast('An error occurred while saving menu order.', false);
                })
                .finally(() => {
                    if (saveBtn) {
                        saveBtn.innerHTML = '<i class="bi bi-check2-circle me-1"></i> Save Arrangement';
                        saveBtn.disabled = false;
                    }
                });
            }

            if (menuListEl) {
                new Sortable(menuListEl, {
                    animation: 180,
                    handle: '.drag-handle',
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    dragClass: 'sortable-drag',
                    onEnd: function () {
                        // Automatically persist changes on drag release
                        saveMenuArrangement();
                    }
                });
            }

            if (saveBtn) {
                saveBtn.addEventListener('click', function () {
                    saveMenuArrangement();
                });
            }
        });
    </script>
@endpush
