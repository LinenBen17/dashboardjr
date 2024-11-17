// Función para convertir el texto de los inputs a mayúsculas
function setInputsToUpperCase() {
    // Selecciona todos los inputs de tipo texto y área de texto
    const inputs = document.querySelectorAll('input[type="text"], textarea');

    // Itera sobre cada input y agrega un evento 'input'
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    });
}

// Llama a la función al cargar la página
window.addEventListener('load', setInputsToUpperCase);

//LLAMAR A LA FUNCION CUANDO UN MODAL APAREZCA

let statusModal = 0;

/* document.querySelectorAll('a.btn').forEach(button => {
    button.addEventListener('click', function() {
        document.querySelectorAll('[role="dialog"]').forEach(modal => {
            modal.addEventListener('transitionend', () => {
                if (modal.classList.contains('modal')) {
                    console.log()
                    setInputsToUpperCase();
                }
            });
        });
    })
});
 */