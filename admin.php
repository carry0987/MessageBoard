<?php
header('content-type:text/html;charset=utf-8');
ob_start();
require dirname(__FILE__).'/include/header.php';
$change_title = ob_get_contents();
ob_end_clean();
$page_title = 'Control Center - '.$main_name;
$change_title = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1'.$page_title.'$3', $change_title);
echo $change_title;

if($now_admin != 1) {
  echo '
    <script>
      alert("'.$lang_not_admin.'");location.href="./";
    </script>
  ';
  exit();
}

echo '
<div id="cssmenu">
    <ul>
';
if(!empty($_SESSION['username']))
{
  echo $menu_index;
  echo $menu_logout;
} else {
  echo $menu_login;
  echo $menu_signup;
}
  echo $menu_setting;
echo '
    </ul>
</div>
';

if(isset($_GET['action'])) {
  $action = $_GET['action'];
} else {
  $action = '';
}

if(!empty($action) && $action == 'message_manager' || $action == 'board_manager') {
  if($action == 'message_manager') {
    require dirname(__FILE__).'/admin/admin_message_manager.php';
  } elseif($action == 'board_manager') {
    require dirname(__FILE__).'/admin/admin_board_manager.php';
  }
} else {
  echo '
  <div class="admin">
    <table>
      <tbody>
        <tr>
          <td>
            <a href="./admin.php?action=message_manager">'.$lang_message_manager.'</a>
          </td>
          <td>
            <a href="./admin.php?action=board_manager">'.$lang_board_manager.'</a>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
      ';
}

?>

<?php require dirname(__FILE__).'/include/footer.php'; ?>