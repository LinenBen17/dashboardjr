// Función para convertir el texto de los inputs a mayúsculas
function setInputsToUpperCase() {
    // Enfoca el primer input de tipo texto
    document.querySelectorAll('input[type="text"]')[0].focus();

    // Selecciona todos los inputs de tipo texto y área de texto
    const inputs = document.querySelectorAll('input[type="text"], textarea');

    // Itera sobre cada input y agrega un evento 'input'
    inputs.forEach(input => {
        input.addEventListener('input', function () {
            this.value = this.value.toUpperCase();
        });
    });
}
window.addEventListener('load', setInputsToUpperCase);

