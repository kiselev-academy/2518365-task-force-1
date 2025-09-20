<?php

namespace app\controllers;

use app\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class UsersController extends Controller
{
    /**
     * @throws NotFoundHttpException
     */
    public function actionView($id): string
    {
        $user = User::findOne($id);
        if (!$user) {
            throw new NotFoundHttpException('Пользователя с ID $id не найдено');
        }
        return $this->render('view', compact('user'));
    }
}