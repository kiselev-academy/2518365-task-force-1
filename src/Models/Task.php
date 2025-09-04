<?php

declare(strict_types=1);

namespace TaskForce\Models;

use TaskForce\Actions\CancelAction;
use TaskForce\Actions\CompleteAction;
use TaskForce\Actions\RefuseAction;
use TaskForce\Actions\RespondAction;
use TaskForce\Actions\StartAction;
use TaskForce\Exceptions\ActionException;
use TaskForce\Exceptions\RoleException;
use TaskForce\Exceptions\StatusException;

class Task
{
    public const STATUS_NEW = 'new';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_WORK = 'work';
    public const STATUS_DONE = 'done';
    public const STATUS_FAILED = 'failed';

    /**
     * Функция для получения ID исполнителя и ID заказчика
     * @param string $status текущий статус задачи
     * @param int $customerId ID заказчика
     * @param ?int $executorId ID исполнителя
     * @throws StatusException Исключение если неверный статус
     * @throws RoleException Исключение если неверная роль
     */
    public function __construct(public string $status, public int $customerId, public ?int $executorId)
    {
        if ($status === self::STATUS_NEW && $executorId !== null) {
            throw new StatusException('Неверный статус задания');
        }
        if (in_array($status, $this->getStatusForExecutor(), true) && $executorId === null) {
            throw new RoleException('Неверная роль для данного статуса');
        }
    }

    /**
     * Функция для возврата «карты» статусов для исполнителя
     * @return array возвращает массив с названиями статусов
     */
    public function getStatusForExecutor(): array
    {
        return [
            self::STATUS_DONE,
            self::STATUS_WORK,
            self::STATUS_FAILED
        ];
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
            self::STATUS_DONE => 'Выполнено',
            self::STATUS_FAILED => 'Провалено'
        ];
    }

    /**
     * Функция для возврата «карты» действия
     * @return array возвращает массив с названием действий
     */
    public function getActionMap(): array
    {
        return [
            CancelAction::class => 'Отменить',
            CompleteAction::class => 'Выполнено',
            RespondAction::class => 'Откликнуться',
            RefuseAction::class => 'Отказаться',
            StartAction::class => 'Начать'
        ];
    }

    /**
     * Функция для получения статуса задания после выполнения указанного действия
     * @param string $action текущее действие задания
     * @return string возвращает статус задания
     * @throws ActionException Исключение если неверное действие
     */

    public function getNextStatus(string $action): string
    {
        if (!array_key_exists($action, $this->getActionMap())) {
            throw new ActionException('Неверное действие для данного статуса');
        }
        return match ($action) {
            CancelAction::class => self::STATUS_CANCELLED,
            CompleteAction::class => self::STATUS_DONE,
            RespondAction::class => self::STATUS_WORK,
            RefuseAction::class => self::STATUS_FAILED,
            StartAction::class => self::STATUS_NEW,
        };
    }

    /**
     * Функция для получения доступных действий для указанного статуса задания
     * @param string $status текущий статус
     * @param int $userId id исполнителя/заказчика
     * @return array возвращает статус задания
     */
    public function getAvailableActions(string $status, int $userId): array
    {
        if ($status === self::STATUS_NEW && $userId === $this->customerId) {
            return [new StartAction(), new CancelAction()];
        }
        if ($status === self::STATUS_NEW && $userId === $this->executorId) {
            return [new RespondAction()];
        }
        if ($status === self::STATUS_WORK && $userId === $this->customerId) {
            return [new CompleteAction()];
        }
        if ($status === self::STATUS_WORK && $userId === $this->executorId) {
            return [new RefuseAction()];
        }

        return [];
    }
}
