$(document).ready(function () {

    $('#sidebarCollapse').on('click', function () {
        $('#sidebar').toggleClass('active');
        if($('#sidebar').hasClass('active'))
            $(this).html('<i class="fas fa-align-left"></i> <span>Afficher</span>');
        else
            $(this).html('<i class="fas fa-align-left"></i> <span>Cacher</span>');
    });

});