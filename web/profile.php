<?php

class Profile{
    
    public function getData($glob_user, $profile_email){
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
        $new_post_form = '';
        /***************************************************************/
        if($profile_email == "all"){
            $my_right = $db->DBSelectOne("users", "rights", array("email"=>$glob_user));
            $order_by = array();
            $order_by[] = array("column"=>"time", "sort"=>"DESC");
            if(isset($_POST["delete_btn"])){
            foreach ($_POST["check_to_del"] as  $value) {
                delete_user($value, $db);
            }
        }
            $all_profile_arr = $db->DBSelectAll("users", "id, name, email, sex, age, rights", array());

            $all_posts = $this->get_all_profiles($all_profile_arr, $my_right["rights"]);
            $user_data = $db->DBSelectOne("users", "id, name , email, sex, age, address, info, phone", array("email" => $glob_user));
            $user_avatar = (file_exists("images/ava/".$user_data["id"].".png")) ? $user_data["id"] : "unknown";
            $user_infos = '<img class="img-circle with_shadow with_border" src="images/ava/'.$user_avatar.'.png" height="200px" width="200px">
                            <h1>'.$user_data["name"].'</h1>'.$user_data["info"];
            $more_infos = get_more_info($user_data);
                /******************************* USERS POSTS ***************************************/
            $you_may_know = get_may_know($user_data["id"], $db);
        }else{
            $user_data = $db->DBSelectOne("users", "id, name , email, sex, age, address, info", array("email" => $profile_email));
            if($user_data != null){
                $user_avatar = (file_exists("images/ava/".$user_data["id"].".png")) ? $user_data["id"] : "unknown";
                $user_infos = '<img class="img-circle with_shadow with_border" src="images/ava/'.$user_avatar.'.png" height="200px" width="200px">
                                <h1>'.$user_data["name"].'</h1>'.$user_data["info"];
                $more_infos = get_more_info($user_data);
                /******************************* USERS POSTS ***************************************/
                $order_by = array();
                $order_by[] = array("column"=>"time", "sort"=>"DESC");
                $all_posts_arr = $db->DBSelectAll("posts", "id, users_id, text, time", array("users_id"=>$user_data["id"]), "", $order_by);

                $all_posts = get_all_posts($db, $all_posts_arr, $db);
                $you_may_know = get_may_know($user_data["id"], $db);
            }else{
                $user_avatar = "unknown";
                $user_infos = '<img class="img-circle with_shadow with_border" src="images/ava/'.$user_avatar.'.png" height="200px" width="200px">
                                <h1>Unknown</h1>
                                    ';
                $more_infos = "profile not found";

                $all_posts = "<div style='text-align:center;'><h2>Profile not found</h2></div>";
                $you_may_know = get_may_know($user_data["id"], $db);
            }
        }

        
        $copy_right = '<p>Copyright &copy; 2015 Vnkid</p>';
        
        
        
        return array("nav_links"    => $nav_links, 
                     "user_infos"   => $user_infos, 
                     "more_infos"   => $more_infos,
                     "you_may_know" => $you_may_know,
                     "all_posts"    => $all_posts,
                     "copy_right"   => $copy_right, 
                     "new_post"     => $new_post_form);
    }
    /**************************************************************************/
    private function get_all_profiles($all_data, $rights){
        $ret ="";
        if($rights) $delete_head = "<td>Delete?</td>";   
        else    $delete_head = "";

        
        if($rights)
            $ret = '<form method="POST" style="padding:0 10px 0 10px;" action="index.php?web=profile&email=all">';
        $ret .= '<table class="table table-striped">';
        $ret .= '<tr style="font-weight:bold;">
                    <td>#</td>
                    <td>Full name</td>
                    <td>E-mail</td>
                    <td>Age</td>
                    <td>Sex</td>
                    '.$delete_head.'
                </tr>';
        foreach ($all_data as $value) {
            if($value["sex"]){
                $sex = '<span class="glyphicon glyphicon-king" style="color:#354f72;"></span>';
            }else{
                $sex = '<span class="glyphicon glyphicon-queen" style="color:#990000;"></span>';
            }
            $disabled = "";
            if($value["rights"] == 1)   $disabled = "disabled";
            $ret .= '<tr>
                    <td style="font-weight:bold;">'.$value["id"].'</td>
                    <td><a href="index.php?web=profile&email='.$value["email"].'">'.$value["name"].'</a></td>
                    <td>'.$value["email"].'</td>
                    <td>'.(date('Y') - $value["age"]).'</td>
                    <td>'.$sex.'</td>';
            if($rights){
                $ret .= '<td><input type="checkbox" value="'.$value["id"].'" name="check_to_del[]" '.$disabled.'></td>';
            }
            $ret .= '</tr>';
        }
        $ret .= '</table>';
        if($rights)
            $ret .= '<input type="submit" class="btn" value="Delete" name="delete_btn"></form>';
        return $ret;
    }

}

?>