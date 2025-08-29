<?php

declare(strict_types=1);

use TaskForce\Actions\CancelAction;
use TaskForce\Actions\CompleteAction;
use TaskForce\Actions\RefuseAction;
use TaskForce\Actions\RespondAction;
use TaskForce\Actions\StartAction;
use TaskForce\Models\Task;

require_once __DIR__ . '/vendor/autoload.php';

$status = 'new';
$customerId = 1;
$executorId = 2;

$strategy1 = new Task($status, $customerId, $executorId);
assert($strategy1->getNextStatus(CancelAction::class) === Task::STATUS_CANCELLED);
assert($strategy1->getNextStatus(RespondAction::class) === Task::STATUS_WORK);
assert($strategy1->getNextStatus(RefuseAction::class) === Task::STATUS_FAILED);
assert($strategy1->getNextStatus(CompleteAction::class) === Task::STATUS_DONE);

$strategy2 = new Task($status, $customerId, $executorId);
assert($strategy2->getAvailableActions(Task::STATUS_NEW, 2) === [new StartAction(), new CancelAction()]);
assert($strategy2->getAvailableActions(Task::STATUS_NEW, 1) === [new RespondAction()]);

$strategy3 = new Task($status, $customerId, $executorId);
assert($strategy3->getAvailableActions(Task::STATUS_WORK, 2) === [new CompleteAction()]);
assert($strategy3->getAvailableActions(Task::STATUS_WORK, 1) === [new RefuseAction()]);
