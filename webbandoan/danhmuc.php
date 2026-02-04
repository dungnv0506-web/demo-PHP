<?php
require 'config/db.php';
$cats = $conn->query("SELECT * FROM categories");
?>

<div class="category-box">
    <a href="thucdon.php">🍽 Tất cả</a>
    <?php while($c = $cats->fetch_assoc()): ?>
        <a href="thucdon.php?cat=<?= $c['id'] ?>">
            <?= $c['name'] ?>
        </a>
    <?php endwhile; ?>
</div>
