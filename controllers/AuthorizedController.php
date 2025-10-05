<?php

namespace app\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;

abstract class AuthorizedController extends Controller implements RulesInterface
{
    /**
     * Метод, определяющий правила доступа для авторизованных пользователей.
     *
     * @return array Массив с настройками.
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'denyCallback' => function () {
                    return $this->redirect('/landing');
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
                'roles' => ['@'],
            ],
        ];
    }
}