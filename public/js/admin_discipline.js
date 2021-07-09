
var dragSrcEl = null;

function handleDragStart(e) {
    if(this.classList.contains("cant") === false) {
        this.style.opacity = '0.5';

        dragSrcEl = this;

        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', this.innerHTML);
    }
}

function handleDragOver(e) {
    if(e.preventDefault){
        e.preventDefault();
    }

    e.dataTransfert.dropEffect = 'move';

    return false;
}

function handleDragEnter(e) {
    if(this.classList.contains("cant") === false) {
        this.classList.add('over');
    }
}

function handleDragLeave(e) {
    this.classList.remove('over');
}

function handleDrop(e) {
    if(this.classList.contains("cant") === false) {
        if (e.stopPropagation) {
            e.stopPropagation();
        }

        if(dragSrcEl !== this){
            console.log(dragSrcEl.innerHTML);
            dragSrcEl.innerHTML = this.innerHTML;
            this.innerHTML = e.dataTransfer.getData('text/html');
        }

    }

    return false;
}

function handleDragEnd(e) {
    var i = 1;
    [].forEach.call(rows, function(row){
        row.querySelector('span').innerHTML = i;
        i++;
        row.classList.remove('over');
        row.style.opacity = "1.0";
    });
}

var rows = document.querySelectorAll('#row .brow');
[].forEach.call(rows, function(row){
    row.addEventListener('dragstart', handleDragStart, false);
    row.addEventListener('dragenter', handleDragEnter, false);
    row.addEventListener('dragover', handleDragOver, false);
    row.addEventListener('dragleave', handleDragLeave, false);
    row.addEventListener('drop', handleDrop, false);
    row.addEventListener('dragend', handleDragEnd, false);
});

$(document).ready(function(){
    $(".num_player").on("keyup blur change", delay(function(){
        if(parseInt(this.value) < 100) {
            $("tbody").html("");
            var value = parseInt(this.value) + 1;
            for (var i = 1; i <= this.value; i++) {
                value -= 1;
                $("tbody").append("<tr><th scope=\"row\">" + i + "</th><td class=\"value\">" + value + "</td></tr>");
            }
        }else{
            alert("Wow...slow down");
            this.value = 0;
        }
    }, 1000));

    $("#discipline_game").on("keyup blur change", function(){
        $(".previewdiscipline h1").html(this.value);
        if(this.value === ""){
            $(".previewdiscipline h1").html("Titre du jeu");
        }
    });

    $("#discipline_banner").on("keyup blur change", function(){
        $(".background").css({
            "background": "linear-gradient(rgba(0, 0, 0, 0.6),rgba(0, 0, 0, 0.6)),url('" + this.value +"') center no-repeat",
            "background-size": "cover"
        });
        if(this.value === ""){
            $(".background").css({
                "background": "linear-gradient(rgba(0, 0, 0, 0.6),rgba(0, 0, 0, 0.6)),url('/img/default.jpg') center no-repeat",
                "background-size": "cover"
            });
        }
    });

    $("#discipline_start_date").on("keyup blur change", function(){
        $(".startdate").html(this.value)
    });

    function delay(fn, ms) {
        let timer = 0
        return function(...args) {
            clearTimeout(timer)
            timer = setTimeout(fn.bind(this, ...args), ms || 0)
        }
    }
    $("#discipline_default").on("keyup blur change", delay(function(){
        var sum = 0;
        $($(".value").get().reverse()).each(function(key) {
            sum = (key * parseFloat($("#discipline_coeff").val()))+parseInt($("#discipline_default").val());
            $(this).html(Math.round(Number(sum)));
            console.log(Math.round(Number(sum)));
        })
    }, 500));
    $("#discipline_coeff").on("keyup blur change", function(){
        var sum = 0;
        $($(".value").get().reverse()).each(function(key) {
            sum = (key * parseFloat($("#discipline_coeff").val()))+parseInt($("#discipline_default").val());
            $(this).html(sum)
        })
    });
    tinymce.init({
        selector: '#discipline_description',
        width: 700,
        height:500,
        skin: "oxide-dark",
        content_css: "dark",
        language : 'fr_FR',
        toolbar: "forecolor backcolor",
        color_cols: "5",
        plugins: [
            'lists wordcount fullscreen textcolor colorpicker textpattern',
        ],
        toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
        toolbar2: 'print preview media | forecolor backcolor emoticons',
        image_advtab: true
    });

});

