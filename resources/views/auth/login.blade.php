<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - ATS Alwaliy Sejahtera</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #14261C; --muted: #5C6F64;
            --green-900: #0E3D25; --green-700: #146C43; --green-600: #1C8752;
            --green-100: #E8F6ED; --border: #DCEEE2; --white: #FFFFFF;
            --danger: #B3492F; --danger-bg: #FBEEEA;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif; margin: 0; min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            background: radial-gradient(circle at top left, var(--green-100), #FFFFFF 55%);
            animation: fadeIn .5s ease both;
        }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .login-card {
            background: var(--white); border: 1px solid var(--border); border-radius: 16px;
            box-shadow: 0 20px 48px -20px rgba(14,61,37,.18);
            width: 360px; padding: 2.5rem;
        }
        .mark {
            width: 44px; height: 44px; border-radius: 12px; margin-bottom: 1.25rem;
            background: linear-gradient(160deg, var(--green-600), var(--green-900));
            display: flex; align-items: center; justify-content: center;
        }
        h1 { font-family: 'Sora', sans-serif; font-size: 1.3rem; color: var(--green-900); margin: 0 0 .2rem; }
        p.sub { color: var(--muted); font-size: .88rem; margin: 0 0 1.75rem; }
        label { display: block; font-size: .82rem; font-weight: 600; color: var(--green-900); margin-bottom: .35rem; }
        input {
            width: 100%; padding: .7rem .85rem; margin-bottom: 1.1rem;
            border: 1px solid var(--border); border-radius: 8px; font-size: .93rem;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        input:focus { outline: none; border-color: var(--green-600); box-shadow: 0 0 0 3px var(--green-100); }
        .remember { display: flex; align-items: center; gap: .5rem; font-size: .85rem; color: var(--muted); margin-bottom: 1.5rem; }
        .remember input { width: auto; margin: 0; }
        button {
            width: 100%; background: var(--green-700); color: white; border: none;
            padding: .75rem; border-radius: 8px; font-weight: 600; font-size: .93rem;
            cursor: pointer; transition: background .15s ease, transform .1s ease;
        }
        button:hover { background: var(--green-900); transform: translateY(-1px); }
        .error-box { background: var(--danger-bg); color: var(--danger); border: 1px solid #F3D6CC; padding: .65rem .9rem; border-radius: 8px; font-size: .85rem; margin-bottom: 1.25rem; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="mark">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <path d="M4 20C4 12 9 5 20 4C19 15 12 20 4 20Z" fill="white" fill-opacity="0.95"/>
            </svg>
        </div>
        <h1>Masuk ke ATS</h1>
        <p class="sub">Alwaliy Sejahtera — Panel HR</p>

        @if ($errors->any())
            <div class="error-box">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('login.attempt') }}" method="POST">
            @csrf
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="nama@alwaliy-sejahtera.com" value="{{ old('email') }}" required autofocus>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required>

            <label class="remember" for="remember"><input type="checkbox" id="remember" name="remember"> Ingat saya</label>

            <button type="submit">Masuk</button>
        </form>
    </div>
</body>
</html>