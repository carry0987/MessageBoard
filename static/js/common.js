$(document).ready(function() {
    $('.up, .down').click(function() {
        var $element = this;
        var row = $($element).closest('.box-list');
        if ($(this).is('.up')) {
            var targetRow = row.prev();
            row.insertBefore(targetRow);
        } else {
            var targetRow = row.next();
            row.insertAfter(targetRow);
        }
        if (!targetRow.length) return false;
        $('.pinned-ol>li').each(function() {
            $(this).attr('new-sort', $(this).index());
        });
    });
    //Show notification list
    $('#notif_box').click(function() {
        $('#notif_box').toggleClass('active');
        $('#notif_wrapper').fadeToggle(300);
        $('#notif_count').fadeOut('slow');
        return false;
    });
    //Document Click
    $(document).click(function() {
        $('#notif_box').removeClass('active');
        $('#notif_wrapper').fadeOut('fast');
    });
    //Popup Click
    $('#notif_box').click(function() {
        if ($('#notif_wrapper:visible').length) {
            checkNotif('index.php', false);
        } else {
            checkNotif('index.php');
        }
    });
});

function autoCheckNotif(url) {
    $.ajax({
        url: url,
        data: {'get_notif_total': true},
        dataType: 'json',
        type: 'GET',
        success: function(result) {
            if (result != false && result > 0) {
                $('.notification-count').show();
                $('.notification-count').text(result);
            }
        },
        error: function() {
            return false;
        }
    });
    if ($('#notif_wrapper:visible').length) {
        checkNotif('index.php', false);
    }
}

function checkNotif(url, showLoading) {
    if (showLoading === undefined) {
        showLoading = true;
    }
    $.ajax({
        url: url,
        data: {'get_notif': true},
        dataType: 'html',
        type: 'GET',
        beforeSend: function() {
            if (showLoading === true) {
                $('#notificationsBody>.notif-loading').show();
            }
        },
        success: function(result) {
            $('#notificationsBody>.notif-loading').hide();
            if (result != false) {
                $('#notificationsBody>ol').html(result);
            }
        },
        error: function() {
            $('#notificationsBody>.notif-loading').hide();
            return false;
        }
    });
}

function closeNotif() {
    $('#notif_box').removeClass('active');
    $('#notif_wrapper').fadeOut('fast');
}

function updateSort(data) {
    var sortArray = {};
    $(data['from']).each(function() {
        var dataID = $(this).attr(data['name']);
        sortArray[$(this).attr(data['sort'])] = dataID;
    });
    $.ajax({
        url: data['url'],
        data: {'pinned_sort': sortArray},
        type: 'POST',
        beforeSend: function() {
            $('.update-loading').show();
        },
        success: function(result) {
            $('.update-loading').hide();
            if (result == 1) {
                $('.update-info').show();
                $('.update-info>#success').show();
                $('.update-info>#failed').hide();
                setTimeout(function() {
                    $('.update-info').fadeOut(300)
                }, 3000);
            } else {
                $('.update-info').show();
                $('.update-info>#failed').show();
                $('.update-info>#success').hide();
                setTimeout(function() {
                    $('.update-info').fadeOut(300)
                }, 3000);
            }
        },
        error: function() {
            $('.update-loading').hide();
            $('.update-info').show();
            $('.update-info>#success').hide();
            $('.update-info>#failed').show();
            setTimeout(function() {
                $('.update-info').fadeOut(300)
            }, 3000);
            return false;
        }
    });
}

function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;
    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
};

function createReply(data) {
    $.ajax({
        url: data['url'],
        data: {'ajax': true, 'submitData': true, 'reply_content': data['content']},
        type: 'POST',
        beforeSend: function() {
            $('.reply-loading').show();
        },
        success: function(result) {
            if (result == 0) {
                $('.reply-loading').hide();
                $('.reply-info').show();
                $('.reply-info>#failed').show();
                $('.reply-info>#success').hide();
                setTimeout(function() {
                    $('.reply-info').fadeOut(300)
                }, 3000);
            } else {
                var currentPageNum = getUrlParameter('page');
                var url = location.href;
                url = url.split('#')[0];
                url = url.split('&&')[0];
                if (!currentPageNum) {
                    currentPageNum = 1;
                    data['totalPage'] = (data['totalPage'] != 0) ? data['totalPage'] : 1;
                    url += '&page=1';
                }
                if (data['checkPage']+1 > data['itemsPerPage']) {
                    var newPage = Number(data['totalPage']) + 1;
                    url = url.replace('page='+currentPageNum, 'page='+newPage);
                } else {
                    var newPage = data['totalPage'];
                    url = url.replace('page='+currentPageNum, 'page='+newPage);
                }
                //url = url.split('&', -1);
                //url = url[0]+'&'+url[1];
                $('.reply-loading').hide();
                $('.reply-info').show();
                $('.reply-info>#success').show();
                $('.reply-info>#failed').hide();
                setTimeout(function() {
                    $('.reply-info').fadeOut(300)
                }, 3000);
                window.location.href = url+'&#reply-'+result;
            }
        },
        error: function() {
            $('.reply-loading').hide();
            $('.reply-info').show();
            $('.reply-info>#success').hide();
            $('.reply-info>#failed').show();
            setTimeout(function() {
                $('.reply-info').fadeOut(300)
            }, 3000);
            return false;
        }
    });
}
