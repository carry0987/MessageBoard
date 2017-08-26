<?php
header('content-type:text/html;charset=utf-8');
require dirname(__FILE__).'/admin/check_database.php';
require dirname(__FILE__).'/include/header.php';

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
  echo $menu_homepage;
  echo $menu_logout;
} else {
  echo $menu_login;
  echo $menu_signup;
}
echo '
    </ul>
</div>
';

if(!empty($_GET['board_id'])) {
if(isset($_GET['page'])) {
  if($_GET['page'] == '1' || $_GET['page'] == '' || $_GET['page'] == '0') {
  header('Location: ./board.php?board_id='.$_GET['board_id'].'');
  } else {
  $page = $_GET['page'];
  }
} else {
  $page = 1;
}
} else {
  header('Location: ./');
}

/* Get Board Name */
$board_sql = 'SELECT board_name,board_description FROM board WHERE id = '.input_safety($_GET['board_id']);
$board_result = $con->query($board_sql);
if($board_result) {
  $board_row = $board_result->fetch_assoc();
} else {
  header('Location: ./');
}

/* Page Script */
$results_per_page = 7;
$showpage = 3;
$this_page_first_result = ($page-1)*$results_per_page;
$sql = 'SELECT id,username,title,board_id,date FROM msg WHERE board_id = '.input_safety($_GET['board_id']).' 
ORDER BY id DESC LIMIT '.$this_page_first_result.','.$results_per_page;
$result = $con->query($sql);

$total_sql = 'SELECT board_id FROM msg WHERE board_id = '.input_safety($_GET['board_id']).'';
$total_result = $con->query($total_sql);
$total = $total_result->num_rows;
$total_pages = ceil($total/$results_per_page);
$pageoffset = ($showpage-1)/2;

echo '<h1 style="text-align: center; margin: 0;">'.$board_row['board_name'].'</h1>';
echo '<h1 style="text-align: center; margin: 0;">'.$lang_message_list.'</h1>';
echo '<br />';

if($result->num_rows > 0) {
    echo '
    <div class="box">
      <table>
      ';
while($row = $result->fetch_assoc()) {
  $format = 'Y-m-d';
  $date = date($format, strtotime($row['date']));
    echo '
          <tbody>
            <tr>
              <th>
                <a href=./content.php?id='.$row['id'].' target="_blank">'.$row['title'].'</a>
              </th>
              <td class="by">
                <cite>
                  <a>'.$row['username'].'</a>
                </cite>
                <br />
                <em>
                  <span>'.$date.'</span>
                </em>
              </td>
            </tr>
          </tbody>
         ';
}
echo '
    </table>
  </div>
    ';
} elseif ($total_result->num_rows > 0) {
  echo '
        <div class="novalue">
          <a>'.$lang_page_not_found.'</a>
        </div>
      ';
} else {
  echo '
        <div class="novalue">
          <a>'.$lang_index_content_empty.'</a>
        </div>
      ';
}

/* Pages */
if($result->num_rows > 0) {
  echo "<div class=\"pages\">\n";
if($page > 1) {
  echo '<a class="pages_tag" href="board.php?board_id='.$_GET['board_id'].'&page='.($page-1).'">'.$lang_pre_page.'</a>';
}

$start = 1;
$end = $total_pages;
if($total_pages > $showpage) {
    if($page > $pageoffset + 1) {
        echo '<a class="pages_more">...</a>';
    }

    if($page > $pageoffset) {
      $start = $page - $pageoffset;
        if($end = $total_pages > $page + $pageoffset) {
          $end = $page + $pageoffset;
        } else {
          $end = $total_pages;
        }
    } else {
       $start = 1;
       if($end = $total_pages > $showpage) {
        $end = $showpage;
      } else {
        $end = $total_pages;
    }
  }
    if($page + $pageoffset > $total_pages) {
       $start = $start - ($page + $pageoffset - $end);
    }
} 

for($i = $start; $i <= $end; $i++){
    if($page == $i){ 
      echo '<a class="active">'.$i.'</a>';
  }else{  
     echo '<a class="pages_tag" href="board.php?board_id='.$_GET['board_id'].'&page='.$i.'">'.$i.'</a>';
  }
}

if($total_pages > $showpage && $total_pages > $page + $pageoffset){
   echo '<a class="pages_more">...</a>';
}

if($page < $total_pages){
echo '<a class="pages_tag" href="board.php?board_id='.$_GET['board_id'].'&page='.($page+1).'">'.$lang_next_page.'</a>';
}
echo "\t</div>\n";
}

if(!empty($_SESSION['username'])) {
echo '
<div class="message_div">
    <form class="message_form" onsubmit="message_check();" action="./admin/send_message.php?board_id='.input_safety($_GET['board_id']).'" method="post">
        <table class="message">
          <tr>
            <td class="message-title">'.$lang_title.'：</td>
            <td class="message-title">
                <input class="message-input" id="title" type="text" name="title" placeholder="Title" maxlength="50">
            </td>
          </tr>
          <tr>
            <td class="message-content">'.$lang_content.'：</td>
            <td class="message-content">
              <script src="./static/js/ckeditor/ckeditor.js"></script>
                <textarea class="message-text" id="message" 
                type="text" name="message" placeholder="Message" 
                rows="7" cols="50" maxlength="100"></textarea>
              <script>
                CKEDITOR.replace( "message", {});
              </script>
            </td>
          </tr>
        </table>
        <br />
        <div class="submit">
          <button type="submit" name="submit">'.$lang_submit.'</button>
        </div>
    </form>
</div>
';
} else {
echo '
<div class="message_div">
    <form class="message_form" onsubmit="message_check();" action="./admin/send_message.php?board_id='.input_safety($_GET['board_id']).'" method="post">
        <table class="message">
          <tr>
            <td class="message-content">
                <textarea class="message-text" id="message" 
                type="text" name="message" placeholder="Message" 
                rows="3" cols="20" maxlength="100" readonly="readonly" style="font-size: 3em;">'.$lang_not_login.'</textarea>
            </td>
          </tr>
        </table>
        <br />
        <div class="submit">
          <button type="submit" name="submit" disabled="disabled">'.$lang_submit.'</button>
        </div>
    </form>
</div>
';
}
?>

<?php require dirname(__FILE__).'/include/footer.php'; ?>