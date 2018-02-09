<?php
header('content-type:text/html;charset=utf-8');
require dirname(__FILE__).'/include/header.php';
require dirname(__FILE__).'/admin/check_database.php';

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

$sql = 'SELECT id,board_name,board_description,date FROM board ORDER BY id ASC';
$result = $con->query($sql);

echo '<h1 style="text-align: center; margin: 0;">'.$lang_board_list.'</h1>';

if($result->num_rows > 0) {
    echo '
    <div class="board">
        <ol>
      ';
while($row = $result->fetch_assoc()) {
  $format = 'Y-m-d';
  $date = date($format, strtotime($row['date']));
  if(mb_strlen($row['board_description'],'utf-8') > 50) {
    $board_description = mb_substr($row['board_description'],0,50,'utf-8').'...';
  } else {
    $board_description = mb_substr($row['board_description'],0,50,'utf-8');
  }
    echo '
            <li>
                <div class="board_list">
                    <span>
                      <a href=./board.php?board_id='.$row['id'].'>
                        <span>'.$row['board_name'].'</span>
                      </a>
                    </span>
                    <p class="p1">'.$board_description.'</p>
                    <p class="p2">
                      <em>
                        <span>'.$date.'</span>
                      </em>
                    </p>
                </div>
            </li>
         ';
}
echo '
    </ol>
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

<?php require dirname(__FILE__).'/include/footer.php'; ?>