@extends('layouts.admin')

@section('title', 'Media Library & Database Manager')
@section('page_title', 'Media Library')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1"><i class="bi bi-collection-play-fill text-primary me-2"></i> Centralized Media Library</h4>
            <p class="text-body-secondary small mb-0">Upload, manage, and embed Images, Videos, Audios, and Documents with persistent database tracking.</p>
        </div>
        <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="collapse" data-bs-target="#uploadCollapse" aria-expanded="false">
            <i class="bi bi-cloud-arrow-up-fill me-1"></i> Upload New Media
        </button>
    </div>

    <!-- Media Overview Analytics Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Files -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-body hover-lift h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-body-secondary small fw-semibold">Total Assets</span>
                        <h4 class="fw-bold mb-0 text-gradient mt-1">{{ number_format($stats['total_count']) }}</h4>
                        <small class="text-muted">{{ $stats['total_size'] }} used</small>
                    </div>
                    <div class="bg-primary-subtle text-primary p-3 rounded-4">
                        <i class="bi bi-folder-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Images -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-body hover-lift h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-body-secondary small fw-semibold">Images</span>
                        <h4 class="fw-bold mb-0 text-info mt-1">{{ number_format($stats['images_count']) }}</h4>
                        <small class="text-muted">WebP, PNG, JPG, SVG</small>
                    </div>
                    <div class="bg-info-subtle text-info p-3 rounded-4">
                        <i class="bi bi-image-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Videos & Audio -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-body hover-lift h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-body-secondary small fw-semibold">Videos & Audio</span>
                        <h4 class="fw-bold mb-0 text-danger mt-1">{{ number_format($stats['videos_count'] + $stats['audios_count']) }}</h4>
                        <small class="text-muted">{{ $stats['videos_count'] }} Videos &bull; {{ $stats['audios_count'] }} Audios</small>
                    </div>
                    <div class="bg-danger-subtle text-danger p-3 rounded-4">
                        <i class="bi bi-camera-reels-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documents -->
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm p-3 rounded-4 bg-body hover-lift h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-body-secondary small fw-semibold">Documents</span>
                        <h4 class="fw-bold mb-0 text-success mt-1">{{ number_format($stats['documents_count']) }}</h4>
                        <small class="text-muted">PDF, DOCX, ZIP, XLS</small>
                    </div>
                    <div class="bg-success-subtle text-success p-3 rounded-4">
                        <i class="bi bi-file-earmark-text-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Drag & Drop Multi-file Upload Zone (Collapsible / Dynamic) -->
    <div class="collapse mb-4" id="uploadCollapse">
        <div class="card border-0 shadow-sm rounded-4 bg-body p-4 border-2 border-dashed" style="border-color: rgba(99, 102, 241, 0.4) !important;">
            <form action="{{ route('admin.media.store') }}" method="POST" enctype="multipart/form-data" id="mediaUploadForm">
                @csrf
                <div class="text-center py-4">
                    <div class="mb-3">
                        <i class="bi bi-cloud-arrow-up text-primary" style="font-size: 3.5rem;"></i>
                    </div>
                    <h5 class="fw-bold mb-1">Drag & Drop Media Files Here</h5>
                    <p class="text-body-secondary small mb-3">Upload multiple Images (PNG, JPG, WEBP, SVG), Videos (MP4, WEBM), Audios (MP3, WAV), or Documents (PDF, DOCX, ZIP).</p>
                    
                    <input type="file" name="files[]" id="fileInput" class="d-none" multiple>
                    <button type="button" class="btn btn-gradient px-4 rounded-pill shadow-sm" onclick="document.getElementById('fileInput').click()">
                        <i class="bi bi-folder-plus me-1"></i> Browse Files from Device
                    </button>
                    <small class="d-block text-muted mt-2">Maximum file size: 100MB per asset</small>
                </div>

                <!-- Selected Files List / Upload Progress -->
                <div id="uploadFileList" class="d-none mt-3 pt-3 border-top">
                    <h6 class="fw-bold small mb-2 text-primary"><i class="bi bi-list-check me-1"></i> Selected Files (<span id="fileCountText">0</span>):</h6>
                    <div id="fileItemsContainer" class="d-flex flex-column gap-2 mb-3" style="max-height: 180px; overflow-y: auto;"></div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" onclick="clearSelectedFiles()">Clear</button>
                        <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4" id="submitUploadBtn">
                            <i class="bi bi-check2-circle me-1"></i> Start Uploading
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Filter & Search Controls Bar -->
    <div class="card border-0 shadow-sm p-3 rounded-4 bg-body mb-4">
        <div class="row g-3 align-items-center justify-content-between">
            <!-- Media Type Tabs -->
            <div class="col-lg-6">
                <div class="d-flex flex-wrap gap-1">
                    <a href="{{ route('admin.media.index') }}" class="btn btn-sm rounded-pill px-3 {{ empty($type) ? 'btn-primary' : 'btn-outline-secondary' }}">
                        All ({{ $stats['total_count'] }})
                    </a>
                    <a href="{{ route('admin.media.index', ['type' => 'image', 'search' => request('search')]) }}" class="btn btn-sm rounded-pill px-3 {{ $type === 'image' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        <i class="bi bi-image me-1"></i> Images ({{ $stats['images_count'] }})
                    </a>
                    <a href="{{ route('admin.media.index', ['type' => 'video', 'search' => request('search')]) }}" class="btn btn-sm rounded-pill px-3 {{ $type === 'video' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        <i class="bi bi-camera-video me-1"></i> Videos ({{ $stats['videos_count'] }})
                    </a>
                    <a href="{{ route('admin.media.index', ['type' => 'audio', 'search' => request('search')]) }}" class="btn btn-sm rounded-pill px-3 {{ $type === 'audio' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        <i class="bi bi-music-note-beamed me-1"></i> Audios ({{ $stats['audios_count'] }})
                    </a>
                    <a href="{{ route('admin.media.index', ['type' => 'document', 'search' => request('search')]) }}" class="btn btn-sm rounded-pill px-3 {{ $type === 'document' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        <i class="bi bi-file-earmark-text me-1"></i> Documents ({{ $stats['documents_count'] }})
                    </a>
                </div>
            </div>

            <!-- Search & Sorting -->
            <div class="col-lg-6">
                <form action="{{ route('admin.media.index') }}" method="GET" class="d-flex gap-2">
                    @if($type)
                        <input type="hidden" name="type" value="{{ $type }}">
                    @endif
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-body-tertiary rounded-start-pill ps-3"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Search file name, title..." value="{{ request('search') }}">
                    </div>

                    <select name="sort" class="form-select form-select-sm rounded-pill" style="max-width: 150px;" onchange="this.form.submit()">
                        <option value="latest" {{ $sort === 'latest' ? 'selected' : '' }}>Newest</option>
                        <option value="oldest" {{ $sort === 'oldest' ? 'selected' : '' }}>Oldest</option>
                        <option value="size_desc" {{ $sort === 'size_desc' ? 'selected' : '' }}>Largest Size</option>
                        <option value="size_asc" {{ $sort === 'size_asc' ? 'selected' : '' }}>Smallest Size</option>
                        <option value="name_asc" {{ $sort === 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                    </select>

                    <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3">Filter</button>
                    @if(request()->hasAny(['search', 'type', 'sort']))
                        <a href="{{ route('admin.media.index') }}" class="btn btn-sm btn-outline-secondary rounded-circle" title="Reset Filters"><i class="bi bi-arrow-counterclockwise"></i></a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Media Grid Cards -->
    <div class="row g-3">
        @forelse($mediaItems as $media)
            <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden bg-body hover-lift position-relative media-card" id="media-card-{{ $media->id }}">
                    <!-- Thumbnail / Preview Area -->
                    <div class="position-relative bg-body-tertiary d-flex align-items-center justify-content-center overflow-hidden" style="height: 140px; cursor: pointer;" onclick="openMediaModal({{ $media->id }})">
                        @if($media->is_image)
                            <img src="{{ $media->url }}" alt="{{ $media->alt_text ?: $media->title }}" class="img-fluid w-100 h-100 object-fit-cover">
                            @if($media->width && $media->height)
                                <span class="badge bg-dark bg-opacity-75 position-absolute bottom-0 end-0 m-1.5 rounded-pill" style="font-size: 0.62rem;">
                                    {{ $media->width }}&times;{{ $media->height }}
                                </span>
                            @endif
                        @elseif($media->is_video)
                            <div class="text-center p-3">
                                <i class="bi bi-play-circle-fill text-danger fs-1"></i>
                                <span class="d-block small fw-semibold text-truncate mt-1" style="font-size: 0.72rem;">Video</span>
                            </div>
                        @elseif($media->is_audio)
                            <div class="text-center p-3">
                                <i class="bi bi-music-note-beamed text-success fs-1"></i>
                                <span class="d-block small fw-semibold text-truncate mt-1" style="font-size: 0.72rem;">Audio</span>
                            </div>
                        @else
                            <div class="text-center p-3">
                                <i class="bi {{ $media->icon }} fs-1"></i>
                                <span class="d-block small fw-bold text-uppercase text-truncate mt-1" style="font-size: 0.68rem;">
                                    {{ pathinfo($media->original_name, PATHINFO_EXTENSION) ?: 'File' }}
                                </span>
                            </div>
                        @endif

                        <!-- Type Badge -->
                        <span class="badge bg-primary-subtle text-primary position-absolute top-0 start-0 m-1.5 rounded-pill text-uppercase" style="font-size: 0.58rem; letter-spacing: 0.5px;">
                            {{ $media->media_type }}
                        </span>
                    </div>

                    <!-- Meta Body -->
                    <div class="p-2.5 d-flex flex-column flex-grow-1">
                        <h6 class="fw-semibold text-truncate mb-1 small" title="{{ $media->original_name }}" style="font-size: 0.82rem;">
                            {{ $media->title ?: $media->original_name }}
                        </h6>
                        <div class="d-flex justify-content-between align-items-center mt-auto small text-muted" style="font-size: 0.72rem;">
                            <span>{{ $media->formatted_size }}</span>
                            <span>{{ $media->created_at->format('M d') }}</span>
                        </div>
                    </div>

                    <!-- Card Actions Strip -->
                    <div class="card-footer bg-body-tertiary border-top p-1.5 d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-link btn-sm text-decoration-none text-primary p-0" title="Inspect & Edit Details" onclick="openMediaModal({{ $media->id }})">
                            <i class="bi bi-pencil-square"></i> Info
                        </button>
                        <button type="button" class="btn btn-link btn-sm text-decoration-none text-secondary p-0" title="Copy Public URL" onclick="copyMediaUrl('{{ $media->url }}')">
                            <i class="bi bi-link-45deg fs-6"></i> Copy
                        </button>
                        <form action="{{ route('admin.media.destroy', $media->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to permanently delete this media file?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link btn-sm text-decoration-none text-danger p-0" title="Delete">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="p-5 text-center bg-body rounded-4 shadow-sm border-0">
                    <i class="bi bi-folder-x fs-1 text-muted"></i>
                    <h5 class="mt-3 fw-bold">No Media Files Found</h5>
                    <p class="text-muted small">Upload your first image, video, audio or document to get started.</p>
                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-4" onclick="document.getElementById('fileInput').click(); new bootstrap.Collapse(document.getElementById('uploadCollapse')).show();">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Upload First Asset
                    </button>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($mediaItems->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $mediaItems->links('pagination::bootstrap-5') }}
        </div>
    @endif

    <!-- Media Inspector / Metadata Edit Modal -->
    <div class="modal fade" id="mediaDetailModal" tabindex="-1" aria-labelledby="mediaDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden bg-body">
                <div class="modal-header border-bottom py-3">
                    <h6 class="modal-title fw-bold" id="mediaDetailModalLabel"><i class="bi bi-info-circle text-primary me-1"></i> Asset Inspector</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <!-- Preview Column -->
                        <div class="col-md-6 text-center d-flex flex-column justify-content-center align-items-center bg-body-tertiary rounded-4 p-3 border">
                            <div id="modalPreviewContent" class="w-100 d-flex align-items-center justify-content-center" style="min-height: 220px; max-height: 300px;">
                                <!-- Dynamically injected via JS -->
                            </div>
                            <div class="mt-3 d-flex gap-2">
                                <a id="modalDownloadBtn" href="#" download class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                    <i class="bi bi-download me-1"></i> Download
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" id="modalCopyUrlBtn">
                                    <i class="bi bi-link-45deg me-1"></i> Copy Link
                                </button>
                            </div>
                        </div>

                        <!-- Metadata Edit Column -->
                        <div class="col-md-6">
                            <form id="mediaEditForm" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="mb-2">
                                    <label class="form-label small fw-bold mb-1">Title</label>
                                    <input type="text" name="title" id="modalMetaTitle" class="form-control form-control-sm rounded-3">
                                </div>

                                <div class="mb-2" id="modalAltTextGroup">
                                    <label class="form-label small fw-bold mb-1">Alt Text (Image SEO & Accessibility)</label>
                                    <input type="text" name="alt_text" id="modalMetaAlt" class="form-control form-control-sm rounded-3" placeholder="Describe image for search engines">
                                </div>

                                <div class="mb-2">
                                    <label class="form-label small fw-bold mb-1">Caption</label>
                                    <textarea name="caption" id="modalMetaCaption" rows="2" class="form-control form-control-sm rounded-3" placeholder="Add a caption..."></textarea>
                                </div>

                                <!-- File Specs Summary -->
                                <div class="p-2.5 rounded-3 bg-body-tertiary border mt-3 small">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">File Name:</span>
                                        <span class="fw-semibold text-truncate" id="modalSpecFileName" style="max-width: 180px;"></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">File Size:</span>
                                        <span class="fw-semibold" id="modalSpecSize"></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="text-muted">Type:</span>
                                        <span class="fw-semibold text-uppercase" id="modalSpecType"></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1" id="modalSpecDimRow">
                                        <span class="text-muted">Dimensions:</span>
                                        <span class="fw-semibold" id="modalSpecDim"></span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Uploaded:</span>
                                        <span class="fw-semibold" id="modalSpecDate"></span>
                                    </div>
                                </div>

                                <div class="mt-3 text-end">
                                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4">
                                        <i class="bi bi-check2-circle me-1"></i> Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Copy Toast Notification -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 99999;">
        <div id="copyToast" class="toast align-items-center text-bg-dark border-0 rounded-4 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="bi bi-clipboard-check-fill text-success fs-5"></i>
                    <span>Media URL copied to clipboard!</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // File Input Selection Handler
    const fileInput = document.getElementById('fileInput');
    const uploadFileList = document.getElementById('uploadFileList');
    const fileItemsContainer = document.getElementById('fileItemsContainer');
    const fileCountText = document.getElementById('fileCountText');

    if (fileInput) {
        fileInput.addEventListener('change', function() {
            fileItemsContainer.innerHTML = '';
            if (this.files && this.files.length > 0) {
                uploadFileList.classList.remove('d-none');
                fileCountText.textContent = this.files.length;

                Array.from(this.files).forEach((file, index) => {
                    const sizeFormatted = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                    const div = document.createElement('div');
                    div.className = 'p-2 rounded-3 bg-body border d-flex align-items-center justify-content-between small';
                    div.innerHTML = `
                        <div class="d-flex align-items-center gap-2 text-truncate">
                            <i class="bi bi-file-earmark text-primary fs-5"></i>
                            <span class="fw-semibold text-truncate" style="max-width: 250px;">${file.name}</span>
                            <span class="text-muted">(${sizeFormatted})</span>
                        </div>
                        <span class="badge bg-primary-subtle text-primary rounded-pill">Ready</span>
                    `;
                    fileItemsContainer.appendChild(div);
                });
            } else {
                uploadFileList.classList.add('d-none');
            }
        });
    }

    function clearSelectedFiles() {
        if (fileInput) fileInput.value = '';
        if (fileItemsContainer) fileItemsContainer.innerHTML = '';
        if (uploadFileList) uploadFileList.classList.add('d-none');
    }

    // Copy Media URL to Clipboard
    function copyMediaUrl(url) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(url).then(() => {
                showCopyToast();
            });
        } else {
            const input = document.createElement('input');
            input.value = url;
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);
            showCopyToast();
        }
    }

    function showCopyToast() {
        const toastEl = document.getElementById('copyToast');
        if (toastEl) {
            const toast = new bootstrap.Toast(toastEl, { delay: 2500 });
            toast.show();
        }
    }

    // Open Media Inspector & Metadata Modal
    function openMediaModal(id) {
        fetch(`/admin/media/${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const media = data.media;
                    const previewContainer = document.getElementById('modalPreviewContent');
                    
                    // Render preview based on type
                    if (media.media_type === 'image') {
                        previewContainer.innerHTML = `<img src="${media.url}" alt="${media.alt_text || ''}" class="img-fluid rounded-3 object-fit-contain shadow-sm" style="max-height: 260px;">`;
                        document.getElementById('modalAltTextGroup').classList.remove('d-none');
                    } else if (media.media_type === 'video') {
                        previewContainer.innerHTML = `
                            <video controls class="w-100 rounded-3 shadow-sm" style="max-height: 240px;">
                                <source src="${media.url}" type="${media.mime_type}">
                                Your browser does not support the video tag.
                            </video>
                        `;
                        document.getElementById('modalAltTextGroup').classList.add('d-none');
                    } else if (media.media_type === 'audio') {
                        previewContainer.innerHTML = `
                            <div class="w-100 text-center">
                                <i class="bi bi-music-note-beamed text-success display-3 mb-2 d-inline-block"></i>
                                <audio controls class="w-100 mt-2">
                                    <source src="${media.url}" type="${media.mime_type}">
                                </audio>
                            </div>
                        `;
                        document.getElementById('modalAltTextGroup').classList.add('d-none');
                    } else {
                        previewContainer.innerHTML = `
                            <div class="text-center">
                                <i class="bi ${media.icon} display-1 mb-2"></i>
                                <div class="fw-bold text-truncate" style="max-width: 250px;">${media.original_name}</div>
                            </div>
                        `;
                        document.getElementById('modalAltTextGroup').classList.add('d-none');
                    }

                    // Download & Copy Button URLs
                    const downloadBtn = document.getElementById('modalDownloadBtn');
                    downloadBtn.href = media.url;

                    const copyBtn = document.getElementById('modalCopyUrlBtn');
                    copyBtn.onclick = () => copyMediaUrl(media.url);

                    // Form Fields
                    document.getElementById('mediaEditForm').action = `/admin/media/${media.id}`;
                    document.getElementById('modalMetaTitle').value = media.title || '';
                    document.getElementById('modalMetaAlt').value = media.alt_text || '';
                    document.getElementById('modalMetaCaption').value = media.caption || '';

                    // Specifications
                    document.getElementById('modalSpecFileName').textContent = media.file_name;
                    document.getElementById('modalSpecFileName').title = media.file_name;
                    document.getElementById('modalSpecSize').textContent = media.formatted_size;
                    document.getElementById('modalSpecType').textContent = media.media_type + ' (' + media.mime_type + ')';
                    document.getElementById('modalSpecDate').textContent = data.formatted_created_at;

                    const dimRow = document.getElementById('modalSpecDimRow');
                    if (media.width && media.height) {
                        dimRow.classList.remove('d-none');
                        document.getElementById('modalSpecDim').textContent = `${media.width} x ${media.height} px`;
                    } else {
                        dimRow.classList.add('d-none');
                    }

                    // Show Modal
                    const modal = new bootstrap.Modal(document.getElementById('mediaDetailModal'));
                    modal.show();
                }
            })
            .catch(err => {
                console.error('Failed to load media specs:', err);
            });
    }
</script>
@endpush
