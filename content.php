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

$sql = 'SELECT id,username,title,content,board_id,date FROM msg WHERE id = '.$_GET["id"];
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
    echo "<div class='main'>\n";
    echo "<header>\n";
    echo "<h1>".$row["title"]."</h1>\n";
    echo '<p class="author">'.$lang_author.'：'.$row['username'].'</p>'."\n";
    echo '<p class="date">'.$lang_publish_date.'：'.$row['date'].'</p>'."\n";
    $board_sql = 'SELECT board_name FROM board WHERE id = '.$row['board_id'];
    $board_result = $con->query($board_sql);
    if($board_result) {
      $board_row = $board_result->fetch_assoc();
      $send_from = $board_row['board_name'];
    } else {
      $send_from = '';
    }
    echo '<p class="from">'.$lang_board_from.'：'.$send_from.'</p>'."\n";
    echo "</header>\n";
    echo "<div class='box-content'>\n".htmlspecialchars_decode($row['content'])."\n</div>\n";
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

<?php require dirname(__FILE__).'/include/footer.php'; ?>