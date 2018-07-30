$(document).ready(function() {
    if (document.cookie.indexOf('collapsed_Nodes') >= 0) {
        var getCollapsed = Cookies.get('collapsed_Nodes');
        var checkCollapsed = getCollapsed.split('|');
        var arrayLength = checkCollapsed.length;
        for (var i = 0; i < arrayLength; i++) {
            $('[data-node="#' + checkCollapsed[i] + '"]').next('.board_ol').addClass('toggle_hide');
            $('[data-node="#' + checkCollapsed[i] + '"]').find('.plusminus').children('img').attr('src', './static/icon/plus.svg');
        }
    }

    var adjustCookie = function() {
        var tags = [];
        $('.category .toggle_hide').each(function() {
            var tag = $(this).prev('.category_index').data('node');
            tags.push(tag.replace('#', ''));
        });

        if (tags.length) {
            Cookies.set('collapsed_Nodes', tags.join('|'), {
                expires: 7
            });
        } else {
            Cookies.remove('collapsed_Nodes');
        }
    }

    $('.category_index').click(function() {
        if ($(this).next('.board_ol').is(':visible')) {
            $(this).next('.board_ol').slideUp(300);
            $(this).next('.board_ol').addClass('toggle_hide');
            $(this).find('.plusminus').children('img').attr('src', './static/icon/plus.svg');
        } else {
            $(this).next('.board_ol').slideDown(300);
            $(this).next('.board_ol').removeClass('toggle_hide');
            $(this).find('.plusminus').children('img').attr('src', './static/icon/minus.svg');
        }
        adjustCookie();
    });
});