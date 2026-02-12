@extends('layouts.super_admin')

@section('title', 'Tambah Super Admin')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-0">Tambah Super Admin Baru</h2>
        <p class="text-muted">Buat akun dengan hak akses penuh sistem.</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="{{ route('super_admin.users.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="Nama Super Admin">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="nik" class="form-label">NIK (Username Login)</label>
                    <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik" name="nik" value="{{ old('nik') }}" required placeholder="Contoh: 99999999">
                    @error('nik')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email (Opsional)</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="email@example.com">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required placeholder="Minimal 6 karakter">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Hidden Fields for Role -->
            <input type="hidden" name="role" value="super_admin">

            <div class="alert alert-info mt-3 bg-light border-0 text-dark">
                <i class="fas fa-info-circle me-2 text-primary"></i> Akun Super Admin akan langsung aktif dan memiliki akses penuh ke semua fitur sistem.
            </div>

            <div class="d-flex justify-content-end mt-4">
                <a href="{{ route('super_admin.users.index') }}" class="btn btn-light border me-2">Batal</a>
                <button type="submit" class="btn btn-danger" style="background-color: var(--primary-color); border-color: var(--primary-color);">
                    <i class="fas fa-user-shield me-2"></i>Simpan Super Admin
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
