<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Lab Komputer STMIK WCD</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lucide-static@latest/font/lucide.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Plus Jakarta Sans',system-ui,sans-serif;background:#f4f2ef;color:#1c1917;line-height:1.6;-webkit-font-smoothing:antialiased}

        /* NAVBAR */
        .nav{background:#1e293b;padding:0}
        .nav-in{max-width:960px;margin:0 auto;padding:0 20px;display:flex;align-items:center;justify-content:space-between;height:52px}
        .nav-l{display:flex;align-items:center;gap:10px}
        .nav-ico{color:#94a3b8;font-size:17px}
        .nav-l span{font-size:14px;font-weight:700;color:#f1f5f9}
        .nav-r{display:flex;align-items:center;gap:14px}
        .nav-r a,.nav-r button{font:inherit;font-size:12px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border-radius:6px;transition:all .12s;cursor:pointer;border:none}
        .nav-pub{background:rgba(255,255,255,.08);color:#94a3b8}
        .nav-pub:hover{background:rgba(255,255,255,.14);color:#cbd5e1}
        .nav-out{background:rgba(239,68,68,.15);color:#fca5a5}
        .nav-out:hover{background:rgba(239,68,68,.25);color:#fecaca}
        .nav-r i{font-size:13px}

        .mx{max-width:960px;margin:0 auto;padding:0 20px}

        [x-cloak]{display:none!important}
    </style>
    @stack('styles')
</head>
<body>

<nav class="nav">
    <div class="nav-in">
        <div class="nav-l">
            <i class="lucide-calendar-days nav-ico"></i>
            <span>Penjadwalan Dosen</span>
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
