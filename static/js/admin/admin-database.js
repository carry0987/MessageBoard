$(document).ready(function() {
    checkDatabaseSize();
    $('.information-link').click(function() {
        checkDatabaseSize();
    });
    /*
    setInterval(function() {
        checkDatabaseSize();
    }, 60000);
    */
    function checkDatabaseSize() {
        $.ajax({
            url: 'admin.php?mod=database&check=db_size',
            method: 'GET',
            success: function(data) {
                $('#database-detail').html(data);
            }
        })
    }
});
