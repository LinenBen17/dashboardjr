//autofocus manifest_code
$(window).on('load', function () {
    $("input, textarea").on("keypress", function () {
        const $input = $(this);
        setTimeout(function () {
            $input.val($input.val().toUpperCase());
        }, 50);
    });
    restartFocus();
});

window.addEventListener("restartFocus", () => {
    document.getElementById("search_guide").focus();
});

// Obtener información de guía en Consulta de Guías
$(document).on('click', '#searchGuideBtn', function () {
    const guide = document.getElementById('search_guide').value.trim();

    if (!guide) return;

    fetch(`/shipment-entries/buscar-guia?guide=${guide}`)
        .then(res => res.json())
        .then(data => {

            // Foreach para guide_data, guide_incomes y guide_outgos
            if (data.guide_data) {
                const guide = data.guide_data;

                $('#sender_name_consult').text(guide.sender_name);
                $('#sender_address_consult').text(guide.sender_address);
                $('#sender_phone_consult').text(guide.sender_phone);
                $('#receiver_name_consult').text(guide.receiver_name);
                $('#receiver_address_consult').text(guide.receiver_address);
                $('#receiver_phone_consult').text(guide.receiver_phone);
                $('#product_consult').text(guide.product_description);
                $('#pieces_consult').text(guide.pieces);
                $('#unit_price_consult').text(guide.unit_price);
                $('#total_consult').text(guide.total);
                $('#date_guide_consult').text(guide.date_guide);
                $('#payment_method_consult').text(guide.payment_method);
                $('#manifest_no_consult').text(guide.manifest_code);

                $('#received_by_name').focus();
            } else {
                $('#sender_name_consult').text('');
                $('#sender_address_consult').text('');
                $('#sender_phone_consult').text('');
                $('#receiver_name_consult').text('');
                $('#receiver_address_consult').text('');
                $('#receiver_phone_consult').text('');
                $('#product_consult').text('');
                $('#pieces_consult').text('');
                $('#unit_price_consult').text('');
                $('#total_consult').text('');
                $('#date_guide_consult').text('');
                $('#payment_method_consult').text('');
                $('#manifest_no_consult').text('');
            }

        })
        .catch(err => {
            console.error('Error al buscar guía:', err);
        });

    fetch(`/shipment-delivery/obtener-datos-entrega?guide=${guide}`)
        .then(res => res.json())
        .then(data => {
            if (data) {
                $('#received_by_name').val(data.received_by_name);
                $('#received_by_document').val(data.received_by_document);
                $('#observations').val(data.observations);
                if (data.signed) {
                    const componentId = document.querySelector('[wire\\:id]').getAttribute('wire:id');
                    Livewire.find(componentId).set('signed', true);
                    window.dispatchEvent(new CustomEvent('set-toggle', { detail: true }));
                } else {
                    const componentId = document.querySelector('[wire\\:id]').getAttribute('wire:id');
                    Livewire.find(componentId).set('signed', false);
                    window.dispatchEvent(new CustomEvent('set-toggle', { detail: false }));
                }

            }

        })
        .catch(err => {
            console.error('Error al buscar guía:', err);
        });
})

// Actualizar el valor del toggle en JS (no clave)
window.addEventListener('toggle-changed', (event) => {
    // También actualizar en Livewire (clave)
    const component = document.querySelector('[wire\\:id]');
    const componentId = component?.getAttribute('wire:id');

    // asignar tambien valor wire al resto de campos
    Livewire.find(componentId).set(`received_by_name`, $('#received_by_name').val());
    Livewire.find(componentId).set(`received_by_document`, $('#received_by_document').val());
    Livewire.find(componentId).set(`observations`, $('#observations').val());

    Livewire.find(componentId).set(`signed`, event.detail);
});

function restartFocus() {
    $("#search_guide").focus();
}
