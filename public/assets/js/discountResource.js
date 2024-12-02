/* console.log(document.getElementById('no_share'))

document.getElementById('no_share').addEventListener('blur', function() {
    console.log("HICE CHANGE" + this)
}) */

/* document.querySelector('#prestamosButton').addEventListener('blur', function() {
    console.log(document.getElementById('no_share'));
    
}) */


document.addEventListener("moonshine:init", () => {
    MoonShine.onCallback('myFunction', function(response, element, events, component) {
        document.getElementById('no_share').addEventListener('blur', function() {
            document.getElementById('amount_share').value = parseFloat(document.getElementById('amount_loan').value / document.getElementById('no_share').value).toFixed(2);
        })
    })
})