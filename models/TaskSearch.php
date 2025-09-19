<?php

namespace app\models;

use app\models\forms\TasksFilter;
use TaskForce\Models\Task as TaskBasic;
use Yii;
use yii\base\Model;
use yii\db\Expression;

class TaskSearch extends Model
{
    public function getTasks(): array
    {
        $tasks = Task::find()
            ->where(['tasks.status' => TaskBasic::STATUS_NEW])
            ->orderBy(['created_at' => SORT_DESC])
            ->with('category')
            ->with('city');

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

        return $tasks->all();
    }
}