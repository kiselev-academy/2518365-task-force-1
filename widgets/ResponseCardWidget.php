<?php

namespace app\widgets;

use app\models\Response;
use app\models\Task;
use app\models\User;
use app\services\UserView;
use yii\base\Widget;


class ResponseCardWidget extends Widget
{
    public User $user;
    public Task $task;
    public Response $response;

    public function run()
    {
        if (!UserView::isViewResponse($this->user->id, $this->task->customer_id, $this->response->executor_id)) {
            return '';
        }

        return $this->render('@app/views/widgets/response-card.php', [
            'user' => $this->user,
            'task' => $this->task,
            'response' => $this->response,
        ]);
    }
}