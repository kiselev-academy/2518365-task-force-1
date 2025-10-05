<?php

namespace TaskForce\Actions;

use TaskForce\Models\Task;

class RespondAction extends AbstractAction
{
    /**
     * Возвращает имя действия.
     *
     * @return string Имя действия.
     */
    public static function getName(): string
    {
        return 'respond';
    }

    /**
     * Возвращает название действия.
     *
     * @return string Название действия.
     */
    public static function getTitle(): string
    {
        return 'Откликнуться';
    }

    /**
     * Проверяет право на выполнение действия "Откликнуться".
     *
     * @param Task $task Объект задачи.
     * @param int $currentUserId ID текущего пользователя.
     * @return bool Возвращает true, если действие разрешено, иначе false.
     */
    public function checkRight(Task $task, int $currentUserId): bool
    {
        return
            $task->currentStatus === Task::STATUS_NEW && $currentUserId === $task->getIdExecutor();
    }
}
