<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Dosen — Lab Komputer STMIK WCD</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lucide-static@latest/font/lucide.min.css">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f4f2ef;
            -webkit-font-smoothing: antialiased;
            padding: 20px
        }

        .login-wrap {
            width: 100%;
            max-width: 400px
        }

        .login-header {
            text-align: center;
            margin-bottom: 28px
        }

        .login-logo {
            width: 48px;
            height: 48px;
            background: #1e293b;
            border-radius: 12px;
            display: grid;
            place-items: center;
            margin: 0 auto 14px;
            color: #94a3b8;
            font-size: 22px
        }

        .login-header h1 {
            font-size: 20px;
            font-weight: 800;
            color: #1c1917;
            letter-spacing: -.3px
        }

        .login-header p {
            font-size: 13px;
            color: #a8a29e;
            margin-top: 4px
        }

        .login-card {
            background: #fff;
            border: 1px solid #e5e1dc;
            border-radius: 16px;
            padding: 28px 24px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .04)
        }

        .field {
            margin-bottom: 18px
        }

        .field label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #57534e;
            margin-bottom: 6px
        }

        .field .input-wrap {
            position: relative
        }

        .field .input-wrap i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 15px;
            color: #d6d3d1
        }

        .field input[type=email],
        .field input[type=password] {
            width: 100%;
            padding: 10px 12px 10px 38px;
            border: 1px solid #e5e1dc;
            border-radius: 10px;
            font: inherit;
            font-size: 14px;
            color: #1c1917;
            background: #fafaf9;
            outline: none;
            transition: border-color .15s, box-shadow .15s
        }

        .field input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .1);
            background: #fff
        }

        .field input::placeholder {
            color: #d6d3d1
        }

        .field-check {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px
        }

        .field-check input[type=checkbox] {
            width: 16px;
            height: 16px;
            accent-color: #1e293b;
            border-radius: 4px;
            cursor: pointer
        }

        .field-check label {
            font-size: 12px;
            color: #57534e;
            cursor: pointer;
            font-weight: 500
        }

        .btn-login {
            width: 100%;
            padding: 11px;
            border: none;
            border-radius: 10px;
            background: #1e293b;
            color: #fff;
            font: inherit;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: background .15s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px
        }

        .btn-login:hover {
            background: #334155
        }

        .btn-login:active {
            background: #0f172a
        }

        .btn-login i {
            font-size: 15px
        }

        .error-box {
            background: #fff1f2;
            border: 1px solid #fecdd3;
            border-radius: 8px;
            padding: 10px 14px;
            margin-bottom: 16px;
            font-size: 12px;
            color: #be123c;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px
        }

        .error-box i {
            font-size: 14px;
            flex-shrink: 0
        }

        .login-footer {
            text-align: center;
            margin-top: 20px
        }

        .login-footer a {
            font-size: 13px;
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px
        }

        .login-footer a:hover {
            text-decoration: underline
        }

        .login-footer a i {
            font-size: 13px
        }

        .login-footer .copy {
            font-size: 11px;
            color: #d6d3d1;
            margin-top: 12px
        }
    </style>
</head>

<body>

    <div class="login-wrap">
        <div class="login-header">
            <h1>Login Dosen</h1>
            <p>Lab Komputer — STMIK Widya Cipta Dharma</p>
        </div>

        <div class="login-card">
            @if($errors->any())
                <div class="error-box">
                    <i class="lucide-alert-circle"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="field">
                    <label for="email">Email</label>
                    <div class="input-wrap">
                        <i class="lucide-mail"></i>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                            placeholder="nama@wicida.ac.id" required autofocus>
                    </div>
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <i class="lucide-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                    </div>
                </div>

                <div class="field-check">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Ingat saya</label>
                </div>

                <button type="submit" class="btn-login">
                    <i class="lucide-log-in"></i>
                    Masuk
                </button>
            </form>
        </div>

        <div class="login-footer">
            <a href="/"><i class="lucide-arrow-left"></i> Kembali ke Halaman Publik</a>
            <p class="copy">&copy; {{ date('Y') }} STMIK Widya Cipta Dharma</p>
        </div>
    </div>

</body>

</html>