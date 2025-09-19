<?php
use app\models\Category;
use app\models\User;
use yii\helpers\Html;


$this->title = "Taskforce";

if (!empty($user)) {
    $categoriesId = explode(", ", $user->specializations);
}
?>

<main class="main-content container">
    <div class="left-column">
        <?php if (!empty($user)): ?>
        <h3 class="head-main"><?=Html::encode($user->name)?></h3>
        <div class="user-card">
            <div class="photo-rate">
                <img class="card-photo" src="<?=$user->avatar?>" width="191" height="190" alt="Фото пользователя">
                <div class="card-rate">
                    <div class="stars-rating big">
                        <?= User::getUSerStars($user->getUserRating) ?>
                    </div>
                    <span class="current-rate"><?=$user->getUserRating?></span>
                </div>
            </div>
            <p class="user-description">
                <?=$user->info?>
            </p>
        </div>
        <div class="specialization-bio">
            <div class="specialization">
                <p class="head-info">Специализации</p>
                <ul class="special-list">
                    <?php foreach($categoriesId as $categoryId): ?>
                        <li class="special-item">
                            <a href="#" class="link link--regular">
                                <?=Category::getCategoryName($categoryId)?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="bio">
                <p class="head-info">Био</p>
                <p class="bio-info">
                    <span class="country-info">Россия</span>, <!-- к этому, видимо, надо будет вернуться позже, так как страна явно в БД не хранится -->
                    <span class="town-info"><?=$user->city->name?></span>,
                    <span class="age-info">
                        <?= trim(Yii::$app->formatter->format(
                            $user->birthday, 'relativeTime'
                        ), "назад") ?>
                    </span></p>
            </div>
        </div>
        <?php if ($user->getExecutorReviews): ?>
            <h4 class="head-regular">Отзывы заказчиков</h4>
            <?php foreach($user->getExecutorReviews as $review): ?>
                <div class="response-card">
                    <img class="customer-photo" src="<?=$review->customer->avatar?>" width="120" height="127" alt="<?=Html::encode($review->customer->name)?>">
                    <div class="feedback-wrapper">
                        <p class="feedback">
                            <?=Html::encode($review->comment)?>
                        </p>
                        <p class="task">Задание «<a href="/tasks/view/<?=$review->task->id?>" class="link link--small"><?=Html::encode($review->task->title)?></a>» выполнено</p>
                    </div>
                    <div class="feedback-wrapper">
                        <div class="stars-rating small">
                            <?= User::getUserStars($review->rating) ?>
                        </div>
                        <p class="info-text"><span class="current-time">
                        <?= Yii::$app->formatter->format(
                            $review->created_at, 'relativeTime'
                        ) ?>
                        </span></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="right-column">
        <div class="right-card black">
            <h4 class="head-card">Статистика исполнителя</h4>
            <dl class="black-list">
                <dt>Всего заказов</dt>
                <dd><?=$user->succesful_tasks?> выполнено, <?=$user->failed_tasks?> провалено</dd>
                <dt>Место в рейтинге</dt>
                <dd>25 место</dd>
                <dt>Дата регистрации</dt>
                <dd>
                    <?= Yii::$app->formatter->asDate(
                        $user->created_at, 'php:d F, H:i'
                    ) ?>
                </dd>
                <dt>Статус</dt>
                <dd><?= $user->getUserStatus; ?></dd>
            </dl>
        </div>
        <div class="right-card white">
            <h4 class="head-card">Контакты</h4>
            <ul class="enumeration-list">
                <li class="enumeration-item">
                    <a href="#" class="link link--block link--phone"><?=Html::encode($user->phone)?></a>
                </li>
                <li class="enumeration-item">
                    <a href="#" class="link link--block link--email"><?=Html::encode($user->email)?></a>
                </li>
                <li class="enumeration-item">
                    <a href="#" class="link link--block link--tg"><?=Html::encode($user->telegram)?></a>
                </li>
            </ul>
        </div>
    </div>
    <?php endif; ?>
</main>