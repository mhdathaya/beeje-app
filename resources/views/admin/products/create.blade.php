@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="product-details">
        <div class="d-flex align-items-center gap-2 mb-4">
            <a href="{{ route('admin.products') }}" class="text-decoration-none text-dark">Home</a> >
            <a href="{{ route('admin.products') }}" class="text-decoration-none text-dark">All Products</a> >
            <span>Add </span>
        </div>

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <input type="text" class="form-control" id="category" name="category" required>
                            </div>

                            <div class="mb-3">
                                <label for="brand" class="form-label">Brand Name</label>
                                <input type="text" class="form-control" id="brand" name="brand" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="stock" class="form-label">Stock Quantity</label>
                                        <input type="number" class="form-control" id="stock" name="stock" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="number" class="form-control" id="price" name="price" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Tambahkan Bagian Promo -->
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_promo" name="is_promo" value="1">
                                    <label class="form-check-label" for="is_promo">Set as Promo Product</label>
                                </div>
                            </div>
                            
                            <div id="promo-fields" style="display: none;">
                                <div class="mb-3">
                                    <label for="promo_price" class="form-label">Harga Promo</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" id="promo_price" name="promo_price" step="0.01">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="promo_start" class="form-label">Tanggal Mulai Promo</label>
                                    <input type="datetime-local" class="form-control" id="promo_start" name="promo_start">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="promo_end" class="form-label">Tanggal Berakhir Promo</label>
                                    <input type="datetime-local" class="form-control" id="promo_end" name="promo_end">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="promo_description" class="form-label">Deskripsi Promo</label>
                                    <textarea class="form-control" id="promo_description" name="promo_description" rows="3"></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="promo_banner" class="form-label">Banner Promo (untuk Mobile)</label>
                                    <div class="dropzone-container border rounded p-3 text-center mb-3" id="dropzone-banner">
                                        <div class="image-preview">
                                            <img src="" alt="" id="preview-banner" style="max-width: 100%; display: none;">
                                        </div>
                                        <div class="upload-placeholder">
                                            <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                            <p>Banner Promo<br>Format: jpeg, png (Disarankan ukuran 1080x400px)</p>
                                        </div>
                                        <input type="file" name="promo_banner" id="banner-upload" class="d-none" accept="image/jpeg,image/png">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Product Gallery (Up to 3 images)</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="dropzone-container border rounded p-3 text-center mb-3" id="dropzone1">
                                            <div class="image-preview">
                                                <img src="" alt="" id="preview-image1" style="max-width: 100%; display: none;">
                                            </div>
                                            <div class="upload-placeholder">
                                                <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                                <p>Image 1</p>
                                            </div>
                                            <input type="file" name="image1" id="image-upload1" class="d-none" accept="image/jpeg,image/png">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="dropzone-container border rounded p-3 text-center mb-3" id="dropzone2">
                                            <div class="image-preview">
                                                <img src="" alt="" id="preview-image2" style="max-width: 100%; display: none;">
                                            </div>
                                            <div class="upload-placeholder">
                                                <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                                <p>Image 2</p>
                                            </div>
                                            <input type="file" name="image2" id="image-upload2" class="d-none" accept="image/jpeg,image/png">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="dropzone-container border rounded p-3 text-center mb-3" id="dropzone3">
                                            <div class="image-preview">
                                                <img src="" alt="" id="preview-image3" style="max-width: 100%; display: none;">
                                            </div>
                                            <div class="upload-placeholder">
                                                <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                                <p>Image 3</p>
                                            </div>
                                            <input type="file" name="image3" id="image-upload3" class="d-none" accept="image/jpeg,image/png">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="uploaded-files mt-3" id="uploaded-files">
                                <!-- Template untuk file yang diupload -->
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="submit" class="btn btn-primary px-4">SAVE</button>
                                <a href="{{ route('admin.products') }}" class="btn btn-outline-secondary px-4">CANCEL</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .dropzone-container {
        min-height: 200px;
        cursor: pointer;
        border: 2px dashed #ddd !important;
    }

    .dropzone-container:hover {
        border-color: #aaa !important;
    }

    .uploaded-file {
        display: flex;
        align-items: center;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-bottom: 10px;
    }

    .uploaded-file img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 4px;
        margin-right: 10px;
    }

    /* Format input price to display thousand separators */
    .price-input {
        text-align: right;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to handle file upload for each dropzone
    function setupDropzone(number) {
        const dropzone = document.getElementById(`dropzone${number}`);
        const imageUpload = document.getElementById(`image-upload${number}`);
        const previewImage = document.getElementById(`preview-image${number}`);

        dropzone.addEventListener('click', () => imageUpload.click());

        dropzone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.style.borderColor = '#666';
        });

        dropzone.addEventListener('dragleave', () => {
            dropzone.style.borderColor = '#ddd';
        });

        dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.style.borderColor = '#ddd';
            
            if (e.dataTransfer.files.length) {
                imageUpload.files = e.dataTransfer.files;
                handleFiles(e.dataTransfer.files[0], previewImage);
            }
        });

        imageUpload.addEventListener('change', (e) => {
            if (e.target.files.length) {
                handleFiles(e.target.files[0], previewImage);
            }
        });
    }

    // Setup banner promo dropzone
    function setupBannerDropzone() {
        const dropzone = document.getElementById('dropzone-banner');
        const imageUpload = document.getElementById('banner-upload');
        const previewImage = document.getElementById('preview-banner');

        if (dropzone && imageUpload && previewImage) {
            dropzone.addEventListener('click', () => imageUpload.click());

            dropzone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropzone.style.borderColor = '#666';
            });

            dropzone.addEventListener('dragleave', () => {
                dropzone.style.borderColor = '#ddd';
            });

            dropzone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropzone.style.borderColor = '#ddd';
                
                if (e.dataTransfer.files.length) {
                    imageUpload.files = e.dataTransfer.files;
                    handleFiles(e.dataTransfer.files[0], previewImage);
                }
            });

            imageUpload.addEventListener('change', (e) => {
                if (e.target.files.length) {
                    handleFiles(e.target.files[0], previewImage);
                }
            });
        }
    }

    function handleFiles(file, previewImage) {
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImage.src = e.target.result;
                previewImage.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    // Price input formatting (optional - adds thousand separators)
    const priceInput = document.getElementById('price');
    if (priceInput) {
        priceInput.addEventListener('input', function(e) {
            // Remove non-numeric characters except decimal point
            let value = e.target.value.replace(/[^0-9]/g, '');
            
            // Format with thousand separators
            if (value) {
                value = parseInt(value).toLocaleString('id-ID');
            }
            
            // Update display (but keep original numeric value for submission)
            const numericValue = e.target.value.replace(/[^0-9]/g, '');
            e.target.setAttribute('data-value', numericValue);
        });

        // Before form submission, ensure numeric value is used
        priceInput.closest('form').addEventListener('submit', function() {
            const numericValue = priceInput.getAttribute('data-value') || priceInput.value.replace(/[^0-9]/g, '');
            priceInput.value = numericValue;
        });
    }

    // Setup all three dropzones
    setupDropzone(1);
    setupDropzone(2);
    setupDropzone(3);
    setupBannerDropzone(); // Tambahkan ini untuk setup banner promo
    
    // Toggle promo fields visibility
    const isPromoCheckbox = document.getElementById('is_promo');
    const promoFields = document.getElementById('promo-fields');
    
    if (isPromoCheckbox && promoFields) {
        isPromoCheckbox.addEventListener('change', function() {
            promoFields.style.display = this.checked ? 'block' : 'none';
        });
    }
});
</script>
@endpush
@endsection