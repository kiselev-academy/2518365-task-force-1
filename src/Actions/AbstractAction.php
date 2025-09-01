<?php

declare(strict_types=1);

namespace TaskForce\Actions;

use TaskForce\Models\Task;
abstract class AbstractAction
{
    protected string $name;
    protected string $internalName;

    public function getName(): string
    {
        return $this->name;
    }

    public function getInternalName(): string
    {
        return $this->internalName;
    }

    abstract protected function isAvailable(Task $task, int $userId): bool;
}
