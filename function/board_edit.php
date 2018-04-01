<?php
header('content-type:text/html;charset=utf-8');
ob_start();
require dirname(__FILE__).'/include/header.php';
$change_title = ob_get_contents();
ob_end_clean();
$page_title = 'Board Edit'.' - '.$main_name;
$change_title = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1'.$page_title.'$3', $change_title);
echo $change_title;

echo '
<div id="cssmenu">
    <ul>
';
echo $menu_admin;
echo $menu_index;
echo $menu_logout;
echo '
    </ul>
</div>
';

/* Select Config */
$sql = 'SELECT board_name,board_description FROM board WHERE id = '.$_GET['board_id'];
$result = $con->query($sql);

/* Show Edit Board */
if($result) {
if($now_admin == 1 && $result->num_rows > 0) {
$row = $result->fetch_assoc();
    echo '
    <div class="setting_div">
      <form action="./function/send_board_edit.php?board_id='.$_GET['board_id'].'" method="post">
        <table class="setting">
          <tbody>
            <tr>
              <td>'.$lang_board_name.'</td>
              <td>
                  <input class="setting-input" type="text" name="board_name" maxlength="20" placeholder="Board Name" value="'.$row['board_name'].'"/>
              </td>
            </tr>
            <tr>
            <td>'.$lang_board_description.'</td>
            <td>
              <textarea class="setting-textarea"
              type="text" name="board_description" placeholder="Description" 
              rows="7" cols="50" maxlength="200">'.$row['board_description'].'</textarea>
            </td>
            </tr>
          </tbody>
        </table>
        <br />
        <div class="submit_edit">
            <button type="submit" name="board_edit">'.$lang_edit.'</button>
        </div>
      </form>
    </div>
';
} elseif($now_admin == 0) {
  echo '
    <script>
      alert("'.$lang_not_admin.'");location.href="login.php";
    </script>
  ';
}
} else {
  echo '<h1>Database Error !</h1>';
  exit();
}
?>

<?php require dirname(__FILE__).'/include/footer.php'; ?>