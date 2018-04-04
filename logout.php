<?php
ob_start();
require dirname(__FILE__).'/source/include/header.php';
$change_title = ob_get_contents();
ob_end_clean();
$page_title = 'Logout - '.$main_name;
$change_title = preg_replace('/(<title>)(.*?)(<\/title>)/i', '$1'.$page_title.'$3', $change_title);
echo $change_title;
session_unset();
session_destroy();

    echo '
        <div id="cssmenu">
            <ul>
                <li><a></a></li>
            </ul>
        </div>
        <script type="text/javascript">
        setTimeout("countdown()", 1000);
        function countdown() {
            var s = document.getElementById("refresh");
            s.innerHTML = s.innerHTML - 1;
            if (s.innerHTML == 0) {
                window.location = "'.$base_url.'";
            } else {
                setTimeout("countdown()", 1000);
            }
        }
        </script>
        ';
    echo '<div class="infomation" style="height: 200px;">';
    echo '<a>'.$lang_logout_info_1.'<span id="refresh">1</span>'.$lang_logout_info_2.'</a>';
    echo '<br>';
    echo '<a class="ifnorefresh" href="'.$base_url.'">'.$lang_no_refresh.'</a>';
    echo '</div>';
?>
<?php require dirname(__FILE__).'/source/include/footer.php'; ?>