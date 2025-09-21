<?php

class User {
    function __construct($db) {
        $this->db = $db;
    }

    public function getUser($token) {
        return $this->db->getUserByToken($token);
    }

    public function login($login, $password) {
        $user = $this->db->getUserByLogin($login);
        if ($user) {
            if (password_verify($password, $user->password_hash)) {
                $token = md5(rand());
                $this->db->updateToken($user->id, $token);
                return [
                    'id' => $user->id,
                    'nickname' => $user->nickname,
                    'token' => $token
                ];
            }
            return ['error' => 1002];
        }
        return ['error' => 1005];
    }

    public function logout($token) {
        $user = $this->db->getUserByToken($token);
        if ($user) {
            $this->db->updateToken($user->id, null);
            return true;
        }
        return ['error' => 1003];
    }

    public function registration($login, $password, $nickname, $email) {
        $user = $this->db->getUserByLogin($login);
        if ($user) {
            return ['error' => 1001];
        }
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $this->db->registration($login, $password_hash, $nickname, $email);
        $user = $this->db->getUserByLogin($login);
        if ($user) {
            $token = md5(rand());
            $this->db->updateToken($user->id, $token);
            return [
                'id' => $user->id,
                'nickname' => $user->nickname,
                'token' => $token
            ];
        }
        return ['error' => 1004];
    }
}
