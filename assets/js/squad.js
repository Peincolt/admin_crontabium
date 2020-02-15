import '../css/squad.css';
const axios = require('axios');

var numberInput = 1;
var data = null;
window.onload = function (){
    axios.post('/units').then(response => {
        var inputs = document.getElementsByClassName('unit-form');
        for (var i=0;i<inputs.length;i++) {
            autocomplete(inputs[i],response.data);
        }
    })
};

function autocomplete(inp,array)
{
    var currentFocus;
    if (!data) {
        data = array;
    }
    inp.addEventListener("input",function(e) {
        var value = this.value;
        deleteItems(value);
        console.log('event');
        var a = document.createElement('div');
        a.classList.add('autocomplete-items');
        a.setAttribute('id',"autocomplete-list");
        this.parentNode.appendChild(a);
        if (!value) {
            return false;
        }
        currentFocus = -1;
        for(var i=0;i<array.length;i++) {
            if (value.toUpperCase() == array[i].substr(0,value.length).toUpperCase()) {
                var div = document.createElement('div');
                div.innerHTML = '<strong>'+value.toUpperCase()+'<strong>'+array[i].substr(value.length);
                div.innerHTML+= '<input type="hidden" value="'+array[i]+'">';
                div.addEventListener('click',function(e) {
                    inp.value = this.getElementsByTagName('input')[0].value;
                    deleteItems(value);
                });
                a.appendChild(div);     
            }
        }
    });

    inp.addEventListener("keydown",function(event) {
        var x = document.getElementById("autocomplete-list");
        if (x)
        var divs = x.getElementsByTagName("div");
        if (event.keyCode == "40") {
            currentFocus++;
            focusElement(divs);
        }

        if (event.keyCode == "38") { 
            currentFocus--;
            focusElement(divs);
        }

        if (event.keyCode == "13") {
            event.preventDefault();
            if (currentFocus > -1) {
                if (divs) {
                    divs[currentFocus].click();
                }
            }
        }
    });

    function focusElement(divs)
    {
        if (!divs) {
            return false;
        }

        removeFocus(divs);

        if (currentFocus > divs.length) {
            currentFocus = 0;
        }

        if (currentFocus < 0) {
            currentFocus = divs.length - 1;
        }

        divs[currentFocus].classList.add("autocomplete-active");
    }

    function removeFocus(divs) {
        for (var i=0;i<divs.length;i++) {
            divs[i].classList.remove("autocomplete-active");
        }
    }

    function deleteItems(value)
    {
        var list = document.getElementsByClassName('autocomplete-items');
        for (var i=0; i<list.length;i++) {
            if (list[i] != value) {
                list[i].parentNode.removeChild(list[i]);
            }
        }
    }

    document.addEventListener("click",function(event){
        deleteItems(event.target);
    });
}

function translate(number)
{
    switch (number) {
        case 2 :
            return 'second';
        break;
        case 3:
            return 'third';
        break;
        case 4:
            return 'fourth';
        break;
        case 5:
            return 'fifth';
        break;
        default:
            return 'undefined';
        break;
    }
    
}