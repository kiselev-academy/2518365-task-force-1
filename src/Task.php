<?php

class Task
{
    const STATUS_NEW = 'new';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_AT_WORK = 'work';
    const STATUS_DONE = 'done';
    const STATUS_FAILED = 'failed';

    const ACTION_CANCEL = 'cancel';
    const ACTION_DONE = 'get done';

    const ACTION_START = 'start';
    const ACTION_RESPOND = 'respond';
    const ACTION_REFUSE = 'refuse';

    public string $status;
    public int $customerId;
    public int $executorId;

    /** Функция для получения ID исполнителя и ID заказчика
     * @param string $status текущий статус задачи
     * @param int $customerId ID заказчика
     * @param int $executorId ID исполнителя
     */
    public function __construct(string $status, int $customerId, int $executorId)
    {
        $this->status = $status;
        $this->customerId = $customerId;
        $this->executorId = $executorId;
    }

    /** Функция для возврата «карты» статусов
     * @return string[] возвращает массив с названием статусов
     */
    public function getStatusMap(): array
    {
        return [
            self::STATUS_NEW => 'Новое',
            self::STATUS_CANCELLED => 'Отменено',
            self::STATUS_AT_WORK => 'В работе',
            self::STATUS_DONE => 'Выполнено',
            self::STATUS_FAILED => 'Провалено'
        ];
    }

    /** Функция для возврата «карты» действия
     * @return string[] возвращает массив с названием действий
     */
    public function getActionMap(): array
    {
        return [
            self::ACTION_START => 'Начать',
            self::ACTION_CANCEL => 'Отменить',
            self::ACTION_DONE => 'Выполнено',
            self::ACTION_RESPOND => 'Откликнуться',
            self::ACTION_REFUSE => 'Отказаться'
        ];
    }

    /** Функция для получения доступных действий для указанного статуса задания
     * @param string $status текущий статус задания
     * @param int $userCurrentId ID исполнителя/заказчика
     * @return array возвращает статус задания в зависимости от роли исполнителя/заказчика
     */
    public function getAvailableActions(string $status, int $userCurrentId): array
    {
        switch ($status) {
            case self::STATUS_NEW:
                if ($userCurrentId === $this->customerId) {
                    return [self::ACTION_START, self::ACTION_CANCEL];
                } elseif ($userCurrentId === $this->executorId) {
                    return [self::ACTION_RESPOND];
                }
                break;

            case self::STATUS_AT_WORK:
                if ($userCurrentId === $this->customerId) {
                    return [self::ACTION_DONE];
                } elseif ($userCurrentId === $this->executorId) {
                    return [self::ACTION_REFUSE];
                }
                break;
        }
                return [];
    }

    /** Функция для получения статуса задания после выполнения указанного действия
     * @param string $action текущее действие задания
     * @param string $currentStatus текущий статус задания
     * @param int $userCurrentId ID исполнителя/заказчика
     * @return string|null возвращает статус задания в зависимости от роли исполнителя/заказчика
     */
    public function getNextStatus(string $action, string $currentStatus, int $userCurrentId): ?string
    {
        if ($action === self::ACTION_CANCEL && $currentStatus === self::STATUS_NEW && $userCurrentId === $this->customerId) {
            return self::STATUS_CANCELLED;
        }
        if ($action === self::ACTION_RESPOND && $currentStatus === self::STATUS_NEW && $userCurrentId === $this->executorId) {
            return self::STATUS_AT_WORK;
        }
        if ($action === self::ACTION_DONE && $currentStatus === self::STATUS_AT_WORK && $userCurrentId === $this->customerId) {
            return self::STATUS_DONE;
        }
        if ($action === self::ACTION_REFUSE && $currentStatus === self::STATUS_AT_WORK && $userCurrentId === $this->executorId) {
            return self::STATUS_FAILED;
        }
        return null;
    }
}
