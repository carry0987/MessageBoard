<?php
header('content-type:text/html;charset=utf-8');
require dirname(__FILE__).'/include/header.php';

if(!empty($_GET['id'])) {
  echo '';
} else {
  header('Location: ./');
}

echo '
<div id="cssmenu">
    <ul>
';
  echo $menu_index;
if(!empty($_SESSION['username']))
{
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

$sql = 'SELECT id,username,title,content,board_id,sort_id,date FROM article WHERE id = '.$_GET["id"];
$result = $con->query($sql);

if(isset($_GET['id'])) {
  if($_GET['id'] == '') {
  header('Location: ./');
  } else {
  $id = $_GET['id'];
  }
} else {
  $id = 1;
}

if($result) {
if($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  $format = 'Y-m-d';
  $publish_date = date($format, strtotime($row['date']));
  $board_sql = 'SELECT board_name FROM board WHERE id = '.$row['board_id'];
  $board_result = $con->query($board_sql);
  if($board_result) {
    $board_row = $board_result->fetch_assoc();
    $board_from = $board_row['board_name'];
  } else {
    $board_from = '';
  }

  $sort_sql = 'SELECT sort_name FROM sort WHERE id = '.$row['sort_id'];
  $sort_result = $con->query($sort_sql);
  if($sort_result) {
    $sort_row = $sort_result->fetch_assoc();
    $sort_from = $sort_row['sort_name'];
  } else {
    $sort_from = '';
  }

  echo "<div class='content'>\n";
  echo "<div class='path'>\n";
  echo "</div>\n";
  echo "<h1>".$row["title"]."</h1>\n";
  echo '<p class="content_from">'.$lang_board_from.' '.$sort_from.', '.$board_from.',</p>';
  echo '<p class="content_author">'.$lang_started_by.' '.$row['username'].',</p>';
  echo '<p class="content_date">'.$publish_date.'</p>';
  echo "<div class='content_main'>\n";
  echo "<div class='content_author_info'>\n";
  echo '<p class="author">'.$lang_author.'：'.$row['username'].'</p>'."\n";
  echo '<p class="date">'.$lang_publish_date.'：'.$row['date'].'</p>'."\n";
  echo "</div>\n";
  echo "<div class='content_box'>\n".htmlspecialchars_decode($row['content'])."\n</div>\n";
  echo "</div>";
  echo "</div>";
} else {
  echo '
      <div class="novalue">
        <a>'.$lang_content_not_found.'</a>
      </div>
      ';
}
} else {
  echo '<h1>'.$lang_system_error.'</h1>';
  exit();
}
?>

<?php
/* Breadcrumb */
$index_url = (isset($_SERVER['HTTPS'])?"https":"http")."://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]";
$board_url = (isset($_SERVER['HTTPS'])?"https":"http")."://$_SERVER[HTTP_HOST]$_SERVER[PHP_SELF]";
$article_url = (isset($_SERVER['HTTPS'])?"https":"http")."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
echo '
    <span itemscope="itemscope" itemtype="http://schema.org/BreadcrumbList">
        <span itemscope="itemscope" itemtype="http://schema.org/ListItem" itemprop="itemListElement">
            <a href="'.str_replace("article.php","",$index_url).'" itemprop="item">
                <span itemprop="name">'.$main_name.'</span>
                <meta content="1" itemprop="position" />
            </a>
        </span>
    </span>
    <span itemscope="itemscope" itemtype="http://schema.org/BreadcrumbList">
        <span itemscope="itemscope" itemtype="http://schema.org/ListItem" itemprop="itemListElement">
            <a href="'.str_replace("article.php","board.php?board_id=".$row['board_id'],$board_url).'" itemprop="item">
                <span itemprop="name">'.$board_row['board_name'].'</span>
                <meta content="2" itemprop="position" />
            </a>
        </span>
    </span>
    <span itemscope="itemscope" itemtype="http://schema.org/BreadcrumbList">
        <span itemscope="itemscope" itemtype="http://schema.org/ListItem" itemprop="itemListElement">
            <a href="'.$article_url.'" itemprop="item">
                <span itemprop="name">'.$row['title'].'</span>
                <meta content="3" itemprop="position" />
            </a>
        </span>
    </span>
    ';
?>

<?php require dirname(__FILE__).'/include/footer.php'; ?>