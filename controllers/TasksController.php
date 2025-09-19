<?php

namespace app\controllers;

use app\models\Category;
use app\models\Task;
use app\models\TaskSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use app\models\forms\TasksFilter;
use yii\web\NotFoundHttpException;

class TasksController extends Controller
{
    public function actionIndex(): string
    {
        $TaskSearch = new TaskSearch();

        $tasks = $TaskSearch->getTasks();
        $filter = new TasksFilter();

        $categoriesQuery = Category::find()->select(['id', 'name'])->all();
        $categories = ArrayHelper::map($categoriesQuery, 'id', 'name');

        return $this->render('index', compact('tasks', 'filter', 'categories'));
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionView($id): string
    {
        $task = Task::findOne($id);
        if (!$task) {
            throw new NotFoundHttpException('Задание с ID $id не найдено');
        }
        return $this->render('view', compact('task'));
    }
}