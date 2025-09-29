import { httpreq, options_menu } from "./common.js";
import { add_post } from "./common.js";
import { postbar_events } from "./common.js";
import { textareaheight } from "./common.js";
import { display_msg } from "./common.js";

document.getElementsByTagName("body")[0].onload = load;
var posts = new Array();
var loadtime = 1;

function loadmore()
{
    let obj = document.getElementById("loadmore");
    if(!obj)
        return;
    obj.onclick = null;
    obj.id = "loadingmore";
    let data = [{name: "time", value: loadtime}];
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
            let i = 0;
            for(let k = 0; k < resp['posts'].length; k++)
            {
                if(posts.includes(resp['posts'][k].id))
                    continue;
                i++;
                posts.push(resp['posts'][k].id);
                add_post(resp['posts'][k].content);
            }
            obj.id = "loadmore";
            if(i > 0)
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

function newpost()
{
    let obj = this;
    obj.classList.add("butt_loading");
    obj.onclick = null;
    let community = document.getElementById("newpost_communities").value;
    let content = document.getElementById("newpostvalue").value;
    let anon = document.getElementById("anon").checked;
    let data = [{name: "community", value: community}, {name: "content", value: content}, {name: "anon", value: anon}];
    httpreq("post", "./php/ajax/newpost.php", data, function(request)
    {   
        if(request.readyState == 4 && request.status != 200)
        {
            obj.onclick = newpost;
            obj.classList.remove("butt_loading");
            display_msg("An error has occurred, try again");
        }
        if(request.readyState == 4 && request.status == 200)
        {
            obj.onclick = newpost;
            obj.classList.remove("butt_loading");
            if(request.responseText == "err0")
            {
                display_msg("Posts must be at least 2 characters long!");
                return;
            }
            let textarea = document.getElementById("newpostvalue");
            document.getElementById("anon").checked = false;
            textarea.style.height = 0;
            textarea.value = "";
            deselect_community();
            add_post(request.responseText, "afterbegin");
            posts.push(Number(document.getElementById("posts").firstChild.id.split("_")[1]));
        }
    });
}

function deselect_community()
{
    document.getElementById("newpost_communities").classList.remove("hidden");
    document.getElementById("select_comm").classList.remove("hidden");
    document.getElementById("anon_bar").classList.add("hidden");
    document.getElementById("send_wrap").classList.add("hidden");
    document.getElementById("newpostvalue").setAttribute("placeholder", "Add your post");
    document.getElementById("newpost_communities").value = "";
}

function select_community()
{
    document.getElementById("newpost_communities").classList.add("hidden");
    document.getElementById("select_comm").classList.add("hidden");
    document.getElementById("anon_bar").classList.remove("hidden");
    document.getElementById("send_post").textContent = "POST IN @" + document.getElementById("newpost_communities").value;
    document.getElementById("send_wrap").classList.remove("hidden");
    document.getElementById("newpostvalue").setAttribute("placeholder", "Post something in @" + document.getElementById("newpost_communities").value+ "\nTo change community click on the little × next to the post button");
}

function load()
{
    postbar_events();
    let post = document.getElementById("posts").firstChild;
    let n = 0;
    if(!document.getElementById("newpost_communities").firstChild.nextSibling)
    {
        document.getElementById("newpostcontainer").classList.add("hidden");
    }
    else
    {
        document.getElementById("newpost_communities").onchange = select_community;
        document.getElementById("change_community").onclick = deselect_community;
        document.getElementById("send_post").onclick = newpost;
        document.getElementById("newpostvalue").oninput = textareaheight;
    }
    while(post)
    {
        n++;
        posts.push(Number(post.id.split("_")[1]));
        post = post.nextSibling;
    }
    let lm = document.getElementById("loadmore");
    lm.onclick = loadmore;
    if(n < 15)
    {
        lm.style.display = "none";
        lm.id = "nomoreload";
    }
    if(n == 0)
    {
        options_menu("No posts", [{value: "It looks like there's no posts for you! Try subscribing to more communities!"}], [{value: "Go to communities", onclick: () => {window.location.href = "./communities.php"}}]);
    }
}

window.onscroll = function()
{
    if ((window.innerHeight + Math.ceil(window.pageYOffset)) >= document.body.offsetHeight)
        loadmore();
}