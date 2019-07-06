$(document).ready(function() {
    $('.nav-trigger').click(function() {
        $('.side-nav').toggleClass('visible');
    });
    $('.option-set>.radio').click(function(event) {
        if (event.target.id === 'enable') {
            $('.option-set>.radio>#disable').removeAttr('checked');
            $('.option-set>.radio>#enable').attr('checked', 'checked');
        } else if (event.target.id === 'disable') {
            $('.option-set>.radio>#enable').removeAttr('checked');
            $('.option-set>.radio>#disable').attr('checked', 'checked');
        }
    });
});

function showSettingDIV() {
    var current_type = $('#current_type').val();
    $('.type-select>.radio>#'+current_type).prop('checked', true);
    $('.type-select>.radio input').attr('disabled', false);
    $('.type-select>.radio>.select-label').css('opacity', '1');
}

function hideSettingDIV() {
    $('.type-select>.radio input').prop('checked', false);
    $('.type-select>.radio input').attr('disabled', true);
    $('.type-select>.radio>.select-label').css('opacity', '.6');
    $('#show-setting').detach();
}

function showSetting(settingType, mod, showType) {
    var setting_type = $('.type-select>.radio input:checked').attr('value');
    var data = {};
    data[settingType] = setting_type;
    $.ajax({
        url: 'admin.php?mod='+mod,
        data: data,
        type: 'POST',
        beforeSend: function() {
            $('.setting-loading').show();
            $('.setting-title').hide();
            $('.setting-list').hide();
        },
        success: function(result) {
            $('.setting-loading').hide();
            $('.setting-title').show();
            $('.setting-list').show();
            if (showType === 'append') {
                $('.main-admin').append('<div class="widget" id="show-setting"></div>');
                $('#show-setting').html(result);
            } else {
                $('#show-setting').html(result);
            }
        },
        error: function() {
            return false;
        }
    });
}

function detectSetting(settingType, mod) {
    var setting_type = $('#change_type').attr('value');
    var data = {};
    data[settingType] = setting_type;
    $.ajax({
        url: 'admin.php?mod='+mod,
        data: data,
        type: 'POST',
        beforeSend: function() {
            $('.setting-loading').show();
            $('.setting-title').hide();
            $('.setting-list').hide();
        },
        success: function(result) {
            $('.setting-loading').hide();
            $('.setting-title').show();
            $('.setting-list').show();
            $('#show-setting').html(result);
        },
        error: function() {
            return false;
        }
    });
}

/* Data CRUD */
function enableDeleteMulti(event) {
    if ($(event).is(':checked')) {
        $('#delete-multi-data').attr('data-set', '#deleteMultiData');
    }
}

function checkSelect() {
    if ($('.data-list input[type=checkbox]:checked').length == 0) {
        $('#delete-multi-data').attr('data-set', '');
    }
}

function deleteData(deleteID, mod, page) {
    var keywords = $('#keyword').val();
    var sortBy = $('#sortBy').val();
    var data = {};
    data['delete_id'] = deleteID;
    data['page'] = page;
    data['keyword'] = keywords;
    data['sortBy'] = sortBy;
    data['delete'] = true;
    $.ajax({
        url: 'admin.php?delete='+mod,
        data: data,
        type: 'POST',
        success: function(result) {
            $('.data-list').html(result);
        },
        error: function() {
            return false;
        }
    });
}

function deleteMultiData(deleteMultiID, mod, page) {
    var keywords = $('#keyword').val();
    var sortBy = $('#sortBy').val();
    var data = {};
    data['delete_multi_id'] = deleteMultiID;
    data['page'] = page;
    data['keyword'] = keywords;
    data['sortBy'] = sortBy;
    data['delete'] = true;
    data['last_page'] = $('.page-item:last-child a').text();
    $.ajax({
        url: 'admin.php?delete='+mod,
        data: data,
        type: 'POST',
        success: function(result) {
            $('.data-list').html(result);
        },
        error: function() {
            return false;
        }
    });
}

function updateData(updateID, updateArray, mod, page) {
    var keywords = $('#keyword').val();
    var sortBy = $('#sortBy').val();
    var data = {};
    data['update_id'] = updateID;
    data['page'] = page;
    data['keyword'] = keywords;
    data['sortBy'] = sortBy;
    data['update_array'] = updateArray;
    $.ajax({
        url: 'admin.php?update='+mod,
        data: data,
        type: 'POST',
        success: function(result) {
            $('.data-list').html(result);
        },
        error: function() {
            return false;
        }
    });
}

function updateMultiData(updateMultiID, updateArray, mod, page) {
    var keywords = $('#keyword').val();
    var sortBy = $('#sortBy').val();
    var data = {};
    data['update_multi_id'] = updateMultiID;
    data['page'] = page;
    data['keyword'] = keywords;
    data['sortBy'] = sortBy;
    data['update_array'] = updateArray;
    $.ajax({
        url: 'admin.php?update='+mod,
        data: data,
        type: 'POST',
        success: function(result) {
            $('.data-list').html(result);
        },
        error: function() {
            return false;
        }
    });
}

function createData(createArray, mod) {
    var data = {};
    data['create_array'] = createArray;
    $.ajax({
        url: 'admin.php?create='+mod,
        data: data,
        type: 'POST',
        success: function(result) {
            $('.data-list').html(result);
        },
        error: function() {
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

/* Setting Test */
function testFTP(testData) {
    testData['values']['ftp_test'] = true;
    $.ajax({
        type: 'POST',
        url: testData['url'],
        data: testData['values'],
        timeout: 30000,
        beforeSend: function() {
            $('.setting-list-test').prop('disabled', true);
            $('.setting-list-box .loading').css('display', 'inline-block');
        },
        error: function() {
            $('.setting-list-test').prop('disabled', false);
            $('.setting-list-box .loading').css('display', 'none');
            $('.notification-box').removeClass('success');
            $('.notification-box').addClass('warning');
            $('.notification-box>p').text('Test Timeout');
            return false;
        },
        success: function(result) {
            $('.setting-list-test').prop('disabled', false);
            $('.setting-list-box .loading').css('display', 'none');
            if (result == 'true') {
                $('.notification-box').removeClass('warning');
                $('.notification-box').addClass('success');
                $('.notification-box>p').text(testData['success']);
            } else {
                $('.notification-box').removeClass('success');
                $('.notification-box').addClass('warning');
                $('.notification-box>p').text(testData['failed']);
            }
        }
    });
}

function testThumbnail(testData) {
    $.ajax({
        type: 'POST',
        url: testData['url'],
        data: testData['data'],
        dataType: testData['type'],
        contentType: false,
        cache: false,
        processData: false,
        timeout: 5000,
        beforeSend: function() {
            $('.setting-list-test').prop('disabled', true);
            $('.setting-list-box .loading').css('display', 'inline-block');
        },
        error: function() {
            $('.setting-list-test').prop('disabled', false);
            $('.setting-list-box .loading').css('display', 'none');
            $('.notification-box').removeClass('success');
            $('.notification-box').addClass('warning');
            $('.notification-box>p').text(testData['failed']);
            return false;
        },
        success: function(data) {
            $('.setting-list-test').prop('disabled', false);
            $('.setting-list-box .loading').css('display', 'none');
            $('#uploadForm')[0].reset();
            $('.notification-box').removeClass('warning');
            $('.notification-box').addClass('success');
            $('.notification-box>p').text(testData['success']);
            $('.gallery').css('display', 'block');
            if (Array.isArray(data.result['thumbnail'])) {
                var dataArray = data.result['thumbnail'];
                dataArray.forEach(function(element) {
                    $('.gallery').append('<img src="'+element+'" alt="Thumbnail Test" />');
                });
            } else {
                $('.gallery').html('<img src="'+data.result['thumbnail']+'" alt="Thumbnail Test" />');
            }
            return true;
        }
    });
}
