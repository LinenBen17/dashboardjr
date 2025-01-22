var fechaActual = new Date();

var age = document.getElementById('age');

document.getElementById('birth_date').addEventListener('focusout', function () {
    var fechaNacimiento = new Date(document.getElementById('birth_date').value);

    // Calcula la edad inicial
    var edad = fechaActual.getFullYear() - fechaNacimiento.getFullYear();

    // Ajusta la edad si el cumpleaños aún no ha ocurrido este año
    var mesActual = fechaActual.getMonth();
    var diaActual = fechaActual.getDate();
    var mesNacimiento = fechaNacimiento.getMonth();
    var diaNacimiento = fechaNacimiento.getDate();

    if (mesActual < mesNacimiento || (mesActual === mesNacimiento && diaActual < diaNacimiento)) {
        edad--;
    }

    age.value = edad;
});

document.getElementById('civil_status_id').addEventListener('change', function () {
    document.getElementById('address').focus();
    console.log("WS");
});