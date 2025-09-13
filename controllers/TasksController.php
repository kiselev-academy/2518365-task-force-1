<?php

namespace app\controllers;

use yii\web\Controller;
use app\models\Task;

class TasksController extends Controller
{
    public function actionIndex(): string
    {
        $tasks = Task::find()
            ->where(['status' => Task::STATUS_NEW])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();
        return $this->render('index', ['tasks' => $tasks]);
    }

}