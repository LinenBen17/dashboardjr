var domain = window.location.protocol + '//' + window.location.hostname + (window.location.port ? ':' + window.location.port : '');

var formPayroll = document.getElementsByClassName('payroll-form');
var formActionPayroll = formPayroll[0].action;

var formPayslips = document.getElementsByClassName('payslips-form');
var formActionPayslips = formPayslips[0].action;

// Cambia la ruta del formulario dependiendo del valor seleccionado
for (let i = 0; i < formPayroll.length; i++) {
    document.querySelectorAll('.payroll-form .benefit-select')[i].addEventListener('change', function() {
        var benefitValue = this.value;

        if (benefitValue) {
            // Si hay un valor seleccionado, cambia la ruta a la ruta deseada
            formPayroll[i].action = domain + '/reports/benefit_payroll'; // Cambia esto a la ruta que desees
        } else {
            // Si no hay valor seleccionado, usa la ruta original
            formPayroll[i].action = formActionPayroll;
        }
    }); 
}

for (let i = 0; i < formPayslips.length; i++) {
    document.querySelectorAll('.payslips-form .benefit-select')[i].addEventListener('change', function() {
        var benefitValue = this.value;

        if (benefitValue) {
            // Si hay un valor seleccionado, cambia la ruta a la ruta deseada
            formPayslips[i].action = domain + '/reports/benefit_payslips'; // Cambia esto a la ruta que desees
        } else {
            // Si no hay valor seleccionado, usa la ruta original
            formPayslips[i].action = formActionPayslips;
        }
    }); 
}