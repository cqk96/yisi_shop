<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Yisi Nails & Beauty')</title>
    <style>
        :root {
            color-scheme: light;
            --ink: #1f2937;
            --muted: #6b7280;
            --line: #e5e7eb;
            --panel: #ffffff;
            --bg: #f6f7f9;
            --brand: #0f766e;
            --brand-dark: #115e59;
            --accent: #d97706;
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
        h1, h2, h3, p { margin-top: 0; }
        h1 { font-size: 30px; margin-bottom: 8px; }
        input, textarea, select {
            border: 1px solid var(--line);
            border-radius: 6px;
            font: inherit;
            padding: 10px 12px;
            width: 100%;
        }
        textarea { min-height: 120px; resize: vertical; }
        .topbar {
            background: var(--panel);
            border-bottom: 1px solid var(--line);
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .nav {
            align-items: center;
            display: flex;
            gap: 20px;
            justify-content: space-between;
            margin: 0 auto;
            max-width: 1180px;
            min-height: 96px;
            padding: 16px 20px;
        }
        .brand { align-items: center; display: inline-flex; }
        .brand img { display: block; height: 56px; width: auto; }
        .top-info {
            align-items: stretch;
            display: grid;
            flex: 1;
            gap: 12px;
            grid-template-columns: minmax(150px, 0.7fr) minmax(260px, 1.3fr);
            min-width: 0;
        }
        .top-info-block {
            background: #f8fafc;
            border: 1px solid var(--line);
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-height: 60px;
            padding: 10px 12px;
        }
        .top-info-label {
            color: var(--muted);
            font-size: 12px;
            line-height: 1.2;
            margin-bottom: 3px;
        }
        .top-info-link {
            color: #1877f2;
            font-weight: 700;
        }
        .top-info-text {
            font-size: 13px;
            line-height: 1.35;
        }
        .nav-links { align-items: center; display: flex; gap: 12px; }
        .language-switcher {
            align-items: center;
            display: inline-flex;
            gap: 8px;
            margin: 0;
        }
        .language-switcher span {
            color: var(--muted);
            font-size: 14px;
        }
        .language-switcher select {
            min-width: 112px;
            padding: 7px 10px;
        }
        .cart-link {
            border: 1px solid var(--line);
            border-radius: 6px;
            padding: 8px 12px;
        }
        .container {
            margin: 0 auto;
            max-width: 1180px;
            padding: 28px 20px 48px;
        }
        .page-head {
            align-items: flex-end;
            display: flex;
            gap: 20px;
            justify-content: space-between;
            margin-bottom: 22px;
        }
        .muted { color: var(--muted); }
        .toolbar {
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 22px;
        }
        .search { display: flex; gap: 8px; min-width: min(100%, 360px); }
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
            gap: 6px;
            justify-content: center;
            min-height: 42px;
            padding: 10px 14px;
            white-space: nowrap;
        }
        .button:hover { background: var(--brand-dark); }
        .button.secondary { background: #ffffff; border: 1px solid var(--line); color: var(--ink); }
        .button.danger { background: var(--danger); }
        .chips { display: flex; flex-wrap: wrap; gap: 8px; }
        .chip {
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 999px;
            color: var(--muted);
            padding: 8px 12px;
        }
        .chip.active { background: var(--brand); border-color: var(--brand); color: #ffffff; }
        .grid {
            display: grid;
            gap: 18px;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        }
        .card {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .product-image {
            aspect-ratio: 4 / 3;
            background: #e5e7eb;
            display: block;
            object-fit: cover;
            width: 100%;
        }
        .card-body {
            display: flex;
            flex: 1;
            flex-direction: column;
            padding: 16px;
        }
        .price { color: var(--accent); font-size: 20px; font-weight: 700; }
        .price-stack {
            align-items: flex-start;
            display: inline-flex;
            flex-direction: column;
            gap: 2px;
        }
        .original-price {
            color: var(--muted);
            font-size: 13px;
            font-weight: 400;
            text-decoration: line-through;
        }
        .product-meta {
            align-items: center;
            display: flex;
            justify-content: space-between;
            margin: 12px 0;
        }
        .product-action {
            margin-top: auto;
        }
        .product-sales {
            color: var(--muted);
            font-size: 13px;
            margin-top: 10px;
            text-align: right;
        }
        .detail {
            display: grid;
            gap: 24px;
            grid-template-columns: minmax(0, 1fr) 380px;
        }
        .detail img { border-radius: 8px; width: 100%; }
        .panel {
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 20px;
        }
        .table {
            background: #ffffff;
            border-collapse: collapse;
            border-radius: 8px;
            overflow: hidden;
            width: 100%;
        }
        .table th, .table td {
            border-bottom: 1px solid var(--line);
            padding: 14px;
            text-align: left;
            vertical-align: middle;
        }
        .table th { color: var(--muted); font-size: 14px; font-weight: 700; }
        .line-actions { align-items: center; display: flex; gap: 8px; }
        .line-actions input { max-width: 88px; }
        .summary {
            align-items: center;
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            margin-top: 18px;
            padding: 16px;
        }
        .form-grid {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .form-grid .full { grid-column: 1 / -1; }
        .alert { border-radius: 8px; margin-bottom: 18px; padding: 12px 14px; }
        .alert.success { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; }
        .alert.error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        .pagination {
            display: flex;
            gap: 8px;
            list-style: none;
            margin: 24px 0 0;
            padding: 0;
        }
        .pagination a, .pagination span {
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 6px;
            display: block;
            min-width: 38px;
            padding: 8px 10px;
            text-align: center;
        }
        .pager {
            align-items: center;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: center;
            margin-top: 26px;
        }
        .pager-link {
            background: #ffffff;
            border: 1px solid var(--line);
            border-radius: 6px;
            display: inline-flex;
            justify-content: center;
            min-width: 40px;
            padding: 8px 12px;
        }
        .pager-link.active {
            background: var(--brand);
            border-color: var(--brand);
            color: #ffffff;
            font-weight: 700;
        }
        .pager-link.disabled {
            color: var(--muted);
            cursor: default;
            opacity: 0.65;
        }
        @media (max-width: 760px) {
            .page-head, .summary { align-items: stretch; flex-direction: column; }
            .nav {
                align-items: center;
                display: grid;
                gap: 10px;
                grid-template-columns: auto minmax(0, 1fr);
                min-height: 88px;
                padding: 10px 12px;
            }
            .brand img { height: 46px; }
            .top-info {
                gap: 6px;
                grid-template-columns: minmax(78px, 0.55fr) minmax(0, 1.45fr);
                min-width: 0;
            }
            .top-info-block {
                border-radius: 7px;
                min-height: 50px;
                padding: 6px 8px;
            }
            .top-info-label {
                font-size: 10px;
                margin-bottom: 1px;
            }
            .top-info-link {
                display: block;
                font-size: 12px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            .top-info-text {
                display: -webkit-box;
                font-size: 10.5px;
                line-height: 1.25;
                max-height: 40px;
                overflow: hidden;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 3;
            }
            .nav-links {
                flex-wrap: wrap;
                grid-column: 1 / -1;
                justify-content: center;
            }
            .detail, .form-grid { grid-template-columns: 1fr; }
            .table { display: block; overflow-x: auto; }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <nav class="nav">
            <a class="brand" href="{{ route('shop.index') }}">
                <img src="{{ asset('images/yisi-logo.png') }}" alt="Yisi Nails & Beauty">
            </a>
            <div class="top-info">
                <a class="top-info-block top-info-facebook" href="https://www.facebook.com/share/1Jut9vRjXA/" target="_blank" rel="noopener">
                    <div class="top-info-label">Facebook</div>
                    <span class="top-info-link">Yisi Nails & Beauty</span>
                </a>
                <div class="top-info-block">
                    <div class="top-info-label">Información del cliente</div>
                    <div class="top-info-text">
                        <strong>dirección:</strong> Calle Jorge Rodríguez Nápoles #21, Esquina J Espinoza, Las Tunas, Tunas<br>
                        <strong>teléfono:</strong> +53 5 4444894
                    </div>
                </div>
            </div>
            <div class="nav-links">
                @include('partials.language-switcher')
                <a href="{{ route('shop.index') }}">{{ __('ui.common.products') }}</a>
                <a href="{{ route('orders.index') }}">{{ __('ui.shop.orders') }}</a>
                <a class="cart-link" href="{{ route('cart.index') }}">{{ __('ui.shop.cart') }} {{ $cartSummary['count'] ?? 0 }}</a>
            </div>
        </nav>
    </header>

    <main class="container">
        @if (session('status'))
            <div class="alert success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>

