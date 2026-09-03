<div class="overflow-x-auto bg-white rounded-xl border shadow-sm">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 border-b text-left text-gray-500 sticky top-0">
            <tr>
                <th class="px-4 py-3 font-medium">Code</th>
                <th class="px-4 py-3 font-medium">Nom</th>
                <th class="px-4 py-3 font-medium">CIN</th>
                <th class="px-4 py-3 font-medium">Téléphone</th>
                <th class="px-4 py-3 font-medium">Adhésion</th>
                <th class="px-4 py-3 font-medium text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            <?php if (empty($societaires)): ?>
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">Aucun sociétaire trouvé.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($societaires as $s): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs text-gray-500"><?= e($s['code_societaire']) ?></td>
                        <td class="px-4 py-3">
                            <a href="/societaires/<?= (int) $s['id'] ?>" class="font-medium text-emerald-700 hover:underline">
                                <?= e($s['nom']) ?> <?= e($s['prenom'] ?? '') ?>
                            </a>
                        </td>
                        <td class="px-4 py-3"><?= e($s['cin'] ?? '—') ?></td>
                        <td class="px-4 py-3"><?= e($s['telephone'] ?? '—') ?></td>
                        <td class="px-4 py-3"><?= e($s['date_adhesion'] ?? '—') ?></td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="/societaires/<?= (int) $s['id'] ?>" class="text-emerald-700 hover:underline">Voir</a>
                            <a href="/societaires/<?= (int) $s['id'] ?>/edit" class="text-gray-500 hover:underline">Modifier</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($pagination['total_pages'] > 1): ?>
    <div class="flex items-center justify-between mt-4 text-sm text-gray-500">
        <p><?= $pagination['total'] ?> sociétaire(s)</p>
        <div class="flex gap-1">
            <?php for ($p = 1; $p <= $pagination['total_pages']; $p++): ?>
                <a href="/societaires?q=<?= urlencode($search) ?>&page=<?= $p ?>"
                   class="px-3 py-1 rounded-lg border <?= $p === $pagination['current_page'] ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white hover:bg-gray-50' ?>">
                    <?= $p ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>
<?php endif; ?>