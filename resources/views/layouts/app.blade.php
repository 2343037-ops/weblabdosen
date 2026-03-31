<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Lab Komputer STMIK WCD</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lucide-static@latest/font/lucide.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
            background: #f4f2ef;
            color: #1c1917;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased
        }

        /* ANIMATIONS */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-10px) }
            to { opacity: 1; transform: translateY(0) }
        }
        @keyframes sweepLight {
            0% { right: -100% }
            20% { right: 100% }
            100% { right: 100% }
        }

        /* NAVBAR (HEADER) */
        .nav {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            padding: 24px 0;
            position: relative;
            overflow: hidden;
            animation: fadeInDown 0.6s cubic-bezier(0.2, 0.8, 0.2, 1) both;
        }

        .nav::after {
            content: "";
            position: absolute;
            top: 0;
            right: -100%;
            width: 50%;
            height: 100%;
            background: linear-gradient(to left, rgba(255,255,255,0) 0%, rgba(255,255,255,0.08) 50%, rgba(255,255,255,0) 100%);
            transform: skewX(-25deg);
            pointer-events: none;
            animation: sweepLight 5s infinite cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-in {
            max-width: 960px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            position: relative;
            z-index: 2;
        }

        .nav-l {
            display: flex;
            align-items: center;
            gap: 12px
        }

        .nav-ico {
            color: #93c5fd;
            font-size: 20px
        }

        .nav-l span {
            font-size: 17px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -.2px
        }

        .nav-r {
            display: flex;
            align-items: center;
            gap: 14px
        }

        .nav-r a,
        .nav-r button {
            font: inherit;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 0 16px;
            height: 34px;
            border-radius: 6px;
            transition: all .12s;
            cursor: pointer;
            border: none;
            line-height: 1
        }

        .nav-r form {
            display: flex;
            align-items: center;
            margin: 0
        }

        .nav-pub {
            background: #ffffff;
            color: #1e3a8a;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .1)
        }

        .nav-pub:hover {
            background: #fafaf9;
            color: #1e40af;
            transform: translateY(-1px)
        }

        .nav-out {
            background: rgba(0, 0, 0, .2);
            color: #ffffff
        }

        .nav-out:hover {
            background: #ef4444;
            color: #ffffff;
            transform: translateY(-1px)
        }

        .nav-r i {
            font-size: 13px
        }

        .mx {
            max-width: 960px;
            margin: 0 auto;
            padding: 0 20px
        }

        [x-cloak] {
            display: none !important
        }

        /* LAYOUT & FOOTER */
        html, body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
        }

        .app-ft {
            margin-top: 60px;
            padding: 40px 20px 30px;
            background: #fff;
            border-top: 1px solid #e5e1dc;
            text-align: center;
            font-size: 13.5px;
            color: #64748b;
        }

        .app-ft p {
            margin: 0 0 8px 0;
        }
    </style>
    @stack('styles')
</head>

<body>

    @if(request('iframe') != '1')
    <nav class="nav">
        <div class="nav-in">
            <div class="nav-l">
                <i class="lucide-calendar-days nav-ico"></i>
                <span>{{ Auth::check() && Auth::user()->role === 'admin' ? 'Dashboard Admin' : 'Dashboard Dosen' }}</span>
            </div>
            <div class="nav-r">
                @if(Auth::check() && Auth::user()->role === 'admin' && request()->routeIs('admin.dosen.jadwal'))
                    <a href="{{ route('admin.dashboard') }}" class="nav-out" style="background: rgba(255,255,255,.1);"><i class="lucide-arrow-left"></i> Kembali</a>
                @endif
                <a href="/" class="nav-pub"><i class="lucide-globe"></i> Halaman Publik</a>
                <form method="POST" action="{{ route('logout') }}" style="margin:0">
                    @csrf
                    <button type="submit" class="nav-out"><i class="lucide-log-out"></i> Logout</button>
                </form>
            </div>
        </div>
    </nav>
    @endif

    <main class="mx" style="padding-top:{{ request('iframe') == '1' ? '0' : '24px' }};padding-bottom:40px">
        @yield('content')
    </main>

    @if(request('iframe') != '1')
    <footer class="app-ft">
        <p>&copy; {{ date('Y') }} Lab Komputer — STMIK Widya Cipta Dharma, Samarinda</p>
    </footer>
    @endif

    @stack('scripts')
</body>

</html>