<div class="mb-6 flex items-start justify-between">
    <div>
        <p class="text-xs font-mono text-gray-400"><?= e($societaire['code_societaire']) ?></p>
        <h1 class="text-2xl font-bold text-gray-800"><?= e($societaire['nom']) ?> <?= e($societaire['prenom'] ?? '') ?></h1>
        <p class="text-sm text-gray-500">Adhérent depuis le <?= e($societaire['date_adhesion'] ?? '—') ?></p>
    </div>
    <div class="flex gap-2">
        <a href="/societaires/<?= (int) $societaire['id'] ?>/edit"
           class="px-4 py-2 rounded-lg border text-gray-600 hover:bg-gray-50 text-sm">Modifier</a>
        <?php if (currentUserRole() === 'admin'): ?>
            <form method="POST" action="/societaires/<?= (int) $societaire['id'] ?>/supprimer"
                  onsubmit="return confirm('Supprimer définitivement ce sociétaire ?');">
                <button type="submit" class="px-4 py-2 rounded-lg border border-red-200 text-red-600 hover:bg-red-50 text-sm">
                    Supprimer
                </button>
            </form>
        <?php endif; ?>
    </div>
</div>

<div class="border-b mb-6">
    <nav class="flex gap-6 text-sm">
        <button type="button" data-tab="infos" class="tab-btn px-1 py-3 border-b-2 border-emerald-600 text-emerald-700 font-medium">Informations</button>
        <button type="button" data-tab="epargne" class="tab-btn px-1 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700">Épargne</button>
        <button type="button" data-tab="credits" class="tab-btn px-1 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700">Crédits</button>
    </nav>
</div>

<div id="tab-infos" class="tab-panel grid grid-cols-2 gap-6 bg-white rounded-xl border shadow-sm p-6">
    <div><p class="text-sm text-gray-500">CIN</p><p class="font-medium"><?= e($societaire['cin'] ?? '—') ?></p></div>
    <div><p class="text-sm text-gray-500">Téléphone</p><p class="font-medium"><?= e($societaire['telephone'] ?? '—') ?></p></div>
    <div><p class="text-sm text-gray-500">Adresse</p><p class="font-medium"><?= e($societaire['adresse'] ?? '—') ?></p></div>
    <div><p class="text-sm text-gray-500">Date de naissance</p><p class="font-medium"><?= e($societaire['date_naissance'] ?? '—') ?></p></div>
    <?php if (!empty($societaire['photo'])): ?>
        <div><p class="text-sm text-gray-500 mb-1">Photo</p><img src="/<?= e($societaire['photo']) ?>" class="w-20 h-20 object-cover rounded-lg border"></div>
    <?php endif; ?>
    <?php if (!empty($societaire['piece_identite'])): ?>
        <div><p class="text-sm text-gray-500 mb-1">Pièce d'identité</p><a href="/<?= e($societaire['piece_identite']) ?>" target="_blank" class="text-emerald-700 hover:underline text-sm">Voir le fichier</a></div>
    <?php endif; ?>
</div>

<div id="tab-epargne" class="tab-panel hidden bg-white rounded-xl border shadow-sm p-6 text-sm text-gray-400">
    Module Épargne à venir (Prompt 4) — les comptes et mouvements de ce sociétaire s'afficheront ici.
</div>

<div id="tab-credits" class="tab-panel hidden bg-white rounded-xl border shadow-sm p-6 text-sm text-gray-400">
    Module Crédit à venir (Prompt 5).
</div>

<script>
document.querySelectorAll('.tab-btn').forEach((btn) => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach((b) => {
            b.classList.remove('border-emerald-600', 'text-emerald-700', 'font-medium');
            b.classList.add('border-transparent', 'text-gray-500');
        });
        btn.classList.add('border-emerald-600', 'text-emerald-700', 'font-medium');
        btn.classList.remove('border-transparent', 'text-gray-500');
        document.querySelectorAll('.tab-panel').forEach((p) => p.classList.add('hidden'));
        document.getElementById('tab-' + btn.dataset.tab).classList.remove('hidden');
    });
});
</script>