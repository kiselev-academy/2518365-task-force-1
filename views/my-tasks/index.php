<?php

/** @var yii\web\View $this */

/** @var yii\data\ActiveDataProvider $dataProvider */

/** @var $task */

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\widgets\Menu;

$this->title = 'Taskforce';
if (!Yii::$app->user->isGuest) {
    $user = User::findOne(Yii::$app->user->getId());
}
?>

<main class="main-content container">

    <div class="left-menu">
        <h3 class="head-main head-task">Мои задания</h3>
        <?= Menu::widget([
            'options' => [
                'class' => 'side-menu-list',
            ],
            'items' => [
                ['label' => 'Новые', 'url' => ['/my-tasks/new'], 'visible' => $user->role === User::ROLE_CUSTOMER],
                ['label' => 'В процессе', 'url' => ['/my-tasks/work']],
                ['label' => 'Просрочено', 'url' => ['/my-tasks/overdue'], 'visible' => $user->role === User::ROLE_EXECUTOR],
                ['label' => 'Закрытые', 'url' => ['/my-tasks/closed']],
            ],
            'itemOptions' => [
                'class' => 'side-menu-item',
            ],
            'linkTemplate' => '<a href="{url}" class="link link--nav">{label}</a>',
            'activeCssClass' => 'side-menu-item--active',
            'activateItems' => true,
            'activateParents' => false,
        ]); ?>
    </div>

    <div class="left-column left-column--task">
        <h3 class="head-main head-regular">Новые задания</h3>
        <?php $tasks = $dataProvider->getModels(); ?>
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
                    <p class="task-text"><?= Html::encode($task->description) ?></p>
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
                'pagination' => $dataProvider->pagination,
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