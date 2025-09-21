<?php
require_once ('database/Database.php');
require_once ('user/User.php');
require_once ('math/Math.php');

class Application {
    function __construct() {
        $db = new Database();
        $this->user = new User($db);
        $this->math = new Math();
    }

    public function login($params) {
        if ($params['login'] && $params['password']) {
            return $this->user->login($params['login'], $params['password']);
        }
        return ['error' => 242];
    }

    public function logout($params) {
        if ($params['token']) {
            $user = $this->user->getUser($params['token']);
            if ($user) {
                return $this->user->logout($params['token']);
            }
            return ['error' => 705];
        }
        return ['error' => 242];
    }

    public function registration($params) {
        if ($params['login'] && $params['password'] && $params['nickname'] && $params['email']) {
            return $this->user->registration($params['login'], $params['password'], $params['nickname'], $params['email']);
        }
        return ['error' => 242];
    }

        public function math($params) {
            $a = (float) ($params['a'] ?? 0);
            $b = (float) ($params['b'] ?? 0);
            $c = (float) ($params['c'] ?? 0);
            $d = (float) ($params['d'] ?? 0);
            $e = (float) ($params['e'] ?? 0);
            
            if ($a != 0 || $b != 0 || $c != 0 || $d != 0 || $e != 0) {
                return $this->math->getAnswers($a, $b, $c, $d, $e);
            }
            return ['error' => 8001];
        }
}
