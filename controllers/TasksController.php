<?php

namespace app\controllers;

use app\models\Category;
use app\models\Task;
use app\models\TaskSearch;
use yii\helpers\ArrayHelper;
use app\models\forms\TasksFilter;
use yii\web\NotFoundHttpException;

class TasksController extends AuthorizedController
{
    public function actionIndex(?int $category = null): string
    {
        $TaskSearch = new TaskSearch();
        $result = $TaskSearch->getTasks($category);
        $filter = new TasksFilter();

        $categoriesQuery = Category::find()->select(['id', 'name'])->all();
        $categories = ArrayHelper::map($categoriesQuery, 'id', 'name');
        $tasks = $result['tasks'];
        $pagination = $result['pagination'];

        return $this->render('index', compact('tasks', 'filter', 'categories', 'pagination'));
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionView($id): string
    {
        $task = Task::find()->with(['responses.executor'])->where(['id' => $id])->one();
        if (!$task) {
            throw new NotFoundHttpException('Задание с ID $id не найдено');
        }
        return $this->render('view', compact('task'));
    }
}