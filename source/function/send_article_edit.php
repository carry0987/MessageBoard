<?php
require dirname(__FILE__).'/../include/header.php';

if(!empty($_GET['id'])) {
  echo '';
} else {
  header('Location: ../');
}

/* Check if not author */
$check_edit_sql = 'SELECT username FROM article WHERE id = '.$_GET['id'];
$check_edit_result = $conn->query($check_edit_sql);
$check_edit_row = $check_edit_result->fetch_assoc();

if (isset($_POST['edit_article']) && $_SESSION['username'] == $check_edit_row['username']) {
  $Title = input_filter($_POST['title']);
  $Content = input_filter($_POST['article']);
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

if($success === '1') {
  $sql_edit = 'UPDATE article SET content = \''.$Content.'\',title = \''.$Title.'\' WHERE id = '.$_GET['id'];
    if ($con->query($sql_edit) === true) {
      echo '
      <script type="text/javascript">
      setTimeout("countdown()", 1000);
      function countdown() {
          var s = document.getElementById("refresh");
          s.innerHTML = s.innerHTML - 1;
          if (s.innerHTML == 0)
              window.location = "'.$base_url.'";
          else
              setTimeout("countdown()", 1000);
      }
      </script>
      ';
      echo '<div class="infomation">';
      echo '<a>'.$lang_send_edit_1.'<span id="refresh">1</span>'.$lang_send_edit_2.'</a>';
      echo '<br />';
      echo '<a class="ifnorefresh" href="'.$base_url.'">'.$lang_no_refresh.'</a>';
      echo '</div>';
} else {
    echo "Error updating report: ".$con->error;
}
}
} else {
    echo '
      <script>
        alert("'.$lang_not_author.'");location.href="'.$base_url.'/home.php";
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

<?php require dirname(__FILE__).'/../include/footer.php'; ?>