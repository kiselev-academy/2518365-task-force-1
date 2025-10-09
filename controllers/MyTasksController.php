<?php

namespace app\controllers;

use app\models\TaskSearch;
use app\models\User;
use TaskForce\Models\Task as TaskBasic;
use Yii;
use yii\base\InvalidRouteException;
use yii\base\Module;
use yii\web\Response;

class MyTasksController extends AuthorizedController
{
    private User $user;
    private TaskSearch $taskSearch;

    public function __construct(string $id, Module $module)
    {
        $this->user = User::getCurrentUser();
        $this->taskSearch = new TaskSearch();
        parent::__construct($id, $module);
    }

    /**
     * Метод по умолчанию. Для заказчиков осуществляется перенаправление на страницу с новыми задачами, для исполнителей - на страницу с задачами в работе.
     *
     * @return Response
     * @throws InvalidRouteException
     */
    public function actionIndex(): Response
    {
        if ($this->user->role === User::ROLE_CUSTOMER) {
            return Yii::$app->response->redirect(["/my-tasks/new"]);
        }
        if ($this->user->role === User::ROLE_EXECUTOR) {
            return Yii::$app->response->redirect(["/my-tasks/work"]);
        }
        return $this->goHome();
    }

    /**
     * Метод для отображения новых задач.
     *
     * @return string|null
     */
    public function actionNew(): ?string
    {
        $dataProvider = $this->taskSearch->getUserTasks($this->user->id, $this->user->role, [
            TaskBasic::STATUS_NEW,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Метод для отображения задач в работе.
     *
     * @return string|null
     */
    public function actionWork(): ?string
    {
        $dataProvider = $this->taskSearch->getUserTasks($this->user->id, $this->user->role, [
            TaskBasic::STATUS_WORK,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Метод для отображения завершенных задач.
     *
     * @return string|null
     */
    public function actionClosed(): ?string
    {
        $dataProvider = $this->taskSearch->getUserTasks($this->user->id, $this->user->role, [
            TaskBasic::STATUS_CANCELLED,
            TaskBasic::STATUS_COMPLETED,
            TaskBasic::STATUS_FAILED,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Метод для отображения просроченных задач.
     *
     * @return string|null
     */
    public function actionOverdue(): ?string
    {
        $dataProvider = $this->taskSearch->getUserTasks($this->user->id, $this->user->role, [
            TaskBasic::STATUS_WORK,
        ], true);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }
}