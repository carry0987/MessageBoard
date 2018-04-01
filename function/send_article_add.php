<?php
header('content-type:text/html;charset=utf-8');
require dirname(__FILE__).'/header_command.php';

if(isset($_POST['submit'])) {
  $Title = input_safety($_POST['title']);
  $Content = input_safety($_POST['article']);
  $Board_id = input_safety($_GET['board_id']);
  $sort_sql = 'SELECT id,sort_id FROM board WHERE id = '.$Board_id.'';
  $sort_result = $con->query($sort_sql);
  if($sort_result) {
    $sort_row = $sort_result->fetch_assoc();
  } else {
    header('Location: ../');
  }
  $Sort_id = $sort_row['sort_id'];
  $OK = '1';
  $TitleErr = $ContentErr = $AnonymousErr = 'wtf';
  $Time = date('Y-m-d H:i:s');
  if(!empty($_SESSION['username'])) {
    $Username = $_SESSION['username'];
  } else {
    $Username == '';
  }
  
  if(empty($Title)) {
      $TitleErr = $lang_title_empty;
      $OK = '0';
  }
  if(empty($Content)) {
      $ContentErr = $lang_content_send_empty;
      $OK = '0';
  }
  if($Username == '') {
      $AnonymousErr = $lang_not_login;
      $OK = '0';
  } else {
      $Username = $_SESSION['username'];
      $AnonymousErr = 'wtf';
  }

  if($OK == '1') {
      $sql = 'INSERT INTO article (username,title,content,board_id,sort_id,date) VALUES (
      '."\"$Username\"".','."\"$Title\"".','."\"$Content\"".','."\"$Board_id\"".','."\"$Sort_id\"".','."\"$Time\"".')';
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
      echo '<a>'.$lang_send_article_1.'<span id="refresh">1</span>'.$lang_send_article_2.'</a>';
      echo '<br />';
      echo '<a class="ifnorefresh" href="../board.php?board_id='.$Board_id.'">'.$lang_no_refresh.'</a>';
      echo '</div>';
    } else {
      echo '<h1>'.$lang_system_error.'</h1>';
    }
  }
} else {
    echo '
      <script type="text/javascript">
        alert("'.$lang_not_login.'");location.href="../login.php";
      </script>
        ';
}
?>

<script>
  <?php
  if($TitleErr != 'wtf')
  {
    echo 'alert("'.$TitleErr.'");'."\n";
    echo 'history.back(-1);'."\n";
  }

  if($ContentErr != 'wtf')
  {
    echo 'alert("'.$ContentErr.'");'."\n";
    echo 'history.back(-1);'."\n";
  }

  if($AnonymousErr != 'wtf')
  {
    echo 'alert("'.$AnonymousErr.'");location.href="../login.php";';
  }
  ?>
</script>

<?php require dirname(__FILE__).'/footer_command.php'; ?>