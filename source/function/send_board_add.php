<?php
header('content-type:text/html;charset=utf-8');
require dirname(__FILE__).'/../include/header.php';

/* Check login */
if($now_admin == 1) {
if(!empty($_SESSION['username'])) {
  echo '';
} else {
    echo '
    <script>
      alert("'.$lang_not_login.'");location.href="../login.php";
    </script>
    ';
  exit();
}  
} else {
    echo '
    <script>
      alert("'.$lang_not_admin.'");location.href="../";
    </script>
    ';
  exit();
}

if(isset($_POST['add_board'])) {
    $Board_name = input_safety($_POST['board_name']);
    $Board_description = input_safety($_POST['board_description']);
    $Time = date('Y-m-d H:i:s');
    $success = '1';
    $Board_name_error = $Board_description_error = '';

    if(empty($Board_name)) {
      $Board_name_error = $lang_board_name_empty;
      $success = '0';
    }
    if(empty($Board_description)) {
      $Board_description_error = $lang_board_description_empty;
      $success = '0';
    }

    if($success == '1') {
    $sql = 'INSERT INTO board (board_name,board_description,date) VALUES (\''.$Board_name.'\',\''.$Board_description.'\',\''.$Time.'\')';
    $result = $con->query($sql);
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
    echo '<a>'.$lang_send_board_add_1.'<span id="refresh">1</span>'.$lang_send_board_add_2.'</a>';
    echo '<br />';
    echo '<a class="ifnorefresh" href="../">'.$lang_no_refresh.'</a>';
    echo '</div>';
} else {
    echo '';
}
} else {
    header('Location: ../');
}
?>

<script>
  <?php
  if($Board_name_error != '')
  {
    echo 'alert("'.$Board_name_error.'");'."\n";
    echo 'history.go(-1);'."\n";
  }

  if($Board_description_error != '')
  {
    echo 'alert("'.$Board_description_error.'");'."\n";
    echo 'history.go(-1);'."\n";
  }
  ?>
</script>

<?php require dirname(__FILE__).'/../include/footer.php'; ?>