import { display_msg, httpreq, textareaheight } from "./common.js";
window.error = display_msg;

document.getElementsByTagName("body")[0].onload = load;
var isload = false;

function add_msg(div, msg, position)
{
    if(position != "beforeend" && position != "afterbegin")
        return;
    div.insertAdjacentHTML(position, msg);
}

function sendmessage()
{   
    isload = true;
    let msgtext = document.getElementById("new_message").value;
    if(msgtext.length < 1)
        return;
    let obj = this;
    obj.classList.add("loading");
    obj.onclick = null;
    let application = document.getElementById("application_wrap").firstChild.id.split("_")[1];
    let div = document.getElementById("messages_div");
    let last_msg = false;
    if(div.firstChild)
        last_msg = div.firstChild.id.split("+")[1];
    let data = [{name: "application", value: application}, {name: "type", value: "send"}, {name: "last_msg", value: last_msg}, {name: "first_msg", value: false}, {name: "msg_text", value: msgtext}];
    httpreq("post", "./php/ajax/messages.php", data, function(request)
    {
        if(request.readyState == 4 && (request.status != 200 || Number(request.responseText)))
        {
            display_msg("There's been an error trying to send your message");
            obj.classList.remove("loading");
            obj.onclick = sendmessage;
            isload = false;
        }
        else if(request.readyState == 4 && request.status == 200)
        {   
            let resp = JSON.parse(request.responseText);
            for(let i = resp.length - 1; i >= 0; i--)
                add_msg(div, resp[i], "afterbegin");
            obj.classList.remove("loading");
            obj.onclick = sendmessage;
            document.getElementById("new_message").value = "";
            document.getElementById("new_message").oninput();
            isload = false;
        }
    });
}

function loadmoremessages()
{
    let obj = this;
    obj.classList.add("loading");
    obj.onclick = null;
    let application = document.getElementById("application_wrap").firstChild.id.split("_")[1];
    let div = document.getElementById("messages_div");
    let first_msg = div.lastChild.id.split("+")[1];
    let data = [{name: "application", value: application}, {name: "type", value: "receive"}, {name: "last_msg", value: false}, {name: "first_msg", value: first_msg}];
    httpreq("post", "./php/ajax/messages.php", data, function(request)
    {
        if(request.readyState == 4 && (request.status != 200 || Number(request.responseText)))
        {
            display_msg("There's been an error trying to load more messages");
            obj.classList.remove("loading");
            obj.onclick = loadmoremessages;
        }
        else if(request.readyState == 4 && request.status == 200)
        {   
            let resp = JSON.parse(request.responseText);
            for(let i = 0; i < resp.length; i++)
                add_msg(div, resp[i], "beforeend");
            obj.classList.remove("loading");
            if(resp.length < 40)
                obj.classList.add("hidden");
            else
                obj.onclick = loadmoremessages;
        }
    });
}

function loadnewmessages()
{
    let application = document.getElementById("application_wrap").firstChild.id.split("_")[1];
    let div = document.getElementById("messages_div");
    let last_msg = false;
    if(div.firstChild)
        last_msg = div.firstChild.id.split("+")[1];
    let data = [{name: "application", value: application}, {name: "type", value: "receive"}, {name: "last_msg", value: last_msg}, {name: "first_msg", value: false}];
    httpreq("post", "./php/ajax/messages.php", data, function(request)
    {
        if(request.readyState == 4 && request.status == 200)
        {   
            if(!isload)
            {
                let resp = JSON.parse(request.responseText);
                for(let i = resp.length - 1; i >= 0; i--)
                    add_msg(div, resp[i], "afterbegin");
            }
            
            setTimeout(loadnewmessages, 2000);
        }
    });
}

function load()
{
    document.getElementById("sendmessage").onclick = sendmessage;
    document.getElementById("new_message").oninput = textareaheight;
    if(document.getElementById("messages_div").childElementCount < 40)
        document.getElementById("loadmore").classList.add("hidden");
    else
        document.getElementById("loadmore").onclick = loadmoremessages;
    setTimeout(loadnewmessages, 2000);
    document.getElementById("messages_wrap").onscroll = function()
    {
        if(Math.ceil(-this.scrollTop + this.clientHeight) >= this.scrollHeight)
            if(document.getElementById("loadmore").onclick)
                document.getElementById("loadmore").onclick();
    }
}