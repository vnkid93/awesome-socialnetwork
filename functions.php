
<?php

function upload_my_file($id, $folder){

	if(isset($_FILES['new_post_file']) && $_FILES['new_post_file']['name']){
      $errors= array();
      $file_name = $_FILES['new_post_file']['name'];
      $file_size = $_FILES['new_post_file']['size'];
      $file_tmp  = $_FILES['new_post_file']['tmp_name'];
      $file_type = $_FILES['new_post_file']['type'];
      $detected_type = exif_imagetype($file_tmp);
      $allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
      if(in_array($detected_type,$allowedTypes)=== false){
         return "extension not allowed, please choose a JPEG or PNG file.";
      }
      
      if($file_size > 2097152){
         return 'File size must be under 2 MB';
      }
      
      if(empty($errors)){
         move_uploaded_file($file_tmp,$folder."".$id.".png");
         return null;
      }else{
         return "Not able to upload photo";
      }
   }
   return null;
}

function call_edit_post($posting){
   if(isset($_POST["update_post_id"])){
      $posting->update_post($_POST["update_post_id"], $_POST["update_post"]);
   }
   if(isset($_POST["edit_delete_img"])){
      $posting->delete_pic($_POST["update_post_id"]);
   }
}

function call_delete_post($posting){
   if(isset($_POST["delete_post_id"])){
      $posting->delete_post($_POST["delete_post_id"]);
   }
}

function check_post_likes($id_post, $user_id, $db){
   $ret_arr = $db->DBSelectAll("user_likes_post", "users_id", array("posts_id"=>$id_post));
   $liked = 0;
   foreach ($ret_arr as $value) {
      if($user_id == $value["users_id"]){
         $liked = 1;
         break;
      }
   }
   $arr = array("count" => count($ret_arr), "liked" => $liked);
   return $arr;
}

function call_like_post($posting, $user_id){
   if(isset($_POST["like_post"])){
      $posting->like_post($_POST["like_post"], $user_id);
   }else if(isset($_POST["unlike_post"])){
      $posting->unlike_post($_POST["unlike_post"], $user_id);
   }
}

function call_register_user(){
   if(isset($_POST["submit_reg"])){
      $name = (isset($_POST["username_reg"])) ? $_POST["username_reg"] : "";
      $email = (isset($_POST["email_reg"])) ? $_POST["email_reg"] : "";
      $pass1 = (isset($_POST["password_reg"])) ? $_POST["password_reg"] : "";
      $pass2 = (isset($_POST["password_reg2"])) ? $_POST["password_reg2"] : "";
      $tel = (isset($_POST["tel_reg"])) ? $_POST["tel_reg"] : "";
      $info = (isset($_POST["addinfo_reg"])) ? $_POST["addinfo_reg"] : "";
      $sex = (isset($_POST["sex_reg"])) ? $_POST["sex_reg"] : 0;
      $file = (isset($_FILES["file_reg"])) ? $_FILES["file_reg"] : null;
      $from = (isset($_POST["from_reg"])) ? $_POST["from_reg"] : "";
      $year = (isset($_POST["year_reg"])) ? $_POST["year_reg"] : 1990;
      include_once("classes/register_user.class.php");
      $reg_class = new MyRegister();
      $error = $reg_class->regUser($name, $email, $pass1, $pass2, $info, $tel, $year, $sex, $from, $file);
      
      return $error;
      
   }
}

function get_may_know($me_id, $db){
   $sort = array();
   $sort[] = array("column"=>"startdate", "sort"=>"DESC");
   $followed = $db->DBSelectAll("user_folows_user", "users_id1", array("users_id"=>$me_id), "",$sort);
   $all_users = $db->DBSelectAll("users", "id, name, email", array());
   $unknown = array();
   $followed_arr = array();
   $all_arr = array();
   if($followed != null && count($followed)>0)
      foreach ($followed as $value)
         $followed_arr[] = $value["users_id1"];
      
   if($all_users != null && count($all_users)>0)
      foreach ($all_users as $value)
         $all_arr[] = array("id"=>$value["id"], "name"=>$value["name"], "email"=>$value["email"]);
   $count = 0;
   foreach ($all_arr as $value) {
      if(!in_array($value["id"], $followed_arr) && $value["id"]!=$me_id){
         $unknown[] = $value;
         $count++;
      }
      if($count>5)   break;
   }
   // ok, found
   $you_may_know = "<h3>You may know</h3>";
   if(count($unknown) > 0){
      $you_may_know .= '<ul>';
      foreach ($unknown as $value) {
         $img = (file_exists("images/ava/".$value["id"].".png")) ? "images/ava/".$value["id"].".png" :"images/ava/unknown.png";
         $you_may_know .= '<li>
                              <img src="'.$img.'" class="img-circle with_shadow with_border">
                              <a href="index.php?web=profile&email='.$value["email"].'">'.$value["name"].'</a>
                          </li>';
      }
         $you_may_know .= '</ul>';

   }else{
      $you_may_know .= 'no new profiles';
   }

   return $you_may_know;

}

function get_time($secs){
  $bit = array(
      'years' => $secs / 31556926 % 12,
      'weeks' => $secs / 604800 % 52,
      'days' => $secs / 86400 % 7,
      'hours' => $secs / 3600 % 24,
      'minutes' => $secs / 60 % 60,
      'seconds' => $secs % 60
      );
  $last_index = "seconds";
  foreach($bit as $k => $v){
      if($v > 0){
          $last_index = $k;
          break;
      }
  }
  return $bit[$last_index]." ".$last_index." ago";
}

function get_all_posts($db, $posts, $db){
        // checking likes

        $ret_text = "";
        foreach ($posts as $single_post) {
                
            $user_id = (file_exists("images/ava/".$single_post["users_id"].".png")) ? $single_post["users_id"] : "unknown";
            $img = "";
            $tmp_img = get_image($single_post["id"]);
            if($tmp_img)    $img = '<div class="post_pictures"><img src="'.$tmp_img.'" class="with_shadow2 img_post" ></div>';
            
            $tmp_arr = $db->DBSelectOne("users", "name, email", array("id"=>$single_post["users_id"]));
            $tmp_username = ($tmp_arr) ? $tmp_arr["name"] : "Unknown user";

            $check_like = check_post_likes($single_post["id"], $_SESSION["user_id"], $db);

            $like_part = get_like($single_post["id"], $check_like);

            if(isset($_POST["edit_post_id"]) && $_POST["edit_post_id"]==$single_post["id"]){
                $edit_form = edit_post_form($single_post["id"], $db);
            }else{
                $edit_form = "";
            }

            $ret_text .= '<div class="single_post">
                        '.$edit_form.'
                        <div class="post_avatar"><img src="images/ava/'.$user_id.'.png" class="img-circle"></div>
                        <div class="post_text">
                            <div style="float:left;"><h2><a href="index.php?web=profile&email='.$tmp_arr["email"].'">'.$tmp_username.'</a></h2></div>
                            <div style="float:right;">
                                '.check_rights($single_post["users_id"], $_SESSION["user_id"], $single_post["id"]).'
                            </div>

                            <div class="post_time clear_panel">'.get_time(time() - $single_post["time"]).'</div>
                                <div>'.$single_post["text"].'<br><br></div>
                                '.$img.'
                                <div class="like_counter">
                                    <form method="POST" class="form_nonstyle">
                                        '.$like_part.'
                                    </form></div>';


            $all_comments_arr = $db->DBSelectAll("comments", "id, text, time, posts_id, users_id", array("posts_id" => $single_post["id"]));
            foreach ($all_comments_arr as $single_comment) {
                
                $commentator = $db->DBSelectOne("users", "id, name, email", array("id"=>$single_comment["users_id"]));
                $ret_text .= '  <div class="post_comment">
                                     <div class="comment_avatar"><img src="'.get_ava($commentator["id"]).'" class="img-circle"></div>
                                        <div class="comment_text">
                                             <div style="float:left; font-weight:bold;"><a href="index.php?web=profile&email='.$commentator["email"].'">'.$commentator["name"].'</a></div>
                                            <div class="post_time" style="float:right;">'.get_time(time() - $single_comment["time"]).'</div>
                                            <div style="clear:both;"></div>
                                            '.$single_comment["text"].'
                                        </div>
                                    <div class="clear_panel min_size"></div>
                                    
                                </div>';
            }


            $ret_text .= '</div>
                            <div class="clear_panel min_size"></div>
                                <div class="new_comment">
                                    <form method="POST" class="new_comment_form" name="new_comment_form" onsubmit="return validate_textarea_newcomment()">
                                        <textarea rows="2" cols="90" maxlength="300" name="comment_textarea" class="form-control" placeholder="Write a comment..."></textarea>
                                        <input type="hidden" value="'.$single_post["id"].'" name="post_id">
                                        <input type="submit" value="Comment" class="btn btn-primary">
                                    </form>
                                </div>
                            <div class="clear_panel"></div></div>'; // end single_post, 
        }
        return $ret_text;
    }


function get_ava($avatar){
  return (file_exists("images/ava/".$avatar.".png")) ? "images/ava/".$avatar.".png" : "images/ava/unknown.png";
}

function get_image($img){
  return (file_exists("images/post/".$img.".png")) ? "images/post/".$img.".png" : null;
}

function get_like($post_id, $check_like){
  if($check_like["liked"]){   // already liked -> unlike
      $name = "unlike_post";
      $like_icon = "liked_icon";
  }else{
      $name = "like_post";
      $like_icon = "unliked_icon";
  }
  $liked = '<input type="hidden" name="'.$name.'" value="'.$post_id.'">
                  <button type="submit"><span class="glyphicon glyphicon-heart '.$like_icon.'"></span></button>
                  '.$check_like["count"].' Likes';
  return $liked;
}
    
function check_rights($idA, $idB, $post_id){
  if($idA == $idB){

      return '<form method="post" class="form_nonstyle">
                  <input type="hidden" name="edit_post_id" value="'.$post_id.'">
                  <button type="submit"><span class="glyphicon glyphicon-pencil post_edit"></span></button>
              </form>
              <form method="post" class="form_nonstyle" onsubmit="return confirm(\'Do you really want to delete the post?\');">
                  <input type="hidden" name="delete_post_id" value="'.$post_id.'">
                  <button type="submit"><span class="glyphicon glyphicon-remove post_delete"></span></button>
              </form>';
  }else{
      return '';
  }
}

function edit_post_form($post_id, $db){
  

  if(file_exists("images/post/".$post_id.".png")){
      $img = '<input type="checkbox" name="edit_delete_img"> Delete the image</checkbox>';
  }else{
      $img = '<input type="checkbox" name="edit_delete_img" disabled> Delete the image</checkbox>';
  }

  $text_arr = $db->DBSelectOne("posts","text", array("id"=>$post_id));
  $text = $text_arr["text"];
  return '<div class="edit_div_form">
              <form method="POST" class="edit_post_form">
                  <textarea rows="4" cols="90" maxlength="1000" name="update_post" class="form-control">'.$text.'</textarea> 
                  <input type="hidden" value="'.$post_id.'" name="update_post_id">
                  </br>
                  <div style="float:left;">
                      '.$img.'
                  </div>
                  <div style="float:right;">
                      <input type="submit" value="Update" class="btn btn-primary">
                  </div>
                  <div class="clear_panel" style="margin-bottom:20px;"></div>
              </form>
          </div>';
}

function get_more_info($user_data){
  $user_sex = ($user_data["sex"] == 1) ? "Male": "Female";
  $user_info_arr = array( "sex"=>"glyphicon-heart-empty",
                          "age"=>"glyphicon-calendar",
                          "phone"=>"glyphicon-earphone",
                          "address"=>"glyphicon-map-marker"
                          );//
  $user_info_arr_suffix = array(  "sex"=>"",
                                  "age"=>" years",
                                  "phone"=>"",
                                  "address"=>"",
                          );
  $more_infos = '<h1>About me</h1>
                  <ul>';
  foreach ($user_info_arr as $key => $value) {    
      if(array_key_exists($key, $user_data) && $user_data[$key]!=null && strlen($user_data[$key])>0){
          $tmp_data = $user_data[$key];
          switch($key){
            case "sex":
               $tmp_data = ($tmp_data) ? "Male" : "Female";
            break;
            case "age":
               $tmp_data = (date('Y')-$tmp_data);
            break;
          }


          $more_infos .= '<li><span class="glyphicon '.$value.'"></span> '.$tmp_data.$user_info_arr_suffix[$key].'</li>';
      }
  }
  $more_infos .= '</ul>';
  return $more_infos;
}

function delete_user($user_id, $db){
   // delete all his comments
   $db->DBDelete("comments", array("users_id"=>$user_id));
   // delete all his posts with its comments
   // get all his posts
   $all_posts = $db->DBSelectAll("posts", "id", array("users_id"=>$user_id));
   if($all_posts != null && count($all_posts) > 0){
      foreach ($all_posts as $value) {
         // get all its comments
         $all_comments = $db->DBSelectAll("comments", "id", array("posts_id"=>$value["id"]));
         // delete them all
         if($all_comments != null && count($all_comments) > 0){
            foreach ($all_comments as $com) {
               $db->DBDelete("comments", array("id"=>$com["id"]));
            }
         }
         // delete the post
         $db->DBDelete("posts", array("id"=>$value["id"]));
         // delete its pic
         $img_to_del = "images/post/".$value["id"].".png";
         if(file_exists($img_to_del)){
            $file = "test.txt";
            unlink($img_to_del);
         }
      }
   }
   
   // delete his ava
   $img_to_del = "images/ava/".$user_id.".png";
   if(file_exists($img_to_del)){
      $file = "test.txt";
      unlink($img_to_del);
   }
   // delete him
   $db->DBDelete("users", array("id"=>$user_id));
}

?>