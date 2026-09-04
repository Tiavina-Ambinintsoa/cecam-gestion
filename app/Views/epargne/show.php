<div class="mb-6 flex items-start justify-between">
    <div>
        <p class="text-xs font-mono text-gray-400"><?= e($compte['numero_compte']) ?></p>
        <h1 class="text-2xl font-bold text-gray-800">
            <?= e($compte['nom']) ?> <?= e($compte['prenom'] ?? '') ?>
            <span class="ml-2 text-sm font-normal text-gray-400">(<?= e($compte['code_societaire']) ?>)</span>
        </h1>
        <span class="inline-block mt-1 text-xs font-semibold px-2 py-0.5 rounded-full
            <?= $compte['type_compte'] === 'DAV' ? 'bg-blue-50 text-blue-700' : ($compte['type_compte'] === 'DAT' ? 'bg-amber-50 text-amber-700' : 'bg-purple-50 text-purple-700') ?>">
            <?= e($compte['type_compte']) ?>
        </span>
        <?php if ($compte['type_compte'] === 'DAT' && !empty($compte['date_echeance'])): ?>
            <span class="ml-2 text-xs text-gray-400">Échéance : <?= e($compte['date_echeance']) ?></span>
        <?php endif; ?>
    </div>
    <div class="text-right">
        <p class="text-sm text-gray-500">Solde actuel</p>
        <p class="text-3xl font-bold text-emerald-700"><?= number_format((float) $compte['solde'], 0, ',', ' ') ?> Ar</p>
    </div>
</div>

<?php if ($error = flash('error')): ?>
    <div class="mb-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-lg px-4 py-2">
        <?= e($error) ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-2 gap-4 mb-6">
    <form method="POST" action="/comptes/<?= (int) $compte['id'] ?>/depot" class="bg-white rounded-xl border shadow-sm p-5">
        <p class="text-sm font-medium text-gray-700 mb-2">Dépôt</p>
        <div class="flex gap-2">
            <input type="number" step="0.01" min="0.01" name="montant" required placeholder="Montant"
                   class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-4 py-2.5 rounded-lg transition">
                Déposer
            </button>
        </div>
    </form>

    <form method="POST" action="/comptes/<?= (int) $compte['id'] ?>/retrait" class="bg-white rounded-xl border shadow-sm p-5"
          onsubmit="return confirm('Confirmer ce retrait ?');">
        <p class="text-sm font-medium text-gray-700 mb-2">Retrait</p>
        <div class="flex gap-2">
            <input type="number" step="0.01" min="0.01" name="montant" required placeholder="Montant"
                   class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
            <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white font-medium px-4 py-2.5 rounded-lg transition">
                Retirer
            </button>
        </div>
    </form>
</div>

<div class="bg-white rounded-xl border shadow-sm overflow-hidden">
    <div class="px-5 py-3 border-b">
        <p class="text-sm font-medium text-gray-700">Historique des mouvements</p>
    </div>
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 border-b text-left text-gray-500">
            <tr>
                <th class="px-4 py-3 font-medium">Date</th>
                <th class="px-4 py-3 font-medium">Type</th>
                <th class="px-4 py-3 font-medium text-right">Montant</th>
                <th class="px-4 py-3 font-medium text-right">Solde après</th>
                <th class="px-4 py-3 font-medium">Agent</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            <?php if (empty($mouvements)): ?>
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Aucun mouvement enregistré.</td></tr>
            <?php else: ?>
                <?php foreach ($mouvements as $m): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3"><?= e($m['date_mouvement']) ?></td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full <?= $m['type_mouvement'] === 'depot' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' ?>">
                                <?= $m['type_mouvement'] === 'depot' ? 'Dépôt' : ($m['type_mouvement'] === 'retrait' ? 'Retrait' : 'Intérêt') ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-medium"><?= number_format((float) $m['montant'], 0, ',', ' ') ?> Ar</td>
                        <td class="px-4 py-3 text-right text-gray-500"><?= number_format((float) $m['solde_apres'], 0, ',', ' ') ?> Ar</td>
                        <td class="px-4 py-3 text-gray-500"><?= e($m['agent_nom'] ?? '—') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($pagination['total_pages'] > 1): ?>
    <div class="flex items-center justify-between mt-4 text-sm text-gray-500">
        <p><?= $pagination['total'] ?> mouvement(s)</p>
        <div class="flex gap-1">
            <?php for ($p = 1; $p <= $pagination['total_pages']; $p++): ?>
                <a href="/comptes/<?= (int) $compte['id'] ?>?page=<?= $p ?>"
                   class="px-3 py-1 rounded-lg border <?= $p === $pagination['current_page'] ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white hover:bg-gray-50' ?>">
                    <?= $p ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>
<?php endif; ?>