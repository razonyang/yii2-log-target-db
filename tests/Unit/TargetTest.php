<?php
namespace RazonYang\Yii2\Setting\Tests\Unit;

use yii\log\Logger;
use Codeception\Test\Unit;
use RazonYang\Yii2\Log\Db\Target;
use Yii;

class TargetTest extends Unit
{
    private function createTarget(): Target
    {
        return new Target([
            'logTable' => 'log',
        ]);
    }

    public function testGetRequestId(): void
    {
        $target = $this->createTarget();
        $method = new \ReflectionMethod(Target::class, 'getRequestId');
        $method->setAccessible(true);
        $this->assertEquals($this->getRequestId(), $method->invoke($target));
    }

    private function getRequestId(): string
    {
        return dechex($_SERVER['REQUEST_TIME_FLOAT'] * 1000000);
    }

    /**
     * @dataProvider dataProviderMessage
     */
    public function testExport(array ... $messages): void
    {
        $target = $this->createTarget();
        $target->messages = $messages + [];
        $target->export();

        foreach ($messages as $message) {
            list($text, $level, $category, $timestamp) = $message;
            $sql = 'SELECT message, level, category FROM log WHERE request_id = :request_id AND level = :level AND message = :message AND category = :category AND log_time = :log_time';
            $parameters = [
                ':request_id' => $this->getRequestId(),
                ':level' => $level,
                ':message' => $text,
                ':category' => $category,
                ':log_time' => $timestamp,
            ];

            $row = $target->db->createCommand($sql)->bindValues($parameters)->queryOne();
            $this->assertNotFalse($row);
        }
    }

    public function dataProviderMessage(): array
    {
        return [
            [
                ['error message', Logger::LEVEL_ERROR, 'error', microtime(true)],
                ['warning message', Logger::LEVEL_WARNING, 'warning', microtime(true)],
            ],
            [
                ['error2', Logger::LEVEL_ERROR, 'error', microtime(true)],
            ],
        ];
    }
}
