<?php

namespace TaskForce\Actions;

use TaskForce\Models\Task;

abstract class AbstractAction
{
    /**
     * Возвращает имя действия.
     *
     * @return string Имя действия.
     */
    abstract public static function getName(): string;

    /**
     * Возвращает название действия.
     *
     * @return string Название действия.
     */
    abstract public static function getTitle(): string;

    /**
     * Проверяет на выполнение действия.
     *
     * @param Task $task Объект задачи.
     * @param int $currentUserId ID текущего пользователя.
     * @return bool Возвращает true, если действие разрешено, иначе false.
     */
    abstract public function checkRight(Task $task, int $currentUserId): bool;
}
