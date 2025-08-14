<?php

declare(strict_types=1);

namespace TaskForce\Models;

use TaskForce\Enums\Action;
use TaskForce\Enums\Status;

class Task
{

    protected Status $currentStatus = Status::New;
    protected int $customerId;
    protected int $executorId;

    /** Функция для получения ID исполнителя и ID заказчика
     * @param Status $currentStatus текущий статус задачи
     * @param int $customerId ID заказчика
     * @param int $executorId ID исполнителя
     */
    public function __construct(Status $currentStatus, int $customerId, int $executorId)
    {
        $this->currentStatus = $currentStatus;
        $this->customerId = $customerId;
        $this->executorId = $executorId;
    }


    /** Функция для получения статуса задания после выполнения указанного действия
     * @param string $action текущее действие задания
     * @param Status $currentStatus текущий статус
     * @return Status|null возвращает статус задания
     */

    public function getNextStatus(string $action, Status $currentStatus): ?Status
    {
        $transitions = [
            Status::New->value => [
                Action::Respond->value => Status::Work,
                Action::Cancel->value => Status::Cancelled
            ],
            Status::Work->value => [
                Action::Done->value => Status::Done,
                Action::Cancel->value => Status::Failed
            ]
        ];

        return $transitions[$currentStatus->value][$action] ?? null;
    }

    /** Функция для получения доступных действий для указанного статуса задания
     * @return array возвращает статус задания
     */
    public function getAvailableActions(Status $status): array
    {
        $actions = [
            Status::New->value => [Action::Respond, Action::Cancel],
            Status::Cancelled->value => [],
            Status::Work->value => [Action::Done, Action::Cancel],
            Status::Done->value => [],
            Status::Failed->value => []
        ];

        return $actions[$status->value] ?? [];
    }
}
