document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById('guideInput');
    // const checkbox = document.getElementById('reincome');

    let motherGuide = [];
    let childGuide = [];

    input.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();

            const valor = input.value.trim();
            // const reingreso = checkbox ? checkbox.checked : false;
            // console.log(reingreso);
            input.value = ''; // Limpiar

            if (!valor) return;

            // Encuentra el componente Livewire
            const component = document.querySelector('[wire\\:id]');
            const componentId = component?.getAttribute('wire:id');

            if (componentId) {
                if (valor.startsWith('GU')) {
                    Livewire.find(componentId).call('addMotherGuide', valor);
                    $('#last_mother_guide').val(valor);
                } else if (valor.startsWith('H')) {
                    Livewire.find(componentId).call('addChildGuide', valor);
                    $('#last_child_guide').val(valor);
                }
                else {
                    console.error("El valor ingresado no es válido. Debe comenzar con 'GU' o 'H'.");
                    return;
                }
            } else {
                console.error("No se encontró el componente Livewire.");
            }
        }
    });
});