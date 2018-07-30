<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, minimum-scale=1.0 ,maximum-scale=1.0, initial-scale=1" user-scalable="no">
    <title>Installation</title>
    <link href="./static/static.php?file=command.css" rel="stylesheet" type="text/css" />
    <link href="./static/static.php?file=mobile-command.css" rel="stylesheet" type="text/css" />
    <link href="./static/static.php?file=table-command.css" rel="stylesheet" type="text/css" />
    <link href="./favicon.ico" rel="shortcut icon" />
    <script src="../static/js/jquery.min.js" type="text/javascript"></script>
</head>
<?php require dirname(__FILE__).'/include/inc.check.php';?>
<body>
    <div id="mainwrapper">
        <header id="header">
            <div id="logo">
                <img id="logo-img" src="./static/icon/logo.png" alt="logo" />
            </div>
        </header>
        <div class="install">
            <div class="install_div">
                <h4>Database</h4>
                <div id="checkbox"></div>
                <div id="display"></div>
                <form id="install" action="./include/data.php" method="post">
                    <input type="text" class="install_input" name="db_host" id="db_host" value="localhost" />
                    <input type="text" class="install_input" name="db_name" id="db_name" value="messageboard" />
                    <input type="text" class="install_input" name="db_port" id="db_port" value="3306" />
                    <input type="text" class="install_input" name="db_user" id="db_user" value="root" />
                    <input type="text" class="install_input" name="db_password" id="db_password" value="root" />
                    <div class="horizon"></div>
                    <h4>Administrator</h4>
                    <input type="text" class="install_input" name="admin_username" id="admin" placeholder="<?=$lang_admin;?>" maxlength="20" />
                    <input type="password" class="install_input" name="admin_password" id="admin_psw" placeholder="<?=$lang_password;?>" maxlength="20" />
                    <input type="password" class="install_input" name="admin_psw_confirm" id="admin_psw_confirm" placeholder="<?=$lang_confirm;?>" maxlength="20" />
                    <input type="text" class="install_input" name="email" id="email" placeholder="<?=$lang_email;?>" maxlength="30" />
                    <input type="text" class="install_input" name="recaptcha_site" id="recaptcha_site" placeholder="reCaptcha SiteKey" />
                    <input type="text" class="install_input" name="recaptcha_secret" id="recaptcha_secret" placeholder="reCaptcha SecretKey" />
                    <div class="install_submit">
                      <button type="submit" name="submit"><?=$lang_install_next;?></button>
                    </div>
                </form>
            </div>
        </div>
        <script>
        $(document).ready(function() {
            $("#install").submit(function() {
                var password1 = $('#admin_psw').val();
                var password2 = $('#admin_psw_confirm').val();
                var password_max = $('#admin_psw').val().length;
                if (password_max > 20) {
                    $('#display').html("<span style='color: red'>Word Count : 20</span>");
                    return false;
                } else if (password1 !== password2) {
                    $('#display').html("<span style='color: red'><?php echo $lang_repassword_error; ?></span>");
                    return false;
                } else if (password1 == '') {
                    $('#display').html("<span style='color: red'><?php echo $lang_password_empty; ?></span>");
                    return false;
                } else {
                    $('#display').html("<span style='color: green'><?php echo $lang_repassword_pass; ?></span>");
                    return true;
                }
            });

            $('#db_host').blur(function() {
                var db_host = $('#db_host').val();
                if (db_host == '') {
                    $('#checkbox').html("<span style='color: red'><?php echo $lang_db_host_empty; ?></span>");
                    return false;
                } else {
                    $('#checkbox').html("");
                    return true;
                }
            });

            $('#db_name').blur(function() {
                var db_name = $('#db_name').val();
                if (db_name == '') {
                    $('#checkbox').html("<span style='color: red'><?php echo $lang_db_name_empty; ?></span>");
                    return false;
                } else {
                    $('#checkbox').html("");
                    return true;
                }
            });

            $('#db_port').blur(function() {
                var db_port = $('#db_port').val();
                if (db_port == '') {
                    $('#checkbox').html("<span style='color: red'><?php echo $lang_db_port_empty; ?></span>");
                    return false;
                } else {
                    $('#checkbox').html("");
                    return true;
                }
            });

            $('#db_user').blur(function() {
                var db_user = $('#db_user').val();
                if (db_user == '') {
                    $('#checkbox').html("<span style='color: red'><?php echo $lang_db_user_empty; ?></span>");
                    return false;
                } else {
                    $('#checkbox').html("");
                    return true;
                }
            });

            $('#db_password').blur(function() {
                var db_password = $('#db_password').val();
                if (db_password == '') {
                    $('#checkbox').html("<span style='color: red'><?php echo $lang_db_password_empty; ?></span>");
                    return false;
                } else {
                    $('#checkbox').html("");
                    return true;
                }
            });

            function isValidEmailAddress(emailAddress) {
                var pattern = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return pattern.test(emailAddress);
            }

            $('#email').blur(function() {
                var email = $('#email').val();
                if (email == '') {
                    $('#checkbox').html("<span style='color: red'><?php echo $lang_email_empty; ?></span>");
                    return false;
                } else if(!isValidEmailAddress(email)) {
                    $('#checkbox').html("<span style='color: red'><?php echo $lang_email_format_error; ?></span>");
                    return false;
                } else {
                    $('#checkbox').html("");
                    return true;
                }
            });

            $('#admin').blur(function() {
                var username = $('#admin').val();
                if (username == '') {
                    $('#checkbox').html("<span style='color: red'><?php echo $lang_username_empty; ?></span>");
                    return false;
                } else {
                    $('#checkbox').html("");
                    return true;
                }
            });

            $("#admin_psw").blur(function() {
                var password = $('#admin_psw').val();
                var password_max = $('#admin_psw').val().length;
                if (password_max > 20) {
                    $('#checkbox').html("<span style='color: red'>Word Count : 20</span>");
                    return false;
                } else if (password == '') {
                    $('#checkbox').html("<span style='color: red'><?php echo $lang_password_empty; ?></span>");
                    return false;
                } else {
                    $('#checkbox').html("");
                    return true;
                }
            });

            $("#admin_psw_confirm").blur(function() {
                var password1 = $('#admin_psw').val();
                var password2 = $('#admin_psw_confirm').val();
                if (password1 !== password2) {
                    $('#checkbox').html("<span style='color: red'><?php echo $lang_repassword_error; ?></span>");
                    return false;
                } else {
                    $('#checkbox').html("");
                    return true;
                }
            });

            $('#recaptcha_site').blur(function() {
                var recaptcha_site = $('#recaptcha_site').val();
                if (recaptcha_site == '') {
                    $('#checkbox').html("<span style='color: red'>reCaptcha Site Empty !</span>");
                    return false;
                } else {
                    $('#checkbox').html("");
                    return true;
                }
            });

            $('#recaptcha_secret').blur(function() {
                var recaptcha_secret = $('#recaptcha_secret').val();
                if (recaptcha_secret == '') {
                    $('#checkbox').html("<span style='color: red'>reCaptcha Secret Empty !</span>");
                    return false;
                } else {
                    $('#checkbox').html("");
                    return true;
                }
            });
        });
        </script>
<?php $today = date("l F jS, Y");?>
    <footer>
      <div id="footericon">
          <a href="mailto:carry0987@gmail.com" title="carry0987 Email" style="text-decoration:none;" target="_blank" rel="noopener">
          <img class="imgspace" src="<?=$base_url;?>/static/icon/email.svg"  width="30px"/>
          </a>
          <a href="https://www.facebook.com/carry0987/" title="carry0987 Facebook" style="text-decoration:none;" target="_blank" rel="noopener">
          <img class="imgspace" src="<?=$base_url;?>/static/icon/facebook.svg"  width="30px"/>
          </a>
          <a href="https://github.com/carry0987/" title="carry0987 GitHub" style="text-decoration:none;" target="_blank" rel="noopener">
          <img class="imgspace" src="<?=$base_url;?>/static/icon/github.svg"  width="30px"/>
          </a>
      </div>
      <?php echo "\t".'<p>&copy; 2017 - '.$today.'</p>'."\n"; ?>
      <div class="change_language">
        <select class="language" name="change_language" onChange="location = this.options[this.selectedIndex].value;">
          <option selected="selected" disabled="disabled" hidden="hidden">Language : <?=$display_language;?></option>
          <option value="<?=$base_url;?>/source/function/change_language.php?lang=en_US">English</option>
          <option value="<?=$base_url;?>/source/function/change_language.php?lang=zh_TW">Chinese</option>
          <option value="<?=$base_url;?>/source/function/change_language.php?lang=ja_JP">Japanese</option>
          <option value="<?=$base_url;?>/source/function/change_language.php?lang=th_TH">Thai</option>
        </select>
      </div>
      <div id="footer">
          <p>
            Made By <a class="developer" href="https://carry0987.github.io/" target="_blank" rel="noopener">carry0987</a> <?='v'.PROGRAM_VERSION;?>
          </p>
      </div>
    </footer>
  </div>
</body>
</html>