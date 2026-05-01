<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: ui-sans-serif, system-ui, sans-serif;
            margin: 0;
            min-height: 100vh;
            background: #0f172a;
            color: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .card {
            width: 100%;
            max-width: 400px;
            background: #1e293b;
            border-radius: 12px;
            padding: 1.75rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.45);
        }
        h1 { font-size: 1.25rem; margin: 0 0 1rem; font-weight: 600; }
        label { display: block; font-size: 0.875rem; color: #94a3b8; margin-bottom: 0.35rem; }
        input[type=text], input[type=email], input[type=password] {
            width: 100%;
            padding: 0.6rem 0.75rem;
            border-radius: 8px;
            border: 1px solid #334155;
            background: #0f172a;
            color: #f8fafc;
            margin-bottom: 1rem;
        }
        input:focus { outline: none; border-color: #64748b; }
        input[type=checkbox] { width: auto; margin-right: 0.35rem; }
        button, .btn-primary {
            display: inline-block;
            width: 100%;
            padding: 0.65rem 1rem;
            border: none;
            border-radius: 8px;
            background: #3b82f6;
            color: white;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            font-size: 1rem;
        }
        button:hover, .btn-primary:hover { background: #2563eb; }
        .btn-secondary {
            background: #334155;
            margin-top: 0.75rem;
        }
        .btn-secondary:hover { background: #475569; }
        .error { color: #fca5a5; font-size: 0.875rem; margin-bottom: 0.75rem; }
        .muted { color: #94a3b8; font-size: 0.875rem; margin-top: 1rem; text-align: center; }
        .muted a { color: #93c5fd; }
        .success { color: #86efac; font-size: 0.875rem; margin-bottom: 0.75rem; }
        .remember { display: flex; align-items: center; margin-bottom: 1rem; font-size: 0.875rem; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="card">
        @yield('content')
    </div>
</body>
</html>
