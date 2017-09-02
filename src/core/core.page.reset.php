<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 12
 */

require_once "core.php";
require_once "core.db.php";
require_once "core.session.php";
require_once "core.email.php";
require_once "core.recaptcha.php";

function process() {

    global $db;
    global $post;
    global $user;
    global $email;
    global $redirect;
    global $recaptcha;
    
    if ($user->signined()) {
        $redirect->set(get_theme_path() . HREF_MAIN);
        return array(
            "redirect" => true
        ); 
    }
    
    $http_user_email = $post->retrieve("user-email");
    $http_recaptcha = $post->retrieve("g-recaptcha-response");    

    if (empty($http_user_email)) {
        return array(
            "result" => true
        );
    }
    
    if (!filter_var($http_user_email, FILTER_VALIDATE_EMAIL)) {
        return array(
            "result" => false,
            "message" => STRINGS["EPRS0"]
        );
    }
    
    // reCAPTCHA
    if (!$recaptcha->verify($http_recaptcha)) {
        return array(
            "result" => false,
            "message" => STRINGS["EPRS1"]
        );
    }
    
    $user_data = $db->in(DB_USER_TABLE)
                    ->select("*")
                    ->where("email", "=", $http_user_email)
                    ->go_and_get();

    if (!$user_data) {
        return array(
            "result" => false,
            "message" => STRINGS["EPRS2"]
        );
    }

    $generated_password = bin2hex(openssl_random_pseudo_bytes(6));
    
    $response = $db->in(DB_USER_TABLE)
                   ->update("password", hash_password($generated_password))
                   ->where("email", "=", $http_user_email)
                   ->go();

    if (!$response) {
        return array( 
            "result" => false,
            "message" => STRINGS["EPRS3"]
        );
    }

    $email_content_table = array(
        "{NAME}" => $user_data["name"],
        "{PASSWORD}" => $generated_password
    );
    
    $email_subject = STRINGS["SPRS0"];
    $email_content = strtr(STRINGS["SPRS1"], $email_content_table);

    if (!$email->send($http_user_email, $email_subject, $email_content)) {
        return array(
            "result" => false,
            "message" => STRINGS["EPRS4"]
        );
    }
    
    $response = $db->in(DB_LOG_TABLE)
                   ->insert("behavior", "reset")
                   ->insert("data", "*")
                   ->go();
    
    return array(
        "result" => true,
        "message" => STRINGS["EPRS5"]
    );
}