<?php
class Login{

public function getData($glob_user){
    $nav_links = '<a href="index.php" class="align_left"><h1>Awesome</h1></a>
                    <ul class="align_left">
                        <li><a href="index.php?web=register">Register</a></li>
                    </ul>';
    $welcome_message = "<h1>Login</h1>
        <p>Welcome back, you are <strong>Awesome</strong></p>";
    $log_form = '<form class="form-horizontal" method="POST" >
                    <div class="form-group">
                        <label for="inputEmail" class="col-sm-3 control-label">Email</label>
                        <div class="col-sm-7">
                          <input type="email" class="form-control" id="inputEmail" name="inputEmail" placeholder="Email">
                        </div>
                    </div>
                    
                    <div class="form-group">
                    <label for="password" class="col-sm-3 control-label">Password</label>
                    <div class="col-sm-7">
                      <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                    </div>
                  </div>
                    
                  <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-10">
                      <button type="submit" class="btn btn-default col-sm-2">Sign in</button>
                    </div>
                  </div>
                </form>';
    $copy_right = '<p>Copyright &copy; 2015 Vnkid</p>';
    if(isset($_POST["inputEmail"]) && isset($_POST["password"])){
        $email = $_POST["inputEmail"];
        $pass = $_POST["password"];
        include_once("classes/login_user.class.php");
        $log = new MyLogin();
        if($log->loginUser($email, $pass)){
            // redirect to homepage
            header("Location: index.php");
            die();
        }else{
            // login failed
            $log_form .= "<p class='col-sm-offset-3'>Wrong username or password!</p>";
        }
    }

    
    return array("nav_links"        => $nav_links,
                 "welcome_message"  => $welcome_message,
                 "log_form"         => $log_form,
                 "copy_right"       => $copy_right);
}




}

?>