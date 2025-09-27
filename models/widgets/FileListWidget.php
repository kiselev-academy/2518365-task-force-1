<?php

namespace app\models\widgets;

use app\models\File;
use yii\base\Widget;

class FileListWidget extends Widget
{
    /**
     * @var File[] Массив моделей File
     */

    public array $files = [];

    public function run()
    {
        if (empty($this->files)) {
            return '';
        }

        return $this->render('@app/views/widgets/file-list.php', [
            'files' => $this->files,
        ]);
    }
}