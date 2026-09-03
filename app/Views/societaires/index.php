<div class="flex items-center justify-between mb-4">
    <input id="search-input" type="text" value="<?= e($search) ?>" placeholder="Rechercher par nom, CIN, téléphone..."
           class="w-full max-w-sm px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none">
    <a href="/societaires/create"
       class="bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-4 py-2.5 rounded-lg transition">
        + Nouveau sociétaire
    </a>
</div>

<div id="results">
    <?php include BASE_PATH . '/app/Views/societaires/_table.php'; ?>
</div>

<script>
(function () {
    const input = document.getElementById('search-input');
    const results = document.getElementById('results');
    let timer = null;

    input.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => {
            const q = encodeURIComponent(input.value);
            fetch('/societaires?q=' + q, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then((res) => res.text())
                .then((html) => {
                    results.innerHTML = html;
                    history.replaceState(null, '', '/societaires?q=' + q);
                });
        }, 400);
    });
})();
</script>