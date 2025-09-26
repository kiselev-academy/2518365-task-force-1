<?php

namespace app\models\forms;

use Yii;
use yii\base\Model;
use yii\db\Exception;
use app\models\Review;
use app\models\Task;
use Taskforce\Models\Task as TaskBasic;
use yii\web\BadRequestHttpException;

class NewReviewForm extends Model
{
    public string $comment = '';
    public string $rating = '';

    public function attributeLabels(): array
    {
        return [
            'comment' => 'Ваш комментарий',
            'rating' => 'Оценка работы',
        ];
    }

    public function rules(): array
    {
        return [
            [['comment', 'rating'], 'required'],
            [['rating'], 'compare', 'compareValue' => 0, 'operator' => '>', 'type' => 'number'],
            [['rating'], 'compare', 'compareValue' => 5, 'operator' => '<=', 'type' => 'number'],
        ];
    }

    public function newReview(int $taskId, int $executorId): Review
    {
        $review = new Review;
        $review->comment = $this->comment;
        $review->rating = $this->rating;
        $review->task_id = $taskId;
        $review->customer_id = Yii::$app->user->getId();
        $review->executor_id = $executorId;
        return $review;
    }

    /**
     * @throws Exception
     * @throws BadRequestHttpException
     */
    public function createReview(int $taskId, int $executorId): bool
    {
        if ($this->validate()) {
            $newReview = $this->newReview($taskId, $executorId);
            $newReview->save(false);

            $task = Task::findOne($taskId);
            $task->status = TaskBasic::STATUS_COMPLETED;
            if (!$task->save()) {
                throw new BadRequestHttpException('Не получилось сохранить данные');
            }
            return true;
        }
        return false;
    }
}