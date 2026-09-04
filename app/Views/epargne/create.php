<div class="max-w-lg bg-white rounded-xl border shadow-sm p-6">
    <p class="text-sm text-gray-500 mb-4">
        Sociétaire : <span class="font-medium text-gray-700"><?= e($societaire['nom']) ?> <?= e($societaire['prenom'] ?? '') ?></span>
    </p>

    <form method="POST" action="/societaires/<?= (int) $societaire['id'] ?>/comptes" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Type de compte *</label>
            <select name="type_compte" id="type_compte"
                    class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none <?= isset($errors['type_compte']) ? 'border-red-400' : 'border-gray-300' ?>">
                <option value="">-- Choisir --</option>
                <option value="DAV" <?= ($old['type_compte'] ?? '') === 'DAV' ? 'selected' : '' ?>>DAV — Dépôt à vue (retrait libre)</option>
                <option value="DAT" <?= ($old['type_compte'] ?? '') === 'DAT' ? 'selected' : '' ?>>DAT — Dépôt à terme (bloqué jusqu'à échéance)</option>
                <option value="PLE" <?= ($old['type_compte'] ?? '') === 'PLE' ? 'selected' : '' ?>>PLE — Plan d'épargne (programmée)</option>
            </select>
            <?php if (isset($errors['type_compte'])): ?><p class="text-xs text-red-600 mt-1"><?= e($errors['type_compte']) ?></p><?php endif; ?>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Taux d'intérêt annuel (%)</label>
            <input type="number" step="0.01" min="0" name="taux_interet" value="<?= e($old['taux_interet'] ?? '0') ?>"
                   class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none <?= isset($errors['taux_interet']) ? 'border-red-400' : 'border-gray-300' ?>">
            <?php if (isset($errors['taux_interet'])): ?><p class="text-xs text-red-600 mt-1"><?= e($errors['taux_interet']) ?></p><?php endif; ?>
            <p class="text-xs text-gray-400 mt-1">Taux non fixé par le référentiel CECAM fourni — à confirmer avec le maître de stage.</p>
        </div>

        <div id="date_echeance_wrapper" class="<?= ($old['type_compte'] ?? '') === 'DAT' ? '' : 'hidden' ?>">
            <label class="block text-sm font-medium text-gray-700 mb-1">Date d'échéance *</label>
            <input type="date" name="date_echeance" value="<?= e($old['date_echeance'] ?? '') ?>"
                   class="w-full px-4 py-2.5 border rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none <?= isset($errors['date_echeance']) ? 'border-red-400' : 'border-gray-300' ?>">
            <?php if (isset($errors['date_echeance'])): ?><p class="text-xs text-red-600 mt-1"><?= e($errors['date_echeance']) ?></p><?php endif; ?>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="/societaires/<?= (int) $societaire['id'] ?>" class="px-4 py-2.5 rounded-lg border text-gray-600 hover:bg-gray-50">Annuler</a>
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-6 py-2.5 rounded-lg transition">
                Créer le compte
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('type_compte').addEventListener('change', function () {
    document.getElementById('date_echeance_wrapper').classList.toggle('hidden', this.value !== 'DAT');
});
</script>