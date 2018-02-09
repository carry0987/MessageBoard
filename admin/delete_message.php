<?php
header('content-type:text/html;charset=utf-8');
require dirname(__FILE__).'/header_command.php';

if (isset($_POST['delete_message']) && $now_admin == 1) {
    if(!empty($_POST['checkbox'])) {
        $delete = $_POST['checkbox'];
        foreach($delete as $selected_massage) {
            $delete_sql = 'DELETE FROM msg WHERE id = '.$selected_massage;
            $con->query($delete_sql);
        }
        echo '
        <script>
          alert("'.$lang_delete_success.$_SESSION['username'].'");location.href="../admin.php";
        </script>
        ';
    } else {
        echo '
        <script>
          alert("'.$lang_delete_empty.'");location.href="../admin.php";
        </script>
        ';
    }
} else {
    echo '
    <script>
      alert("'.$lang_not_admin.'");location.href="../";
    </script>
    ';
    exit();
}
?>

<?php require dirname(__FILE__).'/footer_command.php'; ?>