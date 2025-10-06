<?php

namespace app\controllers;

use app\models\City;
use app\models\forms\RegistrationForm;
use Yii;
use yii\base\InvalidRouteException;
use yii\db\Exception;

class RegistrationController extends GuestController
{
    /**
     * Метод, обрабатывает запрос на регистрацию нового пользователя.
     *
     * @return string Отображение страницы регистрации.
     *
     * @throws Exception
     * @throws \yii\base\Exception
     * @throws InvalidRouteException
     */
    public function actionIndex(): string
    {
        $cities = City::find()->select(['name', 'id'])->indexBy('id')->column();
        $registration = new RegistrationForm();

        if (Yii::$app->request->isPost) {
            $registration->load(Yii::$app->request->post());
            $registration->createUser();
        }

        return $this->render('index', compact('registration', 'cities'));
    }
}