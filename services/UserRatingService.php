<?php

namespace app\services;

use app\models\User;
use yii\db\Exception;

class UserRatingService
{
    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Возвращает рейтинг пользователя.
     *
     * @return string
     */
    public function getUserRating(): string
    {
        $sum = 0;
        $reviews = $this->user->getExecutorReviews()->all();

        foreach ($reviews as $review) {
            $sum += (int)($review['rating'] ?? 0);
        }

        if ($sum < 0) {
            return '0';
        }

        $reviewCount = (int)(count($reviews) + $this->user->failed_tasks);

        if ($reviewCount > 0) {
            return (string)round($sum / $reviewCount, 2);
        }

        return '0';
    }

    /**
     * Увеличивает счетчик выполненных задач пользователя.
     *
     * @return bool
     * @throws Exception
     */
    public function getCounterCompletedTasks(): bool
    {
        $this->user->successful_tasks += 1;
        $this->updateTotalScore();
        return $this->user->save();
    }

    /**
     * Обновляет общий балл пользователя.
     *
     * @return bool
     * @throws Exception
     */
    protected function updateTotalScore(): bool
    {
        $totalScore = $this->calcTotalScore();
        $this->user->total_score = $totalScore;
        return $this->user->save();
    }

    /**
     * Вычисляет общий балл пользователя.
     *
     * @return string
     */
    protected function calcTotalScore(): string
    {
        $reviews = $this->user->getExecutorReviews()->all();
        $sumRating = array_sum(array_column($reviews, 'rating'));
        $totalReviews = count($reviews);
        $totalScore = 0;
        if ($totalReviews > 0) {
            $totalScore = (string)round($sumRating / $totalReviews, 2);
        }
        return $totalScore;
    }

    /**
     * Увеличивает счетчик проваленных задач пользователя.
     *
     * @return bool
     * @throws Exception
     */
    public function getCounterFailedTasks(): bool
    {
        $this->user->failed_tasks += 1;
        $this->updateTotalScore();
        return $this->user->save();
    }

    /**
     * Возвращает место в рейтинге на основе общего балла.
     *
     * @return int
     */
    public function getUserRank(): int
    {
        $users = User::find()
            ->orderBy(['total_score' => SORT_DESC, 'id' => SORT_ASC])
            ->all();

        $rank = 1;
        foreach ($users as $user) {
            if ($user->id === $this->user->id) {
                return $rank;
            }
            $rank++;
        }

        return 0;
    }

}