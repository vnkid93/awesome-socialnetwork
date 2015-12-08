<?php 

	// Twig stahnout z githubu - klidne staci zip a dat do slozky twig-master
		// kontrolu provedete dle umisteni souboru Autoloader.php, ktery prikladam pro kontrolu
	
	// nacist twig - kopie z dokumentace
	require_once 'libs/Twig-1.23.1/lib/Twig/Autoloader.php';//'twig-master/lib/Twig/Autoloader.php';
	Twig_Autoloader::register();

	// cesta k adresari se sablonama - od index.php
	$loader = new Twig_Loader_Filesystem('templates');
	$twig = new Twig_Environment($loader); // takhle je to bez cache

	// nacist danou sablonu z adresare
	//$template = $twig->loadTemplate('template.htm');

    include("functions.php");
    include("classes/login_user.class.php");
    $pr = new MyLogin();
    $glob_user = $pr->checkLogin();
    $user_id = $pr->get_user_id();
    $user_name = $pr->get_username();
    /*******************************************************************/
    //reaguje na odeslani formularu
    if (isset($_POST["logging_out"])) {
        $glob_user = null;
        $pr->logoutUser();
    }



    if($glob_user && $user_id!=-1 && $user_name){
        include_once("classes/posting.class.php");
        $posting = new Posting();
        if(isset($_POST["post_textarea"])){
            $post_id = $posting->send_new_post($user_id, $_POST["post_textarea"]);
            $ret = upload_my_file($post_id, "images/post/");
        }

        if(isset($_POST["comment_textarea"])){
            $posting->send_new_comment($user_id, $user_name, $_POST["comment_textarea"], $_POST["post_id"]);
        }
        call_edit_post($posting);
        call_delete_post($posting);
        call_like_post($posting, $user_id);
    }

    /********************************************************************/

    

    // seznam dostupnych stran k zobrazeni
    $dostupne = array("index", "login", "register", "home", "profile");
    // defaultne se zobrazuje prvni strana
    $zobrazim = $dostupne[0];
    
    if(isset($_GET["web"]) && in_array($_GET["web"], $dostupne)){
        $zobrazim = $_GET["web"];
    }
    

    if($glob_user){
        // pokud je uzivatel přihlašen -> nedostane se do login.php a register.php
        if($zobrazim == "login" || $zobrazim == "register"){
            $zobrazim = $dostupne[0];
        }
    }else{
        if($zobrazim == "index" || $zobrazim == "home" || $zobrazim == "profile"){
            $zobrazim = $dostupne[1];
        }
    }

    $tempFile = "index.htm";

    include("web/".$zobrazim.".php");
    switch($zobrazim){
        case "login":
            $web = new Login();
            $tempFile = "login.htm";
            $data = $web->getData($glob_user);
            break;
        case "register":
            $web = new Register();
            $tempFile = "login.htm";
            $errors = call_register_user();
            $data = $web->getData($glob_user, $errors);
            break;
        case "index":
            $web = new Main();
            $data = $web->getData($glob_user);
            break;
        case "home":
            $web = new Home();
            $data = $web->getData($glob_user);
            break;
        case "profile":
            $web = new Profile();
            $profile_email = (isset($_GET["email"])) ? $_GET["email"] : "unknown";
            $data = $web->getData($glob_user, $profile_email);
            break;
    }

    $template = $twig->loadTemplate($tempFile);
	echo $template->render($data);
    
?>