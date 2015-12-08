<?php

class MyLogin{
    public static $user;
    private $db;
    
    public function __construct(){
        session_start();
        include_once("classes/db.class.php");
        $this->db = new DB();
        $this->db->Connect();
    }
    
    public function checkLogin(){
        return isset($_SESSION["loggedin"]) ? $_SESSION["loggedin"] : null;
    }
    public function get_user_id(){
        return isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : -1;
    }
    public function get_username(){
        return isset($_SESSION["username"]) ? $_SESSION["username"] : null;
    }
    public function get_user_rights(){
        return isset($_SESSION["rights"]) ? $_SESSION["rights"] : null;
    }
    
    public function loginUser($log, $pass){
        // checklogin
        $table_name = "users";
        $select_columns_string = "email, name, id, rights";
        $pass = sha1($pass);
        $where_array = array("email"=>"$log","password"=>"$pass");
        
        $row = $this->db->DBSelectOne($table_name, $select_columns_string, $where_array);
        
        
        if($row){
            $_SESSION["loggedin"] = $log;
            $_SESSION["user_id"] = $row["id"];
            $_SESSION["username"] = $row["name"];
            $_SESSION["rights"] = $row["rights"];
            return true;
        }else{
            return false;
        }
    }
    
    public function logoutUser(){
        session_unset();
        return true;
    }

}

?>