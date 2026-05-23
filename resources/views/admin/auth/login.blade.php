<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login | Quản trị</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.3.4/css/adminlte.min.css" integrity="sha512-Um/vpVpje0gYp+UbwB85CyJj/oXQ+7pa3E9mW4iG+RH+DPfNfKbA5f5GPkN8PCkARhyge92XvXOVI3KDSgX6ZA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #4f46e5 0%, #1d4ed8 100%);
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        .login-card-body {
            border-radius: 1.25rem;
            box-shadow: 0 28px 70px rgba(15,23,42,0.24);
            background: #ffffff;
        }
        .login-logo a {
            color: #ffffff;
            font-size: 2rem;
            font-weight: 700;
            text-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .login-box {
            width: 420px;
            margin: 6% auto;
        }
        .login-box-msg {
            font-size: 1rem;
            color: #475569;
        }
        .btn-primary {
            background: #4f46e5;
            border-color: #4f46e5;
        }
    </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo text-center mb-4">
        <a href="#"><i class="fas fa-robot mr-2"></i><b>Bot</b> Admin</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Đăng nhập để quản lý user, team và báo cáo.</p>

            <form action="{{ route('admin.login.submit') }}" method="post">
                @csrf
                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}" required autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Mật khẩu" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-lock"></span></div>
                    </div>
                </div>
                @error('email')
                    <div class="text-danger mb-3">{{ $message }}</div>
                @enderror
                <div class="row align-items-center">
                    <div class="col-7">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label text-muted" for="remember">Ghi nhớ đăng nhập</label>
                        </div>
                    </div>
                    <div class="col-5">
                        <button type="submit" class="btn btn-primary btn-block">Đăng nhập</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.3.4/js/adminlte.min.js" integrity="sha512-PgybhUL8YBYAuCzwjD5ze1D4tq6l5GBeuv0nmV/eobHnGCisb0HznvK5wV1wnB4A8bH7caNRNJ+hj6NJ5/IuFQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</body>
</html>
