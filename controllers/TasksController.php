<?php

namespace app\controllers;

use app\models\Category;
use app\models\forms\TasksFilter;
use app\models\TaskSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class TasksController extends Controller
{
    public function actionIndex(): string
    {
        $TaskSearch = new TaskSearch();

        $tasks = $TaskSearch->getTasks();
        $filter = new TasksFilter();

        $categoriesQuery = Category::find()->select(['id', 'name'])->all();
        $categories = ArrayHelper::map($categoriesQuery, 'id', 'name');

        return $this->render('index', ['tasks' => $tasks, 'filter' => $filter, 'categories' => $categories]);
    }
}