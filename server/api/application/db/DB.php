<?php
class DB {
    private $pdo;

    function __construct() {
        //$host = '127.0.0.1';
        $host = '127.127.126.15';
        $port = '3306';
        $user = 'root';      
        $pass = '';          
        $db = 'knightwars';  
        $connect = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
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

    
    public function getUserById($id) {
        return $this->query("SELECT * FROM users WHERE id = ?", [$id]);
    }


    public function updateToken($userId, $token) {
        $this->execute("UPDATE users SET token=? WHERE id=?", [$token, $userId]);
    }

    public function registration($login, $password, $nickname) {
        $this->execute("INSERT INTO users (login, password, nickname) VALUES (?, ?, ?)", [$login, $password, $nickname]);
    }

     public function getChatHash() {
        return $this->query("SELECT * FROM hashes WHERE id=1");
    }

    public function updateChatHash($hash) {
        $this->execute("UPDATE hashes SET chat_hash=? WHERE id=1", [$hash]);
    }

    public function addMessage($userId, $message) {
        $this->execute('INSERT INTO messages (user_id, message, created) VALUES (?,?, now())', [$userId, $message]);
    }

    public function getMessages() {
        return $this->queryAll("SELECT u.name AS author, m.message AS message,
                                to_char(m.created, 'yyyy-mm-dd hh24:mi:ss') AS created FROM messages as m 
                                LEFT JOIN users as u on u.id = m.user_id 
                                ORDER BY m.created DESC"
        );
    }

    public function isUserPlaying($userId) {
        return $this->query("SELECT id FROM room_members WHERE user_id = ? AND status = 'started'", [$userId]);
    }

    public function getUserTypeInRoom($userId) {
        return $this->query("SELECT type FROM room_members WHERE user_id=?", [$userId]);
    }

    public function leaveParticipantFromRoom($userId) {
        $this->execute("DELETE FROM room_members WHERE user_id=?", [$userId]);
    }

    public function createRoom($userId) {
        $this->execute("INSERT INTO rooms () VALUES ()");
        $roomId = $this->pdo->lastInsertId();
        $this->addRoomMember($roomId, $userId, 'owner');
    }

    public function addRoomMember($roomId, $userId, $type, $status = 'ready') {
        return $this->execute(
            "INSERT INTO room_members (room_id, user_id, type, status) VALUES (?, ?, ?, ?)",
            [$roomId, $userId, $type, $status]
        );
    }

    public function getRoomById($roomId) {
        return $this->query("SELECT id, status FROM rooms WHERE id=?", [$roomId]);
    }

    public function getRoomMember($roomId, $userId) {
        return $this->query("SELECT * FROM room_members WHERE room_id=? AND user_id=?", [$roomId, $userId]);
    }

    public function updateRoomHash($hash) {
        $this->execute("UPDATE hashes SET room_hash = ? WHERE id = 1", [$hash]);
    }

    public function getRoomHash() {
        $result = $this->query("SELECT room_hash FROM hashes WHERE id = 1");
        return $result ? $result->room_hash : null;
    }

    public function getRoomMemberByUserId($userId) {
        return $this->query("SELECT * FROM room_members WHERE user_id=?", [$userId]);
    }

    public function deleteAllRoomMembers($roomId) {
        $this->execute("DELETE FROM room_members WHERE room_id=?", [$roomId]);
    }

    public function deleteRoom($roomId) {
        $this->execute("DELETE FROM rooms WHERE id=?", [$roomId]);
    }

    public function deleteUser($userId) {
        return $this->execute("DELETE FROM users WHERE id=?", [$userId]);
    }

    public function getAllRoomMembers($roomId) {
        return $this->queryAll("SELECT * FROM room_members WHERE room_id=?", [$roomId]);
    }

    public function updateRoomStatus($roomId, $status) {
        $this->execute("UPDATE rooms SET status=? WHERE id=?", [$status, $roomId]);
    }

    public function updateAllRoomMembersStatus($roomId, $status) {
        $this->execute("UPDATE room_members SET status=? WHERE room_id=?", [$status, $roomId]);
    }

    //?????
    public function getOpenRooms() {
        return $this->queryAll("
            SELECT r.id, r.status, COUNT(rm.user_id) as players_count 
            FROM rooms r 
            LEFT JOIN room_members rm ON r.id = rm.room_id 
            WHERE r.status = 'open' 
            GROUP BY r.id, r.status
        ");
    }

    public function getPersonClassByType($type) {
        return $this->query("SELECT * FROM person_classes WHERE type = ?", [$type]);
    }

    public function getPersonClassById($id) {
        return $this->query("SELECT * FROM person_classes WHERE id = ?", [$id]);
    }

    public function getAllPersonClasses() {
        return $this->queryAll("SELECT * FROM person_classes");
    }

    public function getUserPersonClass($userId, $personClassId) {
        return $this->query(
            "SELECT * FROM users_person_classes WHERE user_id = ? AND person_class_id = ?",
            [$userId, $personClassId]
        );
    }

    public function getUserOwnedClasses($userId) {
        return $this->queryAll(
            "SELECT pc.*, upc.selected FROM person_classes pc
             JOIN users_person_classes upc ON upc.person_class_id = pc.id
             WHERE upc.user_id = ?",
            [$userId]
        );
    }

    public function addUserPersonClass($userId, $personClassId) {
        return $this->execute(
            "INSERT INTO users_person_classes (user_id, person_class_id, selected) VALUES (?, ?, 0)",
            [$userId, $personClassId]
        );
    }

    public function clearSelectedUserClasses($userId) {
        return $this->execute(
            "UPDATE users_person_classes SET selected = 0 WHERE user_id = ?",
            [$userId]
        );
    }

    public function setUserSelectedPersonClass($userId, $personClassId) {
        return $this->execute(
            "UPDATE users_person_classes SET selected = 1 WHERE user_id = ? AND person_class_id = ?",
            [$userId, $personClassId]
        );
    }

    public function updateUserMoneySubtract($userId, $amount) {
        return $this->execute("UPDATE users SET money = money - ? WHERE id = ?", [$amount, $userId]);
    }

    public function beginTransaction() {
        return $this->pdo->beginTransaction();
    }

    public function commit() {
        return $this->pdo->commit();
    }

    public function rollBack() {
        return $this->pdo->rollBack();
    }
}


