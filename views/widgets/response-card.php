<?php

use app\models\forms\UserView;
use app\models\widgets\RatingWidget;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $user app\models\User */
/* @var $task app\models\Task */
/* @var $response app\models\Response */


?>

<div class="response-card">
    <?php if (!empty($response->executor->avatar)): ?>
        <img class="customer-photo" src="<?= Html::encode($response->executor->avatar) ?>" width="146" height="156" alt="Фото исполнителя">
    <?php endif; ?>

    <div class="feedback-wrapper">
        <a href="<?= Url::toRoute(['/users/view/', 'id' => $response->executor->id]) ?>" class="link link--block link--big">
            <?= Html::encode($response->executor->name) ?>
        </a>
        <div class="response-wrapper">
            <div class="stars-rating small">
                <?= RatingWidget::widget(['rating' => $response->executor->getUserRating()]) ?>
            </div>
            <p class="reviews">
                <?= Yii::t('app', '{n, plural, =0{# отзывов} one{# отзыв} =2{# отзыва} =3{# отзыва} =4{# отзыва} few{# отзыва} many{# отзывов} other{# отзывов}}', ['n' => $response->executor->getExecutorReviews()->count()]); ?>
            </p>
        </div>
        <p class="response-message">
            <?= Html::encode($response->comment) ?>
        </p>
    </div>

    <div class="feedback-wrapper">
        <p class="info-text">
            <span class="current-time">
                <?= Yii::$app->formatter->format($response->created_at, 'relativeTime') ?>
            </span>
        </p>
        <p class="price price--small">
            <?= !empty($response->price) ? Html::encode(Yii::$app->formatter->asCurrency($response->price)) : '' ?>&nbsp;₽
        </p>
    </div>

    <?php if (UserView::isViewResponseButtons($user->id, $task->customer_id, $task->status, $response->status)): ?>
        <div class="button-popup">
            <a href="<?= Url::toRoute(['/tasks/accept', 'responseId' => $response->id, 'taskId' => $task->id, 'executorId' => $response->executor->id]) ?>" class="button button--blue button--small">Принять</a>
            <a href="<?= Url::toRoute(['/tasks/refuse', 'responseId' => $response->id]) ?>" class="button button--orange button--small">Отказать</a>
        </div>
    <?php endif; ?>
</div>