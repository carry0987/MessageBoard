<?php
header('content-type:text/html;charset=utf-8');
ob_start();
require dirname(__FILE__).'/include/header.php';
require dirname(__FILE__).'/admin/check_database.php';

$change_title = ob_get_contents();
ob_end_clean();
$page_title = $lang_login.' - '.$main_name;
$change_title = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1'.$page_title.'$3', $change_title);
echo $change_title;

//Check login
if (!empty($_SESSION['username'])) {
  echo '<script>';
  echo 'alert("'.$lang_already_login.'");location.href="./"';
  echo '</script>';
}

  echo '
  <div id="cssmenu">
      <ul>
  ';
  echo $menu_index;
  echo $menu_signup;
  echo $menu_message;
  echo '
      </ul>
  </div>
  ';

echo '<div class="login_div">';
$Online = 1;
$Username_Err = $Password_Err = '';
if(isset($_POST["submit"])){
  $Username = input_safety($_POST["username"]);
  $Password = input_safety($_POST["password"]);

  if($Online){
    $OK = 1;
    $sql = 'SELECT username,password FROM user WHERE username = '."\"$Username\"";
    $result = $con->query($sql);
    $row = $result->fetch_assoc();
    if($result->num_rows == 0){
      $Username_Err = $lang_username_not_exist;
      $OK = 0;
    }
    elseif($row["password"] != $Password){
      $Password_Err = $lang_wrong_password;
      $OK = 0;
    }
    if($OK) {
      echo '
        <script type="text/javascript">
        setTimeout("countdown()", 1000);
        function countdown() {
            var s = document.getElementById("refresh");
            s.innerHTML = s.innerHTML - 1;
            if (s.innerHTML == 0) {
                window.location = "./";
            } else {
                document.getElementById("username").readOnly = true;
                document.getElementById("password").readOnly = true;
                setTimeout("countdown()", 1000);
            }
        }
        </script>
          ';

      $_SESSION['username'] = $row['username'];

      echo '<div class="infomation">';
      echo '<a>'.$lang_login_info_1.'<span id="refresh">1</span>'.$lang_login_info_2.'</a>';
      echo '<br />';
      echo '<a class="ifnorefresh" href="./">'.$lang_no_refresh.'</a>';
      echo '</div>';
    }
  }
}
?>

<?php
if(!isset($_POST['submit'])) {
echo '
  <form class="login_form" onsubmit="return login_check();" action="login.php" method="post">
    <table class="login">
      <tr>
        <td class="login-a">'.$lang_username.'：</td>
        <td class="login-a">
          <input class="login-input" type="text" name="username" id="username" placeholder="Username" maxlength="10">
        </td>
      </tr>
      <tr>
        <td class="login-a">'.$lang_password.'：</td>
        <td class="login-a">
          <input class="login-input" type="password" name="password" id="password" placeholder="Password" maxlength="20">
        </td>
      </tr>
    </table>
    <br />
    <div class="submit">
      <button type="submit" name="submit">'.$lang_login.'</button>
    </div>
  </form>
</div>';
}
?>
<script>
  function login_check(){
  if(document.getElementById("username").value == '')
    {
      <?php echo "alert(\"$lang_username_empty\");"; ?>
      document.getElementById("username").focus();
      return false;
    }
    else if(document.getElementById("password").value == '')
    {
      <?php echo "alert(\"$lang_password_empty\");"; ?>
      document.getElementById("password").focus();
      return false;
    }
    return true;
  }
  <?php
  if($Username_Err != '')
  {
    echo "alert(\"$Username_Err\");location.href='./login.php';";
  }

  if($Password_Err != '')
  {
    echo "alert(\"$Password_Err\");location.href='./login.php';";
  }
  ?>
</script>

<?php require dirname(__FILE__).'/include/footer.php'; ?>