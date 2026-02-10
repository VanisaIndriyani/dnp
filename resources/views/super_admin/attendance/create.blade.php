@extends('layouts.super_admin')

@section('title', 'Isi Absensi')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card text-center">
            <div class="card-header bg-danger text-white">
                <h4 class="mb-0">Absensi Harian</h4>
            </div>
            <div class="card-body py-5">
                <h5 class="card-title mb-4">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</h5>
                <h2 class="display-4 mb-4" id="clock">{{ \Carbon\Carbon::now()->format('H:i:s') }}</h2>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="mt-4">
                    @if(!$todayAttendance)
                        <form action="{{ route('super_admin.attendance.store') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg px-5 py-3 rounded-pill">
                                <i class="fas fa-sign-in-alt me-2"></i> ABSEN MASUK
                            </button>
                        </form>
                    @elseif(!$todayAttendance->time_out)
                        <div class="alert alert-info mb-4">
                            Anda masuk pukul: <strong>{{ \Carbon\Carbon::parse($todayAttendance->time_in)->format('H:i') }}</strong>
                        </div>
                        <form action="{{ route('super_admin.attendance.store') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-lg px-5 py-3 rounded-pill text-white">
                                <i class="fas fa-sign-out-alt me-2"></i> ABSEN KELUAR
                            </button>
                        </form>
                    @else
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle fa-2x mb-3 d-block"></i>
                            <h4>Absensi Selesai</h4>
                            <p class="mb-0">Masuk: {{ \Carbon\Carbon::parse($todayAttendance->time_in)->format('H:i') }}</p>
                            <p>Keluar: {{ \Carbon\Carbon::parse($todayAttendance->time_out)->format('H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    setInterval(function() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('id-ID', { hour12: false });
        document.getElementById('clock').innerText = timeString;
    }, 1000);
</script>
@endsection

