<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline - Todo App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-slate-900 h-screen flex flex-col items-center justify-center p-6 text-center">
    <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200 max-w-sm w-full">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-100 mb-6 text-slate-400">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414"></path></svg>
        </div>
        <h1 class="text-xl font-bold text-slate-900 mb-2">Anda sedang Offline</h1>
        <p class="text-slate-500 mb-6">Koneksi internet terputus. Silakan periksa jaringan Anda dan coba lagi.</p>
        <button onclick="window.location.reload()" class="w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-lg transition-colors">
            Coba Lagi
        </button>
    </div>
</body>
</html>
