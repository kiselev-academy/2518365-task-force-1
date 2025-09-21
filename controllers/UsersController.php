<?php

namespace app\controllers;

use app\models\User;
use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class UsersController extends SecuredController
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

    public function actionLogout(): Response
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
}