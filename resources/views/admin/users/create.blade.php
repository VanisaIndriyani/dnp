@extends('layouts.admin')

@section('title', 'Tambah User')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="mb-0">Tambah User Baru</h2>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="nik" class="form-label">NIK</label>
                    <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik" name="nik" value="{{ old('nik') }}" required>
                    @error('nik')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
              
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                        <option value="">Pilih Role</option>
                        <option value="operator" {{ old('role') == 'operator' ? 'selected' : '' }}>Operator</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    </select>
                    @error('role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="division" class="form-label">Bagian (Khusus Operator)</label>
                    <select class="form-select @error('division') is-invalid @enderror" id="division" name="division">
                        <option value="">Pilih Bagian (Opsional)</option>
                        <option value="case" {{ old('division') == 'case' ? 'selected' : '' }}>Case</option>
                        <option value="cover" {{ old('division') == 'cover' ? 'selected' : '' }}>Cover</option>
                        <option value="inner" {{ old('division') == 'inner' ? 'selected' : '' }}>Inner</option>
                        <option value="endplate" {{ old('division') == 'endplate' ? 'selected' : '' }}>Endplate</option>
                    </select>
                    @error('division')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary me-2">Batal</a>
                <button type="submit" class="btn btn-danger" style="background-color: var(--primary-color);">Simpan User</button>
            </div>
        </form>
    </div>
</div>
@endsection
