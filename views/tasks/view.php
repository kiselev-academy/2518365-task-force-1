<?php

use app\assets\MainAsset;
use app\models\forms\UserView;
use app\models\User;
use app\models\widgets\RatingWidget;
use TaskForce\Models\Task as TaskBasic;
use yii\helpers\Html;use yii\helpers\Url;
use yii\widgets\ActiveForm;

MainAsset::register($this);


$this->title = "Taskforce";

if (!Yii::$app->user->isGuest) {
    $user = User::getCurrentUser();
}

?>
<main class="main-content container">
<div class="left-column">
    <div class="head-wrapper">
        <?php if (!empty($task)): ?>
        <h3 class="head-main"><?= Html::encode($task->title) ?></h3>
        <p class="price price--big"><?= Html::encode(Yii::$app->formatter->asCurrency($task->budget)) ?? '' ?></p>
    </div>
    <p class="task-description">
        <?= Html::encode($task->description) ?>
    </p>
    <?php if (UserView::isViewResponseButton($user->id, $user->role, $task->status, $task->responses)): ?>
        <a href="#" class="button button--blue action-btn" data-action="act_response">Откликнуться на задание</a>
    <?php endif;?>

    <?php if (UserView::isViewRefusalButton($user->id, $task->status, $task->executor_id)): ?>
        <a href="#" class="button button--orange action-btn" data-action="refusal">Отказаться от задания</a>
    <?php endif;?>

    <?php if (UserView::isViewCompletionButton($user->id, $task->status, $task->customer_id)): ?>
        <a href="#" class="button button--pink action-btn" data-action="completion">Завершить задание</a>
    <?php endif;?>

    <?php if (UserView::isViewCancelButton($user->id, $task->status, $task->customer_id)): ?>
        <a href="#" class="button button--yellow action-btn" data-action="cancel">Отменить задание</a>
    <?php endif;?>

    <div class="task-map">
        <img class="map" src="/img/map.png" width="725" height="346" alt="">
        <p class="map-address town">
            <?= $task->city->name ?? 'Удаленная работа' ?>
        </p>
        <?php if (!empty($task->city->name)): ?>
            <p class="map-address">Новый арбат, 23, к. 1</p>
        <?php endif; ?>
    </div>

    <?php if ($task->responses): ?>
    <?php if (UserView::isViewResponsesList($task->responses, $user->id, $task->customer_id)): ?>
        <h4 class="head-regular">Отклики на задание</h4>
        <?php foreach ($task->responses as $response): ?>
        <?php if (UserView::isViewResponse($user->id, $task->customer_id, $response->executor_id)): ?>
            <div class="response-card">
                <?php if($response->executor->avatar):?>
                <img class="customer-photo" src="<?= $response->executor->avatar ?>" width="146" height="156"
                     alt="Фото исполнителя">
                <?php endif;?>
                <div class="feedback-wrapper">
                    <a href="<?=Url::toRoute(['/users/view/', 'id' => $response->executor->id])?>"
                       class="link link--block link--big"><?= Html::encode($response->executor->name) ?></a>
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
                    <p class="info-text"><span class="current-time">
                            <?= Yii::$app->formatter->format($response->created_at, 'relativeTime') ?>
                    </p>
                    <p class="price price--small"><?= Html::encode(Yii::$app->formatter->asCurrency($response->price)) ?? '' ?>&nbsp;₽</p>
                </div>
                <?php if (UserView::isViewResponseButtons($user->id, $task->customer_id, $task->status, $response->status)): ?>
                <div class="button-popup">
                    <a href="<?= Url::toRoute(['/tasks/accept', 'responseId' => $response->id, 'taskId' => $task->id, 'executorId' => $response->executor->id]) ?>" class="button button--blue button--small">Принять</a>
                    <a href="<?= Url::toRoute(['/tasks/refuse', 'responseId' => $response->id]) ?>" class="button button--orange button--small">Отказать</a>
                </div>
                <?php endif;?>
            </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
    <?php endif; ?>

</div>
<div class="right-column">
    <div class="right-card black info-card">
        <h4 class="head-card">Информация о задании</h4>
        <dl class="black-list">
            <dt>Категория</dt>
            <dd><?= $task->category->name ?></dd>
            <dt>Дата публикации</dt>
            <dd>
                <?= Yii::$app->formatter->format($task->created_at, 'relativeTime') ?>
            </dd>
            <dt>Срок выполнения</dt>
            <dd>
                <?= Yii::$app->formatter->asDate($task->deadline, 'php:d F, H:i') ?>
            </dd>
            <dt>Статус</dt>
            <dd><?= TaskBasic::getStatusName($task->status) ?></dd>
        </dl>
    </div>
    <?php if ($files): ?>
        <div class="right-card white file-card">
            <h4 class="head-card">Файлы задания</h4>
            <ul class="enumeration-list">
                <?php foreach ($files as $file): ?>
                    <li class="enumeration-item">
                        <a href="<?=Yii::$app->urlManager->baseUrl.$file->path?>" class="link link--block link--clip">
                            <?=str_replace('/uploads/', '', $file->path)?>
                        </a>
                        <p class="file-size">
                            <?= Yii::$app->formatter->asShortSize(
                                filesize(Yii::getAlias('@webroot').$file->path)
                            )?>
                        </p>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
</div>
</main>
<section class="pop-up pop-up--refusal pop-up--close">
    <div class="pop-up--wrapper">
        <h4>Отказ от задания</h4>
        <p class="pop-up-text">
            <b>Внимание!</b><br>
            Вы собираетесь отказаться от выполнения этого задания.<br>
            Это действие плохо скажется на вашем рейтинге и увеличит счетчик проваленных заданий.
        </p>
        <a href="<?= Url::toRoute(['/tasks/fail', 'taskId' => $task->id, 'executorId' => $user->id]) ?>" class="button button--pop-up button--orange">Отказаться</a>
        <div class="button-container">
            <button class="button--close" type="button">Закрыть окно</button>
        </div>
    </div>
</section>
<section class="pop-up pop-up--completion pop-up--close">
    <div class="pop-up--wrapper">
        <h4>Завершение задания</h4>
        <p class="pop-up-text">
            Вы собираетесь отметить это задание как выполненное.
            Пожалуйста, оставьте отзыв об исполнителе и отметьте отдельно, если возникли проблемы.
        </p>
        <div class="completion-form pop-up--form regular-form">
            <?php $form = ActiveForm::begin([
                'id' => 'new-review',
                'method' => 'post',
                'action' => Url::toRoute(['/tasks/review/', 'taskId' => $task->id, 'executorId' => $task->executor_id]),
                'fieldConfig' => [
                    'template' => "{label}{input}\n{error}",
                ],
            ]);?>
            <?=$form->field($reviewForm, 'comment')->textarea();?>
            <p class="completion-head control-label">Оценка работы</p>
            <div class="stars-rating big active-stars"><span>&nbsp;</span><span>&nbsp;</span><span>&nbsp;</span><span>&nbsp;</span><span>&nbsp;</span></div>
            <?=$form->field($reviewForm, 'rating')->hiddenInput(['id' => 'acceptance-form-rate'])->label(false);?>
            <input type="submit" class="button button--pop-up button--blue" value="Завершить">
            <?php ActiveForm::end();?>
        </div>
        <div class="button-container">
            <button class="button--close" type="button">Закрыть окно</button>
        </div>
    </div>
</section>
<section class="pop-up pop-up--act_response pop-up--close">
    <div class="pop-up--wrapper">
        <h4>Добавление отклика к заданию</h4>
        <p class="pop-up-text">
            Вы собираетесь оставить свой отклик к этому заданию.
            Пожалуйста, укажите стоимость работы и добавьте комментарий, если необходимо.
        </p>
        <div class="addition-form pop-up--form regular-form">
            <?php $form = ActiveForm::begin([
                'id' => 'new-response',
                'method' => 'post',
                'action' => Url::toRoute(['/tasks/response/', 'taskId' => $task->id]),
                'fieldConfig' => [
                    'template' => "{label}{input}\n{error}",
                ],
            ]);?>
            <?=$form->field($responseForm, 'comment')->textarea();?>
            <?=$form->field($responseForm, 'price');?>
            <input type="submit" class="button button--pop-up button--blue" value="Отправить">
            <?php ActiveForm::end();?>
        </div>
        <div class="button-container">
            <button class="button--close" type="button">Закрыть окно</button>
        </div>
    </div>
</section>
<section class="pop-up pop-up--cancel pop-up--close">
    <div class="pop-up--wrapper">
        <h4>Отмена задания</h4>
        <p class="pop-up-text">
            <b>Внимание!</b><br>
            Вы собираетесь отменить это задание.<br>
            Это действие удалит задание из ленты заданий.
        </p>
        <a href="<?= Url::toRoute(['/tasks/cancel', 'taskId' => $task->id]) ?>" class="button button--pop-up button--yellow">Отменить задание</a>
        <div class="button-container">
            <button class="button--close" type="button">Закрыть окно</button>
        </div>
    </div>
</section>
<?php endif; ?>
<div class="overlay"></div>