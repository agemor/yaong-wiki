<?php
/**
 * YaongWiki Engine
 *
 * @version 1.2
 * @author HyunJun Kim
 * @date 2017. 08. 26
 */

const DB_HOST = "";
const DB_USER_NAME = "root";
const DB_USER_PASSWORD = "yaongwiki";
const DB_NAME = "yaongwiki";
const DB_TABLE_PREFIX = "test_";

const DB_USER_TABLE = DB_NAME . '`.`' . DB_TABLE_PREFIX . 'yaongwiki_users';
const DB_ARTICLE_TABLE = DB_NAME . '`.`' . DB_TABLE_PREFIX . 'yaongwiki_articles';
const DB_FILE_TABLE = DB_NAME . '`.`' . DB_TABLE_PREFIX . 'yaongwiki_files';
const DB_REVISION_TABLE = DB_NAME . '`.`' . DB_TABLE_PREFIX . 'yaongwiki_revisions';
const DB_LOG_TABLE = DB_NAME . '`.`' . DB_TABLE_PREFIX . 'yaongwiki_logs';