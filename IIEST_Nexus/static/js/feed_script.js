//Helper Functions:

// Bolden a substring within a string
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
    f='<div class="alert alert-'+ alertClass + ' alert-dismissible fade show" role="alert" id="' + id + '">' +
        message +
        '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
            '<span aria-hidden="true">&times;</span>' +
        '</button>' +
    '</div>';
    return f;
}

function construct_post_string(post){
    let t = post.posted_at.split(/[- :]/);
    let d = new Date(Date.UTC(t[0], t[1]-1, t[2], t[3], t[4], t[5]));
    let d_ist = new Date(d.getTime()+(-330)*60*1000);
    let diff=Date.now()-d_ist;
    let agoString;
    if(diff<60000){
        let q=Math.floor((diff)/1000);
        agoString=q+' sec'
        if(q!=1)
            agoString+='s';
        agoString+=' ago'
    }
    else if(diff<60*60000){
        let q=Math.floor((diff)/(60000));
        agoString=q+' min'
        if(q!=1)
            agoString+='s';
        agoString+=' ago'
    }
    else if(diff<24*60*60000){
        let q=Math.floor((diff)/(60*60000));
        agoString=q+' hr'
        if(q!=1)
            agoString+='s';
        agoString+=' ago'
    }
    else if(diff<30*24*60*60000){
        let q=Math.floor((diff)/(24*60*60000));
        agoString=q+' day'
        if(q!=1)
            agoString+='s';
        agoString+=' ago'
    }
    else if(diff<365*24*60*60000){
        let q=Math.floor((diff)/(30*24*60*60000));
        agoString=q+' month'
        if(q!=1)
            agoString+='s';
        agoString+=' ago'
    }
    else{
        agoString=d_ist.toDateString();
    }
    f='<div class="card">' +
        '<div class="card-body">' +
            '<div class="row">' +
                '<div class="col-sm-9">' +
                    '<h5 class="card-title">' +
                        '<a href="' + post.profilepic + '">' +
                            '<img src="' + post.profilepic + '" alt="" width="30px" height="30px" style="border-radius:50%;">' +
                        '</a>' +
                        '<a class="card-link" href=\"profile.php?userid=' + post.userid + '\" style="padding-left:10px;">' +
                            post.username +
                        '</a>' +
                    '</h5>' +
                '</div>' +
                '<div class="col-sm-3">' +
                    '<span style="float:right;">' +
                        agoString +
                    '</span>' +
                '</div>' +
            '</div>' +

            '<div class="card-text">' +
                post.body +
            '</div>' +
        '</div>' +
    '</div>';
    return f;
}

function construct_search_result_string(result,searchString="",bolden=false){
    f='<div class="card">' +
        '<div class="card-body">' +
            '<div class="card-text">' +
                '<a href="' + result.profilepic + '">' +
                    '<img src="' + result.profilepic + '" alt="" width="30px" height="30px" style="border-radius:50%;">' +
                '</a>' +
                '<a href=\"profile.php?userid=' + result.id + '\" style="padding-left: 8px;">' +
                    ( bolden ? boldenedString(result.name,searchString) : result.name ) +
                '</a>' +
            '</div>' +
        '</div>' +
    '</div>';
    return f;
}


$(document).ready(function(){

    //for loading posts
    function showPosts(post_list_json){
        $('.postBox').empty();
        post_list=JSON.parse(post_list_json);
        if(post_list.data.length)
        {
            // If there are no posts
        }

        for(x in post_list.data){
            post_node=$(construct_post_string(post_list.data[x]));
            $('.postBox').append(post_node);
        }
    }
    $.post("async/post_async.php",{showFollowingPost:true}).done(showPosts);




    //for creating a post
    $('#postCreateBtn').on("click", function()
    {
        var this_button=this;
        var postContent = $('#postContent').val();
        if (postContent.length<1)
        {
            this_alert=$(create_alert_string('No content in Post!','primary',''));
            this_alert.insertBefore($(this).parent());
            this_alert.css("margin","10px 0px");
            this_alert.fadeTo(2000, 0).slideUp(500,function(){
                $(this).remove();
            });
            return;
        }

        $.post("async/post_async.php", {createpost: true , postbody: postContent}).done(function(result_json)
        {
            result=JSON.parse(result_json);
            if(result.executed===false)
            {
                this_alert=$(create_alert_string('Unable to post!','danger',''));
                this_alert.insertBefore($(this_button).parent());
                this_alert.css("margin","10px 0px");
                this_alert.fadeTo(2000, 0).slideUp(500,function(){
                    $(this).remove();
                });
            }
            else {
                // Empty the postbox
                $('#postContent').val("");
                //Reload the posts
                $.post("async/post_async.php",{showAllPost:true}).done(showPosts);
            }
        });
    });





    // For changing placeholder of search bar
    $('input[name="searchPeopleOption"]').on("change", function(){
        $('.peopleSearchResult').empty();
        $('input[name="peopleSearch"]').val('');
        if($('input[name="searchPeopleOption"]:checked').val()==='bySkill'){
            $('input[name="peopleSearch"]').attr('placeholder','Enter Skill');
        }
        else{
            $('input[name="peopleSearch"]').attr('placeholder','Enter Name');
        }
    });


    //for searching people
    $('#searchPeopleBtn').on("click", function(){
        let searchOption = $('input[name="searchPeopleOption"]:checked').val();
        let searchString = $('input[name="peopleSearch"]').val();
        if(searchString.length<1){
            $('.peopleSearchResult').empty();
            return;
        }
        if(searchOption==='bySkill')
        {
            // Search by Skill
            $.post("async/skills_async.php", {searchBySkill: searchString}).done(function(search_result_json){
                search_result=JSON.parse(search_result_json);
                if(search_result.executed===false){
                    return;
                }
                $('.peopleSearchResult').empty();
                for(x in search_result.data){
                    $('.peopleSearchResult').append(construct_search_result_string(search_result.data[x]));
                }
                // If there are no such people
                if(search_result.data.length<1){
                    $('.peopleSearchResult').append('<div class="card">' +
                                                        '<div class="card-body">' +
                                                            '<div class="card-text">' +
                                                                'No results Found' +
                                                            '</div>' +
                                                        '</div>' +
                                                    '</div>');
                }
            });
        }
        else
        {
            // Search by Name
            $.post("async/skills_async.php", {searchByName: searchString}).done(function(search_result_json){
                search_result=JSON.parse(search_result_json);
                if(search_result.executed===false){
                    return;
                }
                $('.peopleSearchResult').empty();
                for(x in search_result.data){
                    $('.peopleSearchResult').append(construct_search_result_string(search_result.data[x],searchString,true));
                }
                // If there are no such people
                if(search_result.data.length<1){
                    $('.peopleSearchResult').append('<div class="card">' +
                                                        '<div class="card-body">' +
                                                            '<div class="card-text">' +
                                                                'No results Found' +
                                                            '</div>' +
                                                        '</div>' +
                                                    '</div>');
                }
            });
        }


    });




    //for searching skills
    $('input[name="peopleSearch"]').on("input", function()
    {
        if($('input[name="searchPeopleOption"]:checked').val()==='byName')
            return;
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

    // FOr updating input with clicked Skill
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


});
