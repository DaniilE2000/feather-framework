<?php

namespace application\components\native\core\base\interfaces;

/**
 * Interface providing the common database access object behaviour contract.
 * 
 * You can define a custom dao class by implementing ```IDatabaseAccessObject``` interface.
 */
interface IDatabaseAccessObject
{
    /** 
     * Assigns a select query to the DAO.
     * 
     * @param string|array $select The select query.
     * 
     * @return static ```$this```
     */
    public function select(string|array $select): static;

    /**
     * Assigns a from query to the DAO.
     * 
     * @param string $from The from query.
     * 
     * @return static ```$this```
     */
    public function from(string $from): static;

    /**
     * Assigns a where query to the DAO, as well as 
     * named parameters to prevent sql-injections.
     * 
     * @param string|array $where The where query.
     * @param array $params [optional] Parameters in form ```['key' => val]```.
     * 
     * @return static ```$this```
     */
    public function where(string|array $where, array $params = []): static;

    /**
     * Sets the limit parameter.
     * 
     * @param int $limit Limit value.
     * 
     * @return static ```$this```
     */
    public function limit(int $limit): static;

    /**
     * Sets the offset parameter.
     * 
     * @param int $offset Offset value.
     * 
     * @return static ```$this```
     */
    public function offset(int $offset): static;

    /**
     * Retrieves data from database according to the composed query.
     * 
     * @return array Database entries.
     */
    public function get(): array;
}

?>