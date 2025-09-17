<?php
namespace app\models;

use Yii;
use app\models\forms\TasksFilter;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class TaskSearch extends Model
{
    public function getTasks(): array
    {
        $tasks = Task::find()
            ->where(['status' => Task::STATUS_NEW])
            ->orderBy(['created_at' => SORT_DESC])
            ->with('category')
            ->with('city');

        $request = Yii::$app->getRequest();

        if ($request->get('TasksFilter')) {

            $categories = $request->get('TasksFilter')['categories'];
            $distantWork = $request->get('TasksFilter')['distantWork'];
            $noResponse = $request->get('TasksFilter')['noResponse'];
            $period = $request->get('TasksFilter')['period'];

            if ($categories) {
                $tasks = $tasks->andWhere(['in', 'category_id', $categories]);
            }

            if ($distantWork) {
                $tasks = $tasks->andWhere(['city_id' => null]);
            }

            if ($noResponse) {
                $tasksWithResponse = Response::find()
                    ->select(['task_id', 'id'])
                    ->all();
                $tasksWithResponse = ArrayHelper::map($tasksWithResponse, 'id', 'task_id');
                $tasks = $tasks->andWhere(['not in', 'id', $tasksWithResponse]);
            }

            if ($period !== TasksFilter::ALL_TIME) {
                $tasks = $tasks->andWhere(['>', 'created_at', new Expression("CURRENT_TIMESTAMP() - INTERVAL $period")]);
            }
        }

        return $tasks->all();
    }
}