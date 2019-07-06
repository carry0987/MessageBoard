$(document).ready(function() {
    if (document.cookie.indexOf('collapsed_Nodes') >= 0) {
        var getCollapsed = Cookies.get('collapsed_Nodes');
        var checkCollapsed = getCollapsed.split('|');
        var arrayLength = checkCollapsed.length;
        for (var i = 0; i < arrayLength; i++) {
            $('[data-node="#' + checkCollapsed[i] + '"]').next('.board-ol').addClass('toggle-hide');
            $('[data-node="#' + checkCollapsed[i] + '"]').find('.plusminus').children('img').attr('src', './static/icon/plus.svg');
        }
    }

    var adjustCookie = function() {
        var tags = [];
        $('.category .toggle-hide').each(function() {
            var tag = $(this).prev('.index-category').data('node');
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

    $('.toggle-category').click(function() {
        if ($(this).closest('.index-category').next('.board-ol').is(':visible')) {
            $(this).closest('.index-category').next('.board-ol').slideUp(300);
            $(this).closest('.index-category').next('.board-ol').addClass('toggle-hide');
            $(this).find('.plusminus').children('img').attr('src', './static/icon/plus.svg');
        } else {
            $(this).closest('.index-category').next('.board-ol').slideDown(300);
            $(this).closest('.index-category').next('.board-ol').removeClass('toggle-hide');
            $(this).find('.plusminus').children('img').attr('src', './static/icon/minus.svg');
        }
        adjustCookie();
    });
});