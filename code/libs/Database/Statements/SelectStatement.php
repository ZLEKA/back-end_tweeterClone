<?php

require_once(__DIR__ . '/QueryStatement.php');

class SelectStatement extends QueryStatement
{
    public function get() {
        $this->query .= "\n$this->clauses";
        $this->query .= ';';
        $pdoStatement = $this->pdo->prepare($this->query);
        $pdoStatement->execute();
        if ($this->withOrm()) {
            return $pdoStatement->fetchAll(PDO::FETCH_CLASS, $this->classMap);
        }
        return $pdoStatement->fetchAll(PDO::FETCH_OBJ);
    }
}
