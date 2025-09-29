var is_closing = false;

export function httpreq(method, url, data, onresponse)
{
    let request = new XMLHttpRequest();
    let formdata = null;
    if(method == "post")
    {
        formdata = new FormData();
        for(let i = 0; i < data.length; i++)
            formdata.append(data[i].name, data[i].value);
    }
    else if(method == "get")
    {
        for(let i = 0; i < data.length; i++)
        {   
            if(i == 0)
                url += "?";
            else
                url += "&";
            url += data[i].name + "=" + data[i].value;
        }
    }
    request.onreadystatechange = function() {onresponse(request)};
    request.open(method, url, true);
    request.send(formdata);
}

function newcommentdiv(postid)
{
    return "<div class='addcomment' id = 'own_addcomment_"+postid+"'><textarea placeholder = 'Add your comment' class= 'newcommentarea' id='newcommentvalue_"+postid+"'></textarea><button class = 'post_comment' id='own_comment_"+postid+"'>COMMENT</button></div>";
}

function showmorebutton(postid, ncomments, pre_id = "own_button_")
{
    return "<button class = 'comm_loadmore' id='" + pre_id + postid+"_"+ncomments+"'>LOAD MORE</button>";
}

export function add_newcommentdiv(elem, postid)
{
    elem.insertAdjacentHTML("afterbegin", newcommentdiv(postid));
    elem.firstChild.firstChild.oninput = textareaheight;
    elem.firstChild.lastChild.onclick = postcomment;
}

function add_comment(commentdiv, comment, position = "beforeend")
{
    if(position != "afterbegin" && position != "beforeend")
        return;
    commentdiv.insertAdjacentHTML(position, comment);
    if(position == "beforeend")
        commentdiv.lastChild.getElementsByClassName("dotdotdot_wrap_comm")[0].onclick = comment_options_menu;
    else
        commentdiv.firstChild.getElementsByClassName("dotdotdot_wrap_comm")[0].onclick = comment_options_menu;
}

function add_applicant(appdiv, comment)
{
    appdiv.insertAdjacentHTML("beforeend", comment);
    let newapp_id = appdiv.lastChild.id;
    let button
    if(button = document.getElementById("accept+" + newapp_id))
        button.onclick = accept_applicant;
    if(button = document.getElementById("chat+" + newapp_id))
        button.onclick = chat_applicant;
    if(button = document.getElementById("cancel+" + newapp_id))
        button.onclick = cancel_applicant;
}

function accept_applicant()
{
    let obj = this;
    obj.onclick = null;
    obj.classList.add("loading");
    let appid = obj.id.split("+")[3];
    let appuser = obj.id.split("+")[2];
    let data = [{name: "appid", value: appid}, {name: "type", value: 1}];
    httpreq("post", "./php/ajax/acceptapplicant.php", data, function(request)
    {
        if(request.readyState == 4 && (request.status != 200 || Number(request.responseText) < 0))
        {
            display_msg("There's been an error trying to accept the application, something might have been deleted, if the problem persists try refreshing the page");
            obj.onclick = accept_applicant;
            obj.classList.remove("loading");
        }
        else if(request.readyState == 4 && request.status == 200)
        {
            let buttondiv = document.getElementById("user+" + appuser + "+" + appid).getElementsByClassName("userbuttons")[0];
            buttondiv.textContent = "";
            let chat = document.createElement("button");
            chat.id = "chat+user+" + appuser + "+" + appid;
            chat.textContent = "CHAT";
            chat.onclick = chat_applicant;
            buttondiv.appendChild(chat);
            let cancel = document.createElement("button");
            cancel.id = "cancel+user+" + appuser + "+" + appid;
            cancel.textContent = "UNACCEPT";
            cancel.onclick = cancel_applicant;
            buttondiv.appendChild(cancel);
            document.getElementById("user+" + appuser + "+" + appid).classList.add("useraccepted");
        }
    });
}

function chat_applicant()
{
    let appid = this.id.split("+")[3];
    window.location.href = "./message.php?id=" + appid;
}

function cancel_applicant()
{
    let obj = this;
    obj.onclick = null;
    obj.classList.add("loading");
    let appid = obj.id.split("+")[3];
    let appuser = obj.id.split("+")[2];
    let data = [{name: "appid", value: appid}, {name: "type", value: 0}];
    httpreq("post", "./php/ajax/acceptapplicant.php", data, function(request)
    {
        if(request.readyState == 4 && (request.status != 200 || Number(request.responseText) < 0))
        {
            display_msg("There's been an error trying to cancel the application, something might have been deleted, if the problem persists try refreshing the page");
            obj.onclick = cancel_applicant;
            obj.classList.remove("loading");
        }
        else if(request.readyState == 4 && request.status == 200)
        {
            let buttondiv = document.getElementById("user+" + appuser + "+" + appid).getElementsByClassName("userbuttons")[0];
            buttondiv.textContent = "";
            let cancel = document.createElement("button");
            cancel.id = "accept+user+" + appuser + "+" + appid;
            cancel.textContent = "ACCEPT";
            cancel.onclick = accept_applicant;
            buttondiv.appendChild(cancel);
            document.getElementById("user+" + appuser + "+" + appid).classList.remove("useraccepted");
        }
    });
}

function showcomments()
{
    let editable = false;
    let obj = this;
    obj.onclick = null;
    let id = obj.id;
    let split_id = id.split("_");
    let postid = split_id[1];
    let ncomments = Number(split_id[2]);

    let post = document.getElementById("post_" + postid);
    let hideapp = post.getElementsByClassName("hide_applicants");
    if(hideapp.length > 0)
        hideapp[0].onclick();

    if(document.getElementById("postbar_" + postid) != null)
        editable = true;
    obj.classList.remove("showcomments");
    obj.classList.add("loadingcomments");
    if(ncomments != 0)
    {
        let elem = document.getElementById("addcommentcontainer_" + postid);
        if(editable && elem.firstChild == null)
            add_newcommentdiv(elem, postid);
        if(!editable && elem.firstChild != null)
            elem.removeChild(elem.firstChild);
        obj.onclick = hidecomments;
        obj.classList.remove("loadingcomments");
        obj.classList.add("hidecomments");
        obj.id = "hidecomments_" + postid + "_" + ncomments;
        document.getElementById("comments_" + postid).classList.remove("hidden");
        return;
    }
    let data = [{name:"postid", value:postid}, {name:"lastcomment", value:"false"}];
    httpreq("get", "./php/ajax/loadcomments.php", data, function(request)
    {
        if(request.readyState == 4 && request.status != 200)
        {
            display_msg("Theres's been an error, try again");
            obj.onclick = showcomments;
            obj.classList.remove("loadingcomments");
            obj.classList.add("showcomments");
        }
        if(request.readyState == 4 && request.status == 200)
        {
            ncomments += 4;
            obj.onclick = hidecomments;
            obj.classList.remove("loadingcomments");
            obj.classList.add("hidecomments");
            obj.id = "hidecomments_" + postid + "_" + ncomments;
            let resp = JSON.parse(request.responseText);
            let elem = document.getElementById("addcommentcontainer_" + postid);
            if(editable)
                add_newcommentdiv(elem, postid);
            elem = elem.nextSibling;
            for(let k = 0; k < resp["comments"].length; k++)
                add_comment(elem, resp["comments"][k]);
            let totncomments = resp["newncomments"];
            obj.firstChild.textContent = totncomments;
            elem = elem.nextSibling;
            if(ncomments < totncomments)
            {
                elem.insertAdjacentHTML("afterbegin", showmorebutton(postid, ncomments));
                elem.firstChild.onclick = loadmorecomments;
            }
        }
    });
}

function loadmorecomments()
{
    let obj = this;
    obj.onclick = null;
    let id = obj.id;
    let split_id = id.split("_");
    let postid = split_id[2];
    let ncomments = Number(split_id[3]);
    obj.classList.remove("comm_loadmore");
    obj.classList.add("loading_comm_loadmore");

    var own = 0;
    let elem = document.getElementById("comments_div_" + postid).firstChild;
    while(elem && elem.id.split("_")[1] == "own")
    {
        own++;
        elem = elem.nextSibling;
    }
    let last_comment = document.getElementById("comments_div_" + postid).lastChild.id.split("_")[1];
    let data = [{name:"postid", value:postid}, {name:"lastcomment", value:last_comment}];
    httpreq("get", "./php/ajax/loadcomments.php", data, function(request)
    {
        if(request.readyState == 4 && request.status != 200)
        {
            display_msg("Theres's been an error, try again");
            obj.onclick = loadmorecomments;
            obj.classList.remove("loading_comm_loadmore");
            obj.classList.add("comm_loadmore");
        }
        if(request.readyState == 4 && request.status == 200)
        {
            let resp = JSON.parse(request.responseText);
            let newncomments = Number(resp['newncomments']) + own + ncomments;
            document.getElementById("hidecomments_"+postid+"_"+ncomments).id = "hidecomments_" + postid + "_" + (ncomments + resp['comments'].length);
            ncomments += resp['comments'].length;
            obj.id = "own_button_" + postid + "_" + ncomments;
            let elem = document.getElementById("comments_div_" + postid);
            for(let k = 0; k < resp['comments'].length; k++)
                add_comment(elem, resp["comments"][k]);
            document.getElementById("hidecomments_"+postid+"_"+ncomments).firstChild.textContent = newncomments;
            if(ncomments >= newncomments - own)
                obj.classList.add("hidden");
            else
            {
                obj.classList.remove("loading_comm_loadmore");
                obj.classList.add("comm_loadmore");
                obj.onclick = loadmorecomments;
            }
        }
    });
}

function hidecomments()
{
    let obj = this;
    obj.onclick = showcomments;
    let id = obj.id;
    let split_id = id.split("_");
    let postid = split_id[1];
    let ncomments = split_id[2];
    obj.classList.remove("hidecomments");
    obj.classList.add("showcomments");
    obj.id = "showcomments_" + postid + "_" + ncomments;
    document.getElementById("comments_" + postid).classList.add("hidden");
}

function postcomment()
{
    let obj = this;
    let id = obj.id;
    let split_id = id.split("_");
    let postid = split_id[2];
    let newcommentvalue = document.getElementById("newcommentvalue_" + postid).value;
    if(newcommentvalue.length < 1)
        return;
    document.getElementById("newcommentvalue_" + postid).value = "";

    obj.onclick = null;
    obj.classList.add("post_comment_loading");
    let data = [{name: "postid", value: postid}, {name: "comment", value: newcommentvalue}];
    httpreq("post", "./php/ajax/postcomment.php", data, function(request)
    {
        if(request.readyState == 4 && (request.status != 200 || request.responseText == "-1"))
        {
            display_msg("Something went wrong trying to post your comment, the post may have been deleted, if it keeps happening try refreshing the page");
            obj.classList.remove("post_comment_loading");
            obj.onclick = postcomment;
        }
        else if(request.readyState == 4 && request.status == 200)
        {
            let resp = JSON.parse(request.responseText);
            add_comment(document.getElementById("comments_div_" + postid), resp["comments"], "afterbegin");
            let commentcount = document.getElementById("postbar_"+postid).firstChild.nextSibling;
            commentcount.firstChild.textContent = Number(commentcount.firstChild.textContent) + 1;
            obj.classList.remove("post_comment_loading");
            obj.onclick = postcomment;
        }
    });
}

function like_unlike()
{
    let obj = this;
    obj.onclick = null;
    let id = obj.id;
    let split_id = id.split("_");

    let text = split_id[0];
    let postid = split_id[1];
    obj.classList.remove("liked");
    obj.classList.remove("unliked");
    obj.classList.add("loadinglike");
    let data = [{name:"postid", value:postid}, {name:"type", value:text}];
    httpreq("post", './php/ajax/likeunlike.php', data, function(request)
    {   
        if(request.readyState == 4 && (request.status != 200 || Number(request.responseText) < 0))
        {
            display_msg("There's been an error trying to like the post, the post might have been deleted, if the problem persists try refreshing the page");
            obj.classList.remove("loadinglike");
            obj.onclick = like_unlike;
            if(text == "liked")
                obj.classList.add("liked");
            else
                obj.classList.add("unliked");
        }
        else if(request.readyState == 4 && request.status == 200)
        {
            let likes = request.responseText;
            obj.firstChild.textContent = likes;
            obj.classList.remove("loadinglike");
            obj.onclick = like_unlike;
            if(text == "liked")
            {
                obj.classList.add("unliked");
                obj.id = "unliked_" + postid;
            }
            else
            {
                obj.classList.add("liked");
                obj.id = "liked_" + postid;
            }
        }
    });
}

function apply_unapply(postid, type, obj, accepted = false)
{
    obj.onclick = null;
    if(document.getElementById("post_" + postid).classList.contains("post_close"))
    {
        display_msg("The post has been closed, meaning new applicants are not accepted anymore");
        obj.onclick = apply_event;
        return;
    }
    if(type == "unapplied" && (document.getElementById("postbar_" + postid) == null))
    {
        display_msg("To apply for this post, you need to be subscribed to the community");
        obj.onclick = apply_event;
        return;
    }
    obj.classList.remove("applied");
    obj.classList.remove("accepted_app");
    obj.classList.remove("pending_app");
    obj.classList.remove("unapplied");
    obj.classList.add("loadingapply");
    let data = [{name:"postid", value:postid}, {name:"type", value:type}];
    httpreq("post", './php/ajax/likeunlike.php', data, function(request)
    {   
        if(request.readyState == 4 && (request.status != 200 || Number(request.responseText) < 0))
        {
            if(Number(request.responseText) == -3)
            {
                display_msg("The post has been closed, meaning new applicants are not accepted anymore");
                document.getElementById("post_" + postid).classList.add("post_close");
            }
            else
                display_msg("There's been an error trying to apply, the post might have been deleted, if the problem persists try refreshing the page");
            obj.classList.remove("loadingapply");
            obj.onclick = apply_event;
            if(type == "applied")
            {
                obj.classList.add("applied");
                if(accepted)
                    obj.classList.add("accepted_app");
                else    
                    obj.classList.add("pending_app");
            }
            else
                obj.classList.add("unapplied");
        }
        else if(request.readyState == 4 && request.status == 200)
        {
            let applies = request.responseText;
            obj.firstChild.textContent = applies;
            obj.classList.remove("loadingapply");
            obj.onclick = apply_event;
            if(type == "applied")
            {
                obj.classList.add("unapplied");
                obj.id = "unapplied_" + postid;
            }
            else
            {
                obj.classList.add("applied");
                obj.id = "applied_" + postid;
                if(accepted)
                {
                    obj.classList.add("accepted_app");
                    obj.id = "acceptedapp_" + postid;
                }
                else
                    obj.classList.add("pending_app");
            }
        }
    });
}

function pre_delete_application(postid, obj)
{
    options_menu("Delete application", [{value: "If you delete you application, you won't be able to interact with the post creator anymore, and the chat related to this post will also be deleted, are you sure?"}], [{value: "Yes", onclick: () => {close_options_menu(); apply_unapply(postid, "applied", obj, true);}, class: "delete"}, {value: "No", onclick: close_options_menu}]);
}

function loadmoreapplicants()
{
    let obj = this;
    obj.onclick = null;
    let postid = obj.id.split("_")[1];
    let post = document.getElementById("post_" + postid);
    let hidecomm = post.getElementsByClassName("hidecomments");
    if(hidecomm.length > 0)
        hidecomm[0].onclick();
    obj.classList.remove("comm_loadmore");
    obj.classList.add("loading_comm_loadmore");

    let lastapp = Number(document.getElementById("applicants_div_" + postid).lastChild.id.split("+")[2]);
    
    let data = [{name:"postid", value:postid}, {name: "lastapp", value: lastapp}];

    httpreq("get", './php/ajax/loadapplicants.php', data, function(request)
    {   
        if(request.readyState == 4 && request.status != 200)
        {
            display_msg("There's been an error trying to load applicants, the post might have been deleted, if the problem persists try refreshing the page");
            obj.classList.remove("loading_comm_loadmore");
            obj.classList.add("comm_loadmore");
            obj.onclick = loadmoreapplicants;
        }
        else if(request.readyState == 4 && request.status == 200)
        {
            obj.classList.remove("loading_comm_loadmore");
            obj.classList.add("comm_loadmore");
            let resp = JSON.parse(request.responseText);
            let elem = document.getElementById("applicants_div_" + postid);
            let napplicants = resp["newnapplicants"];
            document.getElementById("hideapp_"+postid).firstChild.textContent = (elem.childElementCount + napplicants);
            for(let k = 0; k < resp["applicants"].length; k++)
                add_applicant(elem, resp["applicants"][k]);
            if(napplicants > 4)
                obj.onclick = loadmoreapplicants;
            else
                obj.classList.add("hidden");
        }
    });
}

function show_applicants(postid, obj)
{
    let post = document.getElementById("post_" + postid);
    let hidecomm = post.getElementsByClassName("hidecomments");
    if(hidecomm.length > 0)
        hidecomm[0].onclick();
    obj.classList.remove("show_applicants");
    obj.classList.add("loadingapply");
    
    let data = [{name:"postid", value:postid}, {name: "lastapp", value: "false"}];

    httpreq("get", './php/ajax/loadapplicants.php', data, function(request)
    {   
        if(request.readyState == 4 && request.status != 200)
        {
            display_msg("There's been an error trying to load applicants, the post might have been deleted, if the problem persists try refreshing the page");
            obj.classList.remove("loadingapply");
            obj.classList.add("show_applicants");
            obj.onclick = apply_event;
        }
        else if(request.readyState == 4 && request.status == 200)
        {
            obj.onclick = apply_event;
            obj.id = "hideapp_" + postid;
            obj.classList.remove("loadingapply");
            obj.classList.add("hide_applicants");
            let resp = JSON.parse(request.responseText);
            let elem = document.getElementById("applicants_div_" + postid);
            for(let k = 0; k < resp["applicants"].length; k++)
                add_applicant(elem, resp["applicants"][k]);
            let totnapplicants = resp["newnapplicants"];
            obj.firstChild.textContent = totnapplicants;
            elem = elem.nextSibling;
            if(totnapplicants > 4)
            {
                elem.insertAdjacentHTML("afterbegin", showmorebutton(postid, totnapplicants, "showmoreapp_"));
                elem.firstChild.onclick = loadmoreapplicants;
            }
            if(resp["applicants"].length == 0)
                obj.onclick();
        }
    });
}

function hide_applicants(postid, obj)
{
    obj.classList.remove("hide_applicants");
    obj.classList.add("show_applicants");
    obj.id = "reshowapp_" + postid;
    document.getElementById("applicants_" + postid).classList.add("hidden");
    obj.onclick = apply_event;
}

function reshow_applicants(postid, obj)
{
    let post = document.getElementById("post_" + postid);
    let hidecomm = post.getElementsByClassName("hidecomments");
    if(hidecomm.length > 0)
        hidecomm[0].onclick();
    obj.classList.remove("show_applicants");
    obj.classList.add("hide_applicants");
    obj.id = "hideapp_" + postid;
    document.getElementById("applicants_" + postid).classList.remove("hidden");
    obj.onclick = apply_event;
    if(obj.firstChild.textContent == "0")
        obj.onclick();
}

function apply_event()
{
    let obj = this;
    obj.onclick = null;
    let id = obj.id;
    let postid = id.split("_")[1];
    let type = id.split("_")[0];
    if(type == "applied" || type == "unapplied")
        apply_unapply(postid, type, obj);
    if(type == "acceptedapp")
    {
        obj.onclick = apply_event;
        let appid = id.split("_")[2];
        options_menu("Accepted application", [{value: "What do you want to do?"}], [{value: "Chat with post creator!", onclick: () => window.location.href = "./message.php?id=" + appid}, {value: "Delete application", onclick: () => {close_options_menu(); pre_delete_application(postid, obj);}, class: "delete"}]);
    }
    if(type == "showapp")
        show_applicants(postid, obj);
    if(type == "hideapp")
        hide_applicants(postid, obj);
    if(type == "reshowapp")
        reshow_applicants(postid, obj);
}

export function close_options_menu()
{
    document.getElementById("options_menu").remove();
}

export function options_menu(title, data, options)
{
    let elem = document.createElement("div");
    elem.onclick = close_options_menu;
    elem.classList.add("options_wrap");
    elem.id = "options_menu";
    document.getElementById("msg").appendChild(elem);
    let options_menu = document.createElement("div");
    options_menu.classList.add("options");
    options_menu.onclick = function(e) {e.stopPropagation();};

    let titlebar = document.createElement("div");
    titlebar.classList.add("options_titlebar");

    let title_div = document.createElement("p");
    title_div.textContent = title;

    let close_div = document.createElement("p")
    close_div.textContent = "×";
    close_div.classList.add("closediv");
    close_div.onclick = close_options_menu;

    titlebar.appendChild(title_div);
    titlebar.appendChild(close_div);
    options_menu.appendChild(titlebar);

    let info = document.createElement("div");
    info.classList.add("infos");
    for(let i = 0; i < data.length; i++)
    {
        let e0 = document.createElement("div");
        e0.classList.add("info");
        if(data[i].title)
            e0.textContent = data[i].title + ": " + data[i].value;
        else
            e0.textContent = data[i].value;
        if(data[i].class)
            e0.classList.add(data[i].class);
        info.appendChild(e0);
    }
    options_menu.appendChild(info);

    let buttons = document.createElement("div");
    buttons.classList.add("option_buttons");
    for(let i = 0; i < options.length; i++)
    {
        let e0 = document.createElement("div");
        e0.classList.add("option");
        e0.textContent = options[i].value;
        e0.onclick = options[i].onclick;
        if(options[i].class)
            e0.classList.add(options[i].class);
        buttons.appendChild(e0);
    }
    options_menu.appendChild(info);
    options_menu.appendChild(buttons);
    
    elem.appendChild(options_menu);
}

function delete_post(postid)
{
    document.getElementById("post_" + postid).remove();
    close_options_menu();
    httpreq("post", "./php/ajax/delete.php", [{name: "type", value: "post"}, {name: "id", value: postid}], function(request)
    {
        if(request.readyState == 4 && (request.status != 200 || request.responseText != "0"))
        {
            display_msg("The post wasn't deleted correctly");
        }
    });
}

function delete_comment(comm_id)
{
    let comm_div = document.getElementById("comment_" + comm_id);
    let own = false;
    if(!comm_div)
    {
        comm_div = document.getElementById("comment_own_" + comm_id);
        own = true;
    }
    let post_id = comm_div.parentNode.id.split("_")[2];
    comm_div.remove();
    close_options_menu();
    let post = document.getElementById("post_" + post_id);
    if(!own)
    {
        let old_id = post.getElementsByClassName("ncomments")[0].id;
        post.getElementsByClassName("ncomments")[0].id = old_id.split("_")[0] + "_" + post_id + "_" + (Number(old_id.split("_")[2]) - 1);

        if(post.getElementsByClassName("comm_loadmore")[0])
        {
            old_id = post.getElementsByClassName("comm_loadmore")[0].id;
            post.getElementsByClassName("comm_loadmore")[0].id = "own_button_" + post_id + "_" + (Number(old_id.split("_")[3]) - 1);   
        }
    }
    post.getElementsByClassName("commcount")[0].textContent = Number(post.getElementsByClassName("commcount")[0].textContent) - 1;
    
    let last_comment = document.getElementById("comments_div_" + post_id).lastChild;

    if(!last_comment || last_comment.id.split("_")[1] == "own")
    {
        let dummy_div = document.createElement("div");
        dummy_div.id = "dummy_" + comm_id;
        dummy_div.classList.add("hidden");
        document.getElementById("comments_div_" + post_id).appendChild(dummy_div);
    }

    httpreq("post", "./php/ajax/delete.php", [{name: "type", value: "comment"}, {name: "id", value: comm_id}], function(request)
    {
        if(request.readyState == 4 && (request.status != 200 || request.responseText != "0"))
        {
            display_msg("The comment wasn't deleted correctly");
        }
    });
}

function report(id, type)
{
    close_options_menu();
    let data = [{name: "id", value: id}, {name: "type", value: type}];
    httpreq("post", "./php/ajax/report.php", data, function(request)
    {
        if(request.readyState == 4 && (request.status != 200 || request.responseText == "-3"))
        {
            display_msg("There's been an error reporting the " + type + ", if the error persists try refreshing the page, the post may have already been deleted");
        }
        else if(request.readyState == 4 && request.status == 200)
        {
            if(request.responseText == "0")
                display_msg("The " + type + " has been successfully reported", "Success");
            else if(request.responseText == "-1")
                display_msg("The post is already under review, please wait");
            else
                display_msg("The post has already been reviewed");
        }
    });
}

function pre_delete_post(postid)
{
    options_menu("Delete post", [{value: "A deleted post can't be recovered, are you sure?"}], [{value: "Yes", onclick: () => delete_post(postid), class: "delete"}, {value: "No", onclick: close_options_menu}]);
}

function pre_delete_comment(comm_id)
{
    options_menu("Delete comment", [{value: "A deleted comment can't be recovered, are you sure?"}], [{value: "Yes", onclick: () => delete_comment(comm_id), class: "delete"}, {value: "No", onclick: close_options_menu}]);
}

function pre_report(id, type)
{
    options_menu("Report", [{value: "The " + type + " will be reviewed to check for a violation, are you sure?"}], [{value: "Yes", onclick: () => report(id, type), class: "delete"}, {value: "No", onclick: close_options_menu}]);
}

function open_close_post(postid, newval, dotdotdot_id)
{
    is_closing = true;
    let data = [{name: "postid", value: postid}, {name: "newval", value: Number(newval)}];
    httpreq("post", "./php/ajax/openclose.php", data, function(request)
    {
        if(request.readyState == 4 && (request.status != 200 || Number(request.responseText) < 0))
        {
            display_msg("There's been an error, try again");
            is_closing = false;
        }
        else if(request.readyState == 4 && request.status == 200)
        {
            document.getElementById(dotdotdot_id).id = "dotdotdot_" + postid + "_" + dotdotdot_id.split("_")[2] + "_" + Number(newval);
            if(newval)
                document.getElementById("post_" + postid).classList.remove("post_close");
            else
                document.getElementById("post_" + postid).classList.add("post_close");
            is_closing = false;
        }
    });
}

function post_options_menu()
{
    let obj = this;
    let postid = obj.id.split("_")[1];
    let own = obj.id.split("_")[2];
    let open = Number(obj.id.split("_")[3]);
    if(own == "own")
        own = true;
    else
        own = false;
    let data = new Array();
    let post = document.getElementById("post_" + postid);
    let a = post.getElementsByClassName("title username");
    if(a.length > 0)
        data.push({title: "User", value: post.getElementsByClassName("title username")[0].textContent});
    else
        data.push({value: "Anonymous post"});
    data.push({title: "Community", value: post.getElementsByClassName("title post_community")[0].textContent});
    data.push({title: "Date and time", value: post.getElementsByTagName("time")[0].getAttribute("datetime")});

    let postbar = post.getElementsByClassName("postbar")[0];
    let edit = postbar.id.split("_")[0];
    if(edit == "unedit")
        edit = false;
    else
        edit = true;
        
    let options = new Array();

    let likestring = postbar.getElementsByClassName("likes")[0].id;
    if(likestring.split("_")[0] == "unliked")
        likestring = "Like";
    else
        likestring = "Unlike";

    let loading_like = postbar.getElementsByClassName("likes")[0].classList.contains("loadinglike");
    if(loading_like)
        likestring += " (loading)";

    options.push({value: likestring, onclick: function()
    {
        if(loading_like)
            return;
        postbar.getElementsByClassName("likes")[0].onclick();
        close_options_menu();
    }});

    let commentstring = postbar.getElementsByClassName("ncomments")[0].id;
    if(commentstring.split("_")[0] == "showcomments")
        commentstring = "Show comments";
    else
        commentstring = "Hide comments";

    let loading_comm = postbar.getElementsByClassName("ncomments")[0].classList.contains("loadingcomments");
    if(loading_comm)
        commentstring += " (loading)";
    
    options.push({value: commentstring, onclick: function()
    {
        if(loading_comm)
            return;
        postbar.getElementsByClassName("ncomments")[0].onclick();
        close_options_menu();
    }});

    let applystring = postbar.getElementsByClassName("apply_button")[0].id.split("_")[0];
    if(applystring == "showapp" || applystring == "reshowapp")
        applystring = "Show applicants";
    else if(applystring == "hideapp")
        applystring = "Hide applicants";
    else if(applystring == "applied")
        applystring = "Unapply";
    else if(applystring == "unapplied")
        applystring = "Apply";
    else if(applystring == "acceptedapp")
        applystring = "Application options";

    let loading_app = postbar.getElementsByClassName("ncomments")[0].classList.contains("loadingapply");
    if(loading_app)
        applystring += " (loading)";

    if(applystring != "Apply" || (edit && !post.classList.contains("post_close")))
    {
        options.push({value: applystring, onclick: function()
        {
            if(loading_app)
                return;
            postbar.getElementsByClassName("apply_button")[0].onclick();
            close_options_menu();
        }});
    }

    if(own)
    {
        let val = "Close post";
        if(!open)
            val = "Reopen post";
        if(is_closing)
            val += " (loading)"
        options.push({value: val, onclick: function()
        {
            if(is_closing)
                return;
            close_options_menu();
            open_close_post(postid, !open, obj.id);
        }});

        options.push({value: "Delete post", onclick: function()
        {
            close_options_menu();
            pre_delete_post(postid);
        }, class: "delete"});
    }
    else
    {
        options.push({value: "Report", onclick: function()
        {
            close_options_menu();
            pre_report(postid, "post");
        }, class: "delete"});
    }

    options_menu("Post options", data, options);
}

function comment_options_menu()
{
    let obj = this;
    let comm_id = obj.id;
    let own = false;
    if(comm_id.split("_")[1] == "own")
    {
        own = true;
        comm_id = comm_id.split("_")[2];
    }
    else
    {
        if(comm_id.split("_")[2] == "own")
            own = true;
        comm_id = comm_id.split("_")[1];
    }
    let comm_div = document.getElementById("comment_" + comm_id);
    if(!comm_div)
        comm_div = document.getElementById("comment_own_" + comm_id);
    let data = new Array();
    data.push({title: "User", value: comm_div.getElementsByClassName("commenttitle")[0].textContent});
    data.push({title: "Date and time", value: comm_div.getElementsByTagName("time")[0].getAttribute("datetime")});

    let options = new Array();
    if(own)
    {
        options.push({value: "Delete comment", onclick: function()
        {
            close_options_menu();
            pre_delete_comment(comm_id);
        }, class: "delete"});
    }
    else
    {
        options.push({value: "Report", onclick: function()
        {
            close_options_menu();
            pre_report(comm_id, "comment");
        }, class: "delete"});
    }

    options_menu("Comment options", data, options);
}

function add_postbar_events(postbar)
{
    postbar.getElementsByClassName("likes")[0].onclick = like_unlike;
    postbar.getElementsByClassName("ncomments")[0].onclick = showcomments;
    postbar.getElementsByClassName("apply_button")[0].onclick = apply_event;
}

export function postbar_events()
{
    let postbars = document.getElementsByClassName("postbar");
    let dotdotdot = document.getElementsByClassName("dotdotdot_wrap");
    for(let i = 0; i < postbars.length; i++)
    {
        add_postbar_events(postbars[i]);
        dotdotdot[i].onclick = post_options_menu;
    }
}

export function add_post(post, position = "beforeend")
{
    if(position != "afterbegin" && position != "beforeend")
        return;
    document.getElementById("posts").insertAdjacentHTML(position, post);
    let newpost;
    if(position == "beforeend") 
        newpost = document.getElementById("posts").lastChild;
    else
        newpost = document.getElementById("posts").firstChild;
    let elem = (newpost.getElementsByClassName("likes"))[0];
    elem.onclick = like_unlike;
    elem = (newpost.getElementsByClassName("ncomments"))[0];
    elem.onclick = showcomments;
    elem = (newpost.getElementsByClassName("apply_button"))[0];
    elem.onclick = apply_event;
    elem = (newpost.getElementsByClassName("dotdotdot_wrap"))[0];
    elem.onclick = post_options_menu;
}

export function textareaheight()
{
    this.style.height = 0;
    this.style.height = (this.scrollHeight + 2) + 'px';
}

export function display_msg(msg, type = "Error")
{
    options_menu(type, [{value: msg}], [{value: "Ok", onclick: close_options_menu}]);
}