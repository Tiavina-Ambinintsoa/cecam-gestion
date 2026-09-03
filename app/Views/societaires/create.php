<div class="max-w-2xl bg-white rounded-xl border shadow-sm p-6">
    <form method="POST" action="/societaires" enctype="multipart/form-data" class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                <input type="text" name="nom" value="<?= e($old['nom'] ?? '') ?>"
                       class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none <?= isset($errors['nom']) ? 'border-red-400' : 'border-gray-300' ?>">
                <?php if (isset($errors['nom'])): ?><p class="text-xs text-red-600 mt-1"><?= e($errors['nom']) ?></p><?php endif; ?>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                <input type="text" name="prenom" value="<?= e($old['prenom'] ?? '') ?>"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">CIN</label>
                <input type="text" name="cin" value="<?= e($old['cin'] ?? '') ?>"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                <input type="text" name="telephone" value="<?= e($old['telephone'] ?? '') ?>"
                       class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none <?= isset($errors['telephone']) ? 'border-red-400' : 'border-gray-300' ?>">
                <?php if (isset($errors['telephone'])): ?><p class="text-xs text-red-600 mt-1"><?= e($errors['telephone']) ?></p><?php endif; ?>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Adresse</label>
            <input type="text" name="adresse" value="<?= e($old['adresse'] ?? '') ?>"
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date de naissance</label>
            <input type="date" name="date_naissance" value="<?= e($old['date_naissance'] ?? '') ?>"
                   class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none <?= isset($errors['date_naissance']) ? 'border-red-400' : 'border-gray-300' ?>">
            <?php if (isset($errors['date_naissance'])): ?><p class="text-xs text-red-600 mt-1"><?= e($errors['date_naissance']) ?></p><?php endif; ?>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Photo (jpg/png, 2 Mo max)</label>
                <input type="file" name="photo" accept=".jpg,.jpeg,.png" class="w-full text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pièce d'identité (jpg/png/pdf, 5 Mo max)</label>
                <input type="file" name="piece_identite" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-sm">
            </div>
        </div>
        <?php if (isset($errors['fichier'])): ?><p class="text-xs text-red-600"><?= e($errors['fichier']) ?></p><?php endif; ?>

        <div class="flex justify-end gap-3 pt-2">
            <a href="/societaires" class="px-4 py-2.5 rounded-lg border text-gray-600 hover:bg-gray-50">Annuler</a>
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-6 py-2.5 rounded-lg transition">
                Enregistrer
            </button>
        </div>
    </form>
</div>