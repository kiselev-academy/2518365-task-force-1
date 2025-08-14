<?php

declare(strict_types=1);

use TaskForce\Models\Task;
use TaskForce\Enums\Status;
use TaskForce\Enums\Action;

require_once __DIR__ . '/vendor/autoload.php';

$strategy1 = new Task(Status::New, 1, 1);
assert($strategy1->getNextStatus(Action::Cancel->value, Status::New) === Status::Cancelled);
assert($strategy1->getNextStatus(Action::Respond->value, Status::New) === Status::Work);
assert($strategy1->getNextStatus(Action::Cancel->value, Status::Work) === Status::Failed);
assert($strategy1->getNextStatus(Action::Done->value, Status::Work) === Status::Done);

$strategy2 = new Task(Status::New, 1, 1);
assert($strategy2->getAvailableActions(Status::New) === [Action::Respond, Action::Cancel]);
assert($strategy2->getAvailableActions(Status::Work) === [Action::Done, Action::Cancel]);

