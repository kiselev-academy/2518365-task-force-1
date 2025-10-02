<?php

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = 'Taskforce';
if (!Yii::$app->user->isGuest) {
    $user = User::getCurrentUser();
}
?>

<main class="main-content container">

    <div class="left-menu">
        <h3 class="head-main head-task">Мои задания</h3>
        <ul class="side-menu-list">
            <?php if ($user->role === User::ROLE_CUSTOMER): ?>
                <li class="side-menu-item side-menu-item--active">
                    <a href="<?= Url::toRoute(['/my-tasks/new']) ?>" class="link link--nav">Новые</a>
                </li>
                <li class="side-menu-item">
                    <a href="<?= Url::toRoute(['/my-tasks/work']) ?>" class="link link--nav">В процессе</a>
                </li>
                <li class="side-menu-item">
                    <a href="<?= Url::toRoute(['/my-tasks/closed']) ?>" class="link link--nav">Закрытые</a>
                </li>
            <?php elseif ($user->role === User::ROLE_EXECUTOR): ?>
                <li class="side-menu-item side-menu-item--active">
                    <a href="<?= Url::toRoute(['/my-tasks/work']) ?>" class="link link--nav">В процессе</a>
                </li>
                <li class="side-menu-item">
                    <a href="<?= Url::toRoute(['/my-tasks/overdue']) ?>" class="link link--nav">Просрочено</a>
                </li>
                <li class="side-menu-item">
                    <a href="<?= Url::toRoute(['/my-tasks/closed']) ?>" class="link link--nav">Закрытые</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="left-column left-column--task">
        <h3 class="head-main head-regular">Новые задания</h3>
        <?php if (!empty($tasks)): ?>
            <?php foreach ($tasks as $task): ?>
                <div class="task-card">
                    <div class="header-task">
                        <a href="<?= Url::toRoute(['/tasks/view/', 'id' => $task->id]) ?>"
                           class="link link--block link--big"><?= Html::encode($task->title) ?></a>
                        <p class="price price--task">
                            <?= Html::encode(Yii::$app->formatter->asCurrency($task->budget)) ?? '' ?>
                        </p>
                    </div>
                    <p class="info-text">
                        <?= Yii::$app->formatter->format($task->created_at, 'relativeTime') ?>
                    </p>
                    <p class="task-text"><?= $task->description ?></p>
                    <div class="footer-task">
                        <p class="info-text town-text">
                            <?= isset($task->city->name) ? $task->city->name : 'Удаленная работа' ?>
                        </p>
                        <p class="info-text category-text"><?= $task->category->name ?></p>
                        <a href="<?= Url::toRoute(['/tasks/view/', 'id' => $task->id]) ?>" class="button button--black">Смотреть
                            задание</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <h3 class="head-main head-task">Задания не найдены.</h3>
        <?php endif; ?>

        <div class="pagination-wrapper">
            <?= LinkPager::widget([
                'pagination' => $pagination,
                'options' => ['class' => 'pagination-list'],
                'linkOptions' => ['class' => 'link link--page',],
                'linkContainerOptions' => ['class' => 'pagination-item'],
                'activePageCssClass' => 'pagination-item--active',
                'nextPageCssClass' => 'mark',
                'prevPageCssClass' => 'mark',
                'disabledPageCssClass' => 'disabled',
                'prevPageLabel' => '',
                'nextPageLabel' => '',
            ]) ?>
        </div>
    </div>
</main>