//Helper Functions:

// To create an alert div with message provided
function create_alert_string(message, alertClass, id){
    f='<div class="alert alert-'+ alertClass + ' alert-dismissible fade show" role="alert" id="' + id + '">' +
        message +
        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
            '<span aria-hidden="true">&times;</span>' +
        '</button>' +
    '</div>';
    return f;
}

$(document).ready(function()
{
    //for logging in
    $('#loginBtn').on("click", function()
    {
        let username = $('#LoginUsername').val();
        let password = $('#LoginPassword').val();
        let testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
        if (! testEmail.test(username)){
            this_alert=$(create_alert_string('Invalid Username!','primary',''));
            $(this).parent().parent().append(this_alert);
            this_alert.css("margin","10px 0px");
            this_alert.fadeTo(2000, 0).slideUp(500,function(){
                $(this).remove();
            });
            return;
        }
        if (password.length<1){
            this_alert=$(create_alert_string('Please Enter Password!','primary',''));
            $(this).parent().parent().append(this_alert);
            this_alert.css("margin","10px 0px");
            this_alert.fadeTo(2000, 0).slideUp(500,function(){
                $(this).remove();
            });
            return;
        }

        $.post("async/login_async.php", {login:true, loginuser: username , loginpass: password}).done(function(result_json)
        {
            result=JSON.parse(result_json);
            if(result.executed===false)
            {
                this_alert=$(create_alert_string(result.errorMessage,'danger',''));
                $('#loginBtn').parent().parent().append(this_alert);
                this_alert.css("margin","10px 0px");
                this_alert.fadeTo(2000, 0).slideUp(500,function(){
                    $(this).remove();
                });
            }
            else {
                // Redirect to login Page
                window.location.replace(result.redirectLink);
            }
        });
    });

    //for Signing Up
    $('button[name="signup"]').on("click", function()
    {
        let name = $('#SignUpForm').find('input[name="name"]').val();
        let email = $('#SignUpForm').find('input[name="email"]').val();
        let password = $('#SignUpForm').find('input[name="password"]').val();
        let dob = $('#SignUpForm').find('input[name="dob"]').val();

        if ((name===null) || (name.length<1)){
            this_alert=$(create_alert_string('Enter name!','primary',''));
            $(this).parent().append(this_alert);
            this_alert.css("margin","10px 0px");
            this_alert.fadeTo(2000, 0).slideUp(500,function(){
                $(this).remove();
            });
            return;
        }
        let testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
        if (! testEmail.test(email)){
            this_alert=$(create_alert_string('Invalid Email!','primary',''));
            $(this).parent().append(this_alert);
            this_alert.css("margin","10px 0px");
            this_alert.fadeTo(2000, 0).slideUp(500,function(){
                $(this).remove();
            });
            return;
        }

        if (password.length<6){
            this_alert=$(create_alert_string('Password must have min 6 characters!','primary',''));
            $(this).parent().append(this_alert);
            this_alert.css("margin","10px 0px");
            this_alert.fadeTo(2000, 0).slideUp(500,function(){
                $(this).remove();
            });
            return;
        }

        if (dob===''){
            this_alert=$(create_alert_string('Enter Date Of Birth!','primary',''));
            $(this).parent().append(this_alert);
            this_alert.css("margin","10px 0px");
            this_alert.fadeTo(2000, 0).slideUp(500,function(){
                $(this).remove();
            });
            return;
        }

        $.post("async/signup_async.php", {signup:true, name: name , email: email, password: password, dob: dob}).done(function(result_json)
        {
            result=JSON.parse(result_json);
            if(result.executed===false)
            {
                this_alert=$(create_alert_string(result.errorMessage,'danger',''));
                $('button[name="signup"]').parent().append(this_alert);
                this_alert.css("margin","10px 0px");
                this_alert.fadeTo(2000, 0).slideUp(500,function(){
                    $(this).remove();
                });
            }
            else {
                // Redirect to login Page
                window.location.replace(result.redirectLink);
            }
        });

    });


});
