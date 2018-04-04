<?php
header('content-type:text/html;charset=utf-8');
ob_start();
require dirname(__FILE__).'/../include/header.php';
$change_title = ob_get_contents();
ob_end_clean();
require dirname(__FILE__).'/check_database.php';

if(!empty($_GET['id'])) {
  echo '';
} else {
  header('Location: '.$base_url.'');
}

$edit_title = 'SELECT title FROM article WHERE id='.$_GET['id'];
$edit_result = $con->query($edit_title);

if($edit_result) {
if($edit_result->num_rows > 0) {
  while($edit_row = $edit_result->fetch_assoc()) {
    $page_title = $edit_row['title'].' - Edit - '.$main_name;
  }
} else {
    $page_title = 'Not Found !';
}
}

$change_title = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1'.$page_title.'$3', $change_title);
echo $change_title;

echo '
<div id="cssmenu">
    <ul>
';
  echo $menu_index;
if(!empty($_SESSION['username']))
{
  if($now_admin == 1) {
    echo $menu_admin;
  }
  echo $menu_home;
  echo $menu_logout;
} else {
  echo $menu_login;
  echo $menu_signup;
}
echo '
    </ul>
</div>
';

/* Check login */
if (!empty($_SESSION['username'])) {
  echo '';
} else {
  echo '
    <script>
      alert("'.$lang_not_login.'");location.href="login.php";
    </script>
  ';
  exit();
}

/* Check if not author */
$check_user_sql = 'SELECT username FROM article WHERE id='.$_GET['id'];
$check_user_result = $con->query($check_user_sql);
if($check_user_result) {
if($check_user_result->num_rows > 0) {
  while($check_user_row = $check_user_result->fetch_assoc()) {
  if($_SESSION['username'] == $check_user_row['username'] || $now_admin == 1) {
    echo '';
  } else {
    echo '
      <script>
        alert('.$lang_not_author.');location.href="./";
      </script>
        ';
  }
}
} else {
    echo '
          <div class="novalue">
            <a>'.$lang_content_not_found.'</a>
          </div>
        ';
}
}

/* Select Content */
$sql = 'SELECT username,title,content,date FROM article WHERE id='.$_GET['id'];
$result = $con->query($sql);

/* Show Edit Board */
if($result) {
if(!isset($_GET['id']) || $result->num_rows == 0) {
  header('Location: '.$base_url.'');
  exit();
} else {
  $row = $result->fetch_assoc();
}
}
?>

<link rel="stylesheet" href="<?=$base_url;?>/static/editor.php?editor=default.css" id="theme-style" />
<script src="<?=$base_url;?>/static/editor.php?editor=sceditor.js"></script>
<script src="<?=$base_url;?>/static/js/editor/development/icons/monocons.js"></script>
<script src="<?=$base_url;?>/static/js/editor/development/formats/bbcode.js"></script>
<div class="article_div">
  <form class="article-form" action="<?=$base_url;?>/source/function/send_article_edit.php?id=<?=$_GET['id'];?>" method="post">
      <intput type="hidden" name="username" value="<?=$row['username'];?>"/>
      <input type="text" name="title" maxlength="50" value="<?=$row['title'];?>"/>
      <textarea class="article-text" id="article" type="text" name="article" rows="7" cols="50" maxlength="100"><?=$row['content'];?></textarea>
      <div class="submit">
          <button type="submit" name="edit_article"><?=$lang_edit;?></button>
      </div>
  </form>
</div>

<script>
var textarea = document.getElementById("article");
sceditor.create(textarea, {
    ltr: true,
    format: "bbcode",
    icons: "monocons",
    style: "<?=$base_url;?>/static/js/editor/development/themes/content/default.css"
});
</script>

<?php require dirname(__FILE__).'/../include/footer.php'; ?>