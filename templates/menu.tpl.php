<nav class="nav">
  <ul class="nav__list container">
    <?php if (isset($categories)): ?>
      <?php foreach ($categories as $category): ?>
        <?php if (!isset($name_category) || $category['name_category'] != $name_category): ?>
          <li class="nav__item">
            <a href="../category.php?id=<?= $category['id'] ?>"><?= $category['name_category'] ?></a>
          </li>
        <?php else: ?>
          <li class="nav__item nav__item--current">
            <a href="../category.php?id=<?= $category['id'] ?>"><?= $category['name_category'] ?></a>
          </li>
        <?php endif; ?>
      <?php endforeach; ?>
    <?php endif; ?>
  </ul>
</nav>
