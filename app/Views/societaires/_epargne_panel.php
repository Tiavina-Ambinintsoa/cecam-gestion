<div class="flex items-center justify-between mb-4">
    <h3 class="font-medium text-gray-700">Comptes d'épargne</h3>
    <a href="/societaires/<?= (int) $societaire['id'] ?>/comptes/create"
       class="text-sm bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-3 py-2 rounded-lg transition">
        + Nouveau compte
    </a>
</div>

<?php if (empty($comptes)): ?>
    <p class="text-sm text-gray-400">Aucun compte d'épargne pour ce sociétaire.</p>
<?php else: ?>
    <div class="space-y-3">
        <?php foreach ($comptes as $c): ?>
            <a href="/comptes/<?= (int) $c['id'] ?>" class="block border rounded-lg p-4 hover:bg-gray-50">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                            <?= $c['type_compte'] === 'DAV' ? 'bg-blue-50 text-blue-700' : ($c['type_compte'] === 'DAT' ? 'bg-amber-50 text-amber-700' : 'bg-purple-50 text-purple-700') ?>">
                            <?= e($c['type_compte']) ?>
                        </span>
                        <span class="ml-2 font-mono text-xs text-gray-500"><?= e($c['numero_compte']) ?></span>
                    </div>
                    <p class="font-semibold text-emerald-700"><?= number_format((float) $c['solde'], 0, ',', ' ') ?> Ar</p>
                </div>
                <?php if ($c['type_compte'] === 'DAT' && !empty($c['date_echeance'])): ?>
                    <p class="text-xs text-gray-400 mt-1">Échéance : <?= e($c['date_echeance']) ?></p>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>