<?php

namespace app\models;

use app\models\forms\TasksFilter;
use TaskForce\Models\Task as TaskBasic;
use Yii;
use yii\base\Model;
use yii\data\Pagination;
use yii\db\Expression;

class TaskSearch extends Model
{
    /**
     * Метод, который возвращает список задач, удовлетворяющих заданным условиям. По умолчанию (без фильтрации) возвращает список задач в статусе "Новые" без привязки к городу, а также из города пользователя.
     *
     * @return array Массив с задачами и информацией о пагинации.
     */
    public function getTasks(?int $category = null): array
    {
        $tasks = Task::find()
            ->where(['tasks.status' => TaskBasic::STATUS_NEW])
            ->orderBy(['created_at' => SORT_DESC])
            ->with('category')
            ->with('city');

        if ($category) {
            $tasks = $tasks->andWhere(['category_id' => $category]);
        }

        $request = Yii::$app->getRequest();

        $categories = ($request->get('TasksFilter')['categories'] ?? []);
        if (!empty($categories)) {
            $tasks = $tasks->andWhere(['in', 'category_id', $categories]);
        }

        $distantWork = ($request->get('TasksFilter')['distantWork'] ?? []);
        if (!empty($distantWork)) {
            $tasks = $tasks->andWhere(['city_id' => null]);
        }

        $noResponse = ($request->get('TasksFilter')['noResponse'] ?? []);
        if (!empty($noResponse)) {
            $tasks->joinWith('responses')
                ->groupBy('tasks.id')
                ->having('COUNT(responses.id) = 0');
        }

        $period = ($request->get('TasksFilter')['period'] ?? []);
        if (!empty($period) && $period !== TasksFilter::ALL_TIME) {
            $tasks = $tasks->andWhere(['>', 'created_at', new Expression("CURRENT_TIMESTAMP() - INTERVAL $period")]);

        }

        $pagination = new Pagination([
            'totalCount' => $tasks->count(),
            'pageSize' => 5,
        ]);

        $tasks = $tasks->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return [
            'tasks' => $tasks,
            'pagination' => $pagination,
        ];
    }

    /**
     * Метод, который возвращает список задач для пользователя, удовлетворяющих заданным условиям.
     *
     * @param int $userId ID пользователя.
     * @param string $role Роль пользователя (исполнитель или заказчик).
     * @param array $statuses Массив с допустимыми статусами задач.
     * @param bool $isOverdue Флаг для фильтрации просроченных задач.
     * @return array Массив с задачами и информацией о пагинации.
     */
    public function getUserTasks(int $userId, string $role, array $statuses, bool $isOverdue = false): array
    {
        $tasks = Task::find()
            ->andWhere(['in', 'status', $statuses])
            ->andWhere([$role . '_id' => $userId])
            ->orderBy(['created_at' => SORT_DESC])
            ->with('category')
            ->with('city');

        if ($isOverdue) {
            $tasks = $tasks->andWhere(['<', 'deadline', new Expression('CURRENT_TIMESTAMP()')]);
        }

        $pagination = new Pagination([
            'totalCount' => $tasks->count(),
            'pageSize' => 5,
        ]);

        $tasks = $tasks->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return [
            'tasks' => $tasks,
            'pagination' => $pagination,
        ];
    }
}