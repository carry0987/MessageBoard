<?php
header('content-type:text/html;charset=utf-8');
require dirname(__FILE__).'/include/header.php';
require dirname(__FILE__).'/admin/check_database.php';

echo '
<div id="cssmenu">
    <ul>
';
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
  echo $menu_message;
echo '
    </ul>
</div>
';

if(isset($_GET['page'])) {
  if($_GET['page'] == '1' || $_GET['page'] == '' || $_GET['page'] == '0') {
  header('Location: ./');
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
$sql = 'SELECT id,username,title,date FROM msg ORDER BY id DESC LIMIT '.$this_page_first_result.','.$results_per_page;
$result = $con->query($sql);

$total_sql = 'SELECT id FROM msg';
$total_result = $con->query($total_sql);
$total = $total_result->num_rows;
$total_pages = ceil($total/$results_per_page);
$pageoffset = ($showpage-1)/2;

echo '<h1 style="text-align: center; margin: 0;">'.$lang_public_list.'</h1>';

if($row = $result->num_rows > 0) {
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
  echo '<a class="pages_tag" href="index.php?page='.($page-1).'">'.$lang_pre_page.'</a>';
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
      echo "<a class='active'>$i</a>";
  }else{  
     echo "<a class='pages_tag' href='index.php?page=".$i."'>$i</a>";
  }
}

if($total_pages > $showpage && $total_pages > $page + $pageoffset){
   echo '<a class="pages_more">...</a>';
}

if($page < $total_pages){
echo '<a class="pages_tag" href="index.php?page='.($page+1).'">'.$lang_next_page.'</a>';
}
echo "\t</div>\n";
}
?>

<?php require './include/footer.php'; ?>