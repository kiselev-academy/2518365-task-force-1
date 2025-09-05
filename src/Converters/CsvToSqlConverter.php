<?php

declare(strict_types=1);

namespace TaskForce\Converters;

use SplFileObject;
use TaskForce\Exceptions\FileFormatException;
use TaskForce\Exceptions\SourceFileException;

class CsvToSqlConverter
{
    private string $fileName;
    private string $tableName;
    private array $columns;

    /**
     * @param string $fileName имя CSV-файла для чтения
     * @param string $tableName имя таблицы, в которую будут вставляться данные
     * @throws SourceFileException
     */
    public function __construct(string $fileName, string $tableName)
    {
        $this->fileName = $fileName;
        $this->tableName = $tableName;
        $this->readColumnHeading();
    }

    /**
     * @throws SourceFileException
     */
    private function readColumnHeading(): void
    {
        $file = new SplFileObject($this->fileName);
        $file->setFlags(SplFileObject::READ_CSV);

        $this->columns = $file->fgetcsv(',', '"', '\\');

        if (!$this->columns || empty(array_filter($this->columns))) {
            throw new SourceFileException("Файл не существует");
        }

        $this->columns = array_map(function ($column) {
            return preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $column);
        }, $this->columns);
    }

    /**
     * @throws FileFormatException
     */
    public function readTableAndConvert(): string
    {
        $file = new SplFileObject($this->fileName);
        $file->setFlags(SplFileObject::READ_CSV);

        $sql = '';

        $file->seek(1);

        while (!$file->eof()) {
            $data = $file->fgetcsv(',', '"', '\\');

            if ($data === false || (count($data) === 1 && $data[0] === null)) {
                continue;
            }

            if (count($data) !== count($this->columns)) {
                throw new FileFormatException("Заданы неверные заголовки столбцов");
            }

            $values = array_map(function ($value) {
                return is_numeric($value) ? $value : "'" . addslashes($value) . "'";
            }, $data);

            $sql .= sprintf(
                "INSERT INTO %s (%s) VALUES (%s);\n",
                $this->tableName,
                implode(',', $this->columns),
                implode(',', $values)
            );
        }

        return $sql;
    }

    /**
     * @throws FileFormatException
     */
    public function saveToFile(string $outputFile): void
    {
        $sql = $this->readTableAndConvert();

        $directory = dirname($outputFile);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($outputFile, $sql);
    }
}
