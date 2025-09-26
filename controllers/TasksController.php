<?php

namespace app\controllers;

use app\models\Category;
use app\models\File;
use app\models\forms\NewResponseForm;
use app\models\forms\NewReviewForm;
use app\models\forms\NewTaskForm;
use app\models\Task;
use app\models\TaskSearch;
use app\models\User;
use Yii;
use yii\base\InvalidRouteException;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use app\models\forms\TasksFilter;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

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
    public function actionView($id): Response|string
    {
        $task = Task::find()->with(['responses.executor'])->where(['id' => $id])->one();
        if (!$task) {
            throw new NotFoundHttpException('Задание с ID $id не найдено');
        }
        $files = File::find()->where(['task_id' => $task->id])->all();
        $responseForm = new NewResponseForm();
        $reviewForm = new NewReviewForm();

        return $this->render('view', compact('task', 'files', 'responseForm', 'reviewForm'));
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
                return Yii::$app->response->redirect(['/tasks/view/$newTaskId']);
            }
        }

        return $this->render('new', ['newTask' => $taskForm, 'categories' => $categories]);
    }

    /**
     * @throws Exception
     * @throws InvalidRouteException
     * @throws BadRequestHttpException
     */
    public function actionResponse(int $taskId): \yii\web\Response
    {
        $responseForm = new NewResponseForm();
        if (Yii::$app->request->isPost) {
            $responseForm->load(Yii::$app->request->post());
            if (!$responseForm->createResponse($taskId)) {
                throw new BadRequestHttpException('Не удалось создать отклик для задания с id $taskId');
            }
            return Yii::$app->response->redirect(['/tasks/view/$taskId']);
        }
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @throws InvalidRouteException
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     * @throws BadRequestHttpException
     * @throws Exception
     */
    public function actionReview(int $taskId, int $executorId): \yii\web\Response
    {
        $reviewForm = new NewReviewForm();
        if (Yii::$app->request->isPost) {

            $reviewForm->load(Yii::$app->request->post());
            if (!$reviewForm->createReview($taskId, $executorId)) {
                throw new BadRequestHttpException('Не удалось создать отзыв на пользователя по заданию с id $taskId');
            } else {
                $user = User::findOne($executorId);
                if (!$user) {
                    throw new NotFoundHttpException('Нет пользователя с id $executorId');
                }
                if (!$user->getCounterCompletedTasks()) {
                    throw new BadRequestHttpException('Не удалось сохранить данные');
                }
            }

            return Yii::$app->response->redirect(['/tasks/view/$taskId']);
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * @throws Exception
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     */
    public function actionAccept(int $responseId, int $taskId, int $executorId): \yii\web\Response
    {
        $response = Response::findOne($responseId);
        if (!$response) {
            throw new NotFoundHttpException('Нет отклика с id $responseId');
        }
        if (!$response->accept()) {
            throw new BadRequestHttpException('Не удалось сохранить данные');
        }

        $task = Task::findOne($taskId);
        if (!$task) {
            throw new NotFoundHttpException('Задание с ID $id не найдено');
        }
        if (!$task->startWork($executorId)) {
            throw new BadRequestHttpException('Не удалось сохранить данные');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionRefuse(int $responseId): \yii\web\Response
    {
        $response = Response::findOne($responseId);
        if (!$response) {
            throw new NotFoundHttpException('Нет отклика с id $responseId');
        }
        if (!$response->reject()) {
            throw new BadRequestHttpException('Не удалось сохранить данные');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionFail(int $taskId, int $executorId): \yii\web\Response
    {
        $task = Task::findOne($taskId);
        if (!$task) {
            throw new NotFoundHttpException('Задание с ID $id не найдено');
        }
        if (!$task->failTask()) {
            throw new BadRequestHttpException('Не удалось сохранить данные');
        }

        $user = User::findOne($executorId);
        if (!$user) {
            throw new NotFoundHttpException('Нет пользователя с id $executorId');
        }
        if (!$user->getCounterFailedTasks()) {
            throw new BadRequestHttpException('Не удалось сохранить данные');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionCancel(int $taskId): \yii\web\Response
    {
        $task = Task::findOne($taskId);
        if (!$task) {
            throw new NotFoundHttpException('Задание с ID $id не найдено');
        }
        if (!$task->cancelTask()) {
            throw new BadRequestHttpException('Не удалось сохранить данные');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }
}