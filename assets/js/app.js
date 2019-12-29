/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

const imagesContext = require.context('../images/', true, /\.(png|jpg|jpeg|gif|ico|svg|webp)$/);
imagesContext.keys().forEach(imagesContext);

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.css');
const axios = require('axios');
// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
// const $ = require('jquery');

console.log('Hello Webpack Encore! Edit me in assets/js/app.js');

window.onload = function(){
    select = document.getElementById('selectUnit');
    id = select.value;
    if (select.name == "ships") {
        url = "/ship/";
    } else {
        url = "/hero/";
    }

    url+=id;

    axios.post(url).then(response => {
        constructList(response.data.name,response.data.players);
    })
}

document.getElementById('selectUnit').addEventListener('change',function(event){

    document.getElementById('list-ajax').innerHTML="";

    if (this.name == "ships") {
        url = "/ship/";
    } else {
        url = "/hero/";
    }

    url+=this.value;

    axios.post(url).then(response => {
        constructList(response.data.name,response.data.players);
    })
});

function constructList(name, data) {
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
    thName.appendChild(document.createTextNode('Player'));
    thLevel.appendChild(document.createTextNode('Level'));
    thRarity.appendChild(document.createTextNode('Stars'));
    thGalacticalPower.appendChild(document.createTextNode('Galactical Power'));

    table.appendChild(thead);
    thead.appendChild(trhead);
    trhead.appendChild(thName);
    trhead.appendChild(thLevel);
    trhead.appendChild(thRarity);
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
        tr.appendChild(tdGalacticalPower);

        tbody.appendChild(tr);

    }

    div.appendChild(title);
    div.appendChild(table);
}