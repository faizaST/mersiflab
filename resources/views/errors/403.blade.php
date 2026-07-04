<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 — Akses Ditolak | MersifLab</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Outfit', sans-serif; background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 30%, #faf5ff 60%, #fdf2f8 100%); min-height: 100vh; }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4">
    <div class="text-center max-w-md">
        <div class="w-24 h-24 rounded-3xl bg-red-50 border border-red-100 flex items-center justify-center mx-auto mb-6 shadow-sm">
            <i class="fa-solid fa-shield-halved text-red-500 text-4xl"></i>
        </div>
        <h1 class="text-6xl font-extrabold text-slate-800 mb-2">403</h1>
        <h2 class="text-xl font-bold text-slate-700 mb-3">Akses Ditolak</h2>
        <p class="text-slate-500 text-sm mb-8">
            {{ $exception->getMessage() ?: 'Anda tidak memiliki izin untuk mengakses halaman ini.' }}
        </p>
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}"
           class="inline-flex items-center gap-2 px-6 py-3 text-white text-sm font-semibold rounded-xl transition-all"
           style="background: linear-gradient(135deg, #38bdf8 0%, #818cf8 100%); box-shadow: 0 4px 14px rgba(56,189,248,0.35);">
            <i class="fa-solid fa-arrow-left text-xs"></i> Kembali
        </a>
    </div>
</body>
</html>
