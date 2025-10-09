<?php

namespace app\models\forms;

use app\models\Review;
use app\models\Task;
use TaskForce\Models\Task as TaskBasic;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\web\BadRequestHttpException;

class NewReviewForm extends Model
{
    public string $comment = '';
    public string $rating = '';

    /**
     * Возвращает список меток атрибутов.
     *
     * @return array Список меток атрибутов.
     */
    public function attributeLabels(): array
    {
        return [
            'comment' => 'Ваш комментарий',
            'rating' => 'Оценка работы',
        ];
    }

    /**
     * Возвращает список правил валидации для атрибутов модели.
     *
     * @return array Список правил валидации.
     */
    public function rules(): array
    {
        return [
            [['comment', 'rating'], 'required'],
            [['rating'], 'compare', 'compareValue' => 0, 'operator' => '>', 'type' => 'number'],
            [['rating'], 'compare', 'compareValue' => 5, 'operator' => '<=', 'type' => 'number'],
            [['comment', 'rating'], 'filter', 'filter' => 'strip_tags'],
        ];
    }

    /**
     * Создает и сохраняет новый отзыв.
     *
     * @param int $taskId ID задачи.
     * @param int $executorId ID исполнителя.
     * @return bool Возвращает true, если отзыв успешно создан и сохранен, иначе false.
     * @throws BadRequestHttpException|Exception
     */
    public function createReview(int $taskId, int $executorId): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $newReview = $this->newReview($taskId, $executorId);
        $newReview->save(false);

        $task = Task::findOne($taskId);
        $task->status = TaskBasic::STATUS_COMPLETED;
        if (!$task->save()) {
            throw new BadRequestHttpException('Не получилось сохранить данные');
        }
        return true;

    }

    /**
     * Создает новый объект отзыва на основе данных формы.
     *
     * @param int $taskId ID задачи.
     * @param int $executorId ID исполнителя.
     * @return Review Новый объект отзыва.
     */
    protected function newReview(int $taskId, int $executorId): Review
    {
        $review = new Review;
        $review->comment = $this->comment;
        $review->rating = $this->rating;
        $review->task_id = $taskId;
        $review->customer_id = Yii::$app->user->getId();
        $review->executor_id = $executorId;
        return $review;
    }
}