<?php
header('content-type:text/html;charset=utf-8');
ob_start();
require dirname(__FILE__).'/source/include/header.php';
if(!empty($_SESSION['username'])) {
  $name = $_SESSION['username'];
} else {
  echo '
    <script>
      alert("'.$lang_not_login.'");location.href="login.php";
    </script>
  ';
  exit();
}
$change_title = ob_get_contents();
ob_end_clean();
if (!empty($name)) {
  $page_title = $name.'\'s Home - '.$main_name;
} else {
  $page_title = 'Home - '.$main_name;
}
$change_title = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1'.$page_title.'$3', $change_title);
echo $change_title;

echo '
<div id="cssmenu">
    <ul>
';
  echo $menu_index;
if(!empty($_SESSION['username'])) {
  echo $menu_logout;
} else {
  echo $menu_login;
  echo $menu_signup;
}
echo '
    </ul>
</div>
';

if(isset($_GET['page'])) {
  if($_GET['page'] == '1' || $_GET['page'] == '0' || $_GET['page'] == '') {
    header('Location: ./home.php');
  } else {
    $page = $_GET['page'];
  }
} else {
  $page = 1;
}

/* Page Script */
$results_per_page = 7;
$showpage = 3;
$this_page_first_result = ($page-1)*$results_per_page;
$sql = 'SELECT id,username,title,board_id,date FROM article WHERE username = '."\"$name\"".' ORDER BY id DESC LIMIT '.$this_page_first_result.','.$results_per_page;
$result = $con->query($sql);

$total_sql = 'SELECT id FROM article WHERE username = '."\"$name\"";
$total_result = $con->query($total_sql);
$total = $total_result->num_rows;
$total_pages = ceil($total/$results_per_page);
$pageoffset = ($showpage-1)/2;

if(!empty($_SESSION['username'])) {
  $current_user_sql = 'SELECT id FROM user WHERE username = '."\"$name\"";
  $current_user_result = $con->query($current_user_sql);
  $current_user_row = $current_user_result->fetch_array();
  echo '<h1 style="text-align: center; margin: 0;">'.$lang_article_my_list.'</h1>';

  if($row = $result->num_rows > 0) {
    echo '
    <div class="home_box">
      <form action="./source/function/article_delete.php?user_id='.$current_user_row['id'].'" method="post">
        <div class="box">
          <div class="box_detail">
            <dl>
              <div class="checkall">
                <input type="checkbox" name="checkall" value="" id="checkall">
                <label for="checkall"><span></span></label>
              </div>
              <dt>
                <span>'.$lang_title.'</span>
              </dt>
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
              <div class="delete">
                <input type="checkbox" name="checkbox[]" value="'.$row['id'].'" id="'.$row["id"].'">
                <label for="'.$row["id"].'"><span></span></label>
              </div>
              <div class="box_title">
                <a class="box_link" href="./article.php?id='.$row["id"].'">'.$title.'</a>
                <span class="box_username">'.$row['username'].'</span>
                <span class="box_date">, '.$date.'</span>
                <div class="home_button">
                  <a class="home_link" href="./source/function/article_edit.php?id='.$row['id'].'">
                    <span>'.$lang_edit.'</span>
                  </a>
                </div>
              </div>
            </li>
           ';
  }

/* Pages */
if($result->num_rows > 5) {
  echo "<div class=\"pages\">\n";
if($page > 1) {
  echo '<a class="pages_tag" href="home.php?&page='.($page-1).'">'.$lang_pre_page.'</a>';
}

$start = 1;
$end = $total_pages;
if($total_pages > $showpage) {
    if($page > $pageoffset + 2) {
      echo '<a class="pages_first" href="home.php?&page=1">1...</a>';
    } elseif($page == $pageoffset + 2) {
      echo '<a class="pages_first" href="home.php?&page=1">1</a>';
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
     echo "\t\t<a class=\"pages_tag\" href=\"home.php?page=".$i."\">".$i."</a>\n";
  }
}

if($total_pages > $showpage && $total_pages > $page + $pageoffset + 1){
  echo "\t\t".'<a class="pages_final" href="home.php?page='.$total_pages.'">...'.$total_pages.'</a>';
} elseif($total_pages > $showpage && $total_pages == $page + $pageoffset + 1) {
  echo '<a class="pages_tag" href="home.php?page='.$total_pages.'">'.$total_pages.'</a>';
}

if($page < $total_pages){
  echo "\n\t\t<a class=\"pages_tag\" href=\"home.php?page=".($page+1)."\">".$lang_next_page."</a>";
}

echo "
  \t</div>
  ";
}

echo '
          </ol>
        </div>
      </div>
      <div class="submit">
        <button type="submit" name="delete_article">'.$lang_delete_article.'</button>
      </div>
    </form>
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
            <a>'.$lang_home_content_empty.'</a>
          </div>
        ';
  }
} else {
  echo '
    <script>
      alert("'.$lang_not_login.'");location.href="login.php";
    </script>
  ';
}
?>

<script>
$(document).ready(function() {
    $("#checkall").click(function() {
        if ($("#checkall").prop("checked")) {
            $("input[name='checkbox[]']").each(function() {
                $(this).prop("checked", true);
            })
        } else {
            $("input[name='checkbox[]']").each(function() {
                $(this).prop("checked", false);
            })
        }
    })
})
</script>

<?php require dirname(__FILE__).'/source/include/footer.php'; ?>