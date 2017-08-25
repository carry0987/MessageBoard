<?php
header('content-type:text/html;charset=utf-8');
ob_start();
require dirname(__FILE__).'/include/header.php';
$change_title = ob_get_contents();
ob_end_clean();
$page_title = 'Message - '.$main_name;
$change_title = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1'.$page_title.'$3', $change_title);
echo $change_title;

    echo '
    <div id="cssmenu">
        <ul>
    ';
    echo $menu_index;
    echo $menu_logout;
    echo $menu_message;
    echo '
        </ul>
    </div>
    ';
?>

<?php
if(!empty($_SESSION['username'])) {
echo '
<div class="message_div">
    <form class="message_form" onsubmit="message_check();" action="./admin/send_message.php" method="post">
        <table class="message">
          <tr>
            <td class="message-title">'.$lang_title.'：</td>
            <td class="message-title">
                <input class="message-input" id="title" type="text" name="title" placeholder="Title" maxlength="50">
            </td>
          </tr>
          <tr>
            <td class="message-content">'.$lang_content.'：</td>
            <td class="message-content">
              <script src="./static/js/ckeditor/ckeditor.js"></script>
                <textarea class="message-text" id="message" 
                type="text" name="message" placeholder="Message" 
                rows="7" cols="50" maxlength="100"></textarea>
              <script>
                CKEDITOR.replace( "message", {});
              </script>
            </td>
          </tr>
        </table>
        <br />
        <div class="submit">
          <button type="submit" name="submit">'.$lang_submit.'</button>
        </div>
    </form>
</div>
';
} else {
echo '
  <script>
    alert("'.$lang_not_login.'");location.href="login.php";
  </script>
';
}
?>

<?php require dirname(__FILE__).'/include/footer.php'; ?>