<?php

require_once 'src/Task.php';

$strategy1 = new Task('new', 1, 2);
assert($strategy1->getNextStatus(Task::ACTION_CANCEL, Task::STATUS_NEW, 1) === Task::STATUS_CANCELLED);
assert($strategy1->getNextStatus(Task::ACTION_RESPOND, Task::STATUS_NEW, 2) === Task::STATUS_AT_WORK);
assert($strategy1->getNextStatus(Task::ACTION_REFUSE, Task::STATUS_AT_WORK, 1) === Task::STATUS_FAILED);
assert($strategy1->getNextStatus(Task::ACTION_DONE, Task::STATUS_AT_WORK, 2) === Task::STATUS_DONE);

$strategy2 = new Task('new', 1, 2);
assert($strategy2->getAvailableActions(Task::STATUS_NEW, 1) === [Task::ACTION_DONE, Task::ACTION_CANCEL]);
assert($strategy2->getAvailableActions(Task::STATUS_NEW, 2) === [Task::ACTION_RESPOND]);

$strategy3 = new Task('new', 1, 2);
assert($strategy3->getAvailableActions(Task::STATUS_AT_WORK, 1) === [Task::ACTION_DONE]);
assert($strategy3->getAvailableActions(Task::STATUS_AT_WORK, 2) === [Task::ACTION_REFUSE]);

$strategy4 = new Task('new', 1, 2);
$mapAction = $strategy4->getActionMap();
$mapStatus = $strategy4->getStatusMap();
