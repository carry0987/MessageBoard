<?php
require dirname(__FILE__).'/source/class/class_core.php';
require dirname(__FILE__).'/source/class/class_load.php';
$load = new Load;
$load->loadClass('template');
$load->loadFunction('filter', 'core');

//Template setting
$options = array(
    'template_dir' => 'template/common/',
    'css_dir' => 'static/css/',
    'js_dir' => 'static/js/',
    'cache_dir' => 'data/cache/',
    'auto_update' => true,
    'cache_lifetime' => 0,
    'cache_db' => $conn
);

$template = Template::getInstance();
$template->setOptions($options);

//Get url for breadcrumb
$category_url = (isset($_SERVER['HTTPS'])?'https':'http').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

//Check URL
if (!empty($_GET['cid']) && ctype_digit($_GET['cid'])) {
    $category_id = input_filter($_GET['cid']);
    $display = 'view_category';
    //Prepare to get category name
    $category_query = 'SELECT name,description FROM category WHERE cid = ?';
    $category_stmt = $conn->stmt_init();
    //Category info
    if ($category_stmt->prepare($category_query)) {
        $category_stmt->bind_param('i', $category_id);
        $category_stmt->execute();
        $category_stmt->bind_result($name, $description);
        $category_result = $category_stmt->get_result();
        if ($category_result->num_rows != 0) {
            $show_category = true;
            while ($category_row = $category_result->fetch_assoc()) {
                $category_name = $category_row['name'];
                $category_description = $category_row['description'];
                $board_query = 'SELECT bid,name,description,create_date FROM board WHERE category_id = ?';
                $board_stmt = $conn->stmt_init();
                if ($board_stmt->prepare($board_query)) {
                    $board_stmt->bind_param('i', $category_id);
                    $board_stmt->execute();
                    $board_stmt->bind_result($bid, $name, $description, $create_date);
                    $board_result = $board_stmt->get_result();
                    if ($board_result->num_rows != 0) {
                        $show_board = true;
                        $total_board = $board_result->num_rows;
                        while ($board_row = $board_result->fetch_assoc()) {
                            $board[] = array(
                                'bid' => $board_row['bid'],
                                'board_name' => $board_row['name'],
                                'board_description' => $board_row['description'],
                                'create_date' => getDateTime($SYSTEM['system_timezone'], $SYSTEM['user_timezone'], $board_row['create_date'], 'Y-m-d')
                            );
                        }
                    } else {
                        $show_board = false;
                        $total_board = '';
                    }
                }
            }
        } else {
            $show_category = false;
            header('HTTP/1.0 404 Not Found');
        }
    } else {
        header('Location: '.$base_url.'');
        exit();
    }
} else {
    header('Location: '.$base_url.'');
    exit();
}

//Load Template
include($template->loadTemplate('header_common.html'));
include($template->loadTemplate($display.'.html'));
include($template->loadTemplate('footer_common.html'));

//Close database connect
$category_stmt->free_result();
$conn->close();
