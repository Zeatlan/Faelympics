$(document).ready(function() {
    $(".discipline").on("click", function(event){
       var id = $(this).attr('idDiscipline');
       document.location.href = "discipline/"+id;
    });
});