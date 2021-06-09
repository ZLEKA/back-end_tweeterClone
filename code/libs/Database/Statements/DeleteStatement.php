<?php

require_once(__DIR__ . '/QueryStatement.php');

class DeleteStatement extends QueryStatement {
    public function commit(): bool {
        $this->query .= $this->clauses;
        $this->query .= ';';
        $pdoStatement = $this->pdo->prepare($this->query, $this->params);
        return $pdoStatement->execute();
    }
}
