<?php

namespace application\components\native\core\db;

use application\components\native\core\base\interfaces\IDatabaseAccessObject;
use application\components\native\core\exceptions\DBException;

/**
 * A DBO class for accessing mysql databases.
 */
class MySqlDBO implements IDatabaseAccessObject 
{
    /** @var \PDO $db A PDO object accessing mysql database. */
    protected \PDO $db;

    /** Query parameters. */
    protected string $select = '*';
    protected string $where = '';
    protected array $params = [];
    protected string $from = '';
    protected int $limit = -1;
    protected int $offset = 0;

    /**
     * Database connecting operation.
     */
    public function __construct()
    {
        $config = require 'application/components/custom/config/db.php';
        $dsn = 'mysql:host=' . $config['host'] . ';dbname=' . $config['name'];
        try {
        $this->db = new \PDO($dsn, $config['user'], $config['password']);
        } catch(\Throwable $e) {
            throw new DBException($e->getMessage, 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function select(string|array $select): static
    {
        if (is_array($select)) {
            $this->select = '';
            foreach ($select as $selectKey => $selectVal) {
                if (is_int($selectKey)) {
                    $this->select .= $selectVal . ', ';
                } else {
                    $this->select .= "$selectKey as $selectVal, ";
                }
                $this->select = trim($this->select, ', ');
            }
        } else {
            $this->select = $select;
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function from(string $from): static
    {
        $this->from = $from;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function limit(int $limit): static
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function offset(int $offset): static
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function where(string|array $where, array $params = []): static
    {
        $this->params = \array_merge($this->params, $params);

        if (\is_string($where)) {
            $this->where = $where;
        } else {
            foreach ($where as $key => $val) {
                $this->where .= $this->parseWhere($key, $val) . ' AND ';
            }

            $this->where = substr($this->where, 0, strlen($this->where) - 5);
        }

        return $this;
    }

    /**
     * Used to parse passed where-clause if it is an array.
     * 
     * @param mixed $key An arrays element key.
     * @param mixed $val An arrays element value.
     * 
     * @return string A processed part of the where-clause.
     * 
     * @throws DBException If input is ill-formed.
     */
    private function parseWhere(mixed $key, mixed $val): string
    {
        $whereString = '';
        if (!\is_int($key) && !\is_array($val)) {
            $whereString .= "$key = {$this->quotifyStrings($val)}";
        } else if (\is_array($val) && \count($val) === 3) {
            if (\is_array($val[1]) && \count($val[1]) === 3) {
                $whereString .= '(' . $this->parseWhere(0, $val[1]) . ')';
            } else {
                $whereString .= $val[1];
            }
            $whereString .= ' ' . \mb_strtoupper($val[0]) . ' ';
            if (\is_array($val[2])) {
                if (\count($val[2]) === 3) {
                    $whereString .= '(' . $this->parseWhere(0, $val[2]) . ')';
                } else {
                    $whereString .= '(' . \join(', ', $this->quotifyStrings($val[2])) . ')';
                }
            } else {
                $whereString .= $this->quotifyStrings($val[2]);
            }
        } else {
            throw new DBException('Error: ill-formed where-clause input -- ' . print_r($key, true) . ' -> ' . print_r($val, true));
        }
        return $whereString;
    }

    /**
     * Used to surround values with quotes if they are strings.
     * 
     * @param mixed $val A value to check and quotify.
     * 
     * @return mixed ```$val```, quotified if string.
     */
    private function quotifyStrings(mixed $val): mixed
    {
        if (\is_array($val)) {
            return array_map(fn($item) => (\is_string($item) && $item[0] !== ':') ? '"' . $item . '"' : $item, $val);
        }

        if (\is_string($val) && $val[0] !== ':') {
            return '"' . $val . '"';
        }

        return $val;
    }

    /**
     * {@inheritdoc}
     */
    public function get(): array
    {
        $queryCommand = $this->composeQuery();
        $this->reset();

        return $queryCommand->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Composes a query object from class fields.
     * 
     * @return \PDOStatement A php database object.
     * 
     * @throws DBException If query isn't valid.
     */
    private function composeQuery(): \PDOStatement
    {
        if (!$this->queryIsValid()) {
            throw new DBException('Error: ill-composed mysql query.');
        }

        $queryString = "SELECT {$this->select} FROM {$this->from}";

        if (strlen($this->where) > 0) {
            $queryString .= " WHERE {$this->where}"; 
        }

        if ($this->limit > 0) {
            if ($this->offset > 0) {
                $queryString .= " LIMIT {$this->offset}, {$this->limit}";
            } else {
                $queryString .= " LIMIT {$this->limit}";
            }
        } else if ($this->offset > 0) {
            $queryString .= " LIMIT {$this->offset}, " . PHP_INT_MAX;
        }
        
        if (!empty($this->params)) {
            $queryCommand = $this->db->prepare($queryString);

            foreach($this->params as $pKey => $pVal) {
                $queryCommand->bindParam(':' . $pKey, $pVal);
            }

            $queryCommand->execute();
        } else {
            $queryCommand = $this->db->query($queryString);
        }
    
        return $queryCommand;
    }

    /**
     * Checks whether class fields forms a valid query.
     * 
     * @return bool ```true``` if query is valid, ```false``` otherwise.
     */
    private function queryIsValid(): bool
    {
        return (strlen($this->select) > 0
        && strlen($this->from > 0));
    }

    /**
     * Resets DAO class fields to their initial state.
     * 
     * @return void
     */
    private function reset(): void
    {
        $this->select = '*';
        $this->where = '';
        $this->params = [];
        $this->from = '';
        $this->limit = -1;
        $this->offset = 0;
    }
}

?>