const axios = require('axios');

window.onload = function(){
    var select = document.getElementById('selectUnit');
    /* Fill the unit/ship tab when the user came the first time in the page (/heroes,/ships)*/
    if (select) {
        select.addEventListener('change',getUnitInformation);
        getUnitInformation();
    }
}

function getUnitInformation(event)
{
    if (event == undefined) {
        var object = document.getElementById('selectUnit')
    } else {
        var object = event.target;
    }

    document.getElementById('list-ajax').innerHTML="";
    if (object.name == "Ships") {
        url = "/ship/";
        type= 'ship';
    } else {
        url = "/hero/";
        type = 'hero';
    }

    url+=object.value;

    axios.post(url).then(response => {
        constructList(response.data.error_message,response.data.name,response.data.players,type);
    });
}
    
/* Functions */
function constructList(error_message,name, data, type) {
    if (error_message == undefined) {
        div = document.getElementById('list-ajax');
        title = document.createElement('h2');
        table = document.createElement('table');
        thead = document.createElement('thead');
        trhead = document.createElement('tr');
        thName = document.createElement('th');
        thLevel = document.createElement('th');
        thGalacticalPower = document.createElement('th');
        thRarity = document.createElement('th');
        tbody = document.createElement('tbody');

        table.classList.add('table','table-hover');
        title.classList.add('h2');

        title.appendChild(document.createTextNode(name));
        thName.appendChild(document.createTextNode('Joueur'));
        thLevel.appendChild(document.createTextNode('Niveau'));
        thRarity.appendChild(document.createTextNode('Nombre d\'étoile'));
        thGalacticalPower.appendChild(document.createTextNode('Puissance galactique'));

        table.appendChild(thead);
        thead.appendChild(trhead);
        trhead.appendChild(thName);
        trhead.appendChild(thLevel);
        trhead.appendChild(thRarity);

        if (type == 'hero') {
            thGearLevel = document.createElement('th');
            thRelicLevel = document.createElement('th');
            thGearLevel.appendChild(document.createTextNode('Niveau d\'équipement'));
            thRelicLevel.appendChild(document.createTextNode('Niveau de relic'));
            trhead.appendChild(thGearLevel);
            trhead.appendChild(thRelicLevel);
        }

        trhead.appendChild(thGalacticalPower);
        table.appendChild(tbody);

        for (i=0;i<data.length;i++) {
            tr = document.createElement('tr');
            tdName = document.createElement('td');
            tdLevel = document.createElement('td');
            tdGalacticalPower = document.createElement('td');
            tdRarity = document.createElement('td');

            tdName.appendChild(document.createTextNode(data[i].player_name));
            tdLevel.appendChild(document.createTextNode(data[i].level));
            tdRarity.appendChild(document.createTextNode(data[i].stars));
            tdGalacticalPower.appendChild(document.createTextNode(data[i].galactical_puissance));

            tr.appendChild(tdName);
            tr.appendChild(tdLevel);
            tr.appendChild(tdRarity);

            if (type == 'hero') {
                tdGearLevel = document.createElement('td');
                tdRelicLevel = document.createElement('td');
                tdGearLevel.appendChild(document.createTextNode(data[i].gear_level));
                tdRelicLevel.appendChild(document.createTextNode(data[i].relic));
                tr.appendChild(tdGearLevel);
                tr.appendChild(tdRelicLevel);
            }

            tr.appendChild(tdGalacticalPower);

            tbody.appendChild(tr);

        }

        div.appendChild(title);
        div.appendChild(table);
    } else {
        div = document.getElementById('list-ajax');
        title = document.createElement('h2');
        title.classList.add('h2');
        title.appendChild(document.createTextNode(error_message));
        div.appendChild(title);
    }
}