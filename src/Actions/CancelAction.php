<?php

namespace TaskForce\Actions;

use TaskForce\Models\Task;

class CancelAction extends AbstractAction
{
    /**
     * Возвращает имя действия.
     *
     * @return string Имя действия.
     */
    public static function getName(): string
    {
        return 'cancel';
    }

    /**
     * Возвращает название действия.
     *
     * @return string Название действия.
     */
    public static function getTitle(): string
    {
        return 'Отменить';
    }

    /**
     * Проверяет на выполнение действия "Отменить".
     *
     * @param Task $task Объект задачи.
     * @param int $currentUserId ID текущего пользователя.
     * @return bool Возвращает true, если действие разрешено, иначе false.
     */
    public function checkRight(Task $task, int $currentUserId): bool
    {
        return
            $task->currentStatus === Task::STATUS_NEW && $currentUserId === $task->getIdCustomer();
    }
}
