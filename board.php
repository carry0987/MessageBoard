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

/* Today Post */
$today_string = date("Y-m-d");
$today_sql = 'SELECT id FROM msg WHERE date = '."\"$today_string\"";
$today_result = $con->query($today_sql);
$today = $today_result->num_rows;

echo '
  <div class="board_name">
    <table>
      <tbody>
        <tr>
          <th><a href="././board.php?board_id='.$_GET['board_id'].'">'.$board_row['board_name'].'</a></th>
          <td>'.$lang_today_post.':<a class="total_post">'.$today.'</a></td>
          <td>'.$lang_total_post.':<a class="total_post">'.$total.'</a></td>
          <td><img class="refresh" src="./static/image/refresh.svg"></td>
        </tr>
      </tbody>
    </table>
  </div>
  ';
echo '
  <script type="text/javascript">
    var degrees = 0;
      $(".refresh").click(function(){
        degrees += 360;
        $(this).css({
          "transform" : "rotate("+degrees+"deg)",
          "-ms-transform" : "rotate("+degrees+"deg)",
          "-moz-transform" : "rotate("+degrees+"deg)",
          "-webkit-transform" : "rotate("+degrees+"deg)",
          "-o-transform" : "rotate("+degrees+"deg)"
        });
      });
  </script>
  ';
//echo '<h1 style="text-align: center; margin: 0;">'.$lang_message_list.'</h1>';
echo '<br />';

if($result->num_rows > 0) {
    echo '
    <div class="box">
      <table>
        <tbody>
          <tr>
            <th>'.$lang_title.'</th>
            <td class="by">'.$lang_author.'</td>
          </tr>
        </tbody>
      </table>
      <table>
      ';
while($row = $result->fetch_assoc()) {
  $format = 'Y-m-d';
  $date = date($format, strtotime($row['date']));
    echo '
          <tbody class="box_tbody">
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
?>

<?php require dirname(__FILE__).'/include/footer.php'; ?>