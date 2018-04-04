<?php
header('content-type:text/html;charset=utf-8');
require dirname(__FILE__).'/../include/header.php';

if (isset($_POST['change_setting']) && $now_admin == 1) {
    $setting_name = input_safety($_POST['name']);
    $setting_description = input_safety($_POST['description']);
    $setting_sql = 'UPDATE config SET web_name = \''.$setting_name.'\',web_description = \''.$setting_description.'\' WHERE id = 1';
    $con->query($setting_sql);
    echo '
    <script>
      alert("'.$lang_edit_success.'");location.href="../";
    </script>
    ';
} else {
    echo '
    <script>
      alert("'.$lang_not_admin.'");location.href="../";
    </script>
    ';
    exit();
}
?>

<?php require dirname(__FILE__).'/../include/footer.php'; ?>