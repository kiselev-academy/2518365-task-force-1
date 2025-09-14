<?php

namespace app\fixtures;

use yii\test\ActiveFixture;

class UserFixture extends ActiveFixture
{
    public $modelClass = 'app\models\User';
    public $dataFile = '@app/fixtures/data/user.php';
}