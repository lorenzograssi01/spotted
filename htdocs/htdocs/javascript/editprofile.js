import { display_msg } from "./common.js";
import { textareaheight } from "./common.js";
window.error = display_msg;

document.getElementsByTagName("body")[0].onload = load;

function update()
{
    let social = this.id;
    if(this.value == "")
    {
        document.getElementById(social + "fav").disabled = true;
        document.getElementById(social + "fav").selected = false;
        if(document.getElementById("snapchat").value.length != 0)
            document.getElementById("snapchatfav").selected = true;
        if(document.getElementById("facebook").value.length != 0)
            document.getElementById("facebookfav").selected = true;
        if(document.getElementById("instagram").value.length != 0)
            document.getElementById("instagramfav").selected = true;
    }
    else
    {
        document.getElementById(social + "fav").removeAttribute("disabled");
        if(document.getElementById("favorite").value == "")
            document.getElementById(social + "fav").selected = true;
    }
}

function load()
{
    document.getElementById("snapchat").oninput = update;
    document.getElementById("facebook").oninput = update;
    document.getElementById("instagram").oninput = update;
    document.getElementById("description").oninput = textareaheight;
}