<?php

namespace app\controllers;

use app\models\forms\LoginForm;
use Yii;
use yii\web\Response;
use yii\widgets\ActiveForm;

class LandingController extends GuestController
{
    public $layout = 'landing';

    public function actionIndex(): array|Response|string
    {
        $loginForm = new LoginForm();


        if (!Yii::$app->request->isPost) {
            return $this->render('index', ['login' => $loginForm]);
        }

        $loginForm->load(Yii::$app->request->post());

        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($loginForm);
        }

        if ($loginForm->validate() === false) {
            return $this->render('index', ['login' => $loginForm]);
        }

        $user = $loginForm->getUser();

        if (null === $user) {
            return $this->render('index', ['login' => $loginForm]);
        }

        Yii::$app->user->login($user);

        return $this->redirect(['/tasks']);
    }
}