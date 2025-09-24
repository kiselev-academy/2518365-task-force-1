<?php

namespace app\controllers;

use app\models\Category;
use app\models\forms\NewTaskForm;
use app\models\Task;
use app\models\TaskSearch;
use app\models\User;
use Yii;
use yii\base\InvalidRouteException;
use yii\helpers\ArrayHelper;
use app\models\forms\TasksFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class TasksController extends AuthorizedController
{
    public function behaviors(): array
    {
        $rules = parent::behaviors();
        $rule = [
            'allow' => false,
            'actions' => ['new'],
            'matchCallback' => function ($rule, $action) {
                $identity = Yii::$app->user->getIdentity();
                return $identity !== null && $identity->role === User::ROLE_EXECUTOR;
            },
        ];
        array_unshift($rules['access']['rules'], $rule);

        return $rules;
    }

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

    /**
     * @throws InvalidRouteException
     */
    public function actionNew(): Response|string
    {
        $taskForm = new NewTaskForm();

        $categoriesQuery = Category::find()->select(['id', 'name'])->all();
        $categories = ArrayHelper::map($categoriesQuery, 'id', 'name');

        if (Yii::$app->request->isPost) {
            $taskForm->load(Yii::$app->request->post());
            $newTaskId = $taskForm->createTask();
            if ($newTaskId) {
                return Yii::$app->response->redirect(["/tasks/view/$newTaskId"]);
            }
        }

        return $this->render('new', ['newTask' => $taskForm, 'categories' => $categories]);
    }
}