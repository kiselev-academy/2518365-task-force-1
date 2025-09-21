<?php
/** @var yii\web\View $this */

/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var array $categories */
/** @var $task */

/** @var $tasksQuery */

use app\models\forms\TasksFilter;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

$this->title = 'Taskforce';

?>
<main class="main-content container">
    <div class="left-column">
        <h3 class="head-main head-task">Новые задания</h3>
        <?php if (!empty($tasks)): ?>
            <?php foreach ($tasks as $task): ?>
                <div class="task-card">
                    <div class="header-task">
                        <a href="<?= Url::toRoute(['/tasks/view/', 'id' => $task->id]) ?> ?>"
                           class="link link--block link--big"><?= Html::encode($task->title) ?></a>
                        <p class="price price--task"><?= Html::encode($task->budget) ?>&nbsp;₽</p>
                    </div>
                    <p class="info-text">
                        <?= Yii::$app->formatter->format($task->created_at, 'relativeTime') ?>
                    </p>
                    <p class="task-text"><?= Html::encode($task->description) ?></p>
                    <p>Откликов: <?= $task->getResponses()->count(); ?></p>

                    <div class="footer-task">
                        <p class="info-text town-text">
                            <?= $task->city->name ?? 'Удаленная работа' ?>
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
    <div class="right-column">
        <div class="right-card black">
            <div class="search-form">
                <?php $form = ActiveForm::begin([
                    'id' => 'filter-form',
                    'method' => 'get',
                    'fieldConfig' => [
                        'template' => "{input}",
                    ],
                ]); ?>

                <h4 class="head-card">Категории</h4>
                <?php if (!empty($filter)): ?>
                    <?= $form->field($filter, 'categories')->checkboxList(
                        $categories,
                        [
                            'class' => 'checkbox-wrapper',
                            'itemOptions' => [
                                'labelOptions' => [
                                    'class' => 'control-label',
                                ],
                            ],
                        ]
                    ); ?>

                    <h4 class="head-card">Дополнительно</h4>
                    <?= $form->field($filter, 'distantWork')->checkbox(
                        [
                            'id' => 'distant-work',
                            'labelOptions' => [
                                'class' => 'control-label',
                            ],
                        ]
                    ); ?>
                    <?= $form->field($filter, 'noResponse')->checkbox(
                        [
                            'id' => 'no-response',
                            'labelOptions' => [
                                'class' => 'control-label',
                            ],
                        ]
                    ); ?>

                    <h4 class="head-card">Период</h4>
                    <?= $form->field($filter, 'period')->dropDownList(
                        TasksFilter::getPeriodsMap(),
                        [
                            'id' => 'period-value',
                        ]
                    ); ?>
                <?php endif; ?>

                <input type="submit" class="button button--blue" value="Искать">
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</main>
