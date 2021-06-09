<?php

class QueryStatement
{

    protected $pdo;
    protected $query;
    protected $params;
    protected $clauses;
    protected $orm;
    protected $classMap;

    public function __construct(PDO $pdo, string $query, array $params = [])
    {
        $this->pdo = $pdo;
        $this->params = $params;
        $this->query = $query;
        $this->clauses = '';
        $this->orm = false;
    }

    public function where($col1, $exp, $col2)
    {
        $this->clauses .= empty($this->clauses) ? 'WHERE ' : 'AND ';
        $this->clauses .= "$col1 $exp $col2\n";
        return $this;
    }

    public function orWhere($col1, $exp, $col2)
    {
        $this->clauses .= empty($this->clauses) ? 'WHERE ' : 'OR ';
        $this->clauses .= "$col1 $exp $col2\n";
        return $this;
    }

    public function withOrm() {
        return $this->orm && $this->classMap != null;
    }

    public function orm(bool $enable = false, $classMap = null) {
        if (!empty($classMap)) {
            $this->classMap = $classMap;
        }
        $this->orm = $enable;
        return $this;
    }

}
