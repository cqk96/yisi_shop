<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', '后台管理') - LaravelShop</title>
    <style>
        :root {
            --bg: #f4f6f8;
            --panel: #ffffff;
            --ink: #1f2937;
            --muted: #6b7280;
            --line: #e5e7eb;
            --brand: #0f766e;
            --brand-dark: #115e59;
            --danger: #b91c1c;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: var(--bg);
            color: var(--ink);
            font-family: Arial, "Microsoft YaHei", sans-serif;
            line-height: 1.5;
        }
        a { color: inherit; text-decoration: none; }
        .shell { display: grid; grid-template-columns: 220px minmax(0, 1fr); min-height: 100vh; }
        .sidebar { background: #111827; color: #ffffff; padding: 22px 16px; }
        .brand { color: #5eead4; display: block; font-size: 21px; font-weight: 700; margin-bottom: 24px; }
        .side-link { border-radius: 6px; color: #d1d5db; display: block; margin-bottom: 8px; padding: 10px 12px; }
        .side-link:hover, .side-link.active { background: #1f2937; color: #ffffff; }
        .main { min-width: 0; }
        .topbar {
            align-items: center;
            background: var(--panel);
            border-bottom: 1px solid var(--line);
            display: flex;
            justify-content: space-between;
            padding: 14px 24px;
        }
        .content { padding: 24px; }
        .page-head {
            align-items: center;
            display: flex;
            gap: 16px;
            justify-content: space-between;
            margin-bottom: 18px;
        }
        h1 { font-size: 26px; margin: 0; }
        h2 { font-size: 18px; margin: 0 0 14px; }
        .muted { color: var(--muted); }
        .panel, .metric, .table-wrap {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 8px;
        }
        .panel { padding: 20px; }
        .metrics { display: grid; gap: 14px; grid-template-columns: repeat(3, minmax(0, 1fr)); margin-bottom: 18px; }
        .metric { padding: 18px; }
        .metric strong { display: block; font-size: 28px; }
        .button {
            align-items: center;
            background: var(--brand);
            border: 0;
            border-radius: 6px;
            color: #ffffff;
            cursor: pointer;
            display: inline-flex;
            font: inherit;
            font-weight: 700;
            justify-content: center;
            min-height: 40px;
            padding: 9px 13px;
            white-space: nowrap;
        }
        .button:hover { background: var(--brand-dark); }
        .button.secondary { background: #ffffff; border: 1px solid var(--line); color: var(--ink); }
        .button.danger { background: var(--danger); }
        .table { border-collapse: collapse; width: 100%; }
        .table th, .table td { border-bottom: 1px solid var(--line); padding: 13px; text-align: left; vertical-align: middle; }
        .table th { color: var(--muted); font-size: 14px; }
        .actions { align-items: center; display: flex; gap: 8px; }
        .form-grid { display: grid; gap: 16px; grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .full { grid-column: 1 / -1; }
        label { display: block; font-weight: 700; margin-bottom: 6px; }
        input, textarea, select {
            border: 1px solid var(--line);
            border-radius: 6px;
            font: inherit;
            padding: 10px 12px;
            width: 100%;
        }
        textarea { min-height: 130px; resize: vertical; }
        .inline-grid { display: grid; gap: 10px; grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .alert { border-radius: 8px; margin-bottom: 16px; padding: 12px 14px; }
        .alert.success { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; }
        .alert.error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .thumbs { display: flex; gap: 6px; }
        .thumbs img { border-radius: 4px; height: 42px; object-fit: cover; width: 54px; }
        .login { display: grid; min-height: 100vh; place-items: center; padding: 20px; }
        .login-card { background: #ffffff; border: 1px solid var(--line); border-radius: 8px; max-width: 420px; padding: 24px; width: 100%; }
        .pagination { display: flex; gap: 8px; list-style: none; margin: 18px 0 0; padding: 0; }
        .pagination a, .pagination span { background: #ffffff; border: 1px solid var(--line); border-radius: 6px; display: block; padding: 8px 10px; }
        @media (max-width: 850px) {
            .shell { grid-template-columns: 1fr; }
            .sidebar { position: static; }
            .metrics, .form-grid, .inline-grid { grid-template-columns: 1fr; }
            .table-wrap { overflow-x: auto; }
        }
    </style>
</head>
<body>
    @yield('body')
</body>
</html>
