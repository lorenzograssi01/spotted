import { close_options_menu, display_msg, httpreq, options_menu } from "./common.js";

document.getElementsByTagName("body")[0].onload = load;

function add_chat(div, chat)
{
    div.insertAdjacentHTML("beforeend", chat);
    click_event(div.lastChild);
}

function loadmore()
{
    let obj = this;
    obj.onclick = null;
    let type = obj.id.split("_")[2];
    let last_msg = "false";
    let first;
    let div = document.getElementById(type + "_chats_div");
    obj.classList.add("loading");
    if(type == "open")
        first = document.getElementById("open_chats_div").lastChild.lastChild.id.split("_")[1];
    else
        first = document.getElementById("potential_chats_div").lastChild.id.split("_")[1];
    if(document.getElementById("open_chats_div").lastChild)
        last_msg = document.getElementById("open_chats_div").firstChild.lastChild.id.split("_")[1];
    let data = [{name: "type", value: type}, {name: "last_msg", value: last_msg}, {name: "first_msg", value: first}]
    httpreq("get", "./php/ajax/loadmorecontacts.php", data, function(request)
    {
        if(request.readyState == 4 && request.status != 200)
        {
            display_msg("There was an error trying to load more chats");
            obj.classList.remove("loading");
            obj.onclick = loadmore;
        }
        if(request.readyState == 4 && request.status == 200)
        {   
            let resp = JSON.parse(request.responseText);
            for(let i = 0; i < resp.length; i++)
                add_chat(div, resp[i]);
            obj.classList.remove("loading");
            if(resp.length < 7)
                obj.classList.add("hidden");
            else
                obj.onclick = loadmore;
        }
    });
}

function goto_chat()
{
    window.location.href = "./message.php?id=" + this.id.split("_")[1];
}

function unapply(postid)
{
    let data = [{name:"postid", value:postid}, {name:"type", value:"applied"}];
    httpreq("post", './php/ajax/likeunlike.php', data, function(request)
    {   
        if(request.readyState == 4 && (request.status != 200 || Number(request.responseText) < 0))
        {
            display_msg("The application wasn't removed correctly");
        }
    });
}

function unaccept(appid)
{
    let data = [{name: "appid", value: appid}, {name: "type", value: 0}];
    httpreq("post", "./php/ajax/acceptapplicant.php", data, function(request)
    {
        if(request.readyState == 4 && (request.status != 200 || Number(request.responseText) < 0))
        {
            display_msg("The application wasn't unaccepted correctly");
        }
    });
}

function dotdotdot_app(obj_id)
{
    let id = obj_id.split("_")[1];
    let postid = obj_id.split("_")[2];
    let data = new Array();
    let chat = document.getElementById("chat_"+id + "_own");
    if(!chat)
        chat = document.getElementById("chat_"+id+ "_ext");
    data.push({
        title: "Original poster",
        value: chat.getElementsByClassName("title username")[0].textContent
    });

    data.push({
        title: "Applicant",
        value: chat.getElementsByClassName("title username applicant")[0].textContent
    });
    
    data.push({
        title: "Community",
        value: chat.getElementsByClassName("title post_community")[0].textContent
    });

    let options = new Array();

    if(chat.id.split("_")[2] == "own")
    {
        options.push({
            value: "Unaccept applicant",
            onclick: function()
            {
                close_options_menu();
                unaccept(id);
                chat.classList.add("hidden");
            }
        });
    }

    if(chat.id.split("_")[2] == "ext")
    {
        options.push({
            value: "Cancel application",
            onclick: function()
            {
                close_options_menu();
                unapply(postid);
                chat.classList.add("hidden");
            }
        });
    }

    options_menu("Application options", data, options);
}

function click_event(chat)
{
    chat.onclick = goto_chat;
    let elem = chat.firstChild.firstChild;
    while(elem)
    {
        elem.onclick = function(e) {e.stopPropagation();};
        elem = elem.nextSibling;
    }
    chat.getElementsByClassName("dotdotdot_wrap")[0].onclick = function(e) {e.stopPropagation(); dotdotdot_app(this.id);};
}

function load()
{
    let nopen = document.getElementById("open_chats_div").childElementCount;
    let npotential = document.getElementById("potential_chats_div").childElementCount;
    if(nopen < 7)
        document.getElementById("load_more_open").classList.add("hidden");
    else
        document.getElementById("load_more_open").onclick = loadmore;
       
    if(npotential < 7)
        document.getElementById("load_more_potential").classList.add("hidden");
    else
        document.getElementById("load_more_potential").onclick = loadmore;

    if(nopen == 0)
        document.getElementById("open_chats").classList.add("hidden");
    if(npotential == 0)
        document.getElementById("potential_chats").classList.add("hidden");

    if(nopen == 0 && npotential == 0)
        options_menu("No chats", [{value: "You have no chats! Try appliying to some posts!"}], [{value: "Go home", onclick: () => {window.location.href = "./posts.php"}}]);
    
    let chats = document.getElementsByClassName("chat");
    for(let i = 0; i < chats.length; i++)
    {
        click_event(chats[i]);
    }
}