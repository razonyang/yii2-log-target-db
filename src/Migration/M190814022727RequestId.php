<?php
namespace RazonYang\Yii2\Log\Db\Migration;

use yii\db\Migration;
use RazonYang\Yii2\Log\Db\Target;
use Yii;

/**
 * Class M190814022727RequestId
 */
class M190814022727RequestId extends Migration
{
    /**
     * @var DbTarget[] Targets to create log table for
     */
    private $dbTargets = [];

    /**
     * @throws InvalidConfigException
     * @return DbTarget[]
     */
    protected function getDbTargets()
    {
        if ($this->dbTargets === []) {
            $log = Yii::$app->getLog();

            $usedTargets = [];
            foreach ($log->targets as $target) {
                if ($target instanceof Target) {
                    $currentTarget = [
                        $target->db,
                        $target->logTable,
                    ];
                    if (!in_array($currentTarget, $usedTargets, true)) {
                        // do not create same table twice
                        $usedTargets[] = $currentTarget;
                        $this->dbTargets[] = $target;
                    }
                }
            }

            if ($this->dbTargets === []) {
                throw new InvalidConfigException('You should configure "log" component to use one or more database targets before executing this migration.');
            }
        }

        return $this->dbTargets;
    }

    public function up()
    {
        foreach ($this->getDbTargets() as $target) {
            $this->db = $target->db;
            $this->addColumn($target->logTable, 'request_id', $this->char(13)->notNull()->defaultValue('')->after('id'));
            $this->createIndex('idx_request_id', $target->logTable, 'request_id');
        }
    }

    public function down()
    {
        foreach ($this->getDbTargets() as $target) {
            $this->db = $target->db;

            $this->dropColumn($target->logTable, 'request_id');
        }
    }
}
