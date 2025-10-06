<?php

namespace TaskForce\Actions;

use TaskForce\Models\Task;

class RefuseAction extends AbstractAction
{
    /**
     * Возвращает имя действия.
     *
     * @return string Имя действия.
     */
    public static function getName(): string
    {
        return 'refuse';
    }

    /**
     * Возвращает название действия.
     *
     * @return string Название действия.
     */
    public static function getTitle(): string
    {
        return 'Отказаться';
    }

    /**
     * Проверяет на выполнение действия "Отказаться".
     *
     * @param Task $task Объект задачи.
     * @param int $currentUserId Идентификатор текущего пользователя.
     * @return bool Возвращает true, если действие разрешено, иначе false.
     */
    public function checkRight(Task $task, int $currentUserId): bool
    {
        return
            $task->currentStatus === Task::STATUS_WORK && $currentUserId === $task->getIdExecutor();
    }
}
