<?php
class Register{

    public function getData($glob_user, $errors){
        $nav_links = '<a href="index.php" class="align_left"><h1>Awesome</h1></a>
                        <ul class="align_left">
                            <li><a href="index.php?web=login">Login</a></li>
                        </ul> ';
        $welcome_message = "<h1>Register</h1>
            <p>Thank you for joing us, you are <strong>Awesome</strong></p>";
        $err_mess = '';
        if($errors != null && count($errors)>0){
          $err_mess .= '<div class="form-group">
                        <p class="help-block" style="margin-left:210px;">';
          foreach ($errors as $value) {
            $err_mess .= '<span style="color:red;">*</span> '. $value.'<br>';
          }       
          $err_mess .= '</p></div>';
        }

        $log_form = '<form class="form-horizontal" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="username_reg" class="col-sm-3 control-label">Full name *</label>
                            <div class="col-sm-7">
                              <input type="text" class="form-control" id="username_reg" name="username_reg" placeholder="Full name" maxlength="30">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email_reg" class="col-sm-3 control-label">Email *</label>
                            <div class="col-sm-7">
                              <input type="email" class="form-control" id="email_reg" name="email_reg" placeholder="Email" maxlength="40">
                            </div>
                        </div>

                        <div class="form-group">
                        <label for="password_reg" class="col-sm-3 control-label">Password *</label>
                        <div class="col-sm-7">
                          <input type="password" class="form-control" id="password_reg" name="password_reg" placeholder="Password" maxlength="50">
                        </div>
                      </div>
                        <div class="form-group">
                        <label for="password_reg2" class="col-sm-3 control-label">Re-type password *</label>
                        <div class="col-sm-7">
                          <input type="password" class="form-control" id="password_reg2" name="password_reg2" placeholder="Password" maxlength="50">
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="tel_reg" class="col-sm-3 control-label">Tel. numbers</label>
                        <div class="col-sm-7">
                          <input type="text" class="form-control" id="tel_reg" name="tel_reg" placeholder="Telephone numbers" maxlength="9">
                        </div>
                      </div>
                        <div class="form-group">
                        <label for="from_reg" class="col-sm-3 control-label">From</label>
                        <div class="col-sm-7">
                          <input type="text" class="form-control" id="from_reg" name="from_reg" placeholder="State/country" maxlength="40">
                        </div>

                      </div>

                      <div class="form-group">
                        <label for="addinfo_reg" class="col-sm-3 control-label">Info about you</label>
                        <div class="col-sm-7">
                          <input type="text" class="form-control" id="addinfo_reg" name="addinfo_reg" placeholder="I am a awesome person and ..." maxlength="200">
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="sex_reg" class="col-sm-3 control-label">Sex *</label>
                        <div class="col-sm-3">
                          <select class="form-control" name="sex_reg" name="sex_reg">
                            <option>Male</option>
                            <option>Female</option>
                          </select>
                        </div>

                        <label for="year_reg" class="col-sm-2 control-label">Was born in *</label>
                        <div class="col-sm-2">
                          <input type="text" class="form-control" id="year_reg" name="year_reg" placeholder="1993" maxlength="4">
                        </div>
                      </div>

                      <div class="form-group non-margin">
                        <label for="file_reg" class="col-sm-3 control-label">User avatar</label>
                        <div class="col-sm-7">
                          <input type="file" id="file_reg" name="file_reg">
                          <p class="help-block non-margin">Have to be jpeg, gif or png file under 2MB.</p>
                        </div>
                      </div>

                      <div class="form-group" style="margin:0; padding:0;">
                        <p class="help-block" style="margin-left:210px;">* - required</p>
                      </div>

                      <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                          <button type="submit" class="btn btn-default" name="submit_reg">Sign in</button>
                        </div>
                      </div>

                      
                      '.$err_mess.'
                    </form>';
        $copy_right = '<p>Copyright &copy; 2015 Vnkid</p>';


        return array("nav_links"        => $nav_links,
                     "welcome_message"  => $welcome_message,
                     "log_form"         => $log_form,
                     "copy_right"       => $copy_right);
    }




}

?>