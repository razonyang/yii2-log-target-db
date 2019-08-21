<?php
namespace RazonYang\Yii2\Log\Db;

use yii\helpers\VarDumper;
use yii\log\DbTarget as BaseTarget;
use yii\log\LogRuntimeException;
use Yii;

class Target extends BaseTarget
{
    public function export()
    {
        if ($this->db->getTransaction()) {
            // create new database connection, if there is an open transaction
            // to ensure insert statement is not affected by a rollback
            $this->db = clone $this->db;
        }

        $tableName = $this->db->quoteTableName($this->logTable);
        $sql = "INSERT INTO $tableName ([[request_id]], [[level]], [[category]], [[log_time]], [[prefix]], [[message]])
                VALUES (:request_id, :level, :category, :log_time, :prefix, :message)";
        $command = $this->db->createCommand($sql);
        foreach ($this->messages as $message) {
            list($text, $level, $category, $timestamp) = $message;
            if (!is_string($text)) {
                // exceptions may not be serializable if in the call stack somewhere is a Closure
                if ($text instanceof \Throwable || $text instanceof \Exception) {
                    $text = (string) $text;
                } else {
                    $text = VarDumper::export($text);
                }
            }
            if ($command->bindValues([
                    ':request_id' => $this->getRequestId(),
                    ':level' => $level,
                    ':category' => $category,
                    ':log_time' => $timestamp,
                    ':prefix' => $this->getMessagePrefix($message),
                    ':message' => $text,
                ])->execute() > 0) {
                continue;
            }
            throw new LogRuntimeException('Unable to export log through database!');
        }
    }

    protected $requestId;

    protected function getRequestId(): string
    {
        if ($this->requestId === null) {
            $this->requestId = dechex($_SERVER['REQUEST_TIME_FLOAT'] * 1000000);
        }

        return $this->requestId;
    }
}
