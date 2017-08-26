<?php
header('content-type:text/html;charset=utf-8');

if($now_admin != 1) {
  echo '
    <script>
      alert("'.$lang_not_admin.'");location.href="./";
    </script>
  ';
  exit();
}

echo '<h1 style="text-align: center; margin: 0;">'.$lang_board_manager.'</h1>';

/* Show Boards */
$sql = 'SELECT id,board_name,date FROM board';
$result = $con->query($sql);

if($result) {
if($result->num_rows > 0) {
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
                  <a href=./board_edit.php?board_id='.$row["id"].'>'.$row['board_name'].'</a>
                </th>
                <td class="by">
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
  } else {
    echo '
          <div class="novalue">
            <a>'.$lang_board_empty.'</a>
          </div>
        ';
  }
}

echo '
  <div class="add_board">
    <form class="message_form" name="board_add" action="./admin/send_board_add.php" method="post">
        <table class="message">
          <tr>
            <td class="message-title">'.$lang_board_name.'：</td>
            <td class="message-title">
                <input class="message-input" id="title" type="text" name="board_name" id="board_name" placeholder="Board Name" maxlength="20">
            </td>
          </tr>
          <tr>
            <td class="message-content">'.$lang_board_description.'：</td>
            <td class="message-content">
              <textarea class="message-text" id="board_description" 
              type="text" name="board_description" placeholder="Board Description" 
              rows="7" cols="30" maxlength="300"></textarea>
            </td>
          </tr>
        </table>
        <br />
        <div class="submit">
          <button type="submit" name="add_board">'.$lang_add_board.'</button>
        </div>
    </form>
  </div>
';
?>