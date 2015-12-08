<?php

class Posting{
	private $db;

	public function __construct(){
        include_once("classes/db.class.php");
        $this->db = new DB();
        $this->db->Connect();
    }
	public function send_new_post($user_id, $text){
		$text = htmlspecialchars($text, ENT_QUOTES);
		$len = strlen($text);
		// if post is too long
		if($len > 1000){
			$text = substr($text, 0, 1000-$len);
		}
		$item = array("users_id" => $user_id, "text" => $text, "time"=>time());
		$this->db->DBInsert("posts", $item);
		return $this->db->connection->lastInsertId();
	}

	public function send_new_comment($user_id, $user_name, $text, $post_id){
		$text = htmlspecialchars($text, ENT_QUOTES);
		$len = strlen($text);
		// if post is too long
		if($len > 300){
			$text = substr($text, 0, 300-$len);
		}
		$item = array("users_id" => $user_id, "text" => $text, "time"=>time(), "posts_id" => $post_id);
		$this->db->DBInsert("comments", $item);
	}

	public function delete_post($id_post){
		// delete all comment
		$this->db->DBDelete("comments", array("posts_id" => $id_post));

		$this->delete_pic($id_post);

		// delete the post
		$this->db->DBDelete("posts", array("id" => $id_post));

	}

	public function like_post($id_post, $user_id){
		$this->db->DBInsert("user_likes_post", array("users_id"=>$user_id, "posts_id"=>$id_post, "time"=>time()));
	}

	public function unlike_post($id_post, $user_id){
		$this->db->DBDelete("user_likes_post", array("users_id"=>$user_id, "posts_id"=>$id_post));
	}

	public function delete_pic($id_post){
		$img_to_del = "images/post/".$id_post.".png";
		if(file_exists($img_to_del)){
			$file = "test.txt";
			unlink($img_to_del);
		}
	}

	public function update_post($id_post, $text){
		$text = htmlspecialchars($text, ENT_QUOTES);
		$this->db->DBUpdate("posts",array("text"=>$text), array("id"=>$id_post));
	}


}


?>