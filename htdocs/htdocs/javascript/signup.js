import { httpreq } from "./common.js";
import { display_msg } from "./common.js";
window.error = display_msg;

document.getElementsByTagName("body")[0].onload = load;
var repeat = false;

function checkuname()
{
    let obj = this;
    let uname = obj.value;
    if(uname.match(/[^a-z_.0-9-]/i) || uname.length < 2 || uname.length > 20)
    {
        obj.classList.remove("right");
        obj.classList.add("wrong");
        return;
    }
    obj.classList.remove("right");
    obj.classList.remove("wrong");
    obj.oninput = null;
    let data = [{name: "username", value: uname}];
    httpreq("get", "./php/ajax/checkusernameavaliability.php", data, function(request)
    {
        if(request.readyState == 4 && request.status == 200)
        {
            obj.oninput = checkuname;
            if(request.responseText.split("+")[1] == "-1" && request.responseText.split("+")[0] == obj.value)
                obj.classList.add("right");
            else
                obj.classList.add("wrong");
            if(obj.value != request.responseText.split("+")[0])
                obj.oninput();
        }
    });
}

function checkpword()
{
    let obj = this;
    let password = obj.value;
    if(!password.match(/[0-9]/) || !password.match(/[a-z]/i) || password.length > 30 || password.length < 8)
    {
        obj.classList.add("wrong");
        obj.classList.remove("right");
    }
    else
    {
        obj.classList.add("right");
        obj.classList.remove("wrong");
    }
    checkprepeat();
}

function checkprepeat()
{
    let prepeat = document.getElementById("prepeat").value;
    if(prepeat.length > 0)
        repeat = true;
    if(!repeat)
        return;
    let pword = document.getElementById("pword").value;
    if(prepeat != pword)
    {
        document.getElementById("prepeat").classList.add("wrong");
        document.getElementById("prepeat").classList.remove("right");
    }
    else
    {
        document.getElementById("prepeat").classList.add("right");
        document.getElementById("prepeat").classList.remove("wrong");
    }
}

function load()
{
    document.getElementById("uname").oninput = checkuname;
    document.getElementById("pword").oninput = checkpword;
    document.getElementById("prepeat").oninput = checkprepeat;
}