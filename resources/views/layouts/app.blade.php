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

        /* NAVBAR */
        .nav {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            padding: 0
        }

        .nav-in {
            max-width: 960px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 52px
        }

        .nav-l {
            display: flex;
            align-items: center;
            gap: 10px
        }

        .nav-ico {
            color: #93c5fd;
            font-size: 17px
        }

        .nav-l span {
            font-size: 14px;
            font-weight: 700;
            color: #f1f5f9
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
    </style>
    @stack('styles')
</head>

<body>

    <nav class="nav">
        <div class="nav-in">
            <div class="nav-l">
                <i class="lucide-calendar-days nav-ico"></i>
                <span>Dasboard Dosen</span>
            </div>
            <div class="nav-r">
                <a href="/" class="nav-pub"><i class="lucide-globe"></i> Halaman Publik</a>
                <form method="POST" action="{{ route('logout') }}" style="margin:0">
                    @csrf
                    <button type="submit" class="nav-out"><i class="lucide-log-out"></i> Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="mx" style="padding-top:24px;padding-bottom:40px">
        @yield('content')
    </main>

    @stack('scripts')
</body>

</html>