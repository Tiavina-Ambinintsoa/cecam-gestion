<div class="overflow-x-auto bg-white rounded-xl border shadow-sm">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 border-b text-left text-gray-500 sticky top-0">
            <tr>
                <th class="px-4 py-3 font-medium">N° compte</th>
                <th class="px-4 py-3 font-medium">Type</th>
                <th class="px-4 py-3 font-medium">Sociétaire</th>
                <th class="px-4 py-3 font-medium text-right">Solde</th>
                <th class="px-4 py-3 font-medium">Statut</th>
                <th class="px-4 py-3 font-medium text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            <?php if (empty($comptes)): ?>
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Aucun compte trouvé.</td></tr>
            <?php else: ?>
                <?php foreach ($comptes as $c): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs text-gray-500"><?= e($c['numero_compte']) ?></td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                                <?= $c['type_compte'] === 'DAV' ? 'bg-blue-50 text-blue-700' : ($c['type_compte'] === 'DAT' ? 'bg-amber-50 text-amber-700' : 'bg-purple-50 text-purple-700') ?>">
                                <?= e($c['type_compte']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="/societaires/<?= (int) $c['societaire_id'] ?>" class="text-emerald-700 hover:underline">
                                <?= e($c['nom']) ?> <?= e($c['prenom'] ?? '') ?>
                            </a>
                        </td>
                        <td class="px-4 py-3 text-right font-medium"><?= number_format((float) $c['solde'], 0, ',', ' ') ?> Ar</td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full <?= $c['statut'] === 'actif' ? 'bg-emerald-50 text-emerald-700' : 'bg-gray-100 text-gray-500' ?>">
                                <?= e($c['statut']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="/comptes/<?= (int) $c['id'] ?>" class="text-emerald-700 hover:underline">Voir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($pagination['total_pages'] > 1): ?>
    <div class="flex items-center justify-between mt-4 text-sm text-gray-500">
        <p><?= $pagination['total'] ?> compte(s)</p>
        <div class="flex gap-1">
            <?php for ($p = 1; $p <= $pagination['total_pages']; $p++): ?>
                <a href="/epargne?q=<?= urlencode($search) ?>&page=<?= $p ?>"
                   class="px-3 py-1 rounded-lg border <?= $p === $pagination['current_page'] ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white hover:bg-gray-50' ?>">
                    <?= $p ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>
<?php endif; ?>