<!-- ========================================================================= -->
<!-- Summernote Image SEO, Alt, Title & Caption Editor Modal -->
<!-- ========================================================================= -->
<div class="modal fade text-start" id="editorImageCaptionModal" tabindex="-1" aria-labelledby="editorImageCaptionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg bg-body">
            <div class="modal-header border-bottom py-3">
                <h6 class="modal-title fw-bold" id="editorImageCaptionModalLabel">
                    <i class="bi bi-card-image text-primary me-2"></i> Image Attributes & Caption (SEO)
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Image Thumbnail Preview -->
                <div class="text-center mb-3 bg-body-tertiary p-2 rounded-3 border">
                    <img id="captionModalPreviewImg" src="" alt="" class="img-fluid rounded-2" style="max-height: 160px; object-fit: contain;">
                </div>

                <!-- Alt Text -->
                <div class="mb-3">
                    <label class="form-label small fw-bold">Alt Text (Image SEO & Accessibility) <span class="text-danger">*</span></label>
                    <input type="text" id="captionModalAltInput" class="form-control form-control-sm rounded-3" placeholder="Describe this image for Google & screen readers">
                    <div class="d-flex justify-content-between mt-1">
                        <small class="text-muted" style="font-size: 0.75rem;">Crucial for Google Image Search ranking & accessibility.</small>
                        <small id="captionModalAltCount" class="text-muted" style="font-size: 0.75rem;">0 / 125</small>
                    </div>
                </div>

                <!-- Image Title / Hover Tooltip -->
                <div class="mb-3">
                    <label class="form-label small fw-bold">Image Title (Desktop Tooltip on Hover)</label>
                    <input type="text" id="captionModalTitleInput" class="form-control form-control-sm rounded-3" placeholder="Optional title tooltip">
                </div>

                <!-- Caption -->
                <div class="mb-3">
                    <label class="form-label small fw-bold">Caption (Visible under the image in article)</label>
                    <textarea id="captionModalCaptionInput" rows="2" class="form-control form-control-sm rounded-3" placeholder="e.g. Figure 1: Google Pixel Drop November 2026 features overview"></textarea>
                    <small class="text-muted" style="font-size: 0.75rem;">Rendered as a semantic &lt;figcaption&gt; below the image.</small>
                </div>

                <!-- Alignment & Display -->
                <div class="mb-2">
                    <label class="form-label small fw-bold">Display Layout</label>
                    <div class="btn-group w-100 btn-group-sm" role="group">
                        <input type="radio" class="btn-check" name="captionModalAlign" id="alignCenter" value="center" checked>
                        <label class="btn btn-outline-secondary" for="alignCenter"><i class="bi bi-text-center me-1"></i> Center</label>

                        <input type="radio" class="btn-check" name="captionModalAlign" id="alignFull" value="full">
                        <label class="btn btn-outline-secondary" for="alignFull"><i class="bi bi-arrows-fullscreen me-1"></i> Full Width</label>

                        <input type="radio" class="btn-check" name="captionModalAlign" id="alignLeft" value="left">
                        <label class="btn btn-outline-secondary" for="alignLeft"><i class="bi bi-text-left me-1"></i> Float Left</label>

                        <input type="radio" class="btn-check" name="captionModalAlign" id="alignRight" value="right">
                        <label class="btn btn-outline-secondary" for="alignRight"><i class="bi bi-text-right me-1"></i> Float Right</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-4" id="btnApplyImageCaption">
                    <i class="bi bi-check2-circle me-1"></i> Apply to Image
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- Media Library Picker & Quick Uploader Modal for Summernote -->
<!-- ========================================================================= -->
<div class="modal fade text-start" id="editorMediaPickerModal" tabindex="-1" aria-labelledby="editorMediaPickerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow-lg bg-body">
            <div class="modal-header border-bottom py-3">
                <div class="d-flex align-items-center gap-2">
                    <h6 class="modal-title fw-bold" id="editorMediaPickerModalLabel">
                        <i class="bi bi-collection-play-fill text-primary me-2"></i> Media Library &amp; Asset Inserter
                    </h6>
                    <span class="badge bg-primary-subtle text-primary rounded-pill">Images, Audios, Videos, Docs</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Nav Tabs -->
                <ul class="nav nav-tabs px-3 pt-2 bg-body-tertiary border-bottom" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active small fw-semibold" id="tab-library-btn" data-bs-toggle="tab" data-bs-target="#tab-library" type="button" role="tab">
                            <i class="bi bi-grid-3x3-gap me-1"></i> Browse Library
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link small fw-semibold" id="tab-upload-btn" data-bs-toggle="tab" data-bs-target="#tab-upload" type="button" role="tab">
                            <i class="bi bi-cloud-arrow-up-fill me-1 text-primary"></i> Upload New Asset
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Tab 1: Library Browser -->
                    <div class="tab-pane fade show active p-3" id="tab-library" role="tabpanel">
                        <!-- Filters Header -->
                        <div class="row g-2 mb-3 align-items-center">
                            <div class="col-md-7">
                                <div class="btn-group btn-group-sm flex-wrap" role="group">
                                    <button type="button" class="btn btn-outline-primary active" data-picker-type="">All</button>
                                    <button type="button" class="btn btn-outline-primary" data-picker-type="image"><i class="bi bi-image me-1"></i> Images</button>
                                    <button type="button" class="btn btn-outline-primary" data-picker-type="audio"><i class="bi bi-music-note-beamed me-1"></i> Audio</button>
                                    <button type="button" class="btn btn-outline-primary" data-picker-type="video"><i class="bi bi-camera-video me-1"></i> Video</button>
                                    <button type="button" class="btn btn-outline-primary" data-picker-type="document"><i class="bi bi-file-earmark-pdf me-1"></i> Documents</button>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-body-tertiary"><i class="bi bi-search"></i></span>
                                    <input type="text" id="pickerSearchInput" class="form-control" placeholder="Search by name, title, or caption...">
                                </div>
                            </div>
                        </div>

                        <!-- Browser Grid + Detail Sidebar -->
                        <div class="row g-3">
                            <div class="col-lg-8" style="min-height: 380px; max-height: 520px; overflow-y: auto;">
                                <div id="pickerMediaGrid" class="row g-2">
                                    <div class="col-12 text-center py-5 text-muted">
                                        <div class="spinner-border text-primary spinner-border-sm mb-2" role="status"></div>
                                        <div>Loading Media Library...</div>
                                    </div>
                                </div>
                                <div id="pickerPagination" class="d-flex justify-content-center mt-3"></div>
                            </div>
                            
                            <!-- Selection Details Sidebar -->
                            <div class="col-lg-4 border-start bg-body-tertiary rounded-3 p-3">
                                <h6 class="fw-bold small text-muted text-uppercase mb-3"><i class="bi bi-sliders me-1"></i> Asset Details</h6>
                                <div id="pickerSelectedEmpty" class="text-center py-5 text-muted small">
                                    <i class="bi bi-cursor fs-2 d-block mb-2 text-secondary"></i>
                                    Click any asset from the grid to select and insert into your article.
                                </div>
                                <div id="pickerSelectedDetails" class="d-none">
                                    <div class="text-center mb-3 p-2 bg-body rounded-3 border">
                                        <div id="pickerSelectedPreview" style="max-height: 140px; overflow: hidden;" class="d-flex align-items-center justify-content-center"></div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold mb-1">Title</label>
                                        <input type="text" id="pickerSelectedTitle" class="form-control form-control-sm">
                                    </div>
                                    <div class="mb-2" id="pickerSelectedAltGroup">
                                        <label class="form-label small fw-bold mb-1">Alt Text (Image SEO)</label>
                                        <input type="text" id="pickerSelectedAlt" class="form-control form-control-sm">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold mb-1">Caption</label>
                                        <textarea id="pickerSelectedCaption" rows="2" class="form-control form-control-sm" placeholder="Visible caption under image..."></textarea>
                                    </div>
                                    <button type="button" class="btn btn-primary btn-sm w-100 rounded-pill py-2 fw-semibold" id="btnConfirmPickerInsert">
                                        <i class="bi bi-box-arrow-in-down me-1"></i> Insert Into Article
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Quick Upload Dropzone -->
                    <div class="tab-pane fade p-4" id="tab-upload" role="tabpanel">
                        <div class="text-center p-5 border-2 border-dashed rounded-4 bg-body-tertiary" style="border-color: rgba(99, 102, 241, 0.4) !important;">
                            <i class="bi bi-cloud-arrow-up display-3 text-primary mb-3 d-inline-block"></i>
                            <h5 class="fw-bold mb-2">Drag &amp; Drop Media Files Here</h5>
                            <p class="text-muted small mb-3">Upload Images (PNG, JPG, WebP), Audio (MP3, WAV), Videos (MP4), or Documents (PDF, DOCX, ZIP).</p>
                            <input type="file" id="pickerQuickFileInput" class="d-none" multiple accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip">
                            <button type="button" class="btn btn-primary rounded-pill px-4" onclick="document.getElementById('pickerQuickFileInput').click()">
                                <i class="bi bi-folder2-open me-1"></i> Choose Files to Upload
                            </button>
                            <div id="pickerUploadProgress" class="progress mt-4 d-none" style="height: 8px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" style="width: 100%"></div>
                            </div>
                            <div id="pickerUploadStatus" class="small mt-2 text-muted"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-sm btn-secondary rounded-pill" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- Frontend & Editor Caption Styles -->
<!-- ========================================================================= -->
<style>
figure.post-image-figure {
    margin: 1.8rem auto;
    display: table;
    max-width: 100%;
}
figure.post-image-figure.align-left {
    float: left;
    margin: 0.5rem 1.5rem 1rem 0;
    max-width: 48%;
}
figure.post-image-figure.align-right {
    float: right;
    margin: 0.5rem 0 1rem 1.5rem;
    max-width: 48%;
}
figure.post-image-figure.align-full {
    width: 100%;
}
figure.post-image-figure img {
    display: block;
    margin: 0 auto;
    border-radius: 0.75rem;
    max-width: 100%;
    height: auto;
}
figcaption.post-image-caption {
    display: table-caption;
    caption-side: bottom;
    text-align: center;
    font-size: 0.85rem;
    color: rgba(255, 255, 255, 0.65);
    font-style: italic;
    margin-top: 0.45rem;
    padding: 0 0.5rem;
    letter-spacing: 0.01em;
}
[data-bs-theme="light"] figcaption.post-image-caption {
    color: #6c757d;
}
.media-picker-item {
    cursor: pointer;
    transition: transform 0.15s ease, border-color 0.15s ease;
    border: 2px solid transparent;
}
.media-picker-item:hover {
    transform: translateY(-2px);
    border-color: #6366f1 !important;
}
.media-picker-item.selected {
    border-color: #6366f1 !important;
    background-color: rgba(99, 102, 241, 0.08) !important;
}
</style>
