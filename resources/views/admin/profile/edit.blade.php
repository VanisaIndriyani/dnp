@extends('layouts.admin')

@section('title', 'Edit Profil')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header text-white" style="background-color: #C62828;">
                <h5 class="mb-0 fw-bold"><i class="fas fa-user-edit me-2"></i> Edit Profil</h5>
            </div>
            <div class="card-body p-4">
                @if(session('success'))
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row mb-4">
                        <div class="col-md-4 text-center mb-3 mb-md-0">
                            <div class="position-relative d-inline-block">
                                @if($user->photo)
                                    <img src="{{ asset('storage/' . $user->photo) }}" alt="Profile Photo" class="rounded-circle shadow-sm" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #fff;">
                                @else
                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width: 150px; height: 150px; font-size: 50px; border: 4px solid #fff;">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                @endif
                                <label for="photo" class="position-absolute bottom-0 end-0 bg-white rounded-circle shadow-sm p-2" style="cursor: pointer; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-camera text-danger"></i>
                                </label>
                                <input type="file" id="photo" name="photo" class="d-none" accept="image/*" onchange="previewImage(this)">
                            </div>
                            <div class="mt-2 text-muted small">Klik kamera untuk ubah foto</div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="nik" class="form-label fw-bold">NIK</label>
                                <input type="text" class="form-control" id="nik" name="nik" value="{{ old('nik', $user->nik) }}" minlength="4" required>
                            </div>

                            <div class="mb-3">
                                <label for="name" class="form-label fw-bold">Nama Lengkap</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="division" class="form-label fw-bold">Divisi</label>
                                <select class="form-select" id="division" name="division">
                                    <option value="">Pilih Divisi</option>
                                    <option value="case" {{ old('division', $user->division) == 'case' ? 'selected' : '' }}>Case</option>
                                    <option value="cover" {{ old('division', $user->division) == 'cover' ? 'selected' : '' }}>Cover</option>
                                    <option value="inner" {{ old('division', $user->division) == 'inner' ? 'selected' : '' }}>Inner</option>
                                    <option value="endplate" {{ old('division', $user->division) == 'endplate' ? 'selected' : '' }}>Endplate</option>
                                </select>
                            </div>

                             <div class="mb-3">
                                <label for="password" class="form-label fw-bold">Password Baru (Opsional)</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                        <i class="fas fa-eye" id="password-icon"></i>
                                    </button>
                                </div>
                                <div class="form-text">Minimal 8 karakter.</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary px-4 fw-bold">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-danger px-4 fw-bold">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                // Find the image element or create a temp preview
                var container = input.parentElement;
                var img = container.querySelector('img');
                var placeholder = container.querySelector('.bg-secondary');
                
                if (img) {
                    img.src = e.target.result;
                } else if (placeholder) {
                    // Replace placeholder with image
                    var newImg = document.createElement('img');
                    newImg.src = e.target.result;
                    newImg.className = 'rounded-circle shadow-sm';
                    newImg.style = 'width: 150px; height: 150px; object-fit: cover; border: 4px solid #fff;';
                    newImg.alt = 'Preview';
                    placeholder.replaceWith(newImg);
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function togglePassword(fieldId) {
        const passwordField = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '-icon');
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endsection
