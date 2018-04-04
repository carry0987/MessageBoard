<?php
$check_table_article_exists = 'SELECT id FROM article';
$check_table_user_exists = 'SELECT id FROM user';
$check_table_config_exists = 'SELECT id FROM config';
$if_article_exists = $con->query($check_table_article_exists);
$if_user_exists = $con->query($check_table_user_exists);
$if_config_exists = $con->query($check_table_config_exists);

if($if_article_exists->num_rows > 0) {
    echo '';
} elseif($if_user_exists->num_rows > 0) {
    echo '';
} elseif($if_config_exists->num_rows > 0) {
    echo '';
} else {
    header('Location: ../../install');
}
