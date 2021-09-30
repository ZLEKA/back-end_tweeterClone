<?php

const CROSS ='CROSS';
const FULL  ='FULL';
const RIGHT ='RIGHT';
const LEFT  ='LEFT';
CONST INNER ='INNER';

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

    # inject a raw "where" clause into your query
    public function whereRaw($str, $and=false)
    {
        if($and)
            $this->clauses .= "AND $str\n";
        else
            $this->clauses .= "WHERE $str\n";
        return $this;
    }

    public function where($col1, $exp, $col2, $and=false)
    {
        if($and)
            $this->clauses .= "AND $col1 $exp $col2\n";
        else
            $this->clauses .= "WHERE $col1 $exp $col2\n";
        return $this;
    }

    public function orWhere($col1, $exp, $col2)
    {
        $this->clauses .= empty($this->clauses) ? "WHERE $col1 $exp $col2\n" : "OR $col1 $exp $col2\n";
        return $this;
    }

    public function orWhereRaw($str)
    {
        $this->clauses .= empty($this->clauses) ? "WHERE $str\n" :"OR $str\n";
        return $this;
    }

    public function whereBetween($col, $value1, $value2, $and=false)
    {
        if($and)
            $this->clauses .= "AND ($col BETWEEN $value1 AND $value2)\n";
        else
            $this->clauses .= "WHERE ($col BETWEEN $value1 AND $value2)\n";
        return $this;
    }

    public function whereNotBetween($col, $value1, $value2, $and=false)
    {
        if($and)
            $this->clauses .= "AND ($col NOT BETWEEN $value1 AND $value2)\n";
        else
            $this->clauses .= "WHERE ($col NOT BETWEEN $value1 AND $value2)\n";
        return $this;
    }

    public function whereIn($col, $values, $and=false)
    {
        $values_to_str = implode(',', $values);
        if($and)
            $this->clauses .= "AND $col IN ($values_to_str)\n";
        else
            $this->clauses .= "WHERE $col IN ($values_to_str)\n";
        return $this;
    }

    public function whereNotIn($col, $values, $and=false)
    {
        $values_to_str = implode(',', $values);
        if($and)
            $this->clauses .= "AND $col NOT IN ($values_to_str)\n";
        else
            $this->clauses .= "WHERE $col NOT IN ($values_to_str)\n";
        return $this;
    }

    public function whereNull($col, $and=false)
    {
        if($and)
            $this->clauses .= "AND $col IS NULL\n";
        else
            $this->clauses .= "WHERE $col IS NULL\n";
        return $this;
    }

    public function whereNotNull($col, $and=false)
    {
        if($and)
            $this->clauses .= "AND $col NOT IS NULL\n";
        else
            $this->clauses .= "WHERE $col NOT IS NULL\n";
        return $this;
    }

    /**
     * Overloading method whereColumn
     */
    public function whereColumn() {
        $args = func_get_args();
        
        switch (count($args)) {
                      
            // If there is only one argument
            // array of conditions
            case 1:
                if(!isset($args[0]) || !is_array($args[0]))
                    break;
                    
                $count = 0;
                foreach($args[0] as $exp){

                    if(count($exp)!=3)
                        break;

                    if($count==0)
                        $this->clauses .= "WHERE $exp[0] $exp[1] $exp[2]\n";
                    else
                        $this->clauses .= "AND $exp[0] $exp[1] $exp[2]\n";

                    $count = -1;
                }
                break;

            // If two arguments equality check between two columns
            case 2:
                if(
                    !isset($args[0]) || 
                    !isset($args[1]) || 
                    !is_string($args[0]) || 
                    !is_string($args[1])
                ) break;

                $this->clauses .= "WHERE $args[0] = $args[1]\n";
                break;
                        
            // If three arguments classical exp like a > b
            case 3:
                if(
                    !isset($args[0]) || 
                    !isset($args[1]) ||
                    !isset($args[2]) || 
                    !is_string($args[0]) || 
                    !is_string($args[1]) ||
                    !is_string($args[2]) 
                ) break;

                $this->clauses .= "WHERE $args[0] $args[1] $args[2]\n";
                break;
        }

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

    public function innerJoin($modelClassName, string $col1, string $exp, string $col2, $and=false){
        return $this->joinWith($modelClassName::getTableName(), $col1, $exp, $col2,INNER, $and);
    }

    public function rightJoin($modelClassName, string $col1, string $exp, string $col2, $and=false){
        return $this->joinWith($modelClassName::getTableName(), $col1, $exp, $col2,RIGHT, $and);
    }

    public function leftJoin($modelClassName, string $col1, string $exp, string $col2, $and=false){
        return $this->joinWith($modelClassName::getTableName(), $col1j, $expj, $col2j,LEFT, $and);
    }

    public function fullJoin($modelClassName, string $col1, string $exp, string $col2, $and=false){
        return $this->joinWith($modelClassName::getTableName(), $col1, $exp, $col2, FULL, $and);
    }

    public function crossJoin($modelClassName, $and=false){
        return $this->joinWith($modelClassName::getTableName(), null, null, null, CROSS, $and);
    }

    private function joinWith(string $table, $col1, $exp, $col2, $joinType, $and=false)
    {
        $joinTypes = array(INNER, RIGHT, LEFT, FULL, CROSS);

        if(in_array($joinType, $joinTypes)){
            if($and)
                $this->clauses .= "AND ";

            $this->clauses .= "$joinType JOIN $table ";

            if($joinType!==CROSS)
                $this->clauses .= "ON $col1 $exp $table.$col2 \n";
        }

        return $this;
    }
}
