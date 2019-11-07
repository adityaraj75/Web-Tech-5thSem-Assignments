//Helper Functions:
// To create Bold substring
function boldenedString(main,sub){
    let pos=main.toLowerCase().indexOf(sub.toLowerCase());
    if(pos!=0){
        pos=main.toLowerCase().indexOf(' '+sub.toLowerCase());
        if(pos==-1){
            pos=main.toLowerCase().indexOf('('+sub.toLowerCase());
        }
        pos=pos+1;
    }
    return main.substr(0,pos)+'<strong>'+main.substr(pos,sub.length)+'</strong>'+main.substr(pos+sub.length);
}

// To create an alert div with message provided
function create_alert_string(message, alertClass, id){
    f='<div class="alert alert-'+ alertClass + '" role="alert" id="' + id + '">' +
        message +
    '</div>';
    return f;
}

// To construct Skill Object from the object the server returns
function construct_skill_object(skill_arr){
    children={};
    children[-1]=[];
    for(ele in skill_arr){
        if(skill_arr[ele].parent!==null){
            if(children.hasOwnProperty(skill_arr[ele].parent)){
                children[skill_arr[ele].parent].push({"skillid":skill_arr[ele].skillid,"skill":skill_arr[ele].skill});
            }
            else{
                children[skill_arr[ele].parent]=[{"skillid":skill_arr[ele].skillid,"skill":skill_arr[ele].skill}];
            }
        }
        else{
            children[-1].push({"skillid":skill_arr[ele].skillid,"skill":skill_arr[ele].skill});
        }
    }
    return children;
}


// To create skill child String
function construct_skill_child_string(skill_obj, id){
    if(!skill_obj.hasOwnProperty(id))
        return '';
    let f='<ul class="skillList">';
    for(x in skill_obj[id]){
        f+='<li>'
                +'<div class="skillItem">'
                    +'<span id="skillName">'
                        +skill_obj[id][x].skill
                    +'</span>'
                +'</div>'
            +'</li>';
        f+=construct_skill_child_string(skill_obj,skill_obj[id][x].skillid);
    }
    f+='</ul>';
    return f;
}




$(document).ready(function(){

    // To Upload Profile Picture
    $('#profilePicUpload').on('input',function(){
        if( $('#profilePicUpload')[0].files[0].size > 1024*1024){
            this_alert=$(create_alert_string('File Size greater than 1 MB','danger','file-alert'));
            $(this).parent().parent().parent().append(this_alert);
            this_alert.css("margin","10px 0px");
            this_alert.fadeTo(3000, 0).slideUp(500,function(){
                $(this).remove();
            });
            return;
        }
        $('#profilePicForm').submit();
    });


    //For listing User's skills
    function showSkills(skill_list_json){
        skill_list_array=JSON.parse(skill_list_json);
        skill_object=construct_skill_object(skill_list_array);
        let f='<ul class="skillList">';
        for(x in skill_object[-1]){
            f+='<li>'
                    +'<div class="skillItem">'
                        +'<span id="skillName">'
                            +skill_object[-1][x].skill
                        +'</span>'
                    +'</div>'
                +'</li>';
            f+=construct_skill_child_string(skill_object,skill_object[-1][x].skillid);
        }
        f+='</ul>';
        $('.skill-show').html(f);
    }
    $.post("async/skills_async.php",{showIDSkills:true,id:parseInt($('input[name="profileID"]').val())}).done(showSkills);



    //for searching skills
    $('.search-box input[name="skillSearch"]').on("input", function(){
        var inputVal = $(this).val();
        var resultDropdown = $(this).siblings(".result");
        if(inputVal.length)
        {
            $.post("async/skills_async.php", {skillSearch: inputVal}).done(function(skill_suggest_json)
            {
                resultDropdown.empty();
                skill_suggest_array=JSON.parse(skill_suggest_json);
                if(skill_suggest_array.length===0)
                {
                    resultDropdown.html('<p>There are no skills like \"<em>'+inputVal+'</em>\"</p>')
                }
                else
                {
                    for(x in skill_suggest_array)
                    {
                        if(resultDropdown.html())
                            resultDropdown.html(resultDropdown.html()+'<p id=\"skilloption\">'+boldenedString(skill_suggest_array[x].skill,inputVal)+'</p>');
                        else
                            resultDropdown.html('<p id=\"skilloption\">'+boldenedString(skill_suggest_array[x].skill,inputVal)+'</p>');
                    }
                }
            });
        }
        else {
            resultDropdown.empty();
        }
    });

    // For updating Search Box when option is clicked
    $(document).on("click",".result p#skilloption", function(){
        $(this).parents(".search-box").find('input[type="text"]').val($(this).text());
        $(this).parent(".result").empty();
    });


    // For clearing the list when other part of document is clicked:
    function closeAllLists(element){
        var x = document.getElementsByClassName("result");
        for (i in x){
            if (element!=x[i]) {
                x[i].innerHTML="";
            }
        }
    }
    document.addEventListener("click",function(e){
        closeAllLists(e.target);
    });


    // For Adding a new Skill:
    $('#skillAddBtn').click(function(){
        var skillName = $('.search-box input[name="skillSearch"]').val();
        $.post("async/skills_async.php",{addSkill:skillName}).done(function(result_json){
            let result=JSON.parse(result_json);
            if(!result.executed){
                if(result.validSkill===true){
                    if(result.errorInfo[1]===1062){
                        this_alert=$(create_alert_string(skillName + ' is already registered to you!','warning','search-alert'));
                        $('.errorBox').empty();
                        $('.errorBox').append(this_alert);
                        this_alert.css("margin","10px 0px");
                        this_alert.fadeTo(3000, 0).slideUp(500,function(){
                            $(this).remove();
                        });
                        return;
                    }
                }
                this_alert=$(create_alert_string('SQL ERROR! Contact Developers.','primary','search-alert'));
                $('.errorBox').empty();
                $('.errorBox').append(this_alert);
                this_alert.css("margin","10px 0px");
                this_alert.fadeTo(3000, 0).slideUp(500,function(){
                    $(this).remove();
                });
            }
            else if(result.validSkill===false){
                this_alert=$(create_alert_string(skillName + ' is not a valid Skill!','danger','search-alert'));
                $('.errorBox').empty();
                $('.errorBox').append(this_alert);
                this_alert.css("margin","10px 0px");
                this_alert.fadeTo(3000, 0).slideUp(500,function(){
                    $(this).remove();
                });
            }
            else if(result.errorInfo===null){
                this_alert=$(create_alert_string('Skill \"'+skillName+'\" Added Successfully','success','search-alert'));
                $('.errorBox').empty();
                $('.errorBox').append(this_alert);
                this_alert.css("margin","10px 0px");
                this_alert.fadeTo(3000, 0).slideUp(500,function(){
                    $(this).remove();
                });
                // Reload My Skills:
                $.post("async/skills_async.php",{showMy:true}).done(showSkills);
            }

        });
    });

    // For bringing delete icon after a skill:
    if( parseInt($('input[name="profileID"]').val()) === parseInt($('input[name="currentLoginID"]').val()) ){
        $(document).on("mouseenter",".skillItem", function(){
            $(this).addClass("delete");
            $(this).append('<button type="button" id="deleteSkillItem" class="close" aria-label="Close">'
                                +'<span aria-hidden="true">'
                                    +'&times;'
                                +'</span>'
                            +'</button>'
            );
        });
        $(document).on("mouseleave",".skillItem", function(){
            $(this).removeClass("delete");
            $(this).children("#deleteSkillItem").remove();
        });
    }


    // For deleting a skill:
    if( parseInt($('input[name="profileID"]').val()) === parseInt($('input[name="currentLoginID"]').val()) ){
        $(document).on("click","#deleteSkillItem",function(){
            var skillName = $(this).parent().children("#skillName").html();
            $.post("async/skills_async.php",{deleteSkill:skillName}).done(function(result_json){
                let result=JSON.parse(result_json);
                if(result.executed===false)
                {
                    // SQL Error
                    this_alert=$(create_alert_string('SQL ERROR! Contact Developers.','primary','search-alert'));
                    $('.errorBox').empty();
                    $('.errorBox').append(this_alert);
                    this_alert.css("margin","10px 0px");
                    this_alert.fadeTo(3000, 0).slideUp(500,function(){
                        $(this).remove();
                    });
                }
                else if(result.validSkill===false){
                    // Not a valid Skill
                    this_alert=$(create_alert_string(skillName + ' is not a valid Skill!','danger','search-alert'));
                    $('.errorBox').empty();
                    $('.errorBox').append(this_alert);
                    this_alert.css("margin","10px 0px");
                    this_alert.fadeTo(3000, 0).slideUp(500,function(){
                        $(this).remove();
                    });
                }
                else if(result.deleted===true){
                    this_alert=$(create_alert_string('Skill \"'+skillName+'\" and all its Children Skills Deleted Successfully','primary','search-alert'));
                    $('.errorBox').empty();
                    $('.errorBox').append(this_alert);
                    this_alert.css("margin","10px 0px");
                    this_alert.fadeTo(3000, 0).slideUp(500,function(){
                        $(this).remove();
                    });
                }
                $.post("async/skills_async.php",{showMy:true}).done(showSkills);
            });
        });
    }


    // For Following a person:
    $('#followBtn').on("click",function(){
        $.post("async/follow_async.php",{follow:parseInt($('input[name="profileID"]').val())}).done(function(result_json){
            let result=JSON.parse(result_json);
            if(!result.executed){
                if(result.hasOwnProperty('errorInfo')){
                    // Duplicate Entry:
                    if(result.errorInfo[1]===1062){
                        this_alert=$(create_alert_string('You already follow <strong>'+$('#profileName').text()+'</strong>','warning','follow-alert'));
                        $('.errorBox').empty();
                        $('.errorBox').append(this_alert);
                        this_alert.css("margin","10px 0px");
                        this_alert.fadeTo(3000, 0).slideUp(500,function(){
                            $(this).remove();
                        });
                        return;
                    }
                }
                // Wrong User ID
                this_alert=$(create_alert_string(result.errorMessage,'primary','follow-alert'));
                $('.errorBox').empty();
                $('.errorBox').append(this_alert);
                this_alert.css("margin","10px 0px");
                this_alert.fadeTo(3000, 0).slideUp(500,function(){
                    $(this).remove();
                });
            }
            this_alert=$(create_alert_string('Now following <strong>'+$('#profileName').text()+'</strong>','success','follow-alert'));
            $('.errorBox').empty();
            $('.errorBox').append(this_alert);
            this_alert.css("margin","10px 0px");
            this_alert.fadeTo(3000, 0).slideUp(500,function(){
                $(this).remove();
            });

            // Change Buttons
            $('#followBtn').attr('hidden',true);
            $('#unfollowBtn').removeAttr('hidden');
        });
    });


    // For Unfollowing a person:
    $('#unfollowBtn').on("click",function(){
        $.post("async/follow_async.php",{unfollow:parseInt($('input[name="profileID"]').val())}).done(function(result_json){
            let result=JSON.parse(result_json);
            if(!result.executed){
                // Wrong User ID
                this_alert=$(create_alert_string(result.errorMessage,'primary','follow-alert'));
                $('.errorBox').empty();
                $('.errorBox').append(this_alert);
                this_alert.css("margin","10px 0px");
                this_alert.fadeTo(3000, 0).slideUp(500,function(){
                    $(this).remove();
                });
            }
            this_alert=$(create_alert_string('Stopped following <strong>'+$('#profileName').text()+'</strong>','dark','follow-alert'));
            $('.errorBox').empty();
            $('.errorBox').append(this_alert);
            this_alert.css("margin","10px 0px");
            this_alert.fadeTo(3000, 0).slideUp(500,function(){
                $(this).remove();
            });
            // Change Buttons
            $('#unfollowBtn').attr('hidden',true);
            $('#followBtn').removeAttr('hidden');
        });
    });


});
