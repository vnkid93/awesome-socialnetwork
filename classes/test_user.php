<?php

class Test{
    public static $user;
    private $db;
    
    /*public function __construct(){
        session_start();
        echo "konstruct login";
        include("classes/db.class.php");
        $this->db = new DB()->Connect()->getConnection();
    }
    
    public function checkLogin(){
        return isset($_SESSION["loggedin"]) ? $_SESSION["loggedin"] : null;
    }
    
    public function loginUser($log, $pass){
        // check login
        
        echo "<h1 style='font-size:10em;'>HOOOOOOOHOHOOO</h1>";
        $rightLog = true;
        
        $table_name = "users";
        $select_columns_string = "email";
        $where_array = array("email='$log'","password='$pass'");
        
        $row = $db->DBSelectOne($table_name, $select_columns_string, $where_array);
        
        print_r($row);
        
        
        
        if($rightLog){
            $_SESSION["loggedin"] = $log;
            return true;
        }else{
            return false;
        }
    }
    
    public function logoutUser(){
        session_unset();
        return true;
    }*/

}

?>