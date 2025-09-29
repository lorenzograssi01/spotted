import { httpreq } from "./common.js";
import { display_msg } from "./common.js";

document.getElementsByTagName("body")[0].onload = load;
let loading_comm = false;

function sub_event(elem, event = subscribe_unsubscribe)
{
    let buttons = elem.getElementsByClassName("subbutton");
    for(let i = 0; i < buttons.length; i++)
    {
        buttons[i].onclick = event;
    }
}

function empty_search()
{
    document.getElementById("search_res").textContent = "No matches. If you can't find the community you want ";
    let link = document.createElement("a");
    link.textContent = "try creating it!";
    link.classList.add("show");
    link.setAttribute("href", "./newcommunity.php?new_name=" + regularize(document.getElementById("search_bar").value))
    document.getElementById("search_res").appendChild(link);
}

function check_empty()
{
    if(!document.getElementById("sugg_comm").firstChild || document.getElementById("search_bar").value.length != 0)
        document.getElementById("sugg").style.display = "none";
    else     
        document.getElementById("sugg").style.display = "block";

    if(!document.getElementById("yours_comm").firstChild)
        document.getElementById("yours").style.display = "none";
    else     
        document.getElementById("yours").style.display = "block";
    if(!document.getElementById("search_res").firstChild && document.getElementById("search_bar").value.length != 0)
    {
        empty_search();
    }
}

function subscribe_unsubscribe()
{
    let obj = this;
    obj.onclick = null;
    let community = obj.id.split("+")[1];
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
        if(request.readyState == 4 && request.status == 200)
        {
            obj.classList.remove("subbutton_loading");
            obj.classList.add("subbutton");
            let subs_count = Number(document.getElementById("subs_count+"+community).textContent);
            obj.onclick = subscribe_unsubscribe;
            if(type == "subscribe")
            {
                let comm = document.getElementById("community+" + community);
                let comm_inactive = document.getElementById("community+" + community + "$inactive");
                if(comm_inactive)
                    comm_inactive.remove();
                obj.textContent = "UNSUBSCRIBE";
                obj.classList.remove("subscribe");
                obj.classList.add("unsubscribe");
                comm.classList.remove("community_unsubscribed");
                comm.classList.add("community_subscribed");
                obj.id = "unsubscribe_button+" + community;
                document.getElementById("subs_count+"+community).textContent = subs_count + 1;
                comm.remove();
                document.getElementById("yours_comm").prepend(comm);
            }
            else
            {
                let comm = document.getElementById("community+" + community);
                obj.textContent = "SUBSCRIBE";
                obj.classList.remove("unsubscribe");
                obj.classList.add("subscribe");
                comm.classList.add("community_unsubscribed");
                comm.classList.remove("community_subscribed");
                obj.id = "subscribe_button+" + community;
                document.getElementById("subs_count+"+community).textContent = subs_count - 1;
                comm.remove();
            }
            check_empty();
        }
    });
}

function while_loading_comm()
{
    loading_comm = true;
    sub_event(document.getElementById("sugg_comm"), null);
    sub_event(document.getElementById("search_res"), null);
}

function end_loading_comm()
{
    loading_comm = false;
    sub_event(document.getElementById("sugg_comm"));
    sub_event(document.getElementById("search_res"));
}

function getwords(str)
{
    let str2 = regularize(str);
    return str2.replace(/[-._]+/g, "_").replace(/^_/, "").replace(/_$/, "");
}

function regularize(str)
{
    let str2 = str.normalize("NFKD").replace(/[\u0300-\u036f]/g, "");
    str2 = str2.replace(/[^-a-z_.0-9]+/ig, "_");
    return str2;
}

function loadcomm()
{
    let obj = this;
    if(regularize(obj.value).length > 0)
    {
        if(loading_comm)
            return;
        while_loading_comm();
        let data = [{name: "str", value: regularize(obj.value)}, {name: "words", value: getwords(obj.value)}];
        httpreq("get", "./php/ajax/loadsearched.php", data, function(request)
        {   
            if(request.readyState == 4 && request.status == 200)
            {
                end_loading_comm();
                if(regularize(obj.value).length == 0)
                    return;
                document.getElementById("sugg").style.display = "none";
                document.getElementById("search_res").style.display = "block";
                let c = document.getElementById("sugg_comm").firstChild;
                while(c)
                {
                    if(!c.id.includes("$"))
                        c.id = c.id + "$inactive";
                    c = c.nextSibling;
                }
                if(request.responseText == "")
                {
                    empty_search();
                }
                else
                {
                    document.getElementById("search_res").innerHTML = request.responseText;
                    sub_event(document.getElementById("search_res"));
                }
                if(data[0].value != regularize(obj.value))
                    obj.oninput();
            }
        });
    }
    else
    {
        document.getElementById("search_res").style.display = "none";
        if(document.getElementById("sugg_comm").firstChild)
            document.getElementById("sugg").style.display = "block";
        let c = document.getElementById("sugg_comm").firstChild;
        while(c)
        {
            c.id = c.id.split("$")[0];
            c = c.nextSibling;
        }
        document.getElementById("search_res").innerHTML = "";
    }
}

function load()
{
    sub_event(document.getElementById("yours"));
    sub_event(document.getElementById("sugg_comm"));
    document.getElementById("search_bar").oninput = loadcomm;
    check_empty();
}