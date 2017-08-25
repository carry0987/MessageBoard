<?php
header('content-type:text/html;charset=utf-8');
require dirname(__FILE__).'/admin/check_database.php';
ob_start();
require dirname(__FILE__).'/include/header.php';
$change_title = ob_get_contents();
ob_end_clean();
if(!empty($_GET['id'])) {
  echo '';
} else {
  header('Location: ./');
}
$edit_title = 'SELECT title FROM msg WHERE id='.$_GET['id'];
$edit_result = $con->query($edit_title);
if($edit_result) {
if($edit_result->num_rows > 0) {
  while($edit_row = $edit_result->fetch_assoc()) {
    $page_title = $edit_row['title'].' - Edit - '.$main_name;
  }
} else {
    $page_title = 'Not Found !';
}
}
$change_title = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1'.$page_title.'$3', $change_title);
echo $change_title;

echo '
<div id="cssmenu">
    <ul>
';
if(!empty($_SESSION['username']))
{
  if($now_admin == 1) {
    echo $menu_admin;
  }
  echo $menu_homepage;
  echo $menu_logout;
} else {
  echo $menu_login;
  echo $menu_signup;
}
  echo $menu_message;
echo '
    </ul>
</div>
';

/* Check login */
if (!empty($_SESSION['username'])) {
  echo '';
} else {
  echo '
    <script>
      alert("'.$lang_not_login.'");location.href="login.php";
    </script>
  ';
  exit();
}

/* Check if not author */
$check_user_sql = 'SELECT username FROM msg WHERE id='.$_GET['id'];
$check_user_result = $con->query($check_user_sql);
if($check_user_result) {
if($check_user_result->num_rows > 0) {
  while($check_user_row = $check_user_result->fetch_assoc()) {
  if($_SESSION['username'] == $check_user_row['username'] || $now_admin == 1) {
    echo '';
  } else {
    echo '
      <script>
        alert('.$lang_not_author.');location.href="./";
      </script>
        ';
  }
}
} else {
    echo '
          <div class="novalue">
            <a>'.$lang_content_not_found.'</a>
          </div>
        ';
}
}

/* Select Content */
$sql = 'SELECT username,title,content,date FROM msg WHERE id='.$_GET['id'];
$result = $con->query($sql);

/* Show Edit Board */
if($result) {
if(!empty($_GET['id']) && $row = $result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
          echo '
          <form action="./admin/send_edit.php?id='.$_GET['id'].'" method="post">
          <table class="content_edit">
            <tbody>
              <tr>
                <intput type="hidden" name="username" value="'.$row['username'].'"/>
                <td>
                <input type="text" name="title" maxlength="50" value="'.$row['title'].'"/>
                </td>
              </tr>
              <tr>
               <td>
               <script src="./static/js/ckeditor/ckeditor.js"></script>
                    <textarea class="ckeditor" id="message-edit"
                    type="text" name="content" placeholder="Message" 
                    rows="7" cols="50" maxlength="100">'.$row['content'].'</textarea>
                </td>
              </tr>
            </tbody>
          </table>
          <div class="submit_edit">
              <button type="submit" name="change_mg">'.$lang_edit.'</button>
          </div>
          </form>
     ';
  }
}
}
?>

<?php require dirname(__FILE__).'/include/footer.php'; ?>