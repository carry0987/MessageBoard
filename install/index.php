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
    <script src="./static/js/jquery.min.js" type="text/javascript"></script>
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
                <h4><?=$LANG['common']['database'];?></h4>
                <div id="checkbox"></div>
                <form id="install" action="./include/data.php" method="post">
                    <input type="text" class="install_input" name="db_host" id="db_host" value="localhost" />
                    <input type="text" class="install_input" name="db_name" id="db_name" value="messageboard" />
                    <input type="text" class="install_input" name="db_port" id="db_port" value="3306" />
                    <input type="text" class="install_input" name="db_user" id="db_user" value="root" />
                    <input type="text" class="install_input" name="db_password" id="db_password" value="root" />
                    <div class="change_language">
                        <label class="web-language">
                            <span><?=$LANG['common']['default_language'];?> : </span>
                        </label>
                        <select class="language" name="lang">
<?php if (is_array($web_lang_list)) foreach($web_lang_list as $lang_list['key'] => $lang_list['value']) { 
$current_lang_select = ($lang_list['key'] === $current_lang) ? 'selected="selected"' : '';
?>
                            <option <?=$current_lang_select;?> value="<?=$lang_list['key'];?>"><?=$lang_list['value'];?></option>
<?php } ?>
                        </select>
                    </div>
                    <div class="horizon"></div>
                    <div class="email_config">
                        <label><?=$LANG['admin']['email_function'];?>:</label>
                        <div class="radio">
                            <input id="enable" name="email_set" type="radio" value="enable" checked="checked" />
                            <label for="enable" class="radio-label"><?=$LANG['common']['enable'];?></label>
                        </div>
                        <div class="radio">
                            <input id="disable" name="email_set" type="radio" value="disable" />
                            <label for="disable" class="radio-label"><?=$LANG['common']['disable'];?></label>
                        </div>
                        <div class="function_notif">
                            <span><?=$LANG['admin']['email_disable_warning'];?></span>
                            <br />
                            <span><?=$LANG['admin']['email_disable_only'];?></span>
                        </div>
                    </div>
                    <div class="horizon"></div>
                    <h4><?=$LANG['common']['admin'];?></h4>
                    <div id="display"></div>
                    <input type="text" class="install_input" name="admin_display_name" id="admin_display_name" placeholder="<?=$LANG['common']['display_name'];?>" maxlength="20" />
                    <input type="text" class="install_input" name="admin_username" id="admin" placeholder="<?=$LANG['common']['username'];?>" maxlength="20" />
                    <input type="password" class="install_input" name="admin_password" id="admin_psw" placeholder="<?=$LANG['common']['password'];?>" maxlength="20" />
                    <input type="password" class="install_input" name="admin_psw_confirm" id="admin_psw_confirm" placeholder="<?=$LANG['common']['password_confirm'];?>" maxlength="20" />
                    <input type="email" class="install_input" name="email" id="email" placeholder="<?=$LANG['common']['email'];?>" maxlength="80" />
                    <div class="install_submit">
                      <button type="submit" name="submit"><?=$LANG['install']['install_next'];?></button>
                    </div>
                </form>
            </div>
        </div>
        <script type="text/javascript">
        $(document).on('change blur keydown keyup click', function() {
            var empty = $('input').filter(function() {
                return $.trim($(this).val()).length == 0
            }).length == 0;
            if ($('#checkbox').html() == '' && $('#display').html() == '' && empty === true) {
                $('.install_submit button').prop('disabled', false);
            } else {
                $('.install_submit button').prop('disabled', true);
            }
        });
        $(document).ready(function() {
            $('.install_submit button').prop('disabled', true);
            $("#install").submit(function() {
                var password1 = $('#admin_psw').val();
                var password2 = $('#admin_psw_confirm').val();
                var password_max = $('#admin_psw').val().length;
                if (password_max > 20) {
                    $('#display').html("<span style='color: red'>Word Count : 20</span>");
                    return false;
                } else if (password1 !== password2) {
                    $('#display').html("<span style='color: red'><?php echo $LANG['common']['repassword_error']; ?></span>");
                    return false;
                } else if (password1 == '') {
                    $('#display').html("<span style='color: red'><?php echo $LANG['common']['password_empty']; ?></span>");
                    return false;
                } else {
                    $('#display').html("<span style='color: green'><?php echo $LANG['common']['repassword_pass']; ?></span>");
                    return true;
                }
            });

            $('#db_host').blur(function() {
                var db_host = $('#db_host').val();
                if (db_host == '') {
                    $('#checkbox').html("<span style='color: red'><?php echo $LANG['database']['db_host_empty']; ?></span>");
                    return false;
                } else {
                    $('#checkbox').html("");
                    return true;
                }
            });

            $('#db_name').blur(function() {
                var db_name = $('#db_name').val();
                if (db_name == '') {
                    $('#checkbox').html("<span style='color: red'><?php echo $LANG['database']['db_name_empty']; ?></span>");
                    return false;
                } else {
                    $('#checkbox').html("");
                    return true;
                }
            });

            $('#db_port').blur(function() {
                var db_port = $('#db_port').val();
                if (db_port == '') {
                    $('#checkbox').html("<span style='color: red'><?php echo $LANG['database']['db_port_empty']; ?></span>");
                    return false;
                } else {
                    $('#checkbox').html("");
                    return true;
                }
            });

            $('#db_user').blur(function() {
                var db_user = $('#db_user').val();
                if (db_user == '') {
                    $('#checkbox').html("<span style='color: red'><?php echo $LANG['database']['db_user_empty']; ?></span>");
                    return false;
                } else {
                    $('#checkbox').html("");
                    return true;
                }
            });

            $('#db_password').blur(function() {
                var db_password = $('#db_password').val();
                if (db_password == '') {
                    $('#checkbox').html("<span style='color: red'><?php echo $LANG['database']['db_password_empty']; ?></span>");
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

            $('#email').on('blur keydown keyup', function() {
                var email = $('#email').val();
                if (email == '') {
                    $('#display').html("<span style='color: red'><?php echo $LANG['common']['email_empty']; ?></span>");
                    return false;
                } else if(!isValidEmailAddress(email)) {
                    $('#display').html("<span style='color: red'><?php echo $LANG['common']['email_format_error']; ?></span>");
                    return false;
                } else if (email.length >= 80) {
                    $('#display').html("<span style='color: red'><?php echo $LANG['common']['email_length_error']; ?></span>");
                    return false;
                } else {
                    $('#display').html("");
                    return true;
                }
            });

            $('#admin_display_name').blur(function() {
                var username = $('#admin_display_name').val();
                if (username == '') {
                    $('#display').html("<span style='color: red'><?php echo $LANG['common']['display_name_empty']; ?></span>");
                    return false;
                } else {
                    $('#display').html("");
                    return true;
                }
            });

            $('#admin').blur(function() {
                var username = $('#admin').val();
                if (username == '') {
                    $('#display').html("<span style='color: red'><?php echo $LANG['common']['username_empty']; ?></span>");
                    return false;
                } else {
                    $('#display').html("");
                    return true;
                }
            });

            $("#admin_psw").blur(function() {
                var password = $('#admin_psw').val();
                var password_max = $('#admin_psw').val().length;
                if (password_max > 20) {
                    $('#display').html("<span style='color: red'>Word Count : 20</span>");
                    return false;
                } else if (password == '') {
                    $('#display').html("<span style='color: red'><?php echo $LANG['common']['password_empty']; ?></span>");
                    return false;
                } else {
                    $('#display').html("");
                    return true;
                }
            });

            $("#admin_psw_confirm").blur(function() {
                var password1 = $('#admin_psw').val();
                var password2 = $('#admin_psw_confirm').val();
                if (password1 !== password2) {
                    $('#display').html("<span style='color: red'><?php echo $LANG['common']['repassword_error']; ?></span>");
                    return false;
                } else {
                    $('#display').html("");
                    return true;
                }
            });
        });
        </script>
<?php $today = gmdate('Y-m-d', time());?>
        <footer>
            <div id="footericon">
                <a href="https://github.com/carry0987/" title="carry0987 GitHub" style="text-decoration:none;" target="_blank" rel="noopener">
                    <img class="imgspace" src="<?=$base_url;?>/static/icon/github.svg" width="30px" />
                </a>
            </div>
            <?php echo "\t".'<p>'.$today.'</p>'."\n"; ?>
            <script type="text/javascript">
            $('select.language').change(function() {
                console.log('Language: ' + $('select.language').children('option:selected').text());
                $.ajax({
                        url: './',
                        data: {lang: $('select.language').val()},
                        type: 'POST',
                        success: function(data) {
                            setTimeout(function() {
                                location.reload();
                            }, 100);
                        },
                        dataType: 'html'
                });
            });
            </script>
            <div id="footer">
                <p>
                    <span>Made By</span> <a class="developer" href="https://carry0987.github.io/" target="_blank" rel="noopener">carry0987</a> <?='v'.PROGRAM_VERSION;?>
                </p>
            </div>
        </footer>
    </div>
</body>
</html>