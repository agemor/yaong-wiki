<?php
require_once 'common.php';
require_once 'common.db.php';

const MAX_RECENT_CHANGED = 1;

function getRecentUpdates($db) {
    
    if (!$db->query("SELECT `article_title`, `fluctuation` FROM " . REVISION_TABLE . " WHERE `id` IN (SELECT MAX(`id`) FROM " . REVISION_TABLE . " GROUP BY `article_id`) ORDER BY `id` DESC LIMIT " . MAX_RECENT_CHANGED . ";"))
        return array();
    
    if ($db->total_results() < 1)
        return array();
    
    $result_array = array();
    
    while ($result = $db->get_result()) {
        array_push($result_array, array(
        	'title'=>$result["article_title"],
        	'fluctuation'=>intval($result["fluctuation"])));
    }
    
    return $result_array;
}

?>