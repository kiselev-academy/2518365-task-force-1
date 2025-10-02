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

    public function getUserTasks($userId, $role, $statuses, $isOverdue = false): array
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