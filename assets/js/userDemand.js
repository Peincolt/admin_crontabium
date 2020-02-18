const $ = require('jquery');

document.addEventListener('DOMContentLoaded',function() {
    document.getElementById('checkbox-valider').onchange = editDemand;
},false);

function editDemand(event)
{
    var targetId = event.target.id;
    var url = "/user/demand/";
    var regexValider = new RegExp('.*valider.*');
    if (regexValider.test(targetId)) {
        url += "/valid";
    } else {
        url += "/decline";
    }

    $.post(url,
        {
        'id' : event.target.value
        }
    ).done(function(data){
        if (data.error_message) {
            console.log(data.error_message);
        } else {
            console.log(data.message);
            /* foreach sur les checkbox pour récupérer celui qui correspond à l'id que j'aurais renvoyé.
            récupération du parent de la checkbox et du tbody. Suppression du child td qui contient la checkbox */
        }
    });
}