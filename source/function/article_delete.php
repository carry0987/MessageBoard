<?php
header('content-type:text/html;charset=utf-8');
require dirname(__FILE__).'/../include/header.php';

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
          alert("'.$lang_delete_success.$_SESSION['username'].'");location.href="'.$base_url.'/home.php";
        </script>
        ';
    } else {
        echo '
        <script>
          alert("'.$lang_delete_empty.'");location.href="'.$base_url.'home.php";
        </script>
        ';
    }
} else {
    header('Location: '.$base_url.'');
    exit();
}
} else {
    echo '
    <script>
      alert("'.$lang_not_admin.'");location.href="'.$base_url.'";
    </script>
    ';
    exit();
}
?>

<?php require dirname(__FILE__).'/../include/footer.php'; ?>