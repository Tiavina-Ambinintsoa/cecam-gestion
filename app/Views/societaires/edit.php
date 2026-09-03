<div class="max-w-2xl bg-white rounded-xl border shadow-sm p-6">
    <form method="POST" action="/societaires/<?= (int) $societaire['id'] ?>/edit" enctype="multipart/form-data" class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                <input type="text" name="nom" value="<?= e($societaire['nom'] ?? '') ?>"
                       class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none <?= isset($errors['nom']) ? 'border-red-400' : 'border-gray-300' ?>">
                <?php if (isset($errors['nom'])): ?><p class="text-xs text-red-600 mt-1"><?= e($errors['nom']) ?></p><?php endif; ?>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                <input type="text" name="prenom" value="<?= e($societaire['prenom'] ?? '') ?>"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">CIN</label>
                <input type="text" name="cin" value="<?= e($societaire['cin'] ?? '') ?>"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                <input type="text" name="telephone" value="<?= e($societaire['telephone'] ?? '') ?>"
                       class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none <?= isset($errors['telephone']) ? 'border-red-400' : 'border-gray-300' ?>">
                <?php if (isset($errors['telephone'])): ?><p class="text-xs text-red-600 mt-1"><?= e($errors['telephone']) ?></p><?php endif; ?>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
            <input type="text" name="adresse" value="<?= e($societaire['adresse'] ?? '') ?>"
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date de naissance</label>
            <input type="date" name="date_naissance" value="<?= e($societaire['date_naissance'] ?? '') ?>"
                   class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none <?= isset($errors['date_naissance']) ? 'border-red-400' : 'border-gray-300' ?>">
            <?php if (isset($errors['date_naissance'])): ?><p class="text-xs text-red-600 mt-1"><?= e($errors['date_naissance']) ?></p><?php endif; ?>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Photo (jpg/png, 2 Mo max)</label>
                <?php if (!empty($societaire['photo'])): ?>
                    <img src="/<?= e($societaire['photo']) ?>" class="w-16 h-16 object-cover rounded-lg mb-2 border">
                <?php endif; ?>
                <input type="file" name="photo" accept=".jpg,.jpeg,.png" class="w-full text-sm">
                <p class="text-xs text-gray-400 mt-1">Laisser vide pour conserver le fichier actuel.</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pièce d'identité (jpg/png/pdf, 5 Mo max)</label>
                <?php if (!empty($societaire['piece_identite'])): ?>
                    <a href="/<?= e($societaire['piece_identite']) ?>" target="_blank" class="text-xs text-emerald-700 hover:underline block mb-2">Voir le fichier actuel</a>
                <?php endif; ?>
                <input type="file" name="piece_identite" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-sm">
                <p class="text-xs text-gray-400 mt-1">Laisser vide pour conserver le fichier actuel.</p>
            </div>
        </div>
        <?php if (isset($errors['fichier'])): ?><p class="text-xs text-red-600"><?= e($errors['fichier']) ?></p><?php endif; ?>

        <div class="flex justify-end gap-3 pt-2">
            <a href="/societaires/<?= (int) $societaire['id'] ?>" class="px-4 py-2.5 rounded-lg border text-gray-600 hover:bg-gray-50">Annuler</a>
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-6 py-2.5 rounded-lg transition">
                Mettre à jour
            </button>
        </div>
    </form>
</div>