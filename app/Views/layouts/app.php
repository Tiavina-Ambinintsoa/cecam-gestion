<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion CECAM</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">
    <div class="flex min-h-screen">
        <aside class="w-64 bg-emerald-900 text-emerald-50 flex flex-col">
            <div class="px-6 py-5 border-b border-emerald-800">
                <h1 class="font-bold text-lg">Gestion CECAM</h1>
                <p class="text-xs text-emerald-300">Épargne &amp; Crédit</p>
            </div>
            <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
                <a href="/" class="block px-3 py-2 rounded-lg hover:bg-emerald-800">📊 Tableau de bord</a>
                <a href="/societaires" class="block px-3 py-2 rounded-lg hover:bg-emerald-800">👥 Sociétaires</a>
                <a href="/epargne" class="block px-3 py-2 rounded-lg hover:bg-emerald-800">💰 Épargne</a>
                <a href="/credits" class="block px-3 py-2 rounded-lg hover:bg-emerald-800">🏦 Crédits</a>
                <a href="/remboursements" class="block px-3 py-2 rounded-lg hover:bg-emerald-800">🔁 Remboursements</a>
                <a href="/rapports" class="block px-3 py-2 rounded-lg hover:bg-emerald-800">📄 Rapports</a>
            </nav>
            <div class="px-4 py-4 border-t border-emerald-800 text-sm">
                <p class="opacity-80"><?= e($_SESSION['user_name'] ?? '') ?></p>
                <a href="/logout" class="text-emerald-300 hover:text-white text-xs">Déconnexion</a>
            </div>
        </aside>
        <div class="flex-1 flex flex-col">
            <header class="bg-white border-b px-6 py-4">
                <h2 class="font-semibold text-gray-700"><?= $pageTitle ?? '' ?></h2>
            </header>
            <main class="flex-1 p-6">
                <?php if ($success = flash('success')): ?>
                    <div class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg px-4 py-2">
                        <?= e($success) ?>
                    </div>
                <?php endif; ?>
                <?= $content ?>
            </main>
        </div>
    </div>
</body>
</html>