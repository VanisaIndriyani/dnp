<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Cepat - Training Center Part Production</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .card-absensi {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
        }
        .card-header {
            background-color: #C62828;
            padding: 30px;
            text-align: center;
            color: #fff;
            border: none;
        }
        .logo-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 4px solid rgba(255,255,255,0.3);
            object-fit: cover;
            margin-bottom: 15px;
            background: #fff;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #eee;
        }
        .form-control:focus {
            border-color: #C62828;
            box-shadow: none;
        }
        .btn-absen {
            background-color: #C62828;
            color: #fff;
            border-radius: 10px;
            padding: 12px;
            font-weight: bold;
            width: 100%;
            transition: all 0.3s;
        }
        .btn-absen:hover {
            background-color: #b71c1c;
            color: #fff;
            transform: translateY(-2px);
        }
        .btn-back {
            color: #777;
            text-decoration: none;
            font-size: 0.9rem;
            display: inline-block;
            margin-top: 20px;
            transition: color 0.3s;
        }
        .btn-back:hover {
            color: #333;
        }
        .clock-display {
            font-size: 2rem;
            font-weight: 800;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
            letter-spacing: 2px;
        }
        .date-display {
            color: #777;
            text-align: center;
            margin-bottom: 10px;
            font-weight: 500;
        }
    </style>
</head>
<body>

    <div class="card-absensi">
        <div class="card-header">
            <img src="{{ asset('img/logo.jpeg') }}" alt="Logo" class="logo-img">
            <h4 class="mb-0 fw-bold">Absensi Cepat</h4>
            <p class="mb-0 opacity-75 small">
                @if(request('division'))
                    Divisi: {{ ucfirst(request('division')) }}
                @else
                    Pilih Divisi untuk Memulai
                @endif
            </p>
        </div>
        <div class="card-body p-4">
            <!-- Digital Clock -->
            <div class="date-display" id="dateDisplay"></div>
            <div class="clock-display" id="clockDisplay">00:00:00</div>

            @if(session('success'))
                <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info d-flex align-items-center mb-4" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <div>{{ session('info') }}</div>
                </div>
            @endif

            @if(!request('division'))
                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary text-center w-100 mb-3">Pilih Bagian (Divisi)</label>
                    <div class="row g-3">
                        @foreach(['cover', 'case', 'inner', 'endplate'] as $div)
                        <div class="col-6">
                            <a href="{{ route('quick-attendance.index', ['division' => $div]) }}" class="btn btn-outline-danger w-100 py-4 h-100 d-flex flex-column align-items-center justify-content-center gap-2">
                                <i class="fas fa-{{ $div == 'cover' ? 'layer-group' : ($div == 'case' ? 'box' : ($div == 'inner' ? 'cube' : 'square')) }} fa-2x"></i>
                                <span class="fw-bold text-uppercase">{{ $div }}</span>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            @else
                <form action="{{ route('quick-attendance.index') }}" method="GET" class="mb-3">
                    <input type="hidden" name="division" value="{{ request('division') }}">
                    <div class="input-group">
                        <span class="input-group-text bg-white text-muted border-end-0"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Cari Nama atau NIK..." value="{{ request('search') }}" autocomplete="off" oninput="this.form.submit()">
                    </div>
                </form>

                <div class="list-group list-group-flush mb-4" style="max-height: 400px; overflow-y: auto;">
                    @forelse($users as $user)
                        <div class="list-group-item p-3 border rounded mb-2 shadow-sm d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 fw-bold text-dark">{{ $user->name }}</h6>
                                <small class="text-muted"><i class="fas fa-id-card me-1"></i> {{ $user->nik }}</small>
                                @if($user->today_attendance)
                                    <div class="mt-1">
                                        @if($user->today_attendance->time_out)
                                            <span class="badge bg-success">Selesai</span>
                                        @elseif($user->today_attendance->status == 'alpha')
                                            <span class="badge bg-danger">Tidak Hadir</span>
                                        @else
                                            <span class="badge bg-info text-dark">Hadir: {{ \Carbon\Carbon::parse($user->today_attendance->time_in)->format('H:i') }}</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="d-flex flex-column gap-2">
                                @if(!$user->today_attendance)
                                    <form action="{{ route('quick-attendance.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="nik" value="{{ $user->nik }}">
                                        <input type="hidden" name="division" value="{{ request('division') }}">
                                        <input type="hidden" name="action" value="present">
                                        <button type="submit" class="btn btn-success btn-sm w-100">
                                            <i class="fas fa-check me-1"></i> Hadir
                                        </button>
                                    </form>
                                    <form action="{{ route('quick-attendance.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="nik" value="{{ $user->nik }}">
                                        <input type="hidden" name="division" value="{{ request('division') }}">
                                        <input type="hidden" name="action" value="absent">
                                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                            <i class="fas fa-times me-1"></i> Absen
                                        </button>
                                    </form>
                                @elseif(!$user->today_attendance->time_out && $user->today_attendance->status != 'alpha')
                                    <form action="{{ route('quick-attendance.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="nik" value="{{ $user->nik }}">
                                        <input type="hidden" name="division" value="{{ request('division') }}">
                                        <button type="submit" class="btn btn-warning btn-sm w-100 text-white">
                                            <i class="fas fa-sign-out-alt me-1"></i> Pulang
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-secondary btn-sm w-100" disabled>
                                        <i class="fas fa-check-double"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-users-slash fa-3x mb-3"></i>
                            <p>Belum ada karyawan di divisi ini.</p>
                        </div>
                    @endforelse
                </div>
                
                <div class="text-center mt-3">
                    <a href="{{ route('quick-attendance.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-arrow-left me-1"></i> Ganti Divisi
                    </a>
                </div>
            @endif
            
            <div class="text-center mt-4">
                <a href="{{ url('/') }}" class="btn-back">
                    <i class="fas fa-home me-1"></i> Halaman Utama
                </a>
            </div>
        </div>
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', { hour12: false });
            const dateString = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            
            document.getElementById('clockDisplay').textContent = timeString;
            document.getElementById('dateDisplay').textContent = dateString;
        }
        
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>
