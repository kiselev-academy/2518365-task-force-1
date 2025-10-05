<?php

namespace app\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;

abstract class GuestController extends Controller implements RulesInterface
{
    /**
     * Метод, определяющий правила доступа для незарегистрированных пользователей.
     *
     * @return array Массив с настройками.
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'denyCallback' => function () {
                    return $this->redirect('/tasks');
                },
                'rules' => $this->getRules(),
            ],
        ];
    }

    public function getRules(): array
    {
        return [
            [
                'allow' => true,
                'roles' => ['?'],
            ],
        ];
    }
}