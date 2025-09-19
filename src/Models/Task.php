<?php

namespace TaskForce\Models;

use Taskforce\Actions\AcceptAction;
use TaskForce\Actions\CancelAction;
use TaskForce\Actions\RefuseAction;
use TaskForce\Actions\RespondAction;
use TaskForce\Exceptions\ActionException;
use TaskForce\Exceptions\NotStatusException;
use TaskForce\Exceptions\StatusException;

class Task
{
    public const string STATUS_NEW = 'new';
    public const string STATUS_CANCELLED = 'cancelled';
    public const string STATUS_WORK = 'work';
    public const string STATUS_COMPLETED = 'completed';
    public const string STATUS_FAILED = 'failed';

    public const string ACTION_CANCEL = CancelAction::class;
    public const string ACTION_ACCEPT = AcceptAction::class;
    public const string ACTION_RESPOND = RespondAction::class;
    public const string ACTION_REFUSE = RefuseAction::class;

    /**
     * Функция для получения ID исполнителя и ID заказчика
     * @param string $currentStatus текущий статус задачи
     * @param int $customerId ID заказчика
     * @param int $executorId ID исполнителя
     * @throws StatusException Исключение
     */
    public function __construct(public int $customerId, public int $executorId, public string $currentStatus = Task::STATUS_NEW)
    {
        if (!array_key_exists($currentStatus, $this->getStatusMap())) {
            throw new StatusException('Неверный статус задания!');
        }
    }

    public function getIdCustomer(): int
    {
        return $this->customerId;
    }

    public function getIdExecutor(): int
    {
        return $this->executorId;
    }

    /**
     * Функция для возврата «карты» статусов
     * @return array возвращает массив с названием статусов
     */
    public function getStatusMap(): array
    {
        return [
            self::STATUS_NEW => 'Новое',
            self::STATUS_CANCELLED => 'Отменено',
            self::STATUS_WORK => 'В работе',
            self::STATUS_COMPLETED => 'Выполнено',
            self::STATUS_FAILED => 'Провалено',
        ];
    }

    /**
     * Функция для возврата «карты» действия
     * @return array возвращает массив с названием действий
     */
    public function getActionMap(): array
    {
        return [
            self::ACTION_CANCEL => CancelAction::getTitle(),
            self::ACTION_ACCEPT => AcceptAction::getTitle(),
            self::ACTION_RESPOND => RespondAction::getTitle(),
            self::ACTION_REFUSE => RefuseAction::getTitle(),
        ];
    }

    /**
     * Функция для получения статуса задания после выполнения указанного действия
     * @param string $action текущее действие задания
     * @return string возвращает статус задания
     * @throws ActionException Исключение
     */

    public function getNextStatus(string $action): string
    {
        if (!array_key_exists($action, $this->getActionMap())) {
            throw new ActionException('Нет доступных действий!');
        }
        return match ($action) {
            self::ACTION_CANCEL => self::STATUS_CANCELLED,
            self::ACTION_ACCEPT => self::STATUS_COMPLETED,
            self::ACTION_RESPOND => self::STATUS_WORK,
            self::ACTION_REFUSE => self::STATUS_FAILED,
        };
    }

    /**
     * Функция для получения доступных действий для указанного статуса задания
     * @param string $status текущий статус
     * @return array возвращает действие задания
     * @throws NotStatusException Исключение
     */
    public function getAvailableActions(string $status): array
    {
        if (!array_key_exists($status, $this->getStatusMap())) {
            throw new NotStatusException('Несуществующий статус задания!');
        }
        $actions = [
            self::STATUS_NEW => [self::ACTION_RESPOND, self::ACTION_CANCEL],
            self::STATUS_WORK => [self::ACTION_ACCEPT, self::ACTION_REFUSE],
        ];
        return $actions[$status] ?? [];
    }
}
