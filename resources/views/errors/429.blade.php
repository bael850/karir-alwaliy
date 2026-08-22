<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terlalu Banyak Percobaan - ATS Alwaliy Sejahtera</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #14261C; --muted: #5C6F64;
            --green-900: #0E3D25; --green-700: #146C43; --green-600: #1C8752;
            --green-100: #E8F6ED; --border: #DCEEE2; --white: #FFFFFF;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif; margin: 0; min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            background: radial-gradient(circle at top left, var(--green-100), #FFFFFF 55%);
        }
        .error-card {
            background: var(--white); border: 1px solid var(--border); border-radius: 16px;
            box-shadow: 0 20px 48px -20px rgba(14,61,37,.18);
            width: 420px; padding: 2.5rem; text-align: center;
        }
        .mark {
            width: 52px; height: 52px; border-radius: 14px; margin: 0 auto 1.5rem;
            background: linear-gradient(160deg, var(--green-600), var(--green-900));
            display: flex; align-items: center; justify-content: center;
        }
        .code { font-family: 'Sora', sans-serif; font-size: .85rem; font-weight: 700; letter-spacing: .08em; color: var(--green-700); margin-bottom: .5rem; }
        h1 { font-family: 'Sora', sans-serif; font-size: 1.35rem; color: var(--green-900); margin: 0 0 .75rem; }
        p { color: var(--muted); font-size: .92rem; line-height: 1.6; margin: 0 0 1.75rem; }
        .btn {
            display: inline-block; background: var(--green-700); color: white;
            padding: .7rem 1.5rem; border-radius: 8px; font-weight: 600; font-size: .9rem;
            text-decoration: none; transition: background .15s ease;
        }
        .btn:hover { background: var(--green-900); }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="mark">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
        </div>
        <div class="code">ERROR 429</div>
        <h1>Terlalu Banyak Percobaan</h1>
        <p>
            Kamu sudah mencoba login beberapa kali berturut-turut. Demi keamanan akun,
            silakan tunggu sekitar satu menit sebelum mencoba lagi.
        </p>
        <a href="{{ url('/login') }}" class="btn">Coba Lagi</a>
    </div>
</body>
</html>