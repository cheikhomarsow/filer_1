window.onload = function() {
    var forgot_password_box = document.querySelector("#forgot_password_box");
    var forgot_password = document.querySelector("#forgot_password");

    forgot_password.onclick = function(){
        forgot_password_box.style.visibility = "visible";
    };
}
