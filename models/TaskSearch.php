<?php

namespace app\models;

use app\models\forms\TasksFilter;
use TaskForce\Models\Task as TaskBasic;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;

class TaskSearch extends Model
{
    /**
     * Метод, который возвращает список задач, удовлетворяющих заданным условиям. По умолчанию (без фильтрации) возвращает список задач в статусе "Новые" без привязки к городу, а также из города пользователя.
     *
     * @return ActiveDataProvider DataProvider с задачами по фильтру.
     */
    public function getTasks(?int $category = null): ActiveDataProvider
    {
        $query = Task::find()
            ->where(['tasks.status' => TaskBasic::STATUS_NEW])
            ->orderBy(['created_at' => SORT_DESC])
            ->with('category')
            ->with('city');

        if ($category) {
            $query = $query->andWhere(['category_id' => $category]);
        }

        $request = Yii::$app->getRequest();

        $categories = ($request->get('TasksFilter')['categories'] ?? []);
        if (!empty($categories)) {
            $query = $query->andWhere(['in', 'category_id', $categories]);
        }

        $distantWork = ($request->get('TasksFilter')['distantWork'] ?? []);
        if (!empty($distantWork)) {
            $query = $query->andWhere(['city_id' => null]);
        }

        $noResponse = ($request->get('TasksFilter')['noResponse'] ?? []);
        if (!empty($noResponse)) {
            $query->joinWith('responses')
                ->groupBy('tasks.id')
                ->having('COUNT(responses.id) = 0');
        }

        $period = ($request->get('TasksFilter')['period'] ?? []);
        if (!empty($period) && $period !== TasksFilter::ALL_TIME) {
            list($value, $unit) = explode(' ', $period);
            $query = $query->andWhere(
                "created_at > CURRENT_TIMESTAMP() - INTERVAL :value {$unit}"
            )->addParams([':value' => (int)$value]);
        }

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5,
            ],
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);
    }

    /**
     * Метод, который возвращает список задач для пользователя, удовлетворяющих заданным условиям.
     *
     * @param int $userId ID пользователя.
     * @param string $role Роль пользователя (исполнитель или заказчик).
     * @param array $statuses Массив с допустимыми статусами задач.
     * @param bool $isOverdue Флаг для фильтрации просроченных задач.
     * @return ActiveDataProvider DataProvider с задачами пользователя по фильтру.
     */
    public function getUserTasks(int $userId, string $role, array $statuses, bool $isOverdue = false): ActiveDataProvider
    {
        $query = Task::find()
            ->andWhere(['in', 'status', $statuses])
            ->andWhere([$role . '_id' => $userId])
            ->orderBy(['created_at' => SORT_DESC])
            ->with('category')
            ->with('city');

        if ($isOverdue) {
            $query = $query->andWhere(['<', 'deadline', new Expression('CURRENT_TIMESTAMP()')]);
        }

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5,
            ],
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
            ],
        ]);
    }
}