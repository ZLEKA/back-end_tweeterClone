<?php

require_once(__DIR__ . '/QueryStatement.php');

class UpdateStatement extends QueryStatement {
    public function commit(): bool {
        $this->query .= $this->clauses;
        $this->query .= ';';
        $pdoStatement = $this->pdo->prepare($this->query);
        return $pdoStatement->execute($this->params);
    }
}