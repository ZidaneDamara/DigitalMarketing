<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Yamaha Digital Marketing Performance System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #001A4D 0%, #003399 50%, #000F26 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 1.5rem;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(12px);
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.2);
            width: 100%;
            max-width: 440px;
            padding: 2.5rem;
        }

        @media (max-width: 575.98px) {
            body {
                padding: 1rem;
            }
            .login-card {
                padding: 1.75rem 1.25rem;
                border-radius: 20px;
            }
        }

        .brand-logo-box {
            width: 70px;
            height: 70px;
            background: #003399;
            color: #FFFFFF;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem auto;
            box-shadow: 0 10px 20px rgba(0, 51, 153, 0.3);
        }

        .btn-yamaha-login {
            background: linear-gradient(90deg, #003399 0%, #002266 100%);
            color: #FFFFFF;
            font-weight: 700;
            padding: 0.85rem;
            border-radius: 12px;
            border: none;
            width: 100%;
            transition: all 0.3s;
        }

        .btn-yamaha-login:hover {
            background: linear-gradient(90deg, #E60012 0%, #B3000E 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(230, 0, 18, 0.3);
        }

        .form-control {
            border-radius: 12px;
            padding: 0.75rem 1rem;
            border: 1px solid #CBD5E1;
        }

        .form-control:focus {
            border-color: #003399;
            box-shadow: 0 0 0 0.25rem rgba(0, 51, 153, 0.15);
        }
    </style>
</head>
<body>

    <div class="login-card text-center">
        <div class="brand-logo-box">
            <i class="fas fa-motorcycle fa-2x"></i>
        </div>

        <h4 class="fw-bold text-dark mb-1">YAMAHA DMPMS</h4>
        <p class="text-muted small mb-4">Digital Marketing Performance Management System<br><strong>PT. Aspacindo Kedaton Motor</strong></p>

        @if($errors->any())
            <div class="alert alert-danger text-start small mb-4 rounded-3">
                <i class="fas fa-exclamation-circle me-1"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="text-start mb-3">
                <label class="form-label fw-semibold text-dark small">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-muted"></i></span>
                    <input type="email" name="email" class="form-control border-start-0" placeholder="user@aspacindo.co.id" value="{{ old('email') }}" required autofocus>
                </div>
            </div>

            <div class="text-start mb-4">
                <label class="form-label fw-semibold text-dark small">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
                    <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn btn-yamaha-login mb-3">
                MASUK KE SISTEM <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </form>

        <div class="mt-4 pt-3 border-top text-muted" style="font-size: 0.75rem;">
            Akses Khusus Internal Perusahaan PT. Aspacindo Kedaton Motor.<br>Untuk pembuatan akun baru, hubungi Super Admin Digital.
        </div>
    </div>

</body>
</html>
