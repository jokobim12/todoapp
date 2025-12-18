<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Todo App</title>
    
    <!-- PWA & Icons -->
    <link rel="icon" type="image/png" href="img/icon.png">
    <link rel="apple-touch-icon" href="img/icon.png">
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#0f172a">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Loading Overlay */
        #loading-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255,255,255,0.9);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: opacity 0.5s ease;
        }
        .hidden-loader {
            opacity: 0;
            pointer-events: none;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #e2e8f0;
            border-top: 5px solid #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-gray-50 text-slate-900 antialiased min-h-screen">

    <!-- Loading Overlay -->
    <div id="loading-overlay">
        <div class="spinner border-t-slate-800 border-slate-200"></div>
    </div>

    <!-- Main Content Wrapper -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8 sm:py-12">
        
        <!-- Header -->
        <header class="mb-10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-gray-200 pb-6">
            <div>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Todo App.</h1>
                <p class="text-slate-500 text-base mt-2">Dashboard Produktivitas Saya.</p>
            </div>
            <nav class="flex items-center gap-3 w-full sm:w-auto">
                <a href="index.php" onclick="showLoader()" class="px-5 py-2.5 text-sm font-medium text-slate-600 hover:text-slate-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-all shadow-sm">Beranda</a>
                <a href="crud.php" onclick="showLoader()" class="flex-1 sm:flex-none justify-center px-5 py-2.5 text-sm font-bold bg-slate-900 text-white rounded-lg hover:bg-slate-800 transition-all shadow-md flex items-center gap-2 hover:-translate-y-0.5 transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tugas Baru
                </a>
            </nav>
        </header>

        <main>
            <?php if (isset($content)) echo $content; ?>
        </main>

        <footer class="mt-12 pt-8 border-t border-gray-200 text-center text-sm text-slate-400">
            &copy; <?= date('Y') ?> Simple Todo.
        </footer>
    </div>

    <script>
        // Hide loader on page load
        window.addEventListener('load', function() {
            setTimeout(function() {
                const overlay = document.getElementById('loading-overlay');
                overlay.classList.add('hidden-loader');
            }, 300); 
        });

        // Function to show loader manually when navigating
        function showLoader() {
            const overlay = document.getElementById('loading-overlay');
            overlay.classList.remove('hidden-loader');
        }

        // Register Service Worker for PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('sw.js').then(function(registration) {
                    console.log('ServiceWorker registration successful with scope: ', registration.scope);
                }, function(err) {
                    console.log('ServiceWorker registration failed: ', err);
                });
            });
        }
    </script>
    
    <style>
        /* Minimalist Spinner */
        .spinner {
            width: 40px;
            height: 40px;
            border-width: 3px;
            border-style: solid;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</body>
</html>
