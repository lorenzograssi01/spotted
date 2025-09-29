import { httpreq } from "./common.js";
import { add_post } from "./common.js";
import { postbar_events } from "./common.js";
import { add_newcommentdiv } from "./common.js";
import { textareaheight } from "./common.js";
import { display_msg } from "./common.js";
window.error = display_msg;

if(document.getElementById("posts"))
{
    var maxid;
    document.getElementsByTagName("body")[0].onload = load;
    if(document.getElementById("posts").lastChild)
        maxid = Number(document.getElementById("posts").lastChild.id.split("_")[1]);
    var community = document.getElementById("community").firstChild.id.split("+")[1];
    var loadtime = 1;
}

function loadmore()
{
    let obj = document.getElementById("loadmore");
    if(!obj)
        return;
    obj.onclick = null;
    obj.id = "loadingmore";
    let data = [{name: "time", value: loadtime}, {name: "type", value: "comm"}, {name: "community", value: community}, {name: "maxid", value: maxid}];
    httpreq("get", "./php/ajax/loadmore.php", data, function(request)
    {
        if(request.readyState == 4 && request.status != 200)
        {
            obj.onclick = loadmore;
            obj.id = "loadmore";
            display_msg("An error has occurred, try again");
        }
        if(request.readyState == 4 && request.status == 200)
        {   
            let resp = JSON.parse(request.responseText);
            for(let k = 0; k < resp['posts'].length; k++)
            {
                add_post(resp['posts'][k].content);
                maxid = resp['posts'][k].id;
            }
            obj.id = "loadmore";
            if(resp['posts'].length > 0)
            {
                loadtime++;
                obj.onclick = loadmore;
            }
            else
            {
                obj.classList.add("hidden");
                obj.id = "nomoreload";
            }
        }
    });
}

function unable_editing()
{
    let postbars = document.getElementsByClassName("postbar");
    for(let i = 0; i < postbars.length; i++)
    {
        let postid = postbars[i].id.split("_")[1]
        postbars[i].id = "unedit_postbar_" + postid;
        if(postbars[i].firstChild.nextSibling.id.split("_")[0] == "hidecomments")
        {
            let newcommentdiv = document.getElementById("addcommentcontainer_"+postid);
            newcommentdiv.removeChild(newcommentdiv.firstChild);
        }
    }
}

function able_editing()
{
    let postbars = document.getElementsByClassName("postbar");
    for(let i = 0; i < postbars.length; i++)
    {
        let postid = postbars[i].id.split("_")[2]
        postbars[i].id = "postbar_" + postid;
        if(postbars[i].firstChild.nextSibling.id.split("_")[0] == "hidecomments")
        {
            let newcommentdiv = document.getElementById("addcommentcontainer_"+postid);
            add_newcommentdiv(newcommentdiv, postid)
        }
    }
}

function newpost()
{
    let obj = this;
    let sub = document.getElementById("unsubscribe_button+" + community);
    sub.onclick = null;
    obj.onclick = null;
    obj.classList.add("butt_loading");
    let content = document.getElementById("newpostvalue+" + community).value;
    let anon = document.getElementById("anon").checked;
    let data = [{name: "community", value: community}, {name: "content", value: content}, {name: "anon", value: anon}];
    httpreq("post", "./php/ajax/newpost.php", data, function(request)
    {   
        if(request.readyState == 4 && request.status != 200)
        {
            sub.onclick = subscribe_unsubscribe;
            obj.onclick = newpost;
            obj.classList.remove("butt_loading");
            display_msg("An error has occurred, try again");
        }
        else if(request.readyState == 4 && request.status == 200)
        {
            sub.onclick = subscribe_unsubscribe;
            obj.onclick = newpost;
            obj.classList.remove("butt_loading");
            if(request.responseText == "err0")
            {
                display_msg("Posts must be at least 2 characters long!");
                return;
            }
            let textarea = document.getElementById("newpostvalue+" + community);
            document.getElementById("anon").checked = false;
            textarea.style.height = 0;
            textarea.value = "";
            add_post(request.responseText, "afterbegin");
        }
    });
}

function subscribe_unsubscribe()
{
    let obj = this;
    obj.onclick = null;
    let type = obj.id.split("_")[0];
    let data = [{name: "community", value: community}, {name: "type", value: type}];
    obj.classList.remove("subbutton");
    obj.classList.add("subbutton_loading");
    httpreq("post", "./php/ajax/subunsub.php", data, function(request)
    {
        if(request.readyState == 4 && (request.status != 200 || request.responseText == "0"))
        {
            display_msg("An error has occurred, try again");
            obj.classList.remove("subbutton_loading");
            obj.classList.add("subbutton");
            obj.onclick = subscribe_unsubscribe;
        }
        else if(request.readyState == 4 && request.status == 200)
        {
            if(request.responseText == "deletedcomm")
                window.location.href = "./communities.php";
            obj.classList.remove("subbutton_loading");
            obj.classList.add("subbutton");
            let subs_count = Number(document.getElementById("subs_count+"+community).textContent);
            obj.onclick = subscribe_unsubscribe;
            if(type == "subscribe")
            {
                obj.textContent = "UNSUBSCRIBE";
                obj.classList.remove("subscribe");
                obj.classList.add("unsubscribe");
                document.getElementById("community+" + community).classList.remove("community_unsubscribed");
                document.getElementById("community+" + community).classList.add("community_subscribed");
                obj.id = "unsubscribe_button+" + community;
                document.getElementById("addpost+"+community).classList.remove("hidden");
                document.getElementById("subs_count+"+community).textContent = subs_count + 1;
                able_editing();
            }
            else
            {
                obj.textContent = "SUBSCRIBE";
                obj.classList.remove("unsubscribe");
                obj.classList.add("subscribe");
                document.getElementById("community+" + community).classList.add("community_unsubscribed");
                document.getElementById("community+" + community).classList.remove("community_subscribed");
                obj.id = "subscribe_button+" + community;
                document.getElementById("addpost+"+community).classList.add("hidden");
                document.getElementById("subs_count+"+community).textContent = subs_count - 1;
                unable_editing();
            }
        }
    });
}

function load()
{
    postbar_events();
    document.getElementsByClassName("subbutton")[0].onclick = subscribe_unsubscribe;
    let n = document.getElementById("posts").childElementCount;
    let lm = document.getElementById("loadmore");
    document.getElementById("newpostvalue+" + community).oninput = textareaheight;
    document.getElementById("send_post").onclick = newpost;
    lm.onclick = loadmore;
    if(n < 15)
    {
        lm.style.display = "none";
        lm.id = "nomoreload";
    }
}

window.onscroll = function()
{
    if ((window.innerHeight + Math.ceil(window.pageYOffset)) >= document.body.offsetHeight)
        loadmore();
}