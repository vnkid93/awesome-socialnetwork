<?php

class Main{
    
    public function getData($glob_user){
        include_once("classes/db.class.php");
        $db = new DB();
        $db->Connect();
        include_once("./functions.php");
        $nav_links = '<a href="index.php" class="align_left"><h1>Awesome</h1></a>
                        <ul class="align_left">
                            <li><a href="index.php?web=home">Home</a></li>
                            <li><a href="index.php?web=profile&email=all">Profile</a></li>
                        </ul> 
                        
                        <form class="align_right" method="POST" id="log_out_form">
                            <input type="hidden" name="logging_out" value="bar" />
                            <a href="index.php?web=login" class="align_right logout_link"
                                onclick="document.getElementById(\'log_out_form\').submit(); return false;"
                            ><span class="glyphicon glyphicon-log-out"></span> Log out</a>
                        </form>
                        ';
        $new_post_form = '<form method="POST" class="new_post_form" enctype="multipart/form-data" name="new_post_form" onsubmit="return validate_textarea_newpost()">
                            <textarea rows="4" cols="90" maxlength="1000" name="post_textarea" class="form-control" placeholder="Share your story..."></textarea> 
                            </br>
                            <div style="float:left;">
                                <!--<span class="glyphicon glyphicon-camera" style="font-size:2.5em;"></span>-->
                                <input type="file" name="new_post_file">
                            </div>
                            <div style="float:right;">
                                <input type="submit" value="Send" class="btn btn-primary">
                            </div>
                        </form>';
        /***************************************************************/
        $table = "users";
        $selected_column = "id, name , email, sex, age, address, info, rights, phone";
        $where_arr = array("email" => $glob_user);
        $user_data = $db->DBSelectOne($table, $selected_column, $where_arr);
        $user_avatar = $user_data["id"];

        $user_avatar = (file_exists("images/ava/".$user_avatar.".png")) ? $user_avatar : "unknown";
        $user_infos = '<img class="img-circle with_shadow with_border" src="images/ava/'.$user_avatar.'.png" height="200px" width="200px">
                        <h1>'.$user_data["name"].'</h1>
                            '.$user_data["info"];

        $user_sex = ($user_data["sex"] == 1) ? "Male": "Female";
        $user_info_arr = array( "sex"=>"glyphicon-heart-empty",
                                "age"=>"glyphicon-calendar",
                                "phone"=>"glyphicon-earphone",
                                "address"=>"glyphicon-map-marker",
                                "follow"=>"glyphicon-eye-open"
                                );
        $user_info_arr_suffix = array(  "sex"=>"",
                                        "age"=>" years",
                                        "phone"=>"",
                                        "address"=>"",
                                        "follow"=>"followers"
                                );

        $more_infos = get_more_info($user_data);
        /******************************* USERS POSTS ***************************************/
        $order_by = array();
        $order_by[] = array("column"=>"time", "sort"=>"DESC");
        $all_posts_arr = $db->DBSelectAll("posts", "id, users_id, text, time", array(), "", $order_by);


        
        $all_posts = get_all_posts($db, $all_posts_arr, $db, $user_data);


        $you_may_know = get_may_know($user_data["id"], $db);

        $activities = '<ul>
                            <li><span class="glyphicon glyphicon-heart"></span> <strong>Tony Tran</strong> likes your posts <span class="badge">5</span></li>
                            <li><span class="glyphicon glyphicon-eye-open"></span> <strong>Tony Tran</strong> and <strong>3 people</strong> started to follow you <span class="badge">4</span></li>
                            <li><span class="glyphicon glyphicon-heart"></span> <strong>Kin Le</strong> likes your posts <span class="badge">1</span></li>
                            <li></li>
                        </ul>';
        $copy_right = '<p>Copyright &copy; 2015 Vnkid</p>';
        
        
        
        return array("nav_links"    => $nav_links, 
                     "user_infos"   => $user_infos, 
                     "more_infos"   => $more_infos,
                     "you_may_know" => $you_may_know,
                     "activities"   => $activities,
                     "all_posts"    => $all_posts,
                     "copy_right"   => $copy_right,
                     "new_post"     => $new_post_form);
    }



}

?>