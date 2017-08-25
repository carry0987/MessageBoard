<?php
header('content-type:text/html;charset=utf-8');
require 'header_command.php';

if (isset($_POST['submit'])) {
    $Title = input_safety($_POST['title']);
    $Content = input_safety($_POST['message']);
    $OK = '1';
    $TitleErr = $ContentErr = $AnonymousErr = '';
    $Time = date('Y-m-d H:i:s');
    $Username = $_SESSION['username'];
    if(empty($Title)) {
        $TitleErr = $lang_title_empty;
        $OK = '0';
    }
    if(empty($Content)) {
        $ContentErr = $lang_content_send_empty;
        $OK = '0';
    }
    else if(!$_SESSION['username']) {
        $AnonymousErr = $lang_not_login;
        $OK = '0';
    }
    if ($OK == '1') {
        $sql = 'INSERT INTO msg (username,title,content,date) VALUES ('."\"$Username\"".','."\"$Title\"".','."\"$Content\"".','."\"$Time\"".')';
        $con->query($sql);
        echo '
        <script type="text/javascript">
        setTimeout("countdown()", 1000);
        function countdown() {
            var s = document.getElementById("refresh");
            s.innerHTML = s.innerHTML - 1;
            if (s.innerHTML == 0) {
                window.location = "../";
            } else {
                setTimeout("countdown()", 1000);
            }
        }
        </script>
        ';
        echo '<div class="infomation">';
        echo '<a>'.$lang_send_message_1.'<span id="refresh">1</span>'.$lang_send_message_2.'</a>';
        echo '<br />';
        echo '<a class="ifnorefresh" href="../">'.$lang_no_refresh.'</a>';
        echo '</div>';
    }
} else {
    echo '
      <script>
        alert("'.$lang_not_login.'");location.href="../login.php";
      </script>
        ';
}
?>

<script>
  <?php
  if($TitleErr != '')
  {
    echo 'alert("'.$TitleErr.'");'."\n";
    echo 'history.go(-1);'."\n";
  }

  if($ContentErr != '')
  {
    echo 'alert("'.$ContentErr.'");'."\n";
    echo 'history.go(-1);'."\n";
  }

  if($AnonymousErr != '')
  {
    echo 'alert("'.$AnonymousErr.'");location.href="../login.php";';
  }
  ?>
</script>

<?php require 'footer_command.php'; ?>