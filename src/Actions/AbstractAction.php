<?php

namespace TaskForce\Actions;

use TaskForce\Models\Task;
abstract class AbstractAction
{
    abstract public static function getName();
    abstract public static function getTitle();
    abstract public function checkRight(Task $task, int $currentUserId);
}
