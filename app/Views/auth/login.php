<div class="bg-white rounded-2xl shadow-xl p-8">
    <div class="text-center mb-8">
        <div class="w-14 h-14 bg-emerald-600 rounded-xl mx-auto mb-3 flex items-center justify-center text-white font-bold text-xl">CG</div>
        <h1 class="text-xl font-semibold text-gray-800">Gestion des Sociétaires</h1>
        <p class="text-sm text-gray-500">Connectez-vous à votre espace</p>
    </div>

    <?php if ($error = flash('error')): ?>
        <div class="mb-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg px-4 py-2">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/login" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" required
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe</label>
            <input type="password" name="mot_de_passe" required
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
        </div>
        <button type="submit"
                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2.5 rounded-lg transition">
            Se connecter
        </button>
    </form>
</div>