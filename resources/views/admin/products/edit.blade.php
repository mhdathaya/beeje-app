@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.products') }}" class="text-decoration-none text-dark">Produk</a>
            <span>/</span>
            <span>Edit Produk</span>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Kolom Kiri -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Produk</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $product->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">Kategori</label>
                            <select class="form-select @error('category') is-invalid @enderror" 
                                    id="category" name="category" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->category }}" {{ $product->category == $category->category ? 'selected' : '' }}>
                                        {{ ucfirst($category->category) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Harga</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                               id="price" name="price" value="{{ old('price', $product->price) }}" required>
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="stock" class="form-label">Stok</label>
                                    <input type="number" class="form-control @error('stock') is-invalid @enderror" 
                                           id="stock" name="stock" value="{{ old('stock', $product->stock) }}" required>
                                    @error('stock')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tambahkan bagian promo di sini -->
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_promo" name="is_promo" value="1" {{ $product->is_promo ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_promo">Aktifkan Promo</label>
                            </div>
                        </div>
                        
                        <!-- Di dalam bagian promo fields -->
                        <div id="promo-fields" style="{{ $product->is_promo ? 'display:block' : 'display:none' }}">
                            <div class="mb-3">
                                <label for="promo_banner" class="form-label">Banner Promo (untuk Mobile)</label>
                                <div class="dropzone-container border rounded p-3 text-center mb-3" id="dropzone-banner">
                                    <div class="image-preview">
                                        @if($product->promo_banner)
                                            <img src="{{ asset('storage/' . $product->promo_banner) }}" alt="Banner Promo" 
                                                 id="preview-banner" style="max-width: 100%;">
                                        @else
                                            <img src="" alt="" id="preview-banner" style="max-width: 100%; display: none;">
                                        @endif
                                    </div>
                                    <div class="upload-placeholder" @if($product->promo_banner) style="display: none;" @endif>
                                        <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                        <p>Banner Promo<br>Format: jpeg, png (Disarankan ukuran 1080x400px)</p>
                                    </div>
                                    <input type="file" name="promo_banner" id="banner-upload" class="d-none" accept="image/jpeg,image/png">
                                </div>
                            </div>
                                <label for="promo_price" class="form-label">Harga Promo</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" id="promo_price" name="promo_price" value="{{ $product->promo_price }}" step="0.01">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="promo_start" class="form-label">Tanggal Mulai Promo</label>
                                <input type="datetime-local" class="form-control" id="promo_start" name="promo_start" value="{{ $product->promo_start ? date('Y-m-d\\TH:i', strtotime($product->promo_start)) : '' }}">
                            </div>
                            
                            <div class="mb-3">
                                <label for="promo_end" class="form-label">Tanggal Berakhir Promo</label>
                                <input type="datetime-local" class="form-control" id="promo_end" name="promo_end" value="{{ $product->promo_end ? date('Y-m-d\\TH:i', strtotime($product->promo_end)) : '' }}">
                            </div>
                            
                            <div class="mb-3">
                                <label for="promo_description" class="form-label">Deskripsi Promo</label>
                                <textarea class="form-control" id="promo_description" name="promo_description" rows="3">{{ $product->promo_description }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Kanan -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Gambar Produk</label>
                            <div class="dropzone-container border rounded p-3 text-center" id="dropzone">
                                <div class="image-preview mb-3">
                                    @if($product->image)
                                        <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" 
                                             id="preview-image" style="max-width: 100%;">
                                    @else
                                        <img src="" alt="" id="preview-image" style="max-width: 100%; display: none;">
                                    @endif
                                </div>
                                <div class="upload-placeholder">
                                    <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                    <p>Seret gambar ke sini, atau klik untuk memilih<br>Format: jpeg, png</p>
                                </div>
                                <input type="file" name="image" id="image-upload" class="d-none" accept="image/jpeg,image/png">
                            </div>
                            @error('image')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Product Gallery (Up to 3 images)</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="dropzone-container border rounded p-3 text-center mb-3" id="dropzone1">
                                        <div class="image-preview">
                                            @if($product->image1)
                                                <img src="{{ asset('storage/' . $product->image1) }}" alt="{{ $product->name }}" 
                                                     id="preview-image1" style="max-width: 100%;">
                                            @else
                                                <img src="" alt="" id="preview-image1" style="max-width: 100%; display: none;">
                                            @endif
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
                                            @if($product->image2)
                                                <img src="{{ asset('storage/' . $product->image2) }}" alt="{{ $product->name }}" 
                                                     id="preview-image2" style="max-width: 100%;">
                                            @else
                                                <img src="" alt="" id="preview-image2" style="max-width: 100%; display: none;">
                                            @endif
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
                                            @if($product->image3)
                                                <img src="{{ asset('storage/' . $product->image3) }}" alt="{{ $product->name }}" 
                                                     id="preview-image3" style="max-width: 100%;">
                                            @else
                                                <img src="" alt="" id="preview-image3" style="max-width: 100%; display: none;">
                                            @endif
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
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="submit" class="btn btn-primary px-4">Simpan Perubahan</button>
                        <a href="{{ route('admin.products') }}" class="btn btn-outline-secondary px-4">Batal</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to handle file upload for each dropzone
    function setupDropzone(number) {
        const dropzone = document.getElementById(`dropzone${number}`);
        const imageUpload = document.getElementById(`image-upload${number}`);
        const previewImage = document.getElementById(`preview-image${number}`);
        const uploadPlaceholder = dropzone.querySelector('.upload-placeholder');

        // Show/hide placeholder based on existing image
        if (previewImage.style.display !== 'none' && previewImage.src) {
            uploadPlaceholder.style.display = 'none';
        }

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
                handleFiles(e.dataTransfer.files[0], previewImage, uploadPlaceholder);
            }
        });

        imageUpload.addEventListener('change', (e) => {
            if (e.target.files.length) {
                handleFiles(e.target.files[0], previewImage, uploadPlaceholder);
            }
        });

        // Add delete button if image exists
        if (previewImage.style.display !== 'none' && previewImage.src) {
            addDeleteButton(dropzone, previewImage, uploadPlaceholder, imageUpload);
        }
    }

    // Setup banner promo dropzone
    function setupBannerDropzone() {
        const dropzone = document.getElementById('dropzone-banner');
        const imageUpload = document.getElementById('banner-upload');
        const previewImage = document.getElementById('preview-banner');
        const uploadPlaceholder = dropzone.querySelector('.upload-placeholder');

        if (dropzone && imageUpload && previewImage) {
            // Show/hide placeholder based on existing image
            if (previewImage.style.display !== 'none' && previewImage.src) {
                uploadPlaceholder.style.display = 'none';
            }

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
                    handleFiles(e.dataTransfer.files[0], previewImage, uploadPlaceholder);
                }
            });

            imageUpload.addEventListener('change', (e) => {
                if (e.target.files.length) {
                    handleFiles(e.target.files[0], previewImage, uploadPlaceholder);
                }
            });

            // Add delete button if image exists
            if (previewImage.style.display !== 'none' && previewImage.src) {
                addDeleteButton(dropzone, previewImage, uploadPlaceholder, imageUpload);
            }
        }
    }

    function handleFiles(file, previewImage, uploadPlaceholder) {
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImage.src = e.target.result;
                previewImage.style.display = 'block';
                uploadPlaceholder.style.display = 'none';

                // Add delete button
                const dropzone = previewImage.closest('.dropzone-container');
                const imageUpload = dropzone.querySelector('input[type="file"]');
                addDeleteButton(dropzone, previewImage, uploadPlaceholder, imageUpload);
            };
            reader.readAsDataURL(file);
        }
    }

    function addDeleteButton(dropzone, previewImage, uploadPlaceholder, imageUpload) {
        // Remove existing delete button
        const existingBtn = dropzone.querySelector('.btn-danger');
        if (existingBtn) {
            existingBtn.remove();
        }

        const deleteBtn = document.createElement('button');
        deleteBtn.type = 'button';
        deleteBtn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0 m-2';
        deleteBtn.innerHTML = '<i class="fas fa-times"></i>';
        deleteBtn.onclick = (e) => {
            e.stopPropagation();
            previewImage.src = '';
            previewImage.style.display = 'none';
            uploadPlaceholder.style.display = 'block';
            imageUpload.value = '';
            deleteBtn.remove();
        };
        dropzone.appendChild(deleteBtn);
    }

    // Price input formatting (optional - adds thousand separators)
    const priceInput = document.getElementById('price');
    if (priceInput) {
        // Format initial value if exists
        if (priceInput.value) {
            const initialValue = parseInt(priceInput.value);
            if (!isNaN(initialValue)) {
                priceInput.setAttribute('data-value', priceInput.value);
            }
        }

        priceInput.addEventListener('input', function(e) {
            // Remove non-numeric characters
            let value = e.target.value.replace(/[^0-9]/g, '');
            
            // Store numeric value
            e.target.setAttribute('data-value', value);
            
            // Format display with thousand separators (optional)
            if (value) {
                const formatted = parseInt(value).toLocaleString('id-ID');
                // Uncomment the line below if you want to show formatted display
                // e.target.value = formatted;
            }
        });

        // Before form submission, ensure numeric value is used
        priceInput.closest('form').addEventListener('submit', function() {
            const numericValue = priceInput.getAttribute('data-value') || priceInput.value.replace(/[^0-9]/g, '');
            priceInput.value = numericValue;
        });
    }

    // Setup all dropzones
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


// Toggle promo fields visibility
const isPromoCheckbox = document.getElementById('is_promo');
const promoFields = document.getElementById('promo-fields');

if (isPromoCheckbox && promoFields) {
    isPromoCheckbox.addEventListener('change', function() {
        promoFields.style.display = this.checked ? 'block' : 'none';
    });
}
</script>
@endpush
@endsection
