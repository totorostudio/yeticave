<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $category): ?>
            <li class="nav__item">
                <a href="/"><?= $category["name_category"]; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
