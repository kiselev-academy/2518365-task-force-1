<?php

namespace app\models\forms;

use app\models\Response;
use app\models\User;
use TaskForce\Models\Task as TaskBasic;

class UserView
{
    /**
     * Проверяет, может ли пользователь просматривать список откликов на задание.
     * @param array $responses массив откликов на задание
     * @param int $userId ID пользователя
     * @param int $customerId ID заказчика задания
     * @return bool true, если пользователь может просмотреть список, false - в противном случае.
     */
    public static function isViewResponsesList(array $responses, int $userId, int $customerId): bool
    {
        if (!$responses) {
            return false;
        }
        if ($userId === $customerId) {
            return true;
        }
        foreach ($responses as $response) {
            if ($userId === $response->executor_id) {
                return true;
            }
        }
        return false;

    }

    /**
     * Проверяет, может ли пользователь просматривать отклик на задание.
     * @param int $userId ID пользователя
     * @param int $customerId ID заказчика задания
     * @param int $executorId ID исполнителя, оставившего отклик на задание
     * @return bool true, если пользователь может просмотреть отклик, false - в противном случае.
     */
    public static function isViewResponse(int $userId, int $customerId, int $executorId): bool
    {
        return $userId === $customerId || $userId === $executorId;
    }

    /**
     * Проверяет, может ли пользователь просматривать кнопки в отклике на задание.
     * @param int $userId ID пользователя
     * @param int $customerId ID заказчика задания
     * @param string $taskStatus статус задания
     * @param string $responseStatus статус отклика на задание
     * @return bool true, если пользователь может просмотреть кнопки в отклике, false - в противном случае.
     */
    public static function isViewResponseButtons(int $userId, int $customerId, string $taskStatus, string $responseStatus): bool
    {
        return $userId === $customerId && $responseStatus === Response::STATUS_NEW && $taskStatus === TaskBasic::STATUS_NEW;
    }

    /**
     * Проверяет, может ли пользователь увидеть кнопку отклика на задание.
     *
     * @param int $userId ID пользователя
     * @param string $userRole роль пользователя
     * @param string $taskStatus статус задания
     * @param array $responses массив откликов на задание
     * @return bool true, если пользователь может увидеть кнопку отклика, false - в противном случае.
     */
    public static function isViewResponseButton(int $userId, string $userRole, string $taskStatus, array $responses): bool
    {
        if ($userRole !== User::ROLE_EXECUTOR || $taskStatus !== TaskBasic::STATUS_NEW) {
            return false;
        }

        foreach ($responses as $response) {
            if ($userId === $response->executor_id) {
                return false;
            }
        }
        return true;
    }

    /**
     * Проверяет, может ли пользователь увидеть кнопку отказа от задания.
     *
     * @param int $userId ID пользователя
     * @param string $taskStatus статус задания
     * @param int|null $executorId ID исполнителя задания
     * @return bool true, если пользователь может увидеть кнопку отказа, false - в противном случае.
     */
    public static function isViewRefusalButton(int $userId, string $taskStatus, ?int $executorId): bool
    {
        return $userId === $executorId && $taskStatus === TaskBasic::STATUS_WORK;
    }

    /**
     * Проверяет, может ли пользователь увидеть кнопку завершения задания.
     *
     * @param int $userId ID пользователя
     * @param string $taskStatus статус задания
     * @param int|null $customerId ID заказчика задания
     * @return bool true, если пользователь может увидеть кнопку завершения, false - в противном случае.
     */
    public static function isViewCompletionButton(int $userId, string $taskStatus, ?int $customerId): bool
    {
        return $userId === $customerId && $taskStatus === TaskBasic::STATUS_WORK;
    }

    /**
     * Проверяет, может ли пользователь увидеть кнопку отмены задания.
     *
     * @param int $userId ID пользователя
     * @param string $taskStatus статус задания
     * @param int|null $customerId ID заказчика задания
     * @return bool true, если пользователь может увидеть кнопку отмены, false - в противном случае.
     */
    public static function isViewCancelButton(int $userId, string $taskStatus, ?int $customerId): bool
    {
        return $userId === $customerId && $taskStatus === TaskBasic::STATUS_NEW;
    }
}