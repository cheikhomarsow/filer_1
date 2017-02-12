window.onload = function() {
    var rename_box = document.querySelectorAll(".rename_box");
    var my_button_rename = document.querySelector("#my_button_rename");

    var replace_box = document.querySelector("#replace_box");
    var my_button_replace = document.querySelector("#my_button_replace");

    my_button_rename.onclick = function(){
        for (var i = 0; i < rename_box.length; i++) {
            rename_box[i].style.display = 'inline-block';
        }
    };

    my_button_replace.onclick = function(){
        replace_box.style.display = 'inline-block';
    };




};