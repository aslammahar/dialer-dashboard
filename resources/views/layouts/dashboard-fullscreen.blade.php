<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#090d12">
    <title>@yield('page-title', 'Dashboard')</title>

    {{-- Tabler Icons — required for every <i class="ti ti-..."> used across the dashboard --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">

    @stack('css-page')

    <style>
        html, body {
            margin: 0;
            padding: 0;
            background: #090d12;
            overflow-x: hidden;
            height: 100%;
        }

        * {
            box-sizing: border-box;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body {
            background:
                radial-gradient(1200px 600px at 15% -10%, rgba(52,245,197,.06), transparent 60%),
                radial-gradient(900px 500px at 100% 0%, rgba(255,176,32,.04), transparent 55%),
                #090d12;
            min-height: 100vh;
        }

        ::selection {
            background: rgba(52,245,197,.28);
            color: #f3f6f7;
        }

        /* Themed scrollbar (WebKit) */
        ::-webkit-scrollbar { width: 10px; height: 10px; }
        ::-webkit-scrollbar-track { background: #0d1319; }
        ::-webkit-scrollbar-thumb {
            background: #232c36;
            border-radius: 999px;
            border: 2px solid #0d1319;
        }
        ::-webkit-scrollbar-thumb:hover { background: #34f5c5; }

        /* Firefox */
        * { scrollbar-width: thin; scrollbar-color: #232c36 #0d1319; }
    </style>
</head>
<body>
    @yield('content')
    @yield('scripts')
</body>
</html>