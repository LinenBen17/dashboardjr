let manifestedGuides = [];

//autofocus manifest_code
$(window).on('load', function () {
    $("input").on("keypress", function () {
        $input = $(this);
        setTimeout(function () {
            $input.val($input.val().toUpperCase());
        }, 50);
    });
    restartFocus();
});

$(document).on('change', '#route_id', function () {
    const route_id = $(this).val();
    const agency_origin_id = $('#agency_origin_id').val();
    const date = $('#date').val();
    const tbody = document.getElementById('tbodyGuidesManifests');
    const tfoot = document.getElementById('tfootGuidesManifests');
    manifestedGuides = [];

    fetch(`/shipment-manifest/buscar-agencia?route_id=${route_id}`)
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('agency_destination_id');

            const id = Object.keys(data)[0];
            const value = Object.values(data)[0];

            select.innerHTML = '';
            const option = document.createElement('option');
            option.value = id;
            option.text = value;
            select.appendChild(option);
        })
        .catch(err => {
            console.error('Error al buscar agencia:', err);
        });

    fetch(`/shipment-manifest/crear-manifiesto?route_id=${route_id}&date=${date}`)
        .then(res => res.json())
        .then(data => {
            $('#manifest_code').val(data.manifest_code);

            if (data.manifest_repeat) {
                document.getElementById('manifest_repeat_message').innerText =
                    ' - La ruta seleccionada ya tiene un manifiesto para la fecha indicada.';
            } else {
                document.getElementById('manifest_repeat_message').innerText = '';
            }
        })
        .catch(err => {
            console.error('Error al crear el nuevo codigo de manifiesto:', err);
        });

    fetch(`/shipment-manifest/obtener-guia-manifestadas?route_id=${route_id}&manifest_date=${date}&agency_origin_id=${agency_origin_id}`)
        .then(res => res.json())
        .then(data => {
            let totalGuias = data.length;
            let totalPiezas = 0;
            let totalContado = 0;
            let totalPorCobrar = 0;
            let totalCredito = 0;
            let totalPrepago = 0;
            let montoTotal = 0;

            tbody.innerHTML = '';
            data.forEach(guide => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="px-4 py-2 truncate whitespace-nowrap text-center">${guide.mother}</td>
                    <td class="px-4 py-2 truncate whitespace-nowrap">${guide.receiver_name}</td>
                    <td class="px-4 py-2 truncate whitespace-nowrap text-center">${guide.prefix_destination}</td>
                    <td class="px-4 py-2 truncate whitespace-nowrap">${guide.payment_method_name}</td>
                    <td class="px-4 py-2 text-center">${guide.pieces}</td>
                    <td class="px-4 py-2 text-center">${guide.total}</td>
                `;
                tbody.appendChild(tr);

                totalPiezas += parseInt(guide.pieces);
                montoTotal += parseFloat(guide.total);

                if (guide.payment_method_name === 'Contado') {
                    totalContado += parseFloat(guide.total);
                }
                if (guide.payment_method_name === 'Por Cobrar') {
                    totalPorCobrar += parseFloat(guide.total);
                }
                if (guide.payment_method_name === 'Crédito') {
                    totalCredito += parseFloat(guide.total);
                }
                if (guide.payment_method_name === 'Prepago') {
                    totalPrepago += parseFloat(guide.total);
                }

                manifestedGuides.push(guide.id);
            });
            console.log(manifestedGuides);

            tfoot.innerHTML = `
                <tr
                    class="bg-gray-100 dark:bg-gray-800 font-bold text-gray-700 dark:text-gray-200 border-t border-gray-300 dark:border-gray-600">
                    <td class="px-4 py-2 text-left" colspan="3">Guías: &nbsp;<span id="totalGuias">${totalGuias}</span>
                    </td>
                    <td class="px-4 py-2 text-left">Total Piezas:</td>
                    <td class="px-4 py-2 text-center" id="totalPiezas">${totalPiezas}</td>
                </tr>
                <tr class="bg-gray-50 dark:bg-gray-900 text-gray-600 dark:text-gray-300">
                    <td colspan="3"></td>
                    <td class="px-4 py-2 text-left">Contado</td>
                    <td></td>
                    <td class="px-4 py-2 text-center" id="totalContado">${totalContado.toFixed(2)}</td>
                </tr>
                <tr class="bg-gray-50 dark:bg-gray-900 text-gray-600 dark:text-gray-300">
                    <td colspan="3"></td>
                    <td class="px-4 py-2 text-left">Por Cobrar</td>
                    <td></td>
                    <td class="px-4 py-2 text-center" id="totalPorCobrar">${totalPorCobrar.toFixed(2)}</td>
                </tr>
                <tr class="bg-gray-50 dark:bg-gray-900 text-gray-600 dark:text-gray-300">
                    <td colspan="3"></td>
                    <td class="px-4 py-2 text-left">Crédito</td>
                    <td></td>
                    <td class="px-4 py-2 text-center" id="totalCredito">${totalCredito.toFixed(2)}</td>
                </tr>
                <tr class="bg-gray-50 dark:bg-gray-900 text-gray-600 dark:text-gray-300">
                    <td colspan="3"></td>
                    <td class="px-4 py-2 text-left">Prepago</td>
                    <td></td>
                    <td class="px-4 py-2 text-center" id="totalPrepago">${totalPrepago.toFixed(2)}</td>
                </tr>
                <tr
                    class="bg-gray-200 dark:bg-gray-700 font-bold text-gray-800 dark:text-gray-100 border-t-2 border-gray-400 dark:border-gray-500">
                    <td colspan="3"></td>
                    <td class="px-4 py-2 text-left">Total</td>
                    <td></td>
                    <td class="px-4 py-2 text-center text-xl" id="montoTotal">${montoTotal.toFixed(2)}</td>
                </tr>
            `;
        })
        .catch(err => {
            console.error('Error al mostrar guías relacionadas a esa ruta en esa fecha:', err);
        });
});

$('.saveManifest').on('click', function () {
    const manifestCode = $('#manifest_code').val();

    const component = document.querySelector('[wire\\:id]');
    const componentId = component?.getAttribute('wire:id');

    Livewire.find(componentId).set(`manifest_code`, $('#manifest_code').val());
    Livewire.find(componentId).set(`date`, $('#date').val());
    Livewire.find(componentId).set(`route_id`, $('#route_id').val());
    Livewire.find(componentId).set(`agency_destination_id`, $('#agency_destination_id').val());
    Livewire.find(componentId).set(`manifested_guides_id`, manifestedGuides);


    window.open(`/shipment_manifest/${manifestCode}/printManifestGuides`, '_blank');

    $('#route_id').prop('selectedIndex', -1);
});

$('.unlinkGuides').on('click', function () {
    const component = document.querySelector('[wire\\:id]');
    const componentId = component?.getAttribute('wire:id');

    Livewire.find(componentId).set(`manifest_code`, $('#manifest_code').val());
    Livewire.find(componentId).set(`date`, $('#date').val());
    Livewire.find(componentId).set(`route_id`, $('#route_id').val());
    Livewire.find(componentId).set(`agency_destination_id`, $('#agency_destination_id').val());
    Livewire.find(componentId).set(`manifested_guides_id`, manifestedGuides);

    $('#route_id').prop('selectedIndex', -1);

});

// Modificacion de guías logica
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

$(document).on('change', '#agency_destination_manifest_search', function () {
    const agency_id = $(this).val();

    fetch(`/shipment_manifest/buscar-ruta-por-agencia?agency_id=${agency_id}`)
        .then(res => res.json())
        .then(data => {
            const select = document.getElementById('route_destination_manifest_search');
            select.innerHTML = '<option value="">Seleccione una opción</option>';
            Object.entries(data).forEach(([id, prefix]) => {
                const option = document.createElement('option');
                option.value = id;
                option.textContent = prefix;
                select.appendChild(option);
            })
        })
        .catch(err => {
            console.error('Error al buscar ruta por agencia:', err);
        });
});

$(document).on('click', '.rePrintManifest', function () {
    const agency_origin_manifest_search = $('#agency_origin_manifest_search').val();
    const agency_destination_manifest_search = $('#agency_destination_manifest_search').val();
    const route_destination_manifest_search = $('#route_destination_manifest_search').val();
    const manifest_date_manifest_search = $('#manifest_date_manifest_search').val();

    let manifest_code;

    if (!agency_origin_manifest_search || !agency_destination_manifest_search || !route_destination_manifest_search || !manifest_date_manifest_search) {
        alert('Por favor, complete todos los campos de búsqueda para re-imprimir el manifiesto.');
    }
    else {
        fetch(`/shipment-manifest/buscar-manifiesto-por-origen-y-ruta?agency_origin_id=${agency_origin_manifest_search}&agency_destination_id=${agency_destination_manifest_search}&route_id=${route_destination_manifest_search}&manifest_date=${manifest_date_manifest_search}`)
            .then(res => res.json())
            .then(data => {
                manifest_code = data;
                window.open(`/shipment_manifest/${manifest_code}/printManifestGuides`, '_blank');
            })
            .catch(err => {
                console.error('Error al buscar manifiesto:', err);
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
    $("#manifest_code").focus();
}