<?php

use app\models\Category;
use app\models\forms\UserView;
use app\models\User;
use app\models\widgets\RatingWidget;
use yii\helpers\Html;
use yii\helpers\Url;


$this->title = "Taskforce";

if (!empty($user)) {
    $categoriesId = $user->specializations ? explode(", ", $user->specializations) : '';
}
?>

<main class="main-content container">
    <div class="left-column">
        <h3 class="head-main"><?= Html::encode($user->name) ?></h3>
        <div class="user-card">
            <div class="photo-rate">
                <?= Html::img($user->avatar, ['class' => 'card-photo', 'width' => 191, 'height' => 190, 'alt' => 'Фото пользователя']) ?>
                <div class="card-rate">
                    <div class="stars-rating big">
                        <?= RatingWidget::widget(['rating' => $user->getUserRating()]) ?>
                    </div>
                    <span class="current-rate"><?= $user->getUserRating() ?></span>
                </div>
            </div>
            <p class="user-description">
                <?= $user->info ?>
            </p>
        </div>
        <div class="specialization-bio">
            <div class="specialization">
                <p class="head-info">Специализации</p>
                <ul class="special-list">
                    <?php foreach ($categoriesId as $categoryId): ?>
                        <li class="special-item">
                            <a href="<?= Url::to(['tasks/index', 'category' => $categoryId]) ?>"
                               class="link link--regular">
                                <?= Category::getCategoryName($categoryId) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="bio">
                <p class="head-info">Био</p>
                <p class="bio-info">
                    <span class="country-info">Россия</span>,
                    <span class="town-info"><?= $user->city->name ?></span>,
                    <span class="age-info">
                        <?= trim(Yii::$app->formatter->format(
                            $user->birthday, 'relativeTime'
                        ), "назад") ?>
                    </span></p>
            </div>
        </div>
        <?php if ($user->getExecutorReviews()->count() > 0): ?>
            <h4 class="head-regular">Отзывы заказчиков</h4>
            <?php foreach ($user->getExecutorReviews()->all() as $review): ?>
                <div class="response-card">
                    <img class="customer-photo" src="<?= Html::encode($review->customer->avatar) ?>" width="120"
                         height="127"
                         alt="<?= Html::encode($review->customer->name) ?>">
                    <div class="feedback-wrapper">
                        <p class="feedback">
                            <?= Html::encode($review->comment) ?>
                        </p>
                        <p class="task">Задание «<a
                                    href="<?= Url::toRoute(['/tasks/view/', 'id' => $review->task->id]) ?>"
                                    class="link link--small"><?= Html::encode($review->task->title) ?></a>»
                            выполнено</p>
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
        <?php if ($user->role !== User::ROLE_CUSTOMER): ?>
            <div class="right-card black">
                <h4 class="head-card">Статистика исполнителя</h4>
                <dl class="black-list">
                    <dt>Всего заказов</dt>
                    <dd>
                        <?= $user->successful_tasks ? $user->successful_tasks : '0' ?> выполнено,
                        <?= $user->failed_tasks ? $user->failed_tasks : '0' ?> провалено
                    </dd>
                    <dt>Место в рейтинге</dt>
                    <dd><?= $user->userRank ?> место</dd>
                    <dt>Дата регистрации</dt>
                    <dd>
                        <?= Yii::$app->formatter->asDate(
                            $user->created_at, 'php:d F, H:i'
                        ) ?>
                    </dd>
                    <dt>Статус</dt>
                    <dd><?= $user->getUserStatus(); ?></dd>
                </dl>
            </div>
        <?php endif; ?>
        <?php if (UserView::isViewContacts($user->id, $user->hidden_contacts)): ?>
            <div class="right-card white">
                <h4 class="head-card">Контакты</h4>
                <ul class="enumeration-list">
                    <?php if ($user->phone): ?>
                        <li class="enumeration-item">
                            <a href="tel:<?= Html::encode($user->phone) ?>"
                               class="link link--block link--phone"><?= Html::encode($user->phone) ?></a>
                        </li>
                    <?php endif; ?>
                    <?php if ($user->email): ?>
                        <li class="enumeration-item">
                            <a href="mailto:<?= Html::encode($user->email) ?>"
                               class="link link--block link--email"><?= Html::encode($user->email) ?></a>
                        </li>
                    <?php endif; ?>
                    <?php if ($user->telegram): ?>
                        <li class="enumeration-item">
                            <a href="https://t.me/<?= str_replace('@', '', Html::encode($user->telegram)) ?>"
                               class="link link--block link--tg"><?= Html::encode($user->telegram) ?></a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</main>