<?php
$keyword = $_GET['search'] ?? '';
?>

<div class="search-box">
    <form method="get">
        <input
            type="text"
            name="search"
            placeholder="🔍 Tìm món ăn..."
            value="<?= htmlspecialchars($keyword) ?>"
        >
        <button type="submit">Tìm</button>
    </form>
</div>
