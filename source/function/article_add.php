<?php
require dirname(__FILE__).'/../include/header.php';

$board_sql = 'SELECT id FROM board WHERE id = '.input_safety($_GET['board_id']).'';
$board_result = $con->query($board_sql);
if($board_result) {
  $board_row = $board_result->fetch_assoc();
  $Board_id = $board_row['id'];
} else {
  header('Location: '.$base_url.'');
  exit();
}

if(!isset($_SESSION['username'])) {
  echo '
    <script>
      alert("'.$lang_not_login.'");location.href="'.$base_url.'/login.php";
    </script>
      ';
  exit($lang_not_login);
}
?>
<link rel="stylesheet" href="<?=$base_url;?>/static/editor.php?editor=default.css" id="theme-style" />
<script src="<?=$base_url;?>/static/editor.php?editor=sceditor.js"></script>
<script src="<?=$base_url;?>/static/js/editor/development/icons/monocons.js"></script>
<script src="<?=$base_url;?>/static/js/editor/development/formats/bbcode.js"></script>
<div class="article_div">
    <form class="article_form" action="./send_article_add.php?board_id=<?=$Board_id;?>" method="post">
        <span><input class="article-input" id="title" type="text" name="title" placeholder="Title" maxlength="20"></span>
        <span class="word_left"><?=$lang_word_left;?>：<span id="display">20</span></span>
        <textarea class="article-text" id="article" type="text" name="article" rows="7" cols="50" maxlength="100"></textarea>
        <div class="submit">
          <button type="submit" name="submit"><?=$lang_submit;?></button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
  var textarea = document.getElementById("article");
  sceditor.create(textarea, {
      ltr: true,
      format: "bbcode",
      icons: "monocons",
      style: "<?=$base_url;?>/static/js/editor/development/themes/content/default.css"
  })

  function word_left() {
      var maxlength = parseInt($('.article-input').attr('maxlength'));
      var NowEntry =  parseInt($('.article-input').val().length);
      var left = maxlength - NowEntry;
      NowEntry = maxlength <= NowEntry  ? NowEntry : NowEntry+1;
      left = maxlength <= NowEntry ? left : left;
      $('#display').text(left);
  };

  $('.article-input').keyup(function() {
     word_left();
  })
});
</script>


<?php require ROOT_PATH.'/include/footer.php'; ?>