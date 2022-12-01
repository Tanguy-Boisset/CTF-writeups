<?php

include_once "db.php";

class User {

    /**
     * @var DB
     */
    private $con;

    function __construct(){
        global $db;
        $this->con = $db->con;
    }

    function get_all_username() {
        try {
            $st = $this->con->prepare("SELECT username FROM users");
            $st->execute();
            $result = $st->fetchAll(PDO::FETCH_COLUMN, 0);
            return $result;
        } catch(PDOException $e) {
            die($e->getMessage());
        }
    }

    function get_user_password_by_username($username){
        $st = $this->con->prepare("SELECT password FROM users WHERE username=?");
        $st->execute(array($username));
        $result = $st->fetch();
        return $result;
    }

    function get_user_password_by_userid($userid){
        $st = $this->con->prepare("SELECT password FROM users WHERE userid=?");
        $st->execute(array($userid));
        $result = $st->fetch();
        return $result;
    }

    function get_username_by_userid($userid){
        $st = $this->con->prepare("SELECT username FROM users WHERE userid=?");
        $st->execute(array($userid));
        $result = $st->fetch();
        return $result;
    }

    function get_user_info_by_username($username){
        try {
            $st = $this->con->prepare("SELECT username, password FROM users WHERE username=?");
            $st->execute(array($username));
            $result = $st->fetch();
            return $result;
        } catch(PDOException $e) {
            die($e->getMessage());
        }
    }

    function get_user_info_by_userid($userid){
        $st = $this->con->prepare("SELECT username, password FROM users WHERE userid=?");
        $st->execute(array($userid));
        $result = $st->fetch();
        return $result;
    }

    function create_user($username, $password){
        $st = $this->con->prepare("INSERT INTO users (username,password) VALUES (?, ?)");
        $st->execute(array($username, password_hash($password,  PASSWORD_DEFAULT)));
    }

    function delete_user_by_userid($userid){
        $st = $this->con->prepare("DELETE FROM users WHERE userid = ?");
        $st->execute(array($userid));
    }

    function delete_user_by_username($username){
        $st = $this->con->prepare("DELETE FROM users WHERE username = ?");
        $st->execute(array($username));
    }

    function update_user_by_userid($new_username, $new_password, $userid){
        $st = $this->con->prepare("UPDATE users SET username = ?, password = ? WHERE userid = ?");
        $st->execute(array($new_username, $new_password, $userid));
    }

    function update_user_by_username($new_username, $new_password, $username){
        $st = $this->con->prepare("UPDATE users SET username = ?, password = ? WHERE username = ?");
        $st->execute(array($new_username, $new_password, $username));
    }

    function change_username_by_userid($new_username, $userid){
        $password = $this->get_user_password_by_userid($userid);
        update_user_by_userid($new_username, $password, $userid);
    }

    function change_username_by_username($new_username, $username){
        $password = $this->get_user_password_by_username($username);
        update_user_by_username($new_username, $password, $username);
    }

    function change_user_password_by_userid($userid, $new_password){
        $username = get_username_by_userid($userid);
        update_user_by_userid($username, $new_password, $userid);
    }

    function change_user_password_by_username($username, $new_password){
        update_user_by_username($username, $new_password, $username);
    }

}



