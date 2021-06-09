<?php

require_once(__DIR__ . '/QueryStatement.php');

class InsertStatement extends QueryStatement {
    public function commit() {
        $this->query .= ';';
        $pdoStatement = $this->pdo->prepare($this->query);
        if ($this->withOrm()) {
            $pdoStatement->execute($this->params);
            return $this->pdo->lastInsertId();
        }
        return $pdoStatement->execute($this->params);
    }
}
