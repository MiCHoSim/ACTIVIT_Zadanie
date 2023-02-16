
function request(url, callback)
{
    let xhr = new XMLHttpRequest();
    xhr.onload = () => {
        callback(xhr.response);
    }
    xhr.open("GET", url);
    xhr.send();
}

function validateIban()
{
    let iban = document.getElementById("iban");
    let icon = document.getElementById("icon");


    let url = "https://openiban.com/validate/" + iban.value;

    request(url, (data) => {

        const iconsClass = {'true':'fa-check-circle', 'false':'fa-times-circle'};
        const iconsColor = {'true':'text-success', 'false':'text-danger'};
        icon.className = '';
        icon.classList.add('fa');
        icon.classList.add(iconsColor[JSON.parse(data)['valid']]);
        icon.classList.add(iconsClass[JSON.parse(data)['valid']]);
        iban.after(icon);
    });
}

let iban = document.getElementById("iban");
let icon = document.createElement("i");
icon.setAttribute('id', 'icon')
iban.after(icon);