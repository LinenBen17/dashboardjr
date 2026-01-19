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

// Actualizar el valor del toggle firmado en JS (no clave)
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

$(document).on('click', '#searchGuideBtnUpdate', function () {
    const guide = document.getElementById('search_guide_update').value.trim();
    let town_id_searched;
    // También actualizar en Livewire (clave)
    const component = document.querySelector('[wire\\:id]');
    const componentId = component?.getAttribute('wire:id');

    if (!guide) return;

    // fetch para obtener datos de entrega
    fetch(`/shipment-entries/buscar-guia?guide=${guide}`)
        .then(res => res.json())
        .then(data => {

            // Foreach para guide_data, guide_incomes y guide_outgos
            if (data.guide_data) {
                console.log(data);

                const guide = data.guide_data;

                $('#manifest_code_update').val(guide.manifest_code);
                $('#sender_name_update').val(guide.sender_name);
                $('#sender_address_update').val(guide.sender_address);
                $('#sender_phone_update').val(guide.sender_phone);
                $('#receiver_name_update').val(guide.receiver_name);
                $('#receiver_address_update').val(guide.receiver_address);
                $('#receiver_phone_update').val(guide.receiver_phone);
                $('#prefix_origen_update').val(guide.prefix_origin);
                $('#prefix_destino_update').val(guide.prefix_destination);
                town_id_searched = guide.town_id;
                $('#product_update').val(guide.product_description);
                $('#pieces_update').val(guide.pieces);
                $('#unit_price_update').val(guide.unit_price);
                $('#total_update').val(guide.total);
                $('#sender_total_update').val(guide.sender_total);
                $('#receiver_total_update').val(guide.receiver_total);
                $('#date_guide_update').val(guide.date_guide.split('/').reverse().join('-'));
                $('#manifest_code_update').val(guide.manifest_code);

                $('#received_by_name').focus();

                Livewire.find(componentId).call(`buscarGuia`, guide.id);

                // Segundo fetch: obtener municipio (ahora tiene town_id_searched)
                fetch(`/shipment-entries/buscar-unico-municipio?town_id=${town_id_searched}`)
                    .then(res => res.json())
                    .then(data => {
                        const select = document.getElementById('town_id_update');
                        select.innerHTML = '<option value="">Seleccione una opción</option>';

                        Object.entries(data).forEach(([id, name]) => {
                            const option = document.createElement('option');
                            option.value = id;
                            option.textContent = name;
                            select.appendChild(option);
                        });

                        // Si solo hay un registro, seleccionarlo automáticamente
                        if (Object.entries(data).length === 1) {
                            const firstEntry = Object.entries(data)[0];
                            select.value = firstEntry[0];
                            // Disparar el evento change para ejecutar otros eventos asociados
                            select.dispatchEvent(new Event('change'));
                        }
                    })
                    .catch(err => {
                        console.error('Error al buscar municipio:', err);
                    });

                fetch(`/shipment-entries/buscar-ruta?town_id=${town_id_searched}`)
                    .then(res => res.json())
                    .then(data => {
                        const input = document.getElementById('route_destino_update');

                        input.value = data;
                    })
                    .catch(err => {
                        console.error('Error al buscar municipio:', err);
                    });

                $('#payment_method_id_update').val(guide.payment_method_id);
            } else {
                $('#sender_name_update').val('');
                $('#sender_address_update').val('');
                $('#sender_phone_update').val('');
                $('#receiver_name_update').val('');
                $('#receiver_address_update').val('');
                $('#receiver_phone_update').val('');
                $('#product_update').val('');
                $('#pieces_update').val('');
                $('#unit_price_update').val('');
                $('#total_update').val('');
                $('#date_guide_update').val('');
                $('#payment_method_update').val('');
                $('#manifest_code_update').val('');
            }

        })
        .catch(err => {
            console.error('Error al buscar guía:', err);
        });
});

// Buscar destino al escribir en el campo destino
$('#prefix_destino_update').keydown(function () {
    const valor = $(this).val();

    if (valor.length > 2) {

        fetch(`/shipment-entries/buscar-municipios?prefix=${valor}`)
            .then(res => res.json())
            .then(data => {
                const select = document.getElementById('town_id_update');
                select.innerHTML = '<option value="">Seleccione una opción</option>';

                Object.entries(data).forEach(([id, name]) => {
                    const option = document.createElement('option');
                    option.value = id;
                    option.textContent = name;
                    select.appendChild(option);
                });
            })
            .catch(err => {
                console.error('Error al buscar municipios:', err);
            });
    }
});

$(document).on('change', '#town_id_update', function () {
    const town_id = $(this).val();
    console.log(town_id);

    fetch(`/shipment-entries/buscar-ruta?town_id=${town_id}`)
        .then(res => res.json())
        .then(data => {
            const input = document.getElementById('route_destino_update');

            input.value = data;

        })
        .catch(err => {
            console.error('Error al buscar ruta:', err);
        });
})

$(document).on('blur', '.product_index-input', function () {
    const codigoIngresado = $(this).val();
    const indexInput = this.dataset.index;

    fetch(`/shipment-entries/buscar-producto?code=${codigoIngresado}`)
        .then(res => res.json())
        .then(data => {
            $('#pieces_' + indexInput).val(1);
            $('#product_description_' + indexInput).val(data.description);
            $('#unit_price_' + indexInput).val(data.price);

            // También actualizar en Livewire (clave)
            const component = document.querySelector('[wire\\:id]');
            const componentId = component?.getAttribute('wire:id');

            Livewire.find(componentId).set(`productos.${indexInput}.product_id`, $(this).val());
            Livewire.find(componentId).set(`productos.${indexInput}.pieces`, $('#pieces_' + indexInput).val());
            Livewire.find(componentId).set(`productos.${indexInput}.product_description`, data.description);
            Livewire.find(componentId).set(`productos.${indexInput}.unit_price`, data.price);
            Livewire.find(componentId).set(`productos.${indexInput}.subtotal`, data.price);

            setTimeout(() => {
                addingSubtotal();
            }, 50);
        })
        .catch(err => {
            console.error('Error al buscar municipios:', err);
        });
})

$(document).on('change', '.pieces_index-input', function () {
    const indexInput = this.dataset.index;
    const pieces = parseFloat($(this).val());
    const unitPrice = parseFloat($('#unit_price_' + indexInput).val());

    if (!isNaN(pieces) && !isNaN(unitPrice)) {
        const subtotal = pieces * unitPrice;
        $('#subtotal_' + indexInput).val(subtotal.toFixed(2));

        const component = document.querySelector('[wire\\:id]');
        const componentId = component?.getAttribute('wire:id');

        Livewire.find(componentId).set(`productos.${indexInput}.pieces`, pieces);
        Livewire.find(componentId).set(`productos.${indexInput}.unit_price`, unitPrice);
        Livewire.find(componentId).set(`productos.${indexInput}.subtotal`, $('#subtotal_' + indexInput).val());

        addingSubtotal();

    } else {
        $('#subtotal_' + indexInput).val('');
    }
})

$(document).on('change', '.unit_price_index-input', function () {
    const indexInput = this.dataset.index;
    const unitPrice = parseFloat($(this).val());
    const pieces = parseFloat($('#pieces_' + indexInput).val());

    if (!isNaN(unitPrice) && !isNaN(pieces)) {
        const subtotal = pieces * unitPrice;
        $('#subtotal_' + indexInput).val(subtotal.toFixed(2));

        const component = document.querySelector('[wire\\:id]');
        const componentId = component?.getAttribute('wire:id');

        Livewire.find(componentId).set(`productos.${indexInput}.pieces`, pieces);
        Livewire.find(componentId).set(`productos.${indexInput}.unit_price`, unitPrice);

        addingSubtotal();

    } else {
        $('#subtotal_' + indexInput).val('');
    }
})

$(document).on('blur', '.unit_price_index-input', function () {
    const indexInput = this.dataset.index;
    const component = document.querySelector('[wire\\:id]');
    const componentId = component?.getAttribute('wire:id');
    Livewire.find(componentId).set(`productos.${indexInput}.subtotal`, $('#subtotal_' + indexInput).val());

    addingSubtotal();
})

$(document).on('keydown', '.saveShipment', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        e.stopImmediatePropagation(); // ← para evitar conflicto con el handler global
        $('.saveShipment').click(); // ← hace foco y clic
    }
});

$(document).on('keydown', '.saveChilds', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        e.stopImmediatePropagation(); // ← para evitar conflicto con el handler global
        $('.saveChilds').click(); // ← hace foco y clic
    }
});

$('.saveShipment').on('click', function () {
    addingSubtotal();

    setTimeout(() => {
        const component = document.querySelector('[wire\\:id]');
        const componentId = component?.getAttribute('wire:id');

        const updateData = {
            no_guide_user_update: $('#search_guide_update').val(),
            date_guide_update: $('#date_guide_update').val(),
            payment_method_id_update: $('#payment_method_id_update').val(),
            shipment_manifest_id: $('#manifest_code_update').val(),
            sender_total_update: $('#sender_total_update').val(),
            receiver_total_update: $('#receiver_total_update').val(),
            total_update: $('#total_update').val(),
            sender_code_update: $('#codigo_remitente_update').val(),
            sender_name_update: $('#sender_name_update').val(),
            sender_address_update: $('#sender_address_update').val(),
            sender_phone_update: $('#sender_phone_update').val(),
            receiver_code_update: $('#codigo_destinatario_update').val(),
            receiver_name_update: $('#receiver_name_update').val(),
            receiver_address_update: $('#receiver_address_update').val(),
            receiver_phone_update: $('#receiver_phone_update').val(),
            prefix_origin_update: $('#prefix_origen_update').val(),
            prefix_destination_update: $('#prefix_destino_update').val(),
            town_id_update: $('#town_id_update').val()
        };

        Livewire.find(componentId).set('updateData', updateData);
    }, 50);
});

$('.saveChilds').on('click', function () {
    addingSubtotal();

    setTimeout(() => {
        const component = document.querySelector('[wire\\:id]');
        const componentId = component?.getAttribute('wire:id');

        const updateData = {
            no_guide_user_update: $('#search_guide_update').val(),
            date_guide_update: $('#date_guide_update').val(),
            payment_method_id_update: $('#payment_method_id_update').val(),
            shipment_manifest_id: $('#manifest_code_update').val(),
            sender_total_update: $('#sender_total_update').val(),
            receiver_total_update: $('#receiver_total_update').val(),
            total_update: $('#total_update').val(),
            sender_code_update: $('#codigo_remitente_update').val(),
            sender_name_update: $('#sender_name_update').val(),
            sender_address_update: $('#sender_address_update').val(),
            sender_phone_update: $('#sender_phone_update').val(),
            receiver_code_update: $('#codigo_destinatario_update').val(),
            receiver_name_update: $('#receiver_name_update').val(),
            receiver_address_update: $('#receiver_address_update').val(),
            receiver_phone_update: $('#receiver_phone_update').val(),
            prefix_origin_update: $('#prefix_origen_update').val(),
            prefix_destination_update: $('#prefix_destino_update').val(),
            town_id_update: $('#town_id_update').val()
        };

        console.log(updateData);


        Livewire.find(componentId).set('updateData', updateData);
    }, 50);
});

$(document).on('keydown', '#child', function (e) {
    const input = document.getElementById('child');

    if (e.key === 'Enter') {
        console.log("enter child")
        e.preventDefault();

        const valor = input.value.trim();

        input.value = ''; // Limpiar

        if (!valor) return;

        // Encuentra el componente Livewire
        const component = document.querySelector('[wire\\:id]');
        const componentId = component?.getAttribute('wire:id');

        if (componentId) {
            if (valor.startsWith('H')) {
                Livewire.find(componentId).call('addChildGuide', valor);
            }
            else {
                console.error("El valor ingresado no es válido. Debe comenzar con 'H'.");
                return;
            }
        } else {
            console.error("No se encontró el componente Livewire.");
        }
    }
})

function addingSubtotal() {
    const codigo_remitente = document.getElementById('codigo_remitente_update');
    const codigo_destinatario = document.getElementById('codigo_destinatario_update');

    const sender_total = document.getElementById('sender_total_update');
    const receiver_total = document.getElementById('receiver_total_update');
    const totalMount = document.getElementById('total_update');

    const subtotals = document.querySelectorAll('.subtotal_index-input');

    const forma_pago = document.getElementById('payment_method_id_update').value;

    sender_total.value = '0.00';
    receiver_total.value = '0.00';

    let total = 0;

    subtotals.forEach(subtotal => {
        const value = parseFloat(subtotal.value);
        if (!isNaN(value)) {
            total += value;
        }
    });

    if (forma_pago == 1) {
        receiver_total.value = total.toFixed(2);
    } else if (forma_pago == 2) {
        sender_total.value = total.toFixed(2);
    } else if (forma_pago == 3 || forma_pago == 4) {
        if (codigo_remitente.value != null) {
            sender_total.value = total.toFixed(2);
        } else if (codigo_destinatario.value != null) {
            receiver_total.value = total.toFixed(2);
        }
    }

    totalMount.value = (parseFloat(sender_total.value) + parseFloat(receiver_total.value)).toFixed(2);
}

function restartFocus() {
    $("#search_guide").focus();
}
