/* let scannedGuides = [];

$(document).ready(function () {
    const no_guide_input = $('#data\\.guideInput');

    no_guide_input.on("keydown", function (event) {
        if (event.which === 13) {
            event.preventDefault();
            const valor = $(this).val().trim();
            if (valor) {
                Livewire.find($('#your-component-id').data('id')).call('addGuide', valor);
                $(this).val('');
            }
        }
    });
}); */

document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById('guideInput');
    const warehouse = document.getElementById('warehouse_id');

    let motherGuide = [];
    let childGuide = [];

    input.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();

            const valor = input.value.trim();
            const warehouse_id = warehouse.value.trim();

            input.value = ''; // Limpiar

            if (!valor) return;

            let no_guide = valor.replace(/^\D+/g, '').replace(/^0+/, '');

            // Encuentra el componente Livewire
            const component = document.querySelector('[wire\\:id]');
            const componentId = component?.getAttribute('wire:id');

            if (componentId) {
                if (valor.startsWith('GU')) {
                    fetch(`/shipment-entries/buscar-guia?guide=${no_guide}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data) {
                                $('#codigo_remitente').val(data.guide_data.sender_code);
                                $('#remitente').val(data.guide_data.sender_name);
                                $('#dir_remitente').val(data.guide_data.sender_address);
                            }
                        })
                        .catch(error => console.error('Error al buscar la guía:', error));

                    Livewire.find(componentId).call('addMotherGuide', valor, warehouse_id);
                    $('#last_mother_guide').val(valor);
                } else if (valor.startsWith('H')) {
                    Livewire.find(componentId).call('addChildGuide', valor, warehouse_id);
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