@extends('layouts.super_admin')

@section('title', 'Manajemen User')

@section('content')
<div class="row mb-4">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <h2 class="mb-0">Manajemen User</h2>
        <a href="{{ route('super_admin.users.create') }}" class="btn btn-danger" style="background-color: var(--primary-color);">
            <i class="fas fa-plus me-2"></i> Tambah User
        </a>
    </div>
</div>

@if (session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<div class="card">
    <div class="card-header bg-white">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0 text-muted">Daftar Pengguna</h5>
            </div>
            <div class="col-md-6">
                <form action="{{ route('super_admin.users.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Cari nama, NIK, atau email..." value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>NIK</th>
                        <th>Bagian</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random" class="rounded-circle me-2" width="30">
                                {{ $user->name }}
                            </div>
                        </td>
                        <td>{{ $user->nik }}</td>
                        <td>{{ $user->division ? ucfirst($user->division) : '-' }}</td>
                        <td>
                            @if($user->role == 'super_admin')
                                <span class="badge bg-danger">Super Admin</span>
                            @elseif($user->role == 'admin')
                                <span class="badge bg-primary">Admin</span>
                            @else
                                <span class="badge bg-secondary">Operator</span>
                            @endif
                        </td>
                        <td>
                            @if($user->status == 'active')
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($user->status == 'pending')
                                <form action="{{ route('super_admin.users.approve', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success me-1" title="Approve" onclick="return confirm('Apakah Anda yakin ingin menyetujui user ini?')"><i class="fas fa-check"></i></button>
                                </form>
                            @endif
                            
                            <a href="{{ route('super_admin.users.edit', $user) }}" class="btn btn-sm btn-info text-white me-1" title="Edit"><i class="fas fa-edit"></i></a>
                            
                            @if(auth()->id() !== $user->id)
                                <form action="{{ route('super_admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')"><i class="fas fa-trash"></i></button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">Tidak ada data user.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        {{ $users->links() }}
    </div>
</div>
@endsection

