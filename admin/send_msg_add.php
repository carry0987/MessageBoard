<?php
header('content-type:text/html;charset=utf-8');
require dirname(__FILE__).'/header_command.php';

if (isset($_POST['submit'])) {
    $Title = input_safety($_POST['title']);
    $Content = input_safety($_POST['message']);
    $Board_id = input_safety($_GET['board_id']);
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
        $sql = 'INSERT INTO msg (username,title,content,board_id,date) VALUES ('."\"$Username\"".','."\"$Title\"".','."\"$Content\"".','."\"$Board_id\"".','."\"$Time\"".')';
        $result = $con->query($sql);
        if($result) {
        echo '
        <script type="text/javascript">
        setTimeout("countdown()", 1000);
        function countdown() {
            var s = document.getElementById("refresh");
            s.innerHTML = s.innerHTML - 1;
            if (s.innerHTML == 0) {
                window.location = "../board.php?board_id='.$Board_id.'";
            } else {
                setTimeout("countdown()", 1000);
            }
        }
        </script>
        ';
        echo '<div class="infomation">';
        echo '<a>'.$lang_send_message_1.'<span id="refresh">1</span>'.$lang_send_message_2.'</a>';
        echo '<br />';
        echo '<a class="ifnorefresh" href="../board.php?board_id='.$Board_id.'">'.$lang_no_refresh.'</a>';
        echo '</div>';
      } else {
        echo '<h1>'.$lang_system_error.'</h1>';
      }
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

<?php require dirname(__FILE__).'/footer_command.php'; ?>