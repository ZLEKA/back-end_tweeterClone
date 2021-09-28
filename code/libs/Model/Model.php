<?php

require_once(__DIR__ . '/../Database/Database.php');

abstract class Model
{
    private static $db;

    public function hasOne($modelClassName) {
        spl_autoload_register( function($class) {
            require __DIR__."/../../app/Models/".Tools::pascal_case_to_snake_case($class) . '.php';
        });

        if (class_exists($modelClassName, true)) {
            $fnFirst = new ReflectionMethod($modelClassName, 'first');
            $fnGetTableName = new ReflectionMethod($modelClassName, 'getTableName');
            $fkField = $fnGetTableName->invoke(null) . '_id';
            return $fnFirst->invoke(null, 'id', $this->{$fkField});
        }
        return null;
    }

    public function hasMany($modelClassName): array {
        spl_autoload_register( function($class) {
            require __DIR__."/../../app/Models/".Tools::pascal_case_to_snake_case($class) . '.php';
        });

        if (class_exists($modelClassName, true)) {
            $staticMethod = new ReflectionMethod($modelClassName, 'find');
            $fk = self::getTableName() . '_id';
            return $staticMethod->invoke(null, $fk, $this->id);
        }
        return [];
    }

    public function update($vars): bool {
        self::checkConnection();
        unset($vars['id']);
        return self::$db->update(self::getTableName(), $vars)
            ->where('id', '=', $this->id)
            ->commit();
    }

    public function save(): bool {
        self::checkConnection();
        $vars = get_object_vars($this);
        unset($vars['id']);
        return self::$db->update(self::getTableName(), $vars)
            ->where('id', '=', $this->id)
            ->commit();
    }

    public function delete() {
        self::destroy('id', '=', $this->id);
    }

    public static function destroy($col1, $exp, $col2): bool {
        self::checkConnection();
        return self::$db->deleteFrom(self::getTableName())
            ->where($col1, $exp, $col2)
            ->commit();
    }

    public static function first($col1, $col2) {
        return self::find($col1, $col2)[0] ?? null;
    }

    public static function find($col1, $col2) {
        self::checkConnection();
        return self::where($col1, '=', $col2)
                ->orm(true, get_called_class())
                ->get();
    }

    public static function all() {
        self::checkConnection();
        return self::$db->selectFrom(self::getTableName())
            ->orm(true, get_called_class())
            ->get();
    }

    public static function where($col1, $exp, $col2) {
        self::checkConnection();
        return self::$db->selectFrom(self::getTableName())
            ->orm(true, get_called_class())
<<<<<<< HEAD
            ->where($col1, $exp, $col2);
=======
            ->andWhere($col1, $exp, $col2);
>>>>>>> 11fe357... Fixed join methods
    }

    public static function create(array $data) {
        self::checkConnection();
        $lastId = self::$db->insertInto(self::getTableName(), $data)
            ->orm(true, get_called_class())
            ->commit();
        if ($lastId) {
            return self::first('id', $lastId);
        }
        return null;
    }

    public function select(array $columns = []){
        self::checkConnection();
        return self::$db->selectFrom(self::getTableName(), $columns);
    }

    public static function getTableName() {
        return Tools::pascal_case_to_snake_case(get_called_class());
    }

    public static function configConnection($host, $name, $user, $password) {
        if (self::$db == null) {
            $config = new DatabaseConfig($host, $name, $user, $password);
            self::$db = Database::with($config);
        }
    }

    private static function checkConnection() {
        if (self::$db == null) {
            throw new \Exception('Database config not set');
        }
    }
}
