<?php

class MyRegister{
    private $db;
    
    public function __construct(){
        include_once("classes/db.class.php");
        $this->db = new DB();
        $this->db->Connect();
    }
    
    
    public function regUser($name, $email, $pass1, $pass2, $info, $tel, $year, $sex, $address, $file){
        $error = array();
        $ret = $this->validate_name($name);
        if($ret==0){
            $error[] = "The name is too short or too long (3-30 characters)";
        }
        $ret = $this->validate_email($email);
        if($ret==0){
            $error[] = "The email is too short or too long.(max. 40 characters)";
        }elseif ($ret == -1) {
            $error[] = "The email has wrong format (e.g. john@gmail.com)";
        }
        $ret = $this->validate_pass($pass1, $pass2);
        if($ret==0){
            $error[] = "Passwords do not match";
        }elseif ($ret == -1) {
            $error[] = "The password is too short or too long (6-20 characters)";
        }
        $ret = $this->validate_info($info);
        if($ret==0){
            $error[] = "The info is too long (max. 150 characters)";
        }
        $ret = $this->validate_tel($tel);
        if($ret==0){
            $error[] = "The telephone number should have 9 digits";
        }elseif ($ret == -1) {
            $error[] = "The telephone number has wrong format";
        }
        $ret = $this->validate_address($address);
        if($ret==0){
            $error[] = "The address contains non-alphanumeric characters";
        }
        $ret = $this->validate_ava($file['size'], $file['tmp_name']);
        if($ret==0){
            $error[] = "The file is too big (max. 2MB)";
        }elseif ($ret == -1) {
            $error[] = "Please choose image file (JPG, PNG...)";
        }
        $sex = ($sex == "Male")? 1 : 0;
        $ret = $this->validate_sex($sex);
        if($ret==0){
            $error[] = "Wrong value of sex";
        }
        $ret = $this->validate_year($year);
        if($ret==0){
            $error[] = "Year has to be four digits number (e.g. 1993)";
        }elseif($ret==-1){
            $error[] = "Too young or too old (3-100)";
        }
        if(count($error) == 0){
            $ret_db = $this->db->DBSelectOne("users", "id", array("email"=>$email));
            if($ret_db == null || count($ret_db) == 0){
                $name = htmlspecialchars($name, ENT_QUOTES);
                $email = htmlspecialchars($email, ENT_QUOTES);
                $pass1 = htmlspecialchars($pass1, ENT_QUOTES);
                $pass1 = sha1($pass1);
                $year = htmlspecialchars($year, ENT_QUOTES);
                $address = htmlspecialchars($address, ENT_QUOTES);
                $info = htmlspecialchars($info, ENT_QUOTES);
                
                $last_id= $this->db->DBInsert("users", array("name"=>$name,
                                                    "email"=>$email,
                                                    "password"=>$pass1,
                                                    "age"=>$year,
                                                    "sex"=>$sex,
                                                    "address"=>$address,
                                                    "info"=>$info,
                                                    "phone"=>$tel));
                
                echo "OBRAZEKKKKK";
                $this->upload_ava($last_id, $file);
                return null;
            }else{
                $error[] = "Email is already in use";
            }
            
        }else{
            return $error;
        }
        
    }
    public function validate_name($name){
        $len = strlen($name);
        if($len >= 3 && $len <= 30){
            return  1;
        }
        return 0;
    }
    public function validate_email($email){
        $len = strlen($email);
        if($len>0 && $len <= 40){
            if(filter_var($email, FILTER_VALIDATE_EMAIL))
                return 1;
            else
                return -1;
        }
        return 0;
        
    }
    public function validate_pass($pass1, $pass2){
        if($pass1 === $pass2){ // if they are the same
            $len = strlen($pass1);
            if($len >=6 && $len <=20){  // length is ok
                return 1;
            }
            return -1;
        }
        return 0;
    }
    public function validate_info($info){
        if(strlen($info)<=150){
            return 1;
        }
        return 0;
    }
    public function validate_tel($tel){
        if(strlen($tel == 0))   return 1;
        if(strlen($tel) == 9){
            if(is_numeric($tel) && $tel >0){
                return 1;
            }
            return -1;
        }
        return 0;
    }
    public function validate_address($add){
        $len = strlen($add);
        if($len == 0) return 1;
        if($len > 0 && $len <= 40){
            return 1;   
        }
        return 0;
    }

    public function validate_ava($file_size, $file_tmp){
        if($file_tmp!=null && strlen($file_tmp)>0){
            $detected_type = exif_imagetype($file_tmp);
            $allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
            if(in_array($detected_type,$allowedTypes)=== false){
                return -1;
            }
            if($file_size > 2097152){
                return 0;
            }
            return 1;
        }
        return 1;
    }
    public function validate_year($year){
        $len = strlen($year);
        if($len == 4){
            $actual = date('Y');
            if($year<= ($actual-3) && $year >= ($actual-100)){
                return 1;
            }
            return -1;
        }
        return 0;
    }

    public function validate_sex($sex){
        if($sex == 1 || $sex==0){
            return 1;
        }else{
            return 0;
        }
    }

    public function upload_ava($id, $file){
        if($file['name']){
                $errors= array();
                $file_size = $file['size'];
                $file_tmp  = $file['tmp_name'];
                $detected_type = exif_imagetype($file_tmp);
                $allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
                if(in_array($detected_type,$allowedTypes)=== false){
                 return "extension not allowed, please choose a JPEG or PNG file.";
                }

                if($file_size > 2097152){
                 return 'File size must be under 2 MB';
                }

                if(empty($errors)){
                 move_uploaded_file($file_tmp,"images/ava/".$id.".png");
                 return null;
                }else{
                 return "Not able to upload photo";
                }
        }
       return null;
    }


}

?>