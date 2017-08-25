<?php
header('content-type:text/html;charset=utf-8');
require 'header_command.php';

if(!empty($_GET['id'])) {
  echo '';
} else {
  header('Location: ../');
}

/* Check if not author */
$check_edit_sql = 'SELECT username FROM msg WHERE id='.$_GET['id'];
$check_edit_result = $con->query($check_edit_sql);
$check_edit_row = $check_edit_result->fetch_assoc();

if (isset($_POST['change_mg']) && $_SESSION['username'] == $check_edit_row['username']) {
  $Title = input_safety($_POST['title']);
  $Content = input_safety($_POST['content']);
  $success = '1';
  $Title_Error = $Content_Error = '';

  if(empty($Title)) {
    $Title_Error = $lang_title_empty;
    $success = '0';
  }
  if(empty($Content)) {
    $Content_Error = $lang_content_send_empty;
    $success = '0';
  }

if($success == '1') {
  $sql_edit = 'UPDATE msg SET content = \''.$Content.'\',title = \''.$Title.'\' WHERE id = '.$_GET['id'];
    if ($con->query($sql_edit) === TRUE) {
      echo '
      <script type="text/javascript">
      setTimeout("countdown()", 1000);
      function countdown() {
          var s = document.getElementById("refresh");
          s.innerHTML = s.innerHTML - 1;
          if (s.innerHTML == 0)
              window.location = "../";
          else
              setTimeout("countdown()", 1000);
      }
      </script>
      ';
      echo '<div class="infomation">';
      echo '<a>'.$lang_send_edit_1.'<span id="refresh">1</span>'.$lang_send_edit_2.'</a>';
      echo '<br />';
      echo '<a class="ifnorefresh" href="../">'.$lang_no_refresh.'</a>';
      echo '</div>';
} else {
    echo "Error updating report: ".$con->error;
}
}
} else {
    echo '
      <script>
        alert("'.$lang_not_author.'");location.href="../homepage.php";
      </script>
        ';
}
?>

<script>
  <?php
  if($Title_Error != '')
  {
    echo 'alert("'.$Title_Error.'");'."\n";
    echo 'history.go(-1);'."\n";
  }

  if($Content_Error != '')
  {
    echo 'alert("'.$Content_Error.'");'."\n";
    echo 'history.go(-1);'."\n";
  }
  ?>
</script>

<?php require 'footer_command.php'; ?>