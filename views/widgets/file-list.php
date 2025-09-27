<?php

use yii\helpers\Html;

/* @var $files array */

?>

<div class="right-card white file-card">
    <h4 class="head-card">Файлы задания</h4>
    <ul class="enumeration-list">
        <?php foreach ($files as $file): ?>
            <li class="enumeration-item">
                <a href="<?= Yii::$app->urlManager->baseUrl . $file->path ?>" class="link link--block link--clip">
                    <?= Html::encode(basename($file->path)) ?>
                </a>
                <p class="file-size">
                    <?= Yii::$app->formatter->asShortSize(
                        filesize(Yii::getAlias('@webroot') . $file->path)
                    ) ?>
                </p>
            </li>
        <?php endforeach; ?>
    </ul>
</div>