/* document.getElementById('end_date').addEventListener('focusout', function () {
    fechaInicio = moment(document.getElementById('start_date').value);
    fechaFin = moment(document.getElementById('end_date').value);
    let dias = fechaFin.diff(fechaInicio, 'days');
    document.getElementById('days_requested').value = dias;
}) */

document.addEventListener("moonshine:init", () => {
    MoonShine.onCallback('createVacation', function (response, element, events, component) {
        document.getElementById('days_requested').readOnly = true;
        console.log(document.getElementById('days_requested'));
        document.getElementById('end_date').addEventListener('blur', function () {
            let fechaInicio = moment(document.getElementById('start_date').value);
            let fechaFin = moment(document.getElementById('end_date').value);

            let diasHabiles = 0;
            while (fechaInicio.isBefore(fechaFin) || fechaInicio.isSame(fechaFin, 'day')) {
                if (fechaInicio.isoWeekday() !== 6 && fechaInicio.isoWeekday() !== 7) { // 6 = Saturday, 7 = Sunday
                    diasHabiles++;
                }
                fechaInicio.add(1, 'days');
            }

            document.getElementById('days_requested').value = diasHabiles;
        })
    })
})