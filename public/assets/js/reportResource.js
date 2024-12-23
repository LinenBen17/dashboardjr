var domain = window.location.protocol + '//' + window.location.hostname + (window.location.port ? ':' + window.location.port : '');

var form = document.getElementsByClassName('payroll-form');
var formAction = form[0].action;

// Cambia la ruta del formulario dependiendo del valor seleccionado
for (let i = 0; i < form.length; i++) {
    document.getElementsByClassName('benefit-select')[i].addEventListener('change', function() {
        var benefitValue = this.value;

        if (benefitValue) {
            // Si hay un valor seleccionado, cambia la ruta a la ruta deseada
            form[i].action = domain + '/reports/benefit_payroll'; // Cambia esto a la ruta que desees
        } else {
            // Si no hay valor seleccionado, usa la ruta original
            form[i].action = formAction;
        }
    }); 
}