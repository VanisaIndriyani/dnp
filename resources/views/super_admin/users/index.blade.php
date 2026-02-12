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
    <div class="card-header bg-white p-0">
        <div class="d-flex justify-content-between align-items-center p-3">
            <h5 class="mb-0 text-muted">Daftar Pengguna</h5>
            <div style="width: 300px;">
                <form action="{{ route('super_admin.users.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Cari NIK" value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <ul class="nav nav-tabs card-header-tabs m-0 px-3" id="userTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab" aria-controls="active" aria-selected="true">
                    Pengguna Aktif
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="false">
                    Menunggu Persetujuan
                    @if($pendingUsers->count() > 0)
                        <span class="badge bg-danger ms-1">{{ $pendingUsers->count() }}</span>
                    @endif
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body p-0">
        <div class="tab-content" id="userTabsContent">
            <!-- Active Users Tab -->
            <div class="tab-pane fade show active" id="active" role="tabpanel" aria-labelledby="active-tab">
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
                                    <span class="badge bg-success">Aktif</span>
                                </td>
                                <td class="text-center">
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
                                <td colspan="6" class="text-center py-4">Tidak ada data user aktif.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3 border-top">
                    {{ $users->links() }}
                </div>
            </div>

            <!-- Pending Users Tab -->
            <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
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
                            @forelse($pendingUsers as $user)
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
                                    <span class="badge bg-warning text-dark">Pending</span>
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('super_admin.users.approve', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success me-1" title="Approve" onclick="return confirm('Apakah Anda yakin ingin menyetujui user ini?')"><i class="fas fa-check"></i></button>
                                    </form>
                                    
                                    <a href="{{ route('super_admin.users.edit', $user) }}" class="btn btn-sm btn-info text-white me-1" title="Edit"><i class="fas fa-edit"></i></a>
                                    
                                    <form action="{{ route('super_admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Tolak / Hapus" onclick="return confirm('Apakah Anda yakin ingin menolak/menghapus user ini?')"><i class="fas fa-times"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">Tidak ada user yang menunggu persetujuan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

