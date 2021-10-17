<?php

namespace application\components\native\core\base;

use application\components\native\core\base\interfaces\IDatabaseAccessObject;
use application\components\native\core\db\MySqlDBO;
use application\components\native\core\exceptions\DBException;

/**
 * Provides common functions for Feather Models.
 */
abstract class BaseModel extends BaseObject
{
    /** @var string|null $tableName A name of the table. */
    public string $tableName = '';
    /** @var IDatabaseAccessObject $dbo Object for accessing database. */
    private IDatabaseAccessObject $dbo;

    /** Resolving database type, connecting to specified database. */
    public function __construct() {
        ['type' => $dbType] = require 'application/components/custom/config/db.php';
        switch($dbType) {
            case 'mysql':
                $this->dbo = new MySqlDBO();
                break;
            default:
                throw new DBException("Undefined database type: $dbType");
        }
    }

    /** 
     * Method to access database object. 
     * Specifies ```from``` clause.
     * 
     * @return IDatabaseAccessObject
     */
    public function find()
    {
        // if tablename isn't specified, resolving to undercased classname.
        $table = $this->tableName ?: \mb_strtolower(\basename(static::class));

        return $this->dbo->from($table);
    }
}

?>
