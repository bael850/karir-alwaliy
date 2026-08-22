<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Karir')  - Alwaliy Sejahtera</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #14261C; --muted: #5C6F64;
            --green-900: #0E3D25; --green-700: #146C43; --green-600: #1C8752; --green-500: #2EA968;
            --green-100: #E8F6ED; --green-50: #F5FBF7;
            --border: #DCEEE2; --white: #FFFFFF;
            --danger: #B3492F; --danger-bg: #FBEEEA;
            --radius: 14px; --radius-sm: 8px;
            --shadow: 0 1px 2px rgba(14,61,37,.04), 0 10px 28px -14px rgba(14,61,37,.16);
        }
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; color: var(--ink); background: var(--green-50); margin: 0; -webkit-font-smoothing: antialiased; }
        h1, h2, h3 { font-family: 'Sora', sans-serif; color: var(--green-900); margin: 0 0 .25rem; letter-spacing: -0.01em; }
        a { color: var(--green-700); text-decoration: none; }
        a:hover { color: var(--green-900); }

        .topbar { background: var(--white); border-bottom: 1px solid var(--border); }
        .topbar-inner { max-width: 960px; margin: 0 auto; padding: 0 1.5rem; height: 64px; display: flex; align-items: center; justify-content: space-between; }
        .brand { display: flex; align-items: center; gap: .6rem; font-family: 'Sora', sans-serif; font-weight: 700; font-size: 1.05rem; color: var(--green-900); }
        .brand-mark { width: 32px; height: 32px; border-radius: 9px; background: linear-gradient(160deg, var(--green-600), var(--green-900)); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .brand em { font-style: normal; color: var(--muted); font-weight: 500; font-size: .85rem; margin-left: .35rem; }
        .topbar-nav a { font-size: .87rem; font-weight: 600; color: var(--muted); padding: .5rem .9rem; border-radius: var(--radius-sm); }
        .topbar-nav a:hover { background: var(--green-50); color: var(--green-900); }

        .page-wrap { max-width: 960px; margin: 0 auto; padding: 3rem 1.5rem 4rem; }
        .card { background: var(--white); border: 1px solid var(--border); border-radius: var(--radius); box-shadow: var(--shadow); padding: 2rem 2.25rem; }

        .status, .error { border-radius: var(--radius-sm); padding: .8rem 1.1rem; margin-bottom: 1.5rem; font-size: .92rem; }
        .status { background: var(--green-100); color: var(--green-900); border: 1px solid #C9EBD6; }
        .error { background: var(--danger-bg); color: var(--danger); border: 1px solid #F3D6CC; }
        .error ul { margin: 0; padding-left: 1.1rem; }

        .btn, button { font-family: 'Inter', sans-serif; font-weight: 600; font-size: .9rem; background: var(--green-700); color: var(--white); border: none; padding: .7rem 1.4rem; border-radius: var(--radius-sm); cursor: pointer; display: inline-flex; align-items: center; gap: .4rem; transition: background .15s ease; }
        .btn:hover, button:hover { background: var(--green-900); }
        .btn-ghost { background: transparent; color: var(--green-700); border: 1px solid var(--border); }
        .btn-ghost:hover { background: var(--green-50); color: var(--green-900); }

        label { display: block; font-size: .85rem; font-weight: 600; color: var(--green-900); margin-bottom: .4rem; }
        input, select, textarea { width: 100%; padding: .65rem .8rem; margin-bottom: 1.35rem; border: 1px solid var(--border); border-radius: var(--radius-sm); font-family: 'Inter', sans-serif; font-size: .93rem; color: var(--ink); background: var(--white); }
        input:focus, select:focus, textarea:focus { outline: none; border-color: var(--green-600); box-shadow: 0 0 0 3px var(--green-100); }
        .required { color: var(--danger); margin-left: .15rem; }
        .field-hint { display: block; margin-top: -1.1rem; margin-bottom: 1.35rem; font-size: .78rem; color: var(--muted); }
        .section-title { font-family: 'Sora', sans-serif; font-size: .92rem; font-weight: 700; color: var(--green-900); margin: 0 0 1.1rem; padding-top: 1.75rem; border-top: 1px solid var(--border); }
        .section-title:first-child { margin-top: 0; padding-top: 0; border-top: none; }

        .job-card { background: var(--white); border: 1px solid var(--border); border-radius: var(--radius); padding: 1.5rem 1.75rem; margin-bottom: 1rem; transition: box-shadow .15s ease; }
        .job-card:hover { box-shadow: var(--shadow); }
        .job-meta { color: var(--muted); font-size: .85rem; margin-top: .35rem; }
        .badge-type { display: inline-block; background: var(--green-100); color: var(--green-700); font-size: .72rem; font-weight: 600; padding: .2rem .6rem; border-radius: 999px; margin-top: .6rem; }
    </style>
</head>
<body>
    <nav class="topbar">
        <div class="topbar-inner">
            <a href="{{ route('careers.index') }}" class="brand">
                <span class="brand-mark">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M4 20C4 12 9 5 20 4C19 15 12 20 4 20Z" fill="white" fill-opacity="0.95"/>
                    </svg>
                </span>
                Karir <em>Alwaliy Sejahtera</em>
            </a>
            <div class="topbar-nav">
                <a href="{{ route('careers.status.form') }}">Cek Status Lamaran</a>
            </div>
        </div>
    </nav>

    <div class="page-wrap">
        @if (session('status'))
            <div class="status">✓ {{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </div>
</body>
</html>