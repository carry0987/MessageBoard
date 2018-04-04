<?php
header('content-type:text/html;charset=utf-8');
require dirname(__FILE__).'/source/include/header.php';
require dirname(__FILE__).'/source/function/check_database.php';

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

/* Get Board Name */
$board_sql = 'SELECT board_name,board_description FROM board WHERE id = '.input_safety($_GET['board_id']);
$board_result = $con->query($board_sql);
if($board_result) {
  $board_row = $board_result->fetch_assoc();
} else {
  header('Location: '.$base_url.'');
  exit();
}

/* Breadcrumb */
$index_url = (isset($_SERVER['HTTPS'])?"https":"http").'://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
$board_url = (isset($_SERVER['HTTPS'])?"https":"http").'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
if ($board_result && $board_result->num_rows > 0) {
  if(mb_strlen($board_row['board_name'],'utf-8') > 8) {
      $board_name = mb_substr($board_row['board_name'],0,8,'utf-8').'...';
    } else {
      $board_name = mb_substr($board_row['board_name'],0,8,'utf-8');
    }
echo '
    <div class="breadcrumbs">
      <span itemscope="itemscope" itemtype="http://schema.org/BreadcrumbList">
          <span itemscope="itemscope" itemtype="http://schema.org/ListItem" itemprop="itemListElement">
              <a class="fileTrail" href="'.str_replace("board.php","",$index_url).'" itemprop="item">
                  <span class="breadcrumbs_home" itemprop="name">'.$main_name.'</span>
                  <img class="breadcrumbs_img" src="'.$base_url.'/static/icon/home.svg">
                  <meta content="1" itemprop="position" />
              </a>
          </span>
      </span>
      <span class="fileTrailDividers">></span>
      <span itemscope="itemscope" itemtype="http://schema.org/BreadcrumbList">
          <span itemscope="itemscope" itemtype="http://schema.org/ListItem" itemprop="itemListElement">
              <a class="fileTrail" href="'.$board_url.'" itemprop="item">
                  <span class="fileTrailCurrent" itemprop="name">'.$board_name.'</span>
                  <meta content="2" itemprop="position" />
              </a>
          </span>
      </span>
    </div>
    ';
}

/* Check URL */
if(!empty($_GET['board_id']) && ctype_digit($_GET['board_id'])) {
if(isset($_GET['page'])) {
  if($_GET['page'] == '1' || $_GET['page'] == '' || $_GET['page'] == '0') {
  header('Location: '.$base_url.'/board.php?board_id='.$_GET['board_id'].'');
  } else {
  $page = $_GET['page'];
  }
} else {
  $page = 1;
}
} else {
  header('Location: '.$base_url.'');
}

/* Page Script */
$results_per_page = 7;
$showpage = 3;
$this_page_first_result = ($page-1)*$results_per_page;
$sql = 'SELECT id,username,title,board_id,date FROM article WHERE board_id = '.input_safety($_GET['board_id']).' 
ORDER BY id DESC LIMIT '.$this_page_first_result.','.$results_per_page;
$result = $con->query($sql);

$total_sql = 'SELECT board_id FROM article WHERE board_id = '.input_safety($_GET['board_id']).'';
$total_result = $con->query($total_sql);
$total = $total_result->num_rows;
$total_pages = ceil($total/$results_per_page);
$pageoffset = ($showpage-1)/2;

/* Today Post */
$today_string = date("Y-m-d");
$today_sql = 'SELECT id FROM article WHERE DATE(date) = '."\"$today_string\"";
$today_result = $con->query($today_sql);
$today = $today_result->num_rows;

echo '<div class="board">';

echo '
  <div class="board_name">
    <table>
      <tbody>
        <tr>
          <th><a href="././board.php?board_id='.$_GET['board_id'].'">'.$board_row['board_name'].'</a></th>
          <td>'.$lang_total_post.':<a class="total_post">'.$total.'</a></td>
        </tr>
      </tbody>
    </table>
    <div class="board_description">
      <span>'.$board_row['board_description'].'</span>
    </div>
  </div>
  ';

echo '
  <div class="post">
    <div class="post_button">
        <a class="post_link" href="./source/function/article_add.php?board_id='.input_safety($_GET['board_id']).'">
          <span>'.$lang_post_article.'</span>
        </a>
    </div>
  ';

/* Pages Top */
if($result->num_rows > 0) {
  echo "\t<div class=\"pages\">\n";
if($page > 1) {
  echo '<a class="pages_tag" href="board.php?board_id='.$_GET['board_id'].'&page='.($page-1).'">'.$lang_pre_page.'</a>';
}

$start = 1;
$end = $total_pages;
if($total_pages > $showpage) {
    if($page > $pageoffset + 2) {
      echo '<a class="pages_first" href="board.php?board_id='.$_GET['board_id'].'&page=1">1...</a>';
    } elseif($page == $pageoffset + 2) {
      echo '<a class="pages_first" href="board.php?board_id='.$_GET['board_id'].'&page=1">1</a>';
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
      echo "\t\t<a class=\"active\">".$i."</a>\n";
  } else {  
     echo "\t\t<a class=\"pages_tag\" href=\"board.php?board_id=".$_GET['board_id']."&page=".$i."\">".$i."</a>\n";
  }
}

if($total_pages > $showpage && $total_pages > $page + $pageoffset + 1){
  echo '<a class="pages_final" href="board.php?board_id='.$_GET['board_id'].'&page='.$total_pages.'">...'.$total_pages.'</a>';
} elseif($total_pages > $showpage && $total_pages == $page + $pageoffset + 1) {
  echo '<a class="pages_tag" href="board.php?board_id='.$_GET['board_id'].'&page='.$total_pages.'">'.$total_pages.'</a>';
}

if($page < $total_pages){
  echo "\t\t<a class=\"pages_tag\" href=\"board.php?board_id=".$_GET['board_id']."&page=".($page+1)."\">".$lang_next_page."</a>";
}

echo "
  \t</div>
  </div>
  ";
} else {
echo "
  </div>
  ";
}

/* Box */
if($result->num_rows > 0) {
    echo '
    <div class="box">
      <div class="box_detail">
        <dl>
          <dt><span>'.$lang_title.'</span></dt>
          <dd class="box_reply">'.$lang_reply.'</dd>
          <dd class="box_latest_reply">'.$lang_final_reply.'</dd>
        </dl>
        <ol class="box_ol">
      ';
while($row = $result->fetch_assoc()) {
  if(mb_strlen($row['title'],'utf-8') > 20) {
      $title = mb_substr($row['title'],0,20,'utf-8').'...';
    } else {
      $title = mb_substr($row['title'],0,20,'utf-8');
    }
  $format = 'Y-m-d';
  $date = date($format, strtotime($row['date']));
    echo '
          <li class="box_list">
            <div class="box_title">
              <a class="box_link" href=./article.php?id='.$row['id'].' target="_blank">'.$title.'</a>
              <span>'.$row['username'].'</span>
              <span>, '.$date.'</span>
            </div>
          </li>
         ';
}
echo '
        </ol>
      </div>
    </div>
    ';
} elseif ($total_result->num_rows > 0) {
  echo '
        <div class="novalue">
          <a>'.$lang_page_not_found.'</a>
        </div>
      </div>
      ';
} else {
  echo '
        <div class="novalue">
          <a>'.$lang_index_content_empty.'</a>
        </div>
      </div>
      ';
}

/* Pages Bottom */
if($result->num_rows > 5) {
  echo '
    <div class="post">
      <div class="post_button">
          <a class="post_link" href="./source/function/article_add.php?board_id='.input_safety($_GET['board_id']).'">
            <span>'.$lang_post_article.'</span>
          </a>
      </div>
    ';
  echo "\t<div class=\"pages\">\n";
if($page > 1) {
  echo '<a class="pages_tag" href="board.php?board_id='.$_GET['board_id'].'&page='.($page-1).'">'.$lang_pre_page.'</a>';
}

$start = 1;
$end = $total_pages;
if($total_pages > $showpage) {
    if($page > $pageoffset + 2) {
      echo '<a class="pages_first" href="board.php?board_id='.$_GET['board_id'].'&page=1">1...</a>';
    } elseif($page == $pageoffset + 2) {
      echo '<a class="pages_first" href="board.php?board_id='.$_GET['board_id'].'&page=1">1</a>';
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
      echo "\t\t<a class=\"active\">".$i."</a>\n";
  } else {  
     echo "\t\t<a class=\"pages_tag\" href=\"board.php?board_id=".$_GET['board_id']."&page=".$i."\">".$i."</a>\n";
  }
}

if($total_pages > $showpage && $total_pages > $page + $pageoffset + 1){
  echo '<a class="pages_final" href="board.php?board_id='.$_GET['board_id'].'&page='.$total_pages.'">...'.$total_pages.'</a>';
} elseif($total_pages > $showpage && $total_pages == $page + $pageoffset + 1) {
  echo '<a class="pages_tag" href="board.php?board_id='.$_GET['board_id'].'&page='.$total_pages.'">'.$total_pages.'</a>';
}

if($page < $total_pages){
  echo "\t\t<a class=\"pages_tag\" href=\"board.php?board_id=".$_GET['board_id']."&page=".($page+1)."\">".$lang_next_page."</a>";
}

echo "
  \t</div>
  </div>
</div>
  ";
} else {
echo "
  </div>
  ";
}
?>

<?php require dirname(__FILE__).'/source/include/footer.php'; ?>