import { postbar_events } from "./common.js";
import { httpreq } from "./common.js";
import { add_post } from "./common.js";

if(document.getElementById("posts"))
{
    var maxid;
    document.getElementsByTagName("body")[0].onload = load;
    if(document.getElementById("posts").lastChild)
        maxid = Number(document.getElementById("posts").lastChild.id.split("_")[1]);
    var user = document.getElementById("user").firstChild.id.split("+")[1];
    var loadtime = 1;
}

function loadmore()
{
    let obj = document.getElementById("loadmore");
    if(!obj)
        return;
    obj.onclick = null;
    obj.id = "loadingmore";
    let data = [{name: "time", value: loadtime}, {name: "type", value: "user"}, {name: "user", value: user}, {name: "maxid", value: maxid}];
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

function load()
{
    postbar_events();
    let n = document.getElementById("posts").childElementCount;
    let lm = document.getElementById("loadmore");
    if(document.getElementById("edit+user+" + user))
        document.getElementById("edit+user+" + user).onclick = () => {window.location.href = "./editprofile.php"};
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