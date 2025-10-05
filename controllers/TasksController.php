<?php

namespace app\controllers;

use app\models\Category;
use app\models\File;
use app\models\forms\NewResponseForm;
use app\models\forms\NewReviewForm;
use app\models\forms\NewTaskForm;
use app\models\forms\TasksFilter;
use app\models\Response;
use app\models\Task;
use app\models\TaskSearch;
use app\models\User;
use Yii;
use yii\base\InvalidRouteException;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

class TasksController extends AuthorizedController
{
    /**
     * Метод, определяющий правила доступа.
     *
     * @return array Массив с ролевыми настройками.
     */
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

    /**
     * Отображение списка заданий.
     *
     * @return string Отображение страницы.
     */
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
     * Отображение задания по идентификатору.
     *
     * @param int $id ID задания.
     * @return \yii\web\Response|string Отображение страницы или редирект.
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): \yii\web\Response|string
    {
        $task = Task::findOne($id);
        if (!$task) {
            throw new NotFoundHttpException("Задание с ID $id не найдено");
        }
        $files = File::find()->where(['task_id' => $task->id])->all();
        $responseForm = new NewResponseForm();
        $reviewForm = new NewReviewForm();

        return $this->render('view', compact('task', 'files', 'responseForm', 'reviewForm'));
    }

    /**
     * Создание нового задания.
     *
     * @return \yii\web\Response |string Отображение страницы или редирект.
     * @throws InvalidRouteException
     */
    public function actionNew(): \yii\web\Response|string
    {
        $taskForm = new NewTaskForm();

        $categoriesQuery = Category::find()->select(['id', 'name'])->all();
        $categories = ArrayHelper::map($categoriesQuery, 'id', 'name');

        if (!Yii::$app->request->isPost) {
            return $this->render('new', ['newTask' => $taskForm, 'categories' => $categories]);
        }
        $taskForm->load(Yii::$app->request->post());
        $newTaskId = $taskForm->createTask();
        if ($newTaskId) {
            return Yii::$app->response->redirect(["/tasks/view/$newTaskId"]);
        }
        return $this->render('new', ['newTask' => $taskForm, 'categories' => $categories]);
    }

    /**
     * Создание отклика на задание.
     *
     * @param int $taskId ID задания.
     * @return \yii\web\Response Редирект.
     * @throws Exception
     * @throws InvalidRouteException
     * @throws BadRequestHttpException
     */
    public function actionResponse(int $taskId): \yii\web\Response
    {
        $responseForm = new NewResponseForm();
        if (!Yii::$app->request->isPost) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        $responseForm->load(Yii::$app->request->post());
        if (!$responseForm->createResponse($taskId)) {
            throw new BadRequestHttpException("Не удалось создать отклик для задания с id $taskId");
        }
        return Yii::$app->response->redirect(["/tasks/view/$taskId"]);

    }

    /**
     * Создание отзыва на задание.
     *
     * @param int $taskId ID задания.
     * @param int $executorId ID исполнителя.
     * @return \yii\web\Response Редирект.
     * @throws InvalidRouteException
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     * @throws Exception
     */
    public function actionReview(int $taskId, int $executorId): \yii\web\Response
    {
        $reviewForm = new NewReviewForm();
        if (!Yii::$app->request->isPost) {
            return $this->redirect(Yii::$app->request->referrer);
        }
        $reviewForm->load(Yii::$app->request->post());
        if (!$reviewForm->createReview($taskId, $executorId)) {
            throw new BadRequestHttpException("Не удалось создать отзыв на пользователя по заданию с id $taskId");
        }
        $user = User::findOne($executorId);
        if (!$user) {
            throw new NotFoundHttpException("Нет пользователя с id $executorId");
        }
        if (!$user->getCounterCompletedTasks()) {
            throw new BadRequestHttpException('Не удалось сохранить данные');
        }

        return Yii::$app->response->redirect(["/tasks/view/$taskId"]);

    }

    /**
     * Принятие отклика на задание.
     *
     * @param int $responseId ID отклика.
     * @param int $taskId ID задания.
     * @param int $executorId ID исполнителя.
     * @return \yii\web\Response Редирект.
     * @throws Exception
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     */
    public function actionAccept(int $responseId, int $taskId, int $executorId): \yii\web\Response
    {
        $response = Response::findOne($responseId);
        if (!$response) {
            throw new NotFoundHttpException("Нет отклика с id $responseId");
        }
        if (!$response->accept()) {
            throw new BadRequestHttpException('Не удалось сохранить данные');
        }

        $task = Task::findOne($taskId);
        if (!$task) {
            throw new NotFoundHttpException("Задание с id $taskId не найдено");
        }
        if (!$task->startWork($executorId)) {
            throw new BadRequestHttpException('Не удалось сохранить данные');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Отклонение отклика на задание.
     *
     * @param int $responseId ID отклика.
     * @return \yii\web\Response Редирект.
     *
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     * @throws Exception
     */
    public function actionRefuse(int $responseId): \yii\web\Response
    {
        $response = Response::findOne($responseId);
        if (!$response) {
            throw new NotFoundHttpException("Нет отклика с id $responseId");
        }
        if (!$response->reject()) {
            throw new BadRequestHttpException('Не удалось сохранить данные');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Провал задания.
     *
     * @param int $taskId ID задания.
     * @param int $executorId ID исполнителя.
     * @return \yii\web\Response Редирект.
     * @throws BadRequestHttpException
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionFail(int $taskId, int $executorId): \yii\web\Response
    {
        $task = Task::findOne($taskId);
        if (!$task) {
            throw new NotFoundHttpException("Задание с id $taskId не найдено");
        }
        if (!$task->failTask()) {
            throw new BadRequestHttpException('Не удалось сохранить данные');
        }

        $user = User::findOne($executorId);
        if (!$user) {
            throw new NotFoundHttpException("Нет пользователя с id $executorId");
        }
        if (!$user->getCounterFailedTasks()) {
            throw new BadRequestHttpException('Не удалось сохранить данные');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Отмена задания.
     *
     * @param int $taskId ID задания.
     * @return \yii\web\Response Редирект.
     * @throws BadRequestHttpException
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionCancel(int $taskId): \yii\web\Response
    {
        $task = Task::findOne($taskId);
        if (!$task) {
            throw new NotFoundHttpException("Задание с id $taskId не найдено");
        }
        if (!$task->cancelTask()) {
            throw new BadRequestHttpException('Не удалось сохранить данные');
        }

        return $this->redirect(Yii::$app->request->referrer);
    }
}