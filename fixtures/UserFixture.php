<?php

namespace app\fixtures;

use yii\test\ActiveFixture;

class UserFixture extends ActiveFixture
{
    /**
     * Метод создания фикстур для модели User
     *
     */
    public $modelClass = 'app\models\User';
    public $dataFile = '@app/fixtures/data/user.php';
}