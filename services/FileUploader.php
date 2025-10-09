<?php

namespace app\services;

use Yii;
use yii\web\UploadedFile;

class FileUploader
{
    /**
     * Загружает файл и возвращает относительный путь.
     *
     * @param UploadedFile $file Загруженный файл.
     * @param string $folder Папка для сохранения.
     * @param string|null $filename Имя файла, если не указано — сгенерируется.
     * @return string|null Возвращает путь к файлу или null при ошибке.
     */
    public function upload(UploadedFile $file, string $folder = 'uploads', ?string $filename = null): ?string
    {
        $folderPath = Yii::getAlias('@webroot/' . $folder);
        if (!is_dir($folderPath)) {
            if (!mkdir($folderPath, 0777, true)) {
                return null;
            }
        }
        $ext = $file->getExtension();
        $name = $filename ?? uniqid('upload') . '.' . $ext;
        $filePath = $folder . '/' . $name;
        $fullPath = $folderPath . '/' . $name;

        if ($file->saveAs($fullPath)) {
            return '/' . $filePath;
        }
        return null;
    }
}