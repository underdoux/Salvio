<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Salvio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a;
            color: #f1f5f9;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md p-8 bg-slate-800 rounded-lg shadow-lg">
        <h1 class="text-3xl font-bold mb-6 text-center text-indigo-400">Salvio Login</h1>
        <?php if (!empty($error)): ?>
            <div class="mb-4 p-3 bg-red-600 text-white rounded">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="/login" class="space-y-6">
            <div>
                <label for="username" class="block mb-2 font-semibold">Username</label>
                <input type="text" id="username" name="username" required autofocus
                    class="w-full px-4 py-2 rounded bg-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-indigo-400" />
            </div>
            <div>
                <label for="password" class="block mb-2 font-semibold">Password</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-2 rounded bg-slate-700 text-white focus:outline-none focus:ring-2 focus:ring-indigo-400" />
            </div>
            <button type="submit" class="w-full py-3 bg-indigo-500 hover:bg-indigo-600 rounded text-white font-semibold transition-colors">
                Log In
            </button>
        </form>
    </div>
</body>
</html>
