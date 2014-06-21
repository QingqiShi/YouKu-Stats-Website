<?php

class DB {
    private static $_instance = null;
    private $_pdo, 
            $_query, 
            $_error = false, 
            $_results, 
            $_count = 0;

    // Constructor. Use PHP Data Objects extension to create a database connection
    private function __construct() {
        try {
            $this->_pdo = new PDO(
                'mysql:host=' . Config::get('mysql/host') . ';dbname=' . Config::get('mysql/db') . ';charset=utf8',
                Config::get('mysql/username'),
                Config::get('mysql/password'));
        } catch (PDOException $e) {
            die("致命错误：无法创建数据库连接。");
        }
    }

    // Singleton pattern which allows a Glabol access point to a single instance of the database connection
    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new DB();
        }
        return self::$_instance;
    }

    // Apply a query string to the database, binding any parameters if needed
    public function query($query_str, $params = array()) {
        $this->_error = false;
        if ($this->_query = $this->_pdo->prepare($query_str)) {
            if (count($params)) {
                $x = 1;
                foreach ($params as $param) {
                    $this->_query->bindValue($x, $param);
                    $x++;
                }
            }
            if ($this->_query->execute()) {
                $this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
                $this->_count = $this->_query->rowCount();
            } else {
                $this->_error = true;
            }
        } else {
            $this->_error = true;
        }

        return $this;
    }

    // Apply an action such as SELECT OR DELETE on the database to a specific table, also using the where clause
    private function action($action, $table, $where = array()) {
        if (count($where) === 3) {
            $operators = array('=', '>', '<', '>=', '<=');

            $field      = $where[0];
            $operator   = $where[1];
            $value      = $where[2];

            if (in_array($operator, $operators)) {
                $sql = "{$action} * FROM {$table} WHERE {$field} {$operator} ?";
                $this->query($sql, array($value));
            }

            return $this;
        }

        return false;
    }

    public function get($table, $where) {
        return $this->action('SELECT', $table, $where);
    }

    public function delete($table, $where) {
        return $this->action('DELETE', $table, $where);
    }

    public function insert($table, $fields = array()) {
        $keys = array_keys($fields);
        $values = '';
        $x = count($fields);

        foreach ($fields as $field) {
            $values .= '?';
            if ($x > 1) {
                $values .= ', ';
            }
            $x--;
        }

        $sql = "INSERT INTO users (`" . implode('`, `', $keys) ."`) VALUES (" . $values . ")";

        if (!$this->query($sql, $fields)->error()) {
            return true;
        }
        return false;
    }

    public function update($table, $ids = array(), $fields = array()) {
        if (count($ids) == 2) {
            $set = '';
            $x = count($fields);

            foreach($fields as $name => $value) {
                $set .= "{$name} = ?";
                if ($x > 1) {
                    $set .= ', ';
                }
                $x--;
            }

            $sql = "UPDATE {$table} SET {$set} WHERE {$ids[0]} = {$ids[1]}";
            
            if (!$this->query($sql, $fields)->error()) {
                return true;
            }
            return false;
        } else {
            return false;
        }
    }

    public function results() {
        return $this->_results;
    }

    public function error() {
        return $this->_error;
    }

    public function count() {
        return $this->_count;
    }
}