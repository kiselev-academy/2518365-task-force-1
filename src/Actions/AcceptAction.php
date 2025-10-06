<?php

namespace Taskforce\Actions;

use Taskforce\Models\Task;

class AcceptAction extends AbstractAction
{
    /**
     * Возвращает имя действия.
     *
     * @return string Имя действия.
     */
    public static function getName(): string
    {
        return 'accept';
    }

    /**
     * Возвращает название действия.
     *
     * @return string Название действия.
     */
    public static function getTitle(): string
    {
        return 'Принять';
    }

    /**
     * Проверяет на выполнение действия "Принять".
     *
     * @param Task $task Объект задачи.
     * @param int $currentUserId ID текущего пользователя.
     * @return bool Возвращает true, если действие разрешено, иначе false.
     */
    public function checkRight(Task $task, int $currentUserId): bool
    {
        return
            $task->currentStatus === Task::STATUS_WORK && $currentUserId === $task->getIdCustomer();
    }
}