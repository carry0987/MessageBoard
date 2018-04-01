<?php
header('content-type:text/html;charset=utf-8');
require dirname(__FILE__).'/header_command.php';

$board_sql = 'SELECT id FROM board WHERE id = '.input_safety($_GET['board_id']).'';
$board_result = $con->query($board_sql);
if($board_result) {
  $board_row = $board_result->fetch_assoc();
} else {
  header('Location: ../');
}
$Board_id = $board_row['id'];

if(!empty($_SESSION['username'])) {
  echo '
    <script src="../static/jquery.php?file=jquery.min.js"></script>
    <link rel="stylesheet" href="../static/js/editor/development/themes/default.css" id="theme-style" />
    <script src="../static/js/editor/development/sceditor.js"></script>
    <script src="../static/js/editor/development/icons/monocons.js"></script>
    <script src="../static/js/editor/development/formats/bbcode.js"></script>
    <div class="article_div">
        <form class="article_form" action="./send_article_add.php?board_id='.$Board_id.'" method="post">
            <input class="article-input" id="title" type="text" name="title" placeholder="Title" maxlength="50">
            <textarea class="article-text" id="article" type="text" name="article" rows="7" cols="50" maxlength="100"></textarea>
            <div class="submit">
              <button type="submit" name="submit">'.$lang_submit.'</button>
            </div>
        </form>
    </div>
    <script>
    var textarea = document.getElementById("article");
    sceditor.create(textarea, {
        format: "bbcode",
        icons: "monocons",
        style: "../static/js/editor/development/themes/content/default.css"
    });
    </script>
      ';
} else {
  echo '
    <script>
      alert("'.$lang_not_login.'");location.href="../login.php";
    </script>
      ';
}


?>

<?php require dirname(__FILE__).'/footer_command.php'; ?>