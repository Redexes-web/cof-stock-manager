//dom content loaded
document.addEventListener('DOMContentLoaded', function () {
    console.log("home.js");
    document.addEventListener('htmx:afterSwap', function (event) {
        console.log('htmx:afterSwap', event);
        console.log('htmx:afterSwap', event);
        console.log('htmx:afterSwap', event);
    });
});
// $(function () {
//     console.log("home.js");
//     document.addEventListener('htmx:after-swap', function (event) {
//         console.log('htmx:afterSwap', event);
//     });
// });