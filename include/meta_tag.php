<?php
header('content-type:text/html;charset=utf-8');

if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = 'SELECT title,content FROM msg WHERE id = '.$id;
} else {
    $sql = 'SELECT web_name,web_description FROM config WHERE id = 1';
}

$result = $con->query($sql);
$web = 'SELECT web_name,web_description FROM config WHERE id = 1';
$web_result = $con->query($web);

if($web_result) {
    if ($web_result->num_rows > 0) {
    while($web_row = $web_result->fetch_assoc()) {
        $main_name = $web_row['web_name'];
        $main_description = $web_row['web_description'];
    }
    }
} else {
    $main_name = 'MessageBoard';
    $main_description = 'This MessageBoard was made by carry0987';
}

$server_url = (isset($_SERVER['HTTPS'])?"https":"http")."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$dns_prefetch = (isset($_SERVER['HTTPS'])?"https":"http")."://$_SERVER[HTTP_HOST]";
echo "<link rel=\"dns-prefetch\" href=\"$dns_prefetch\">\n";

if($result) {
if (!empty($_GET['id']) && $result->num_rows > 0) {
while($row = $result->fetch_assoc()) {
    echo "\t<title>".$row["title"].' - '.$main_name."</title>\n";
    echo "\t<meta name=\"description\" content=\"".strip_tags(htmlspecialchars_decode($row['content']))."\">\n";
    echo "\t<meta property=\"og:title\" content=\"".$row["title"].' - '.$main_name."\">\n";
    echo "\t<meta property=\"og:description\" content=\"".strip_tags(htmlspecialchars_decode($row["content"]))."\">\n";
    echo "\t<meta property=\"og:site_name\" content=\"".$main_name."\">\n";
    echo "\t<meta property=\"og:url\" content=\"".$server_url."\">\n";
}
} else {
    echo "\t<title>".$main_name."</title>\n";
    echo "\t<meta name=\"description\" content=\"".$main_description."\">\n";
    echo "\t<meta property=\"og:title\" content=\"".$main_name."\">\n";
    echo "\t<meta property=\"og:description\" content=\"".$main_description."\">\n";
    echo "\t<meta property=\"og:site_name\" content=\"".$main_name."\">\n";
    echo "\t<meta property=\"og:url\" content=\"".$server_url."\">\n";
}
}
?>