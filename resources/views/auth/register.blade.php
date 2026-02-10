<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Training Center Part Production</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }
        .register-card {
            max-width: 500px;
            width: 100%;
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .register-header {
            background-color: #C62828;
            padding: 30px 20px;
            text-align: center;
            color: #fff;
        }
        .register-logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            margin-bottom: 10px;
        }
        .register-body {
            padding: 40px 30px;
            background-color: #fff;
        }
        .form-control:focus, .form-select:focus {
            border-color: #C62828;
            box-shadow: 0 0 0 0.25rem rgba(198, 40, 40, 0.25);
        }
        .btn-register {
            background-color: #C62828;
            border: none;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            margin-top: 10px;
            color: white;
        }
        .btn-register:hover {
            background-color: #b71c1c;
            color: white;
        }
        .login-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #6c757d;
            text-decoration: none;
            font-size: 0.9rem;
        }
        .login-link:hover {
            color: #C62828;
        }
    </style>
</head>
<body>

    <div class="register-card">
        <div class="register-header">
            <img src="{{ asset('img/logo.jpeg') }}" alt="DNP Logo" class="register-logo">
            <h5 class="mb-2 fw-bold text-wrap" style="line-height: 1.2;">TRAINING CENTER PART PRODUCTION</h5>
            <h4 class="mb-0">Daftar Akun Baru</h4>
            <p class="mb-0 small opacity-75">Isi data diri untuk mendaftar</p>
        </div>
        <div class="register-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Nama Lengkap" value="{{ old('name') }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="nik" class="form-label">NIK</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                        <input type="text" class="form-control" id="nik" name="nik" placeholder="Nomor Induk Karyawan" value="{{ old('nik') }}" minlength="4" required>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="role" class="form-label">Daftar Sebagai</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="" selected disabled>Pilih Role</option>
                            <option value="operator" {{ old('role') == 'operator' ? 'selected' : '' }}>Operator</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="division" class="form-label">Bagian / Divisi</label>
                        <select class="form-select" id="division" name="division">
                            <option value="" selected disabled>Pilih Bagian</option>
                            <option value="cover" {{ old('division') == 'cover' ? 'selected' : '' }}>Cover</option>
                            <option value="case" {{ old('division') == 'case' ? 'selected' : '' }}>Case</option>
                            <option value="inner" {{ old('division') == 'inner' ? 'selected' : '' }}>Inner</option>
                            <option value="endplate" {{ old('division') == 'endplate' ? 'selected' : '' }}>Endplate</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Ulangi Password" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-register">
                    <i class="fas fa-user-plus me-2"></i> Daftar Sekarang
                </button>

                <a href="{{ route('login') }}" class="login-link">
                    Sudah punya akun? Login disini
                </a>
            </form>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePasswordVisibility(inputId, buttonId) {
            const passwordInput = document.getElementById(inputId);
            const toggleButton = document.getElementById(buttonId);
            const icon = toggleButton.querySelector('i');

            toggleButton.addEventListener('click', function () {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        }

        togglePasswordVisibility('password', 'togglePassword');
        togglePasswordVisibility('password_confirmation', 'togglePasswordConfirm');
    </script>
</body>
</html>