<?php

/** @var yii\web\View $this */

/** @var string $content */

use app\assets\AppAsset;
use app\assets\MainAsset;
use app\models\User;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\widgets\Menu;

AppAsset::register($this);
MainAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);
if (Yii::$app->user->isGuest) {
    $user = User::findOne(Yii::$app->user->getId());
}
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="table-layout">
    <header class="page-header">
        <nav class="main-nav">
            <a href='#' class="header-logo">
                <img class="logo-image" src="/img/logotype.png" width=227 height=60 alt="taskforce">
            </a>
            <?php if (!Yii::$app->user->isGuest):
            $user = Yii::$app->user->identity;
            ?>
            <div class="nav-wrapper">
                <?= Menu::widget([
                    'options' => [
                        'class' => 'nav-list'
                    ],
                    'items' => [
                        ['label' => 'Новое', 'url' => ['/tasks/index']],
                        [
                            'label' => 'Мои задания',
                            'url' => ['/my-tasks'],
                            'active' => str_starts_with(Yii::$app->controller->getRoute(), 'my-tasks')
                        ],
                        ['label' => 'Создать задание', 'url' => ['/tasks/new'], 'visible' => $user->role === User::ROLE_CUSTOMER],
                        ['label' => 'Настройки', 'url' => ['/users/edit']]
                    ],
                    'itemOptions' => [
                        'class' => 'list-item'],
                    'linkTemplate' => '<a href="{url}" class="link link--nav">{label}</a>',
                    'activeCssClass' => 'list-item--active'
                ]); ?>
            </div>
        </nav>
        <div class="user-block">
            <a href="<?= Url::toRoute(['/users/view/', 'id' => $user->id]) ?>">
                <?= Html::img($user->avatar, [
                    'class' => 'user-photo',
                    'width' => 55,
                    'height' => 55,
                    'alt' => 'Аватар',
                ]) ?>
            </a>
            <div class="user-menu">
                <p class="user-name"><?= Html::encode($user->name) ?></p>
                <div class="popup-head">
                    <ul class="popup-menu">
                        <li class="menu-item">
                            <a href="<?= Url::toRoute(['/users/edit']) ?>" class="link">Настройки</a>
                        </li>
                        <li class="menu-item">
                            <a href="#" class="link">Связаться с нами</a>
                        </li>
                        <li class="menu-item">
                            <a href="<?= Url::to(['/users/logout']) ?>" class="link">Выход из системы</a>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </header>

    <?php if (!empty($this->params['breadcrumbs'])): ?>
        <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]) ?>
    <?php endif ?>
    <?= Alert::widget() ?>
    <?= $content ?>

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
