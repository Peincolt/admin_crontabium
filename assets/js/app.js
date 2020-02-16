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
const $ = require('jquery');
// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
// const $ = require('jquery');

console.log('Hello Webpack Encore! Edit me in assets/js/app.js'); 

$(".navbar-toggler-icon").click(function() {
    if ($("button[data-target='#navbarColor01'").hasClass("collapsed")) {
        $("button[data-target='#navbarColor01'").removeClass("collapsed");
        $("#navbarColor01").slideDown();
    } else {
        $("button[data-target='#navbarColor01'").addClass("collapsed");
        $("#navbarColor01").slideUp();
    }
});

$(".dropdown-toggle").click(function(event) {
    event.preventDefault();
    if ($(this).hasClass("show")) {
        $(this).removeClass("show");
        $(".dropdown-menu").slideUp();
    } else {
        $(this).addClass("show");
        $(".dropdown-menu").slideDown();
    }
})