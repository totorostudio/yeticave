<main>
    <? include 'menu.tpl.php' ?>;
    <section class="lot-item container">
        <h2><?= $lot["title"]; ?></h2>
        <div class="lot-item__content">
            <div class="lot-item__left">
                <div class="lot-item__image">                    
                    <img src="../uploads/<?= $lot["img"]; ?>" width="730" height="548" alt="Сноуборд">
                </div>
                <p class="lot-item__category">Категория: <span><?= $lot["name_category"]; ?></span></p>
                <p class="lot-item__description"><?= $lot["lot_description"]; ?></p>
            </div>
            <div class="lot-item__right">
                <div class="lot-item__state">
                    <?php $res = get_time_left($lot["date_finish"]) ?>
                    <div class="lot-item__timer timer <?php if ($res[0] < 1): ?>timer--finishing<?php endif; ?>">
                        <?= $res; ?>
                    </div>
                    <div class="lot-item__cost-state">
                        <div class="lot-item__rate">
                            <span class="lot-item__amount">Текущая цена</span>
                            <span class="lot-item__cost"><?= format_price($current_price); ?></span>
                        </div>
                        <div class="lot-item__min-cost">
                            Мин. ставка <span><?= format_price($min_bet); ?></span>
                        </div>
                    </div>
                    <form class="lot-item__form" action="lot.php?id=<?= $id;?>" method="post" autocomplete="off">
                        <p class="lot-item__form-item form__item form__item--invalid">
                            <?php if (isExpired($lot["date_finish"])): ?>
                                <label for="cost">Аукцион завершен</label>
                            <?php elseif ($is_auth): ?>
                                <label for="cost">Ваша ставка</label>
                            <?php else: ?>
                                <label for="cost">Войдите, чтобы сделать ставку</label>
                            <?php endif; ?>
                            <input id="cost" type="text" name="cost" <?php if (!$is_auth || !isset($_SESSION["id"]) || $lot["user_id"] === $_SESSION["id"] || isExpired($lot["date_finish"])): ?>disabled<?php endif; ?>>
                            <span class="form__error"><?= $error; ?></span>
                        </p>       
                        <button type="submit" class="button" <?php if (!$is_auth || $lot["user_id"] === $_SESSION["id"] || isExpired($lot["date_finish"])): ?>disabled<?php endif; ?>>Сделать&nbsp;ставку</button>
                    </form>
                </div>
                <?php if (!empty($history)): ?>
                <div class="history">
                    <h3>История ставок (<span><?= count($history) ?></span>)</h3>
                    <table class="history__list">
                        <?php foreach($history as $bet): ?>
                        <tr class="history__item">
                            <td class="history__name"><?= $bet["user_name"]; ?></td>
                            <td class="history__price"><?= format_price($bet["price_bet"]); ?></td>
                            <td class="history__time"><?= $bet["date_bet"]; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                    
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>