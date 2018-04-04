<?php
header('content-type:text/html;charset=utf-8');
require dirname(__FILE__).'/source/include/header.php';
require dirname(__FILE__).'/source/function/check_database.php';

echo '
<div id="cssmenu">
    <ul>
';
  echo $menu_index;
if(!empty($_SESSION['username']))
{
  if($now_admin == 1) {
    echo $menu_admin;
  }
  echo $menu_home;
  echo $menu_logout;
} else {
  echo $menu_login;
  echo $menu_signup;
}
echo '
    </ul>
</div>
';

/* Breadcrumb */
$index_url = (isset($_SERVER['HTTPS'])?"https":"http").'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
echo '
    <div class="breadcrumbs">
        <span itemscope="itemscope" itemtype="http://schema.org/BreadcrumbList">
            <span itemscope="itemscope" itemtype="http://schema.org/ListItem" itemprop="itemListElement">
                <a class="fileTrail" href="'.$index_url.'" itemprop="item">
                    <span class="breadcrumbs_home" itemprop="name">'.$main_name.'</span>
                    <img class="breadcrumbs_img" src="'.$base_url.'/static/icon/home.svg">
                    <meta content="1" itemprop="position" />
                </a>
            </span>
        </span>
    </div>
    ';

$sort_sql = 'SELECT id,sort_name,sort_description FROM sort ORDER BY id ASC';
$sort_result = $con->query($sort_sql);
if($sort_result->num_rows > 0) {
    echo '
      <div class="sort">
      ';
while($sort_row = $sort_result->fetch_assoc()) {
    echo '
      <li class="sort_index">
      <div class="sort_title">
        <img class="sort_icon" src="./static/icon/folder.svg">
        <span>'.$sort_row['sort_name'].'</span>
        <div class="sort_toggle">
            <span>+</span>
        </div>
      </div>
      ';

$board_sql = 'SELECT id,board_name,board_description,date FROM board WHERE sort_id = '.$sort_row['id'].' ORDER BY id ASC';
$board_result = $con->query($board_sql);
//echo '<h1 style="text-align: center; margin: 0;">'.$lang_board_list.'</h1>';
if($board_result->num_rows > 0) {
    echo '
        <ol class="board_ol">
      ';
while($board_row = $board_result->fetch_assoc()) {
  $total_article_sql = 'SELECT id FROM article WHERE board_id = '.$board_row['id'].'';
  $total_article_result = $con->query($total_article_sql);
  $total_article = $total_article_result->num_rows;

  $total_reply_sql = 'SELECT id FROM reply WHERE board_id = '.$board_row['id'].'';
  $total_reply_result = $con->query($total_reply_sql);
  $total_reply = $total_reply_result->num_rows;

  $latest_post_sql = 'SELECT id,username,title,date FROM article WHERE board_id = '.$board_row['id'].' ORDER BY id DESC LIMIT 1';
  $latest_post_result = $con->query($latest_post_sql);
  if($latest_post_result->num_rows > 0) {
    $latest_post_row = $latest_post_result->fetch_assoc();
    $format = 'Y-m-d';
    $date = date($format, strtotime($latest_post_row['date']));
    if(mb_strlen($latest_post_row['title'],'utf-8') > 8) {
    $latest_post_title = mb_substr($latest_post_row['title'],0,8,'utf-8').'...';
  } else {
    $latest_post_title = mb_substr($latest_post_row['title'],0,8,'utf-8');
  }
  }
    echo '
        <li class="board_list">
            <div class="board_img">
                <img class="board_icon" src="./static/icon/board_icon.svg">
            </div>
            <div class="board_text">
                <div class="board_title">
                  <span>
                    <a href=./board.php?board_id='.$board_row['id'].'>
                      <span>'.$board_row['board_name'].'</span>
                    </a>
                  </span>
                </div>
                <div class="board_info">
                  <p class="p1">'.$lang_article.':'.$total_article.'</p>
                  <p class="p1">'.$lang_reply.':'.$total_reply.'</p>
                </div>
            </div>
        ';

        if($latest_post_result->num_rows > 0) {
            echo '
                <div class="board_latest">
                    <div class="board_latest_title">
                        <span>'.$lang_board_latest_post.':</span>
                        <a href=./article.php?id='.$latest_post_row['id'].' title="'.$latest_post_row['title'].'">'.$latest_post_title.'</a>
                    </div>
                    <div class="board_latest_info">
                        <span>'.$latest_post_row['username'].'</span>
                        <span>'.$date.'</span>
                    </div>
                </div>
                ';
        }
echo '
    </li>
     ';
}
echo '
    </ol>
    ';
}
}
echo '
        </li>
    </div>
    ';
} else {
  echo '
        <div class="novalue">
          <a>'.$lang_index_content_empty.'</a>
        </div>
      ';
}
?>

<script>
$(document).ready(function() {
    $(".sort_toggle").click(function(){ 
        $(this).next().slideToggle();
    })
});
</script>

<?php require dirname(__FILE__).'/source/include/footer.php'; ?>