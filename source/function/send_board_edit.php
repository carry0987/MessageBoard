<?php
require dirname(__FILE__).'/../include/header.php';

if(!empty($_GET['board_id'])) {
  echo '';
} else {
  header('Location: ../');
}

if (isset($_POST['board_edit']) && $login['admin'] == 1) {
  $board_name = input_filter($_POST['board_name']);
  $board_description = input_filter($_POST['board_description']);
  $success = '1';
  $Board_Name_Error = $Board_Description_Error = '';

  if(empty($board_name)) {
    $Board_Name_Error = $lang_board_name_empty;
    $success = '0';
  }
  if(empty($board_description)) {
    $Board_Description_Error = $lang_board_description_empty;
    $success = '0';
  }

if($success == '1') {
  $sql_edit = 'UPDATE board SET board_name = \''.$board_name.'\', board_description = \''.$board_description.'\' WHERE id = '.$_GET['board_id'];
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
        alert("'.$lang_not_admin.'");location.href="../";
      </script>
        ';
}
?>

<script>
  <?php
  if($Board_Name_Error != '')
  {
    echo 'alert("'.$Board_Name_Error.'");'."\n";
    echo 'history.go(-1);'."\n";
  }

  if($Board_Description_Error != '')
  {
    echo 'alert("'.$Board_Description_Error.'");'."\n";
    echo 'history.go(-1);'."\n";
  }
  ?>
</script>

<?php require dirname(__FILE__).'/../include/footer.php'; ?>