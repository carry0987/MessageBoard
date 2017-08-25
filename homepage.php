<?php
header('content-type:text/html;charset=utf-8');
ob_start();
require dirname(__FILE__).'/include/header.php';
$name = $_SESSION['username'];
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
if(!empty($_SESSION['username']))
{
  echo $menu_index;
  echo $menu_logout;
} else {
  echo $menu_login;
  echo $menu_signup;
}
  echo $menu_message;
echo '
    </ul>
</div>
';

if(isset($_GET['page'])) {
  if($_GET['page'] == '1' || $_GET['page'] == '0' || $_GET['page'] == '') {
    header('Location: ./homepage.php');
  } else {
    $page = $_GET['page'];
  }
} else {
  $page = 1;
}

$results_per_page = 7;
$showpage = 3;
$this_page_first_result = ($page-1)*$results_per_page;
$sql = 'SELECT id,username,title,date FROM msg WHERE username = '."\"$name\"".' ORDER BY id DESC LIMIT '.$this_page_first_result.','.$results_per_page;
$result = $con->query($sql);

$total_sql = 'SELECT id FROM msg WHERE username = '."\"$name\"";
$total_result = $con->query($total_sql);
$total = $total_result->num_rows;
$total_pages = ceil($total/$results_per_page);
$pageoffset = ($showpage-1)/2;

if(!empty($_SESSION['username'])) {
echo '<h1 style="text-align: center; margin: 0;">'.$lang_own_list.'</h1>';

  if($row = $result->num_rows > 0) {
      echo '
      <div class="box">
        <table>
        ';
  while ($row = $result->fetch_assoc()) {
    $format = 'Y-m-d';
    $date = date($format, strtotime($row['date']));
      echo '
            <tbody>
              <tr>
                <th>
                  <a href=./content_edit.php?id='.$row["id"].'>'.$row["title"].'</a>
                </th>
                <td class="by">
                  <cite>
                    <a>'.$row["username"].'</a>
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
            <a>'.$lang_homepage_content_empty.'</a>
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

/* Pages */
if($result->num_rows > 0) {
  echo "<div class=\"pages\">\n";
if($page > 1) {
  echo '<a class="pages_tag" href="homepage.php?page='.($page-1).'">'.$lang_next_page.'</a>';
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
     echo '<a class="pages_tag" href="homepage.php?page='.$i.'">'.$i.'</a>';
  }
}

if($total_pages > $showpage && $total_pages > $page + $pageoffset){
   echo '<a class="pages_more">...</a>';
}

if($page < $total_pages){
echo '<a class="pages_tag" href="homepage.php?page='.($page+1).'">'.$lang_next_page.'</a>';
}
echo "\t</div>\n";
}
?>

<?php require dirname(__FILE__).'/include/footer.php'; ?>