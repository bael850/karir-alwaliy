<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ATS - Alwaliy Sejahtera</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #14261C;
            --muted: #5C6F64;
            --green-900: #0E3D25;
            --green-700: #146C43;
            --green-600: #1C8752;
            --green-500: #2EA968;
            --green-100: #E8F6ED;
            --green-50: #F5FBF7;
            --border: #DCEEE2;
            --white: #FFFFFF;
            --danger: #B3492F;
            --danger-bg: #FBEEEA;
            --radius: 14px;
            --radius-sm: 8px;
            --shadow: 0 1px 2px rgba(14,61,37,.04), 0 10px 28px -14px rgba(14,61,37,.16);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            color: var(--ink);
            background: var(--green-50);
            margin: 0;
            -webkit-font-smoothing: antialiased;
        }

        h1, h2, h3 { font-family: 'Sora', sans-serif; color: var(--green-900); margin: 0 0 .25rem; letter-spacing: -0.01em; }
        h2 { font-size: 1.5rem; }

        a { color: var(--green-700); text-decoration: none; }
        a:hover { color: var(--green-900); }

        /* ── Top nav ── */
        .topbar {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            position: sticky; top: 0; z-index: 10;
        }
        .topbar-inner {
            max-width: 1080px; margin: 0 auto; padding: 0 2rem;
            height: 64px; display: flex; align-items: center; justify-content: space-between;
        }
        .brand { display: flex; align-items: center; gap: .6rem; font-family: 'Sora', sans-serif; font-weight: 700; font-size: 1.05rem; color: var(--green-900); }
        .brand:hover { color: var(--green-900); }
        .brand-mark {
            width: 32px; height: 32px; border-radius: 9px;
            background: linear-gradient(160deg, var(--green-600), var(--green-900));
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .brand em { font-style: normal; color: var(--muted); font-weight: 500; font-size: .85rem; margin-left: .35rem; }

        .topbar-right { display: flex; align-items: center; gap: 1rem; }
        .user-chip { font-size: .9rem; color: var(--muted); }
        .user-chip strong { color: var(--ink); font-weight: 600; }

        .topbar-nav { display: flex; align-items: center; gap: .35rem; }
        .topbar-nav a {
            font-size: .87rem; font-weight: 600; color: var(--muted);
            padding: .45rem .8rem; border-radius: var(--radius-sm);
            transition: background .12s ease, color .12s ease;
        }
        .topbar-nav a:hover { background: var(--green-50); color: var(--green-900); }
        .topbar-nav a.is-active { color: var(--green-700); background: var(--green-100); }
        @media (max-width: 640px) { .topbar-nav { display: none; } }

        /* ── Layout & animation ── */
        .page-wrap { max-width: 1080px; margin: 0 auto; padding: 3.5rem 2rem 4rem; }
        .fade-in { animation: fadeInUp .45s ease both; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (prefers-reduced-motion: reduce) {
            .fade-in { animation: none; }
        }

        .card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 2rem 2.25rem;
        }

        /* ── Alerts ── */
        .status, .error {
            border-radius: var(--radius-sm);
            padding: .8rem 1.1rem;
            margin-bottom: 1.5rem;
            font-size: .92rem;
            display: flex; align-items: center; gap: .6rem;
        }
        .status { background: var(--green-100); color: var(--green-900); border: 1px solid #C9EBD6; }
        .error { background: var(--danger-bg); color: var(--danger); border: 1px solid #F3D6CC; }
        .error ul { margin: 0; padding-left: 1.1rem; }

        /* ── Buttons ── */
        .btn, button {
            font-family: 'Inter', sans-serif; font-weight: 600; font-size: .9rem;
            background: var(--green-700); color: var(--white); border: none;
            padding: .65rem 1.3rem; border-radius: var(--radius-sm); cursor: pointer;
            transition: background .15s ease, transform .1s ease;
            display: inline-flex; align-items: center; gap: .4rem;
        }
        .btn:hover, button:hover { background: var(--green-900); transform: translateY(-1px); }
        .btn:active, button:active { transform: translateY(0); }
        .btn-ghost { background: transparent; color: var(--green-700); border: 1px solid var(--border); }
        .btn-ghost:hover { background: var(--green-50); color: var(--green-900); }
        .btn-danger { background: transparent; color: var(--danger); border: 1px solid #F3D6CC; padding: .4rem .8rem; font-size: .82rem; }
        .btn-danger:hover { background: var(--danger-bg); color: var(--danger); }

        /* ── Table ── */
        table { width: 100%; border-collapse: collapse; }
        th {
            text-align: left; font-size: .78rem; text-transform: uppercase; letter-spacing: .05em;
            color: var(--muted); font-weight: 600; padding: 1.1rem .75rem 1rem;
            border-bottom: 1px solid var(--border);
        }
        td { padding: 1.15rem .75rem; border-bottom: 1px solid var(--border); font-size: .93rem; }
        tbody tr { transition: background .12s ease; }
        tbody tr:hover { background: var(--green-50); }
        tbody tr:last-child td { border-bottom: none; }

        /* ── Status badge ── */
        .badge {
            display: inline-block; padding: .25rem .65rem; border-radius: 999px;
            font-size: .75rem; font-weight: 600; letter-spacing: .01em;
        }
        .badge-draft { background: #EEF1EF; color: var(--muted); }
        .badge-published { background: var(--green-100); color: var(--green-700); }
        .badge-closed { background: #F3EBDD; color: #93672B; }
        .badge-archived { background: #F1F1F1; color: #7A7A7A; }

        /* ── Forms ── */
        label { display: block; font-size: .85rem; font-weight: 600; color: var(--green-900); margin-bottom: .4rem; }
        input, select, textarea {
            width: 100%; padding: .65rem .8rem; margin-bottom: 1.35rem;
            border: 1px solid var(--border); border-radius: var(--radius-sm);
            font-family: 'Inter', sans-serif; font-size: .93rem; color: var(--ink);
            background: var(--white); transition: border-color .15s ease, box-shadow .15s ease;
        }
        input:focus, select:focus, textarea:focus {
            outline: none; border-color: var(--green-600);
            box-shadow: 0 0 0 3px var(--green-100);
        }
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 0 1.25rem; }
        @media (max-width: 640px) { .form-row { grid-template-columns: 1fr; } }

        /* ── Form sections (create/edit) ── */
        .section-title {
            font-family: 'Sora', sans-serif; font-size: .92rem; font-weight: 700;
            color: var(--green-900); margin: 0 0 1.1rem;
            padding-top: 1.75rem; border-top: 1px solid var(--border);
        }
        .section-title:first-child { margin-top: 0; padding-top: 0; border-top: none; }
        .required { color: var(--danger); margin-left: .15rem; }
        .field-hint {
            display: block; margin-top: -1.1rem; margin-bottom: 1.35rem;
            font-size: .78rem; color: var(--muted);
        }
        .form-actions { display: flex; gap: .75rem; align-items: center; margin-top: .5rem; }

        /* ── Filter bar (index) ── */
        .filter-bar {
            display: flex; gap: .9rem; align-items: flex-end; flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }
        .filter-bar .field { flex: 1; min-width: 170px; margin-bottom: 0; }
        .filter-bar .field label { margin-bottom: .35rem; }
        .filter-bar input, .filter-bar select { margin-bottom: 0; }
        .filter-bar .filter-actions { display: flex; gap: .6rem; }

        /* ── Stat cards (show) ── */
        .stat-row { display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
        .stat-card {
            flex: 1; min-width: 150px; background: var(--white);
            border: 1px solid var(--border); border-radius: var(--radius-sm);
            padding: 1.1rem 1.3rem;
        }
        .stat-card .num { font-family: 'Sora', sans-serif; font-size: 1.55rem; font-weight: 700; color: var(--green-900); line-height: 1.2; }
        .stat-card .lbl { font-size: .78rem; color: var(--muted); margin-top: .3rem; }
    </style>
</head>
<body>
    @auth
    <nav class="topbar">
        <div class="topbar-inner">
            <a href="{{ route('job-postings.index') }}" class="brand">
                <span class="brand-mark">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M4 20C4 12 9 5 20 4C19 15 12 20 4 20Z" fill="white" fill-opacity="0.95"/>
                    </svg>
                </span>
                ATS <em>Alwaliy Sejahtera</em>
            </a>
            <nav class="topbar-nav">
                <a href="{{ route('job-postings.index') }}" class="{{ request()->routeIs('job-postings.*') && !request()->routeIs('job-postings.screening-questions.*') ? 'is-active' : '' }}">Lowongan</a>
                <a href="{{ route('pipeline-templates.index') }}" class="{{ request()->routeIs('pipeline-templates.*') ? 'is-active' : '' }}">Template Pipeline</a>
            </nav>
            <div class="topbar-right">
                <span class="user-chip">Halo, <strong>{{ auth()->user()->name }}</strong></span>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-ghost">Logout</button>
                </form>
            </div>
        </div>
    </nav>
    @endauth

    <div class="page-wrap">
        <div class="fade-in">
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
    </div>
</body>
</html>