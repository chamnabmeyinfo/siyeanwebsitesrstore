<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') · {{ config('app.name') }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: ui-sans-serif, system-ui, sans-serif; margin: 0; background: #f1f5f9; color: #0f172a; }
        .layout { display: flex; min-height: 100vh; }
        .sidebar { width: 240px; background: #0f172a; color: #e2e8f0; padding: 1.25rem 0; flex-shrink: 0; }
        .sidebar h2 { margin: 0 1.25rem 1.25rem; font-size: 1rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; color: #94a3b8; }
        .sidebar a { display: block; padding: 0.65rem 1.25rem; color: #cbd5e1; text-decoration: none; border-left: 3px solid transparent; font-size: 0.95rem; }
        .sidebar a:hover { background: #1e293b; color: #f8fafc; }
        .sidebar a.active { background: #1e293b; color: #f8fafc; border-left-color: #3b82f6; font-weight: 600; }
        .sidebar .group-label { font-size: 0.7rem; color: #64748b; padding: 1rem 1.25rem 0.35rem; text-transform: uppercase; letter-spacing: 0.08em; }
        .main { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .topbar { background: white; padding: 0.85rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; }
        .topbar .who { font-size: 0.875rem; color: #475569; }
        .topbar form { display: inline; }
        .topbar button { background: transparent; border: 1px solid #cbd5e1; padding: 0.4rem 0.85rem; border-radius: 6px; color: #334155; cursor: pointer; font-size: 0.85rem; }
        .topbar button:hover { background: #f8fafc; }
        .content { padding: 1.5rem; flex: 1; }
        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; }
        .page-header h1 { margin: 0; font-size: 1.5rem; }
        .card { background: white; border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); padding: 1.25rem; margin-bottom: 1.25rem; }
        .grid-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
        .stat { background: white; border-radius: 10px; padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .stat .label { font-size: 0.8rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }
        .stat .value { font-size: 1.75rem; font-weight: 700; margin-top: 0.35rem; }
        table { width: 100%; border-collapse: collapse; }
        thead th { text-align: left; font-size: 0.75rem; text-transform: uppercase; color: #64748b; letter-spacing: 0.05em; padding: 0.75rem 0.5rem; border-bottom: 1px solid #e2e8f0; }
        tbody td { padding: 0.75rem 0.5rem; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }
        tbody tr:hover { background: #f8fafc; }
        .btn { display: inline-block; padding: 0.5rem 0.9rem; background: #3b82f6; color: white; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; font-size: 0.875rem; font-weight: 500; }
        .btn:hover { background: #2563eb; }
        .btn-secondary { background: #e2e8f0; color: #1e293b; }
        .btn-secondary:hover { background: #cbd5e1; }
        .btn-danger { background: #ef4444; }
        .btn-danger:hover { background: #dc2626; }
        .btn-sm { padding: 0.3rem 0.6rem; font-size: 0.8rem; }
        .flash { padding: 0.85rem 1rem; border-radius: 8px; margin-bottom: 1rem; font-size: 0.9rem; }
        .flash-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .flash-error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        form .field { margin-bottom: 1rem; }
        form label { display: block; font-size: 0.85rem; color: #475569; margin-bottom: 0.35rem; font-weight: 500; }
        form input[type=text], form input[type=email], form input[type=number], form input[type=date], form input[type=time], form input[type=url], form input[type=file], form select, form textarea {
            width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; background: white;
        }
        form input:focus, form select:focus, form textarea:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; }
        .badge { display: inline-block; padding: 0.2rem 0.55rem; border-radius: 999px; font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-confirmed { background: #dbeafe; color: #1e40af; }
        .badge-picked_up { background: #dcfce7; color: #166534; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }
        .errors { background: #fee2e2; color: #991b1b; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1rem; font-size: 0.85rem; }
        .errors ul { margin: 0.25rem 0 0 1rem; padding: 0; }
        .actions-bar { display: flex; gap: 0.5rem; align-items: center; }
        .text-muted { color: #64748b; font-size: 0.85rem; }
        .text-right { text-align: right; }
        @media (max-width: 768px) {
            .layout { flex-direction: column; }
            .sidebar { width: 100%; }
            .grid-2, .grid-3 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <h2>SR Mac Shop</h2>
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Dashboard</a>

            <div class="group-label">Catalog</div>
            <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}">Products</a>

            <div class="group-label">Operations</div>
            <a href="{{ route('admin.sales.index') }}" class="{{ request()->routeIs('admin.sales.*') ? 'active' : '' }}">Sales</a>
            <a href="{{ route('admin.bookings.index') }}" class="{{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">Bookings</a>

            <div class="group-label">Settings</div>
            <a href="{{ route('admin.store-menu.index') }}" class="{{ request()->routeIs('admin.store-menu.*') ? 'active' : '' }}">Store Menu</a>
            <a href="/" target="_blank">View Site ↗</a>
        </aside>

        <div class="main">
            <header class="topbar">
                <div></div>
                <div class="actions-bar">
                    <span class="who">{{ auth()->user()->name ?? auth()->user()->email }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">Sign out</button>
                    </form>
                </div>
            </header>

            <main class="content">
                @if (session('success'))
                    <div class="flash flash-success">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="flash flash-error">{{ session('error') }}</div>
                @endif
                @if ($errors->any())
                    <div class="errors">
                        <strong>Please fix the following:</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
