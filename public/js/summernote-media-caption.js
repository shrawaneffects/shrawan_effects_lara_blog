/**
 * Summernote Media Library & Image Caption Plugin
 * Shrawan Effects - 2026 CMS Suite
 */

let activeEditorImg = null;
let activePickerItem = null;
let currentPickerType = '';
let currentPickerPage = 1;

function initSummernoteWithMedia(editorSelector, options = {}) {
    const $editor = $(editorSelector);
    if (!$editor.length) return;

    // Define Custom Button: Media Library
    const MediaLibraryButton = function (context) {
        const ui = $.summernote.ui;
        const button = ui.button({
            contents: '<i class="bi bi-collection-play-fill text-primary me-1"></i> <span class="fw-semibold small">Media</span>',
            tooltip: 'Insert Image, Audio, Video, or Document from Media Library',
            click: function () {
                openEditorMediaPicker();
            }
        });
        return button.render();
    };

    // Define Custom Button: Image Caption / Alt SEO
    const ImageCaptionButton = function (context) {
        const ui = $.summernote.ui;
        const button = ui.button({
            contents: '<i class="bi bi-chat-quote-fill text-warning me-1"></i> <span class="fw-semibold small">Caption & Alt</span>',
            tooltip: 'Add or Edit Image Caption & Alt SEO Tags',
            click: function () {
                // If an image is selected, edit it. Otherwise prompt user to select an image.
                const $editable = $editor.next('.note-editor').find('.note-editable');
                let $selectedImg = $editable.find('img:hover, img.active, img.selected');
                if (!$selectedImg.length) {
                    $selectedImg = $editable.find('img').last();
                }
                if ($selectedImg.length) {
                    openImageCaptionModal($selectedImg[0]);
                } else {
                    alert('Please click on an image in the editor first, or insert one from the Media Library.');
                }
            }
        });
        return button.render();
    };

    // Merge Options
    const defaultToolbar = [
        ['style', ['style']],
        ['font', ['bold', 'italic', 'underline', 'clear']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['table', ['table']],
        ['insert', ['link', 'picture', 'video']],
        ['mediaSuite', ['mediaLibraryBtn', 'imageCaptionBtn']],
        ['view', ['fullscreen', 'codeview', 'help']]
    ];

    const summernoteConfig = Object.assign({
        height: 380,
        toolbar: defaultToolbar,
        buttons: {
            mediaLibraryBtn: MediaLibraryButton,
            imageCaptionBtn: ImageCaptionButton
        },
        callbacks: {
            onInit: function () {
                setupEditorImageClickHandlers($editor);
            },
            onChange: function (contents) {
                if (typeof updateDetectedHeadings === 'function') {
                    updateDetectedHeadings(contents);
                }
            },
            onImageUpload: function (files) {
                uploadSummernoteFiles(files, $editor);
            }
        }
    }, options);

    $editor.summernote(summernoteConfig);
}

// ---------------------------------------------------------------------------
// Image Click & Double-Click Handlers (Inside Editor)
// ---------------------------------------------------------------------------
function setupEditorImageClickHandlers($editor) {
    const $editable = $editor.next('.note-editor').find('.note-editable');

    // Double click on any image opens caption editor
    $editable.on('dblclick', 'img', function (e) {
        e.preventDefault();
        e.stopPropagation();
        openImageCaptionModal(this);
    });

    // Also support clicking on existing figcaption to edit
    $editable.on('dblclick', 'figcaption.post-image-caption', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const img = $(this).closest('figure').find('img')[0];
        if (img) openImageCaptionModal(img);
    });
}

// ---------------------------------------------------------------------------
// Image Caption / Alt SEO Modal Logic
// ---------------------------------------------------------------------------
function openImageCaptionModal(imgElement) {
    if (!imgElement) return;
    activeEditorImg = imgElement;

    const $img = $(imgElement);
    const $figure = $img.closest('figure.post-image-figure');
    const existingCaption = $figure.length ? $figure.find('figcaption.post-image-caption').text().trim() : '';

    // Populate modal fields
    $('#captionModalPreviewImg').attr('src', $img.attr('src') || '');
    $('#captionModalAltInput').val($img.attr('alt') || '');
    $('#captionModalTitleInput').val($img.attr('title') || '');
    $('#captionModalCaptionInput').val(existingCaption);

    // Update count indicator
    $('#captionModalAltCount').text(($img.attr('alt') || '').length + ' / 125');

    // Determine layout alignment
    if ($figure.hasClass('align-left')) {
        $('#alignLeft').prop('checked', true);
    } else if ($figure.hasClass('align-right')) {
        $('#alignRight').prop('checked', true);
    } else if ($figure.hasClass('align-full')) {
        $('#alignFull').prop('checked', true);
    } else {
        $('#alignCenter').prop('checked', true);
    }

    const modalEl = document.getElementById('editorImageCaptionModal');
    if (modalEl) {
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }
}

// Alt text character counter
$(document).on('input', '#captionModalAltInput', function () {
    $('#captionModalAltCount').text($(this).val().length + ' / 125');
});

// Apply Caption, Alt & Title to Image
$(document).on('click', '#btnApplyImageCaption', function () {
    if (!activeEditorImg) return;

    const $img = $(activeEditorImg);
    const altText = $('#captionModalAltInput').val().trim();
    const titleText = $('#captionModalTitleInput').val().trim();
    const captionText = $('#captionModalCaptionInput').val().trim();
    const alignment = $('input[name="captionModalAlign"]:checked').val() || 'center';

    // Update img attributes
    if (altText) {
        $img.attr('alt', altText);
    } else {
        $img.removeAttr('alt');
    }

    if (titleText) {
        $img.attr('title', titleText);
    } else {
        $img.removeAttr('title');
    }

    $img.addClass('img-fluid rounded-3 shadow-sm');

    let $figure = $img.closest('figure.post-image-figure');

    if (captionText) {
        // If caption provided, ensure image is wrapped in <figure>
        if (!$figure.length) {
            $figure = $('<figure class="post-image-figure my-3"></figure>');
            $img.wrap($figure);
            $figure = $img.closest('figure.post-image-figure');
        }

        // Apply alignment class
        $figure.removeClass('align-left align-right align-full text-center');
        if (alignment === 'left') {
            $figure.addClass('align-left');
        } else if (alignment === 'right') {
            $figure.addClass('align-right');
        } else if (alignment === 'full') {
            $figure.addClass('align-full text-center');
        } else {
            $figure.addClass('text-center');
        }

        // Update or insert <figcaption>
        let $figcaption = $figure.find('figcaption.post-image-caption');
        if (!$figcaption.length) {
            $figcaption = $('<figcaption class="post-image-caption text-muted small mt-1"></figcaption>');
            $figure.append($figcaption);
        }
        $figcaption.html(`<em>${captionText}</em>`);
    } else {
        // If caption was cleared, remove <figcaption>
        if ($figure.length) {
            $figure.find('figcaption.post-image-caption').remove();
            // If alignment is center, keep or unwrap
            $figure.removeClass('align-left align-right align-full').addClass('text-center');
        }
    }

    // Trigger Summernote change event
    $('#summernoteEditor').summernote('saveRange');
    $('#summernoteEditor').trigger('summernote.change');

    // Close modal
    const modalEl = document.getElementById('editorImageCaptionModal');
    if (modalEl) {
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }
});

// ---------------------------------------------------------------------------
// Media Library Picker Modal Logic
// ---------------------------------------------------------------------------
function openEditorMediaPicker() {
    activePickerItem = null;
    $('#pickerSelectedEmpty').removeClass('d-none');
    $('#pickerSelectedDetails').addClass('d-none');

    loadPickerMedia(1);

    const modalEl = document.getElementById('editorMediaPickerModal');
    if (modalEl) {
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }
}

// Fetch Media Items via AJAX
function loadPickerMedia(page = 1) {
    currentPickerPage = page;
    const search = $('#pickerSearchInput').val() || '';
    const type = currentPickerType;

    const $grid = $('#pickerMediaGrid');
    $grid.html(`
        <div class="col-12 text-center py-5 text-muted">
            <div class="spinner-border text-primary spinner-border-sm mb-2" role="status"></div>
            <div>Loading Media Library...</div>
        </div>
    `);

    let url = `/admin/media-picker-api?page=${page}`;
    if (type) url += `&type=${encodeURIComponent(type)}`;
    if (search) url += `&search=${encodeURIComponent(search)}`;

    fetch(url, {
        headers: { 'Accept': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
        const items = data.data || [];
        if (items.length === 0) {
            $grid.html(`
                <div class="col-12 text-center py-5 text-muted">
                    <i class="bi bi-folder2-open display-4 text-secondary mb-2 d-inline-block"></i>
                    <p class="mb-1">No media files found matching criteria.</p>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill mt-2" onclick="$('#tab-upload-btn').tab('show')">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Upload First Asset
                    </button>
                </div>
            `);
            $('#pickerPagination').html('');
            return;
        }

        let html = '';
        items.forEach(item => {
            let preview = '';
            if (item.media_type === 'image') {
                preview = `<img src="${item.url}" alt="${item.alt_text || ''}" class="w-100 h-100 object-fit-cover rounded-2">`;
            } else if (item.media_type === 'video') {
                preview = `<div class="d-flex flex-column align-items-center justify-content-center h-100 text-danger"><i class="bi bi-play-btn-fill fs-2"></i><span class="small" style="font-size:0.65rem">VIDEO</span></div>`;
            } else if (item.media_type === 'audio') {
                preview = `<div class="d-flex flex-column align-items-center justify-content-center h-100 text-success"><i class="bi bi-music-note-beamed fs-2"></i><span class="small" style="font-size:0.65rem">AUDIO</span></div>`;
            } else {
                preview = `<div class="d-flex flex-column align-items-center justify-content-center h-100 text-primary"><i class="bi bi-file-earmark-text-fill fs-2"></i><span class="small" style="font-size:0.65rem">DOC</span></div>`;
            }

            html += `
                <div class="col-6 col-sm-4 col-md-3">
                    <div class="card h-100 border shadow-sm rounded-3 media-picker-item p-1" data-item='${JSON.stringify(item).replace(/'/g, "&apos;")}'>
                        <div class="bg-body-tertiary rounded-2 overflow-hidden position-relative" style="height: 90px;">
                            ${preview}
                        </div>
                        <div class="p-1 text-truncate small fw-semibold" style="font-size: 0.72rem;" title="${item.title || item.original_name}">
                            ${item.title || item.original_name}
                        </div>
                    </div>
                </div>
            `;
        });
        $grid.html(html);

        // Render Pagination buttons
        renderPickerPagination(data);
    })
    .catch(err => {
        $grid.html(`<div class="col-12 text-center py-5 text-danger">Failed to load media files.</div>`);
    });
}

function renderPickerPagination(data) {
    const $p = $('#pickerPagination');
    if (!data.last_page || data.last_page <= 1) {
        $p.html('');
        return;
    }
    let html = '<div class="btn-group btn-group-sm">';
    if (data.prev_page_url) {
        html += `<button type="button" class="btn btn-outline-secondary" onclick="loadPickerMedia(${data.current_page - 1})">&laquo; Prev</button>`;
    }
    html += `<span class="btn btn-outline-secondary disabled">${data.current_page} of ${data.last_page}</span>`;
    if (data.next_page_url) {
        html += `<button type="button" class="btn btn-outline-secondary" onclick="loadPickerMedia(${data.current_page + 1})">Next &raquo;</button>`;
    }
    html += '</div>';
    $p.html(html);
}

// Filter button clicks
$(document).on('click', '[data-picker-type]', function () {
    $('[data-picker-type]').removeClass('active');
    $(this).addClass('active');
    currentPickerType = $(this).data('picker-type');
    loadPickerMedia(1);
});

// Search input
let pickerSearchTimer = null;
$(document).on('input', '#pickerSearchInput', function () {
    clearTimeout(pickerSearchTimer);
    pickerSearchTimer = setTimeout(() => {
        loadPickerMedia(1);
    }, 350);
});

// Click item in grid
$(document).on('click', '.media-picker-item', function () {
    $('.media-picker-item').removeClass('selected');
    $(this).addClass('selected');

    const item = $(this).data('item');
    activePickerItem = item;

    $('#pickerSelectedEmpty').addClass('d-none');
    $('#pickerSelectedDetails').removeClass('d-none');

    // Details sidebar
    let preview = '';
    if (item.media_type === 'image') {
        preview = `<img src="${item.url}" class="img-fluid rounded-2" style="max-height: 130px; object-fit: contain;">`;
        $('#pickerSelectedAltGroup').removeClass('d-none');
    } else if (item.media_type === 'video') {
        preview = `<div class="p-3 text-center"><i class="bi bi-play-circle-fill text-danger fs-1"></i><div class="small mt-1">${item.original_name}</div></div>`;
        $('#pickerSelectedAltGroup').addClass('d-none');
    } else if (item.media_type === 'audio') {
        preview = `<div class="p-3 text-center"><i class="bi bi-music-note-beamed text-success fs-1"></i><div class="small mt-1">${item.original_name}</div></div>`;
        $('#pickerSelectedAltGroup').addClass('d-none');
    } else {
        preview = `<div class="p-3 text-center"><i class="bi bi-file-earmark-pdf text-danger fs-1"></i><div class="small mt-1">${item.original_name}</div></div>`;
        $('#pickerSelectedAltGroup').addClass('d-none');
    }

    $('#pickerSelectedPreview').html(preview);
    $('#pickerSelectedTitle').val(item.title || item.original_name);
    $('#pickerSelectedAlt').val(item.alt_text || item.title || '');
    $('#pickerSelectedCaption').val(item.caption || '');
});

// Confirm Insert into Summernote
$(document).on('click', '#btnConfirmPickerInsert', function () {
    if (!activePickerItem) return;

    const item = activePickerItem;
    const title = $('#pickerSelectedTitle').val().trim() || item.original_name;
    const alt = $('#pickerSelectedAlt').val().trim() || title;
    const caption = $('#pickerSelectedCaption').val().trim();

    let nodeHtml = '';

    if (item.media_type === 'image') {
        nodeHtml = `
            <figure class="post-image-figure text-center my-3">
                <img src="${item.url}" alt="${alt}" title="${title}" class="img-fluid rounded-3 shadow-sm">
                ${caption ? `<figcaption class="post-image-caption text-muted small mt-1"><em>${caption}</em></figcaption>` : ''}
            </figure>
            <p><br></p>
        `;
    } else if (item.media_type === 'document') {
        nodeHtml = `
            <div class="my-3 p-3 border rounded-3 bg-body-tertiary d-inline-flex align-items-center gap-3">
                <i class="bi bi-file-earmark-arrow-down-fill fs-2 text-danger"></i>
                <div>
                    <div class="fw-bold">${title}</div>
                    <small class="text-muted">${item.formatted_size || ''} &bull; Document Download</small>
                </div>
                <a href="${item.url}" target="_blank" download="${item.original_name}" class="btn btn-sm btn-primary rounded-pill px-3 ms-2">
                    <i class="bi bi-download me-1"></i> Download
                </a>
            </div>
            <p><br></p>
        `;
    } else if (item.media_type === 'audio') {
        nodeHtml = `
            <div class="my-3 p-3 border rounded-3 bg-body-tertiary">
                <div class="fw-semibold small mb-2"><i class="bi bi-music-note-beamed text-success me-1"></i> ${title}</div>
                <audio controls class="w-100" preload="metadata">
                    <source src="${item.url}" type="${item.mime_type}">
                    Your browser does not support audio playback.
                </audio>
            </div>
            <p><br></p>
        `;
    } else if (item.media_type === 'video') {
        nodeHtml = `
            <div class="my-3 text-center">
                <video controls class="w-100 rounded-3 shadow-sm" preload="metadata" style="max-height: 480px;">
                    <source src="${item.url}" type="${item.mime_type}">
                    Your browser does not support the video tag.
                </video>
                ${caption ? `<div class="text-muted small mt-1"><em>${caption}</em></div>` : ''}
            </div>
            <p><br></p>
        `;
    }

    // Insert into Summernote
    $('#summernoteEditor').summernote('pasteHTML', nodeHtml);

    // Hide Modal
    const modalEl = document.getElementById('editorMediaPickerModal');
    if (modalEl) {
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();
    }
});

// Quick Upload Files from Editor Modal
$(document).on('change', '#pickerQuickFileInput', function () {
    const files = this.files;
    if (!files || !files.length) return;

    const formData = new FormData();
    for (let i = 0; i < files.length; i++) {
        formData.append('files[]', files[i]);
    }

    $('#pickerUploadProgress').removeClass('d-none');
    $('#pickerUploadStatus').html('<span class="text-primary fw-semibold">Uploading asset(s)...</span>');

    fetch('/admin/media', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        $('#pickerUploadProgress').addClass('d-none');
        if (data.success) {
            $('#pickerUploadStatus').html('<span class="text-success fw-semibold"><i class="bi bi-check-circle me-1"></i> Upload complete! Switching to library...</span>');
            setTimeout(() => {
                $('#pickerUploadStatus').html('');
                $('#tab-library-btn').tab('show');
                loadPickerMedia(1);
            }, 800);
        } else {
            $('#pickerUploadStatus').html(`<span class="text-danger">${data.message || 'Upload failed.'}</span>`);
        }
    })
    .catch(err => {
        $('#pickerUploadProgress').addClass('d-none');
        $('#pickerUploadStatus').html('<span class="text-danger">Upload error. Please try again.</span>');
    });
});

// Intercept drag-and-drop or default picture upload into Summernote
function uploadSummernoteFiles(files, $editor) {
    if (!files || !files.length) return;

    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        const formData = new FormData();
        formData.append('file', file);

        fetch('/admin/media', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.media && data.media.length) {
                const item = data.media[0];
                const nodeHtml = `
                    <figure class="post-image-figure text-center my-3">
                        <img src="${item.url}" alt="${item.alt_text || item.title}" title="${item.title}" class="img-fluid rounded-3 shadow-sm">
                        <figcaption class="post-image-caption text-muted small mt-1"><em>${item.title}</em></figcaption>
                    </figure>
                    <p><br></p>
                `;
                $editor.summernote('pasteHTML', nodeHtml);
            }
        })
        .catch(err => {
            console.error('Summernote image upload error:', err);
        });
    }
}
