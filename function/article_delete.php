<?php
header('content-type:text/html;charset=utf-8');
require dirname(__FILE__).'/header_command.php';

if($now_admin == 1) {
if (isset($_POST['delete_article'])) {
    if(!empty($_POST['checkbox'])) {
        $delete = input_safety($_POST['checkbox']);
        foreach($delete as $selected_article) {
            $delete_sql = 'DELETE FROM article WHERE id = '.$selected_article;
            $con->query($delete_sql);
        }
        echo '
        <script>
          alert("'.$lang_delete_success.$_SESSION['username'].'");location.href="../home.php";
        </script>
        ';
    } else {
        echo '
        <script>
          alert("'.$lang_delete_empty.'");location.href="../home.php";
        </script>
        ';
    }
} else {
    header('Location: ../');
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
?>

<?php require dirname(__FILE__).'/footer_command.php'; ?>