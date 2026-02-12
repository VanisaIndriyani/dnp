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

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white p-0 border-bottom-0">
        <div class="d-flex justify-content-between align-items-center p-4">
            <div>
                <h5 class="mb-1 fw-bold">Daftar Pengguna</h5>
                <p class="text-muted small mb-0">Kelola data pengguna, role, dan status akun.</p>
            </div>
            <div style="width: 350px;">
                <form action="{{ route('super_admin.users.index') }}" method="GET">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0 text-muted ps-3"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control bg-light border-start-0" name="search" placeholder="Cari NIK atau Nama..." value="{{ request('search') }}">
                        <button class="btn btn-danger" type="submit" style="background-color: var(--primary-color);">Cari</button>
                    </div>
                </form>
            </div>
        </div>
        <ul class="nav nav-tabs card-header-tabs mx-4 border-bottom-0" id="userTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-medium" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button" role="tab" aria-controls="active" aria-selected="true" style="color: var(--primary-color); border-bottom: 2px solid var(--primary-color);">
                    Pengguna Aktif
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-medium text-muted" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="false">
                    Menunggu Persetujuan
                    @if($pendingUsers->count() > 0)
                        <span class="badge rounded-pill ms-2" style="background-color: var(--primary-color);">{{ $pendingUsers->count() }}</span>
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
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 text-uppercase text-muted small fw-bold">Nama</th>
                                <th class="py-3 text-uppercase text-muted small fw-bold">NIK</th>
                                <th class="py-3 text-uppercase text-muted small fw-bold">Bagian</th>
                                <th class="py-3 text-uppercase text-muted small fw-bold">Role</th>
                                <th class="py-3 text-uppercase text-muted small fw-bold">Status</th>
                                <th class="text-center py-3 text-uppercase text-muted small fw-bold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random" class="rounded-circle me-3 shadow-sm" width="35" height="35">
                                        <span class="fw-medium text-dark">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="text-muted">{{ $user->nik }}</td>
                                <td>
                                    @if($user->division)
                                        <span class="badge bg-light text-dark border">{{ ucfirst($user->division) }}</span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->role == 'super_admin')
                                        <span class="badge rounded-pill" style="background-color: var(--primary-color);">Super Admin</span>
                                    @elseif($user->role == 'admin')
                                        <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle">Admin</span>
                                    @else
                                        <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle">Operator</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle">Aktif</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('super_admin.users.edit', $user) }}" class="btn btn-sm btn-light text-primary border me-1" title="Edit"><i class="fas fa-edit"></i></a>
                                    
                                    @if(auth()->id() !== $user->id)
                                        <form action="{{ route('super_admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light text-danger border" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')"><i class="fas fa-trash"></i></button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-users fa-3x mb-3 text-light"></i>
                                    <p class="mb-0">Tidak ada data user aktif.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-top">
                    {{ $users->links() }}
                </div>
            </div>

            <!-- Pending Users Tab -->
            <div class="tab-pane fade" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 text-uppercase text-muted small fw-bold">Nama</th>
                                <th class="py-3 text-uppercase text-muted small fw-bold">NIK</th>
                                <th class="py-3 text-uppercase text-muted small fw-bold">Bagian</th>
                                <th class="py-3 text-uppercase text-muted small fw-bold">Role</th>
                                <th class="py-3 text-uppercase text-muted small fw-bold">Status</th>
                                <th class="text-center py-3 text-uppercase text-muted small fw-bold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingUsers as $user)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random" class="rounded-circle me-3 shadow-sm" width="35" height="35">
                                        <span class="fw-medium text-dark">{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="text-muted">{{ $user->nik }}</td>
                                <td>
                                    @if($user->division)
                                        <span class="badge bg-light text-dark border">{{ ucfirst($user->division) }}</span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->role == 'super_admin')
                                        <span class="badge rounded-pill" style="background-color: var(--primary-color);">Super Admin</span>
                                    @elseif($user->role == 'admin')
                                        <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle">Admin</span>
                                    @else
                                        <span class="badge rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle">Operator</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-warning-subtle text-warning border border-warning-subtle">Pending</span>
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('super_admin.users.approve', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success me-1 rounded-pill px-3" title="Approve" onclick="return confirm('Apakah Anda yakin ingin menyetujui user ini?')">
                                            <i class="fas fa-check me-1"></i> Approve
                                        </button>
                                    </form>
                                    
                                    <form action="{{ route('super_admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" title="Tolak / Hapus" onclick="return confirm('Apakah Anda yakin ingin menolak/menghapus user ini?')">
                                            <i class="fas fa-times me-1"></i> Tolak
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-user-clock fa-3x mb-3 text-light"></i>
                                    <p class="mb-0">Tidak ada user yang menunggu persetujuan.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-tabs .nav-link {
        border: none;
        color: #6c757d;
        padding: 1rem 1.5rem;
        transition: all 0.2s ease;
    }
    .nav-tabs .nav-link:hover {
        color: var(--primary-color);
        border: none;
    }
    .nav-tabs .nav-link.active {
        color: var(--primary-color);
        background: transparent;
        border-bottom: 2px solid var(--primary-color);
    }
    .badge {
        font-weight: 500;
        padding: 0.5em 0.8em;
    }
</style>
@endsection

