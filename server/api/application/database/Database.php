<?php
class Database {
    private $pdo;

    function __construct() {
        $host = 'MySQL-8.0';
        $user = 'root';      
        $pass = '';          
        $db = 'knightwars_db';  
        $connect = "mysql:host=$host;dbname=$db;charset=utf8mb4";
        $this->pdo = new PDO($connect, $user, $pass);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function __destruct() {
        $this->pdo = null;
    }

    private function execute($sql, $params = []) {
        $sth = $this->pdo->prepare($sql);
        return $sth->execute($params);
    }

    private function query($sql, $params = []) {
        $sth = $this->pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetch(PDO::FETCH_OBJ);
    }

    private function queryAll($sql, $params = []) {
        $sth = $this->pdo->prepare($sql);
        $sth->execute($params);
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserByLogin($login) {
        return $this->query("SELECT * FROM users WHERE login=?", [$login]);
    }

    public function getUserByToken($token) {
        return $this->query("SELECT * FROM users WHERE token=?", [$token]);
    }

    public function updateToken($userId, $token) {
        $this->execute("UPDATE users SET token=? WHERE id=?", [$token, $userId]);
    }

    public function registration($login, $password_hash, $nickname, $email) {
        $token = md5(uniqid()); 
        $this->execute("INSERT INTO users (login, password_hash, nickname, email, token) VALUES (?, ?, ?, ?, ?)", [$login, $password_hash, $nickname, $email, $token]);
    }
}

