<?php

namespace app\fixtures;

use yii\test\ActiveFixture;

class TaskFixture extends ActiveFixture
{
    /**
     * Метод создания фикстур для модели Task
     *
     */
    public $modelClass = 'app\models\Task';
    public $dataFile = '@app/fixtures/data/task.php';
}