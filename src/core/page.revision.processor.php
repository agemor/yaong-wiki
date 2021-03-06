<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 09. 02
 */

require_once __DIR__ . "/common.php";
require_once __DIR__ . "/libs/parsedown.php";

function process() {

    $db = Database::get_instance();
    $user = UserManager::get_instance();
    $log = LogManager::get_instance();
    $http_vars = HttpVarsManager::get_instance();
    
    $parsedown = new Parsedown();

    $http_revision_id = $http_vars->get("i");
    $http_revision_target_id = $http_vars->get("j") !== null ? $http_vars->get("j") : 0;
    $http_rollback = !empty($http_vars->get("rollback"));
        
    if (empty($http_revision_id)) {
        return array(
            "result" => false,
            "redirect" => "./?page-not-found"
        );
    }

    if (!$db->connect()) {
        return array(
            "result" => false,
            "message" => STRINGS["ESDB0"],
            "redirect" => "./?out-of-service"
        );
    }
    
    $revision_data = $db->in(DB_REVISION_TABLE)
                        ->select("*")
                        ->where("id", "=", $http_revision_id)
                        ->go_and_get();
    
    if (!$revision_data) {
        return array(
            "result" => false,
            "message" => STRINGS["EPRV0"],
            "redirect" => "./?page-not-found"
        );
    }

    // 비교 대상이 정해지지 않았다면 바로 이전의 버전과 비교
    if ($http_revision_target_id == 0) {
        $http_revision_target_id = intval($revision_data["predecessor_id"]);
    }
    
    $article_data = $db->in(DB_ARTICLE_TABLE)
                       ->select("*")
                       ->where("id", "=", $revision_data["article_id"])
                       ->go_and_get();
    
    if (!$article_data) {
        return array(
            "result" => false,
            "message" => STRINGS["EPRV1"],
            "redirect" => "./?page-not-found"
        );
    }
    
    // 특정 버전으로 글 되돌리기
    if ($http_rollback) {
        if (!$user->authorized()) {
            return array(
                "result" => false,
                "redirect" => "./?signin"
            );
        }
        
        if ($user->get("permission") < intval($article_data["permission"])) {
            return array(
                "result" => false,
                "message" => STRINGS["EPRV2"]
            );
        }

        $revision_comment = strtr(STRINGS["SPRV0"], array("{REVISION}" => "#". $revision_data["id"]));

        $response_1 = $db->in(DB_REVISION_TABLE)
                         ->insert("article_id", $article_data["id"])
                         ->insert("article_title", $article_data["title"])
                         ->insert("predecessor_id", $article_data["latest_revision_id"])
                         ->insert("revision", intval($article_data["revisions"]) + 1)
                         ->insert("user_name", $user->get("name"))
                         ->insert("snapshot_content", $revision_data["snapshot_content"])
                         ->insert("snapshot_tags", $revision_data["snapshot_tags"])
                         ->insert("fluctuation", (strlen($revision_data["snapshot_content"]) - strlen($article_data["content"])))
                         ->insert("comment", $revision_comment)
                         ->go();
                         
        $recent_revision_id = $db->last_insert_id();

        $response_2 = $db->in(DB_ARTICLE_TABLE)
                         ->update("revisions", "`revisions` + 1", true)
                         ->update("latest_revision_id", $recent_revision_id)
                         ->update("content", $revision_data["snapshot_content"])
                         ->update("tags", $revision_data["snapshot_tags"])
                         ->update("title", $revision_data["article_title"])
                         ->where("id", "=", $article_data["id"])
                         ->go();                 

        if (!$response_1 || !$response_2) {
            return array(
                "result" => false,
                "message" => STRINGS["EPRV3"]
            );
        }
        
        return array(
            "result" => true,
            "redirect" => "./?read&i=" . $article_data["id"]
        );
    }
    
    $comparison_data = null;

    // 가장 초기 버전이라면
    if ($http_revision_target_id != 0) {

        $comparison_data = $db->in(DB_REVISION_TABLE)
                              ->select("*")
                              ->where("id", "=", $http_revision_target_id)
                              ->go_and_get();

        if (!$comparison_data) {
            return array(
                "result" => false,
                "message" => STRINGS["EPRV4"]
            );
        }
    }
    
    $article_data["content"] = $parsedown->text($revision_data["snapshot_content"]);
    $article_data["tags"] = parse_tags($revision_data["snapshot_tags"]);

    return array(
        "result" => true,
        "comparison_target" => $comparison_data,
        "revision" => $revision_data,
        "article" => $article_data
    );
}

function parse_tags($tags_string) {
    $tags = explode(",", $tags_string);
    $new_tags = array();
    for ($i = 0; $i < count($tags); $i++) {
        $tag = trim($tags[$i]);
        if ($tag != "") {
            array_push($new_tags, $tag);
        }
    }
    return $new_tags;
}