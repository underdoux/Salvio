<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl mx-auto text-center">
        <div class="bg-white rounded-2xl shadow-xl p-8 md:p-12 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-red-500 to-orange-600"></div>
            
            <div class="mb-8">
                <h1 class="text-6xl md:text-8xl font-bold text-gray-200 mb-4">500</h1>
                <h2 class="text-2xl md:text-3xl font-semibold text-gray-800 mb-2">Server Error</h2>
                <p class="text-gray-600 text-lg">We're experiencing some technical difficulties.<br>Our team has been notified and is working on it.</p>
            </div>

            <div class="space-y-4">
                <a href="{{ url('/') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-500 to-orange-600 text-white font-medium rounded-lg transition-transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <i class="fas fa-redo-alt mr-2"></i>
                    Try Again
                </a>
                
                <div class="text-sm text-gray-500 mt-8">
                    <p>If the problem persists, please contact our support team</p>
                    <p class="mt-2">Error Reference: {{ request()->getRequestUri() }}</p>
                </div>
            </div>

            <div class="absolute bottom-0 right-0 opacity-10">
                <i class="fas fa-cogs text-9xl text-gray-300"></i>
            </div>
        </div>
    </div>

    <script>
        // Optional: Add smooth hover effects
        document.querySelectorAll('a').forEach(button => {
            button.addEventListener('mouseover', e => {
                button.style.transform = 'translateY(-2px)';
            });
            button.addEventListener('mouseout', e => {
                button.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>
