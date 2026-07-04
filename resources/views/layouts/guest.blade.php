<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MersifLab') }} - Auth</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- FontAwesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <style>
            :root {
                --primary: #6366f1;
                --primary-hover: #4f46e5;
                --bg-gradient-1: #f8fafc;
                --bg-gradient-2: #eff6ff;
                --surface: #ffffff;
                --surface-border: #e2e8f0;
                --text-main: #1e293b;
                --text-muted: #64748b;
                --input-bg: #f8fafc;
                --input-border: #e2e8f0;
                --input-focus: #6366f1;
                --error: #ef4444;
            }

            * {
                box-sizing: border-box;
                margin: 0;
                padding: 0;
            }

            body {
                font-family: 'Outfit', sans-serif;
                background: linear-gradient(135deg, var(--bg-gradient-1), var(--bg-gradient-2));
                color: var(--text-main);
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                position: relative;
                overflow-x: hidden;
                overflow-y: auto;
                padding: 40px 20px;
            }

            /* Animated Background Elements */
            .bg-shape {
                position: absolute;
                border-radius: 50%;
                filter: blur(80px);
                z-index: 0;
                animation: float 15s infinite ease-in-out alternate;
            }
            .shape-1 {
                width: 400px;
                height: 400px;
                background: rgba(99, 102, 241, 0.08);
                top: -100px;
                left: -100px;
                animation-delay: 0s;
            }
            .shape-2 {
                width: 500px;
                height: 500px;
                background: rgba(236, 72, 153, 0.05);
                bottom: -150px;
                right: -100px;
                animation-delay: -5s;
            }

            @keyframes float {
                0% { transform: translate(0, 0) scale(1); }
                100% { transform: translate(40px, 60px) scale(1.1); }
            }

            /* Clean Container */
            .auth-container {
                position: relative;
                z-index: 10;
                background: var(--surface);
                border: 1px solid var(--surface-border);
                border-radius: 24px;
                padding: 40px;
                width: 100%;
                max-width: 420px;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
                animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            }

            @keyframes slideUp {
                from { opacity: 0; transform: translateY(30px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .logo-container {
                display: flex;
                justify-content: center;
                margin-bottom: 25px;
            }

            .logo-container img {
                transition: transform 0.3s ease;
            }
            
            .logo-container img:hover {
                transform: scale(1.05);
            }

            /* Form Elements */
            .form-group {
                margin-bottom: 22px;
            }

            .form-label {
                display: block;
                font-size: 0.9rem;
                font-weight: 500;
                margin-bottom: 8px;
                color: var(--text-main);
                letter-spacing: 0.3px;
            }

            .form-input {
                width: 100%;
                background: var(--input-bg);
                border: 1px solid var(--input-border);
                color: var(--text-main);
                padding: 14px 16px;
                border-radius: 12px;
                font-family: 'Outfit', sans-serif;
                font-size: 1rem;
                transition: all 0.3s ease;
                outline: none;
            }
            
            .form-input::placeholder {
                color: var(--text-muted);
                opacity: 0.6;
            }

            .form-input:focus {
                border-color: var(--input-focus);
                box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
                background: white;
            }

            .form-error {
                color: var(--error);
                font-size: 0.85rem;
                margin-top: 6px;
                display: block;
            }

            /* Password wrapper */
            .pwd-wrapper {
                position: relative;
            }
            .pwd-wrapper .form-input {
                padding-right: 44px;
            }
            .pwd-toggle {
                position: absolute;
                right: 14px;
                top: 50%;
                transform: translateY(-50%);
                background: none;
                border: none;
                cursor: pointer;
                color: #94a3b8;
                padding: 0;
                line-height: 1;
                font-size: 15px;
                transition: color 0.2s;
            }
            .pwd-toggle:hover {
                color: #6366f1;
            }

            /* Buttons */
            .btn-primary {
                width: 100%;
                background: var(--primary);
                color: white;
                border: none;
                padding: 14px;
                border-radius: 12px;
                font-size: 1.05rem;
                font-weight: 600;
                font-family: 'Outfit', sans-serif;
                cursor: pointer;
                transition: all 0.3s ease;
                margin-top: 10px;
                box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
            }

            .btn-primary:hover {
                background: var(--primary-hover);
                box-shadow: 0 8px 16px rgba(99, 102, 241, 0.3);
                transform: translateY(-1px);
            }

            .btn-primary:active {
                transform: translateY(0);
            }

            /* Links & Checkbox */
            .flex-between {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-top: 10px;
                margin-bottom: 25px;
            }

            .checkbox-label {
                display: flex;
                align-items: center;
                cursor: pointer;
                font-size: 0.95rem;
                color: var(--text-muted);
            }

            .checkbox-label input[type="checkbox"] {
                margin-right: 10px;
                cursor: pointer;
                accent-color: var(--primary);
                width: 18px;
                height: 18px;
                border-radius: 4px;
            }

            .auth-link {
                color: var(--primary);
                text-decoration: none;
                font-size: 0.95rem;
                font-weight: 500;
                transition: color 0.3s;
            }

            .auth-link:hover {
                color: var(--primary-hover);
                text-decoration: underline;
            }

            .auth-title {
                text-align: center;
                font-size: 1.5rem;
                line-height: 1.3;
                font-weight: 700;
                margin-bottom: 8px;
                color: #1e293b;
            }

            .auth-subtitle {
                text-align: center;
                color: var(--text-muted);
                font-size: 1rem;
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="bg-shape shape-1"></div>
        <div class="bg-shape shape-2"></div>

        <div class="auth-container">
            <div class="logo-container">
                <a href="/">
                    <img src="{{ asset('img/logo-mersfilab.png') }}" alt="MersifLab Logo" style="height: 52px; width: auto; object-fit: contain;">
                </a>
            </div>
            
            {{ $slot }}
        </div>
    </body>
</html>
