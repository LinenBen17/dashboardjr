//focus guia madre cuando se carga la pagina
$(window).on('load', function () {
    $("input").on("keypress", function () {
        $input = $(this);
        setTimeout(function () {
            $input.val($input.val().toUpperCase());
        }, 50);
    })

    restartFocus();

    $("#forma_pago").val(1);

    $(document).on('keydown', 'input, select, textarea, button', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();

            // Cualquier elemento focuseable que esté visible y habilitado
            const focusables = $(
                'input:not(#child):not([type=hidden]):enabled:visible,' +   // <input>
                'select:enabled:visible,' +                     // <select>
                'textarea:enabled:visible,' +                   // <textarea>
                'button:enabled:visible,' +                     // <button>
                'input[type=button]:enabled:visible,' +         // <input type="button">
                'input[type=submit]:enabled:visible,' +         // <input type="submit">
                '[tabindex]:not([tabindex="-1"]):enabled:visible' // cualquier otro con tabindex
            );

            let idx = focusables.index(this);
            if (idx > -1 && idx < focusables.length - 1) {
                focusables.eq(idx + 1).focus();
            }
        }
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
    });

    // Buscar destino al escribir en el campo destino
    $('#prefix_destino').keydown(function () {
        const valor = $(this).val();

        if (valor.length > 2) {

            fetch(`/shipment-entries/buscar-municipios?prefix=${valor}`)
                .then(res => res.json())
                .then(data => {
                    const select = document.getElementById('town_id');
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

    $(document).on('blur', '#guia_madre', function () {
        if ($(this).val() != '') {
            const guiaMadre = $(this).val();

            fetch(`/shipment-entries/buscar-guia?guide=${guiaMadre}`)
                .then(res => res.json())
                .then(data => {
                    if (data.guide_data) {
                        const component = document.querySelector('[wire\\:id]');
                        const componentId = component?.getAttribute('wire:id');

                        $('#guia_madre').val('');
                        Livewire.find(componentId).call('notificationJs', 'Guía madre ya existe', `La guía madre ${guiaMadre} ya existe en el sistema. Por favor, ingrese una guía madre diferente.`, 'warning');
                        restartFocus();
                    }
                })
                .catch(err => {
                    console.error('Error al buscar guía madre:', err);
                });
        }
    });

});

$(document).on('blur', '#codigo_remitente', function () {
    if ($(this).val() != '') {
        const codigo = $(this).val();

        fetch(`/shipment-entries/buscar-cliente?code=${codigo}`)
            .then(res => res.json())
            .then(data => {
                if (Object.keys(data).length !== 0) {
                    if (Object.keys(data).length !== 0) {
                        $('#sender_name').val(data.name);
                        $('#sender_address').val(data.address);
                        $('#sender_phone').val(data.phone);
                        $('#codigo_destinatario').focus();
                    } else {
                        $('#sender_name').val('');
                        $('#sender_address').val('');
                        $('#sender_phone').val('');
                    }
                }

            })
            .catch(err => {
                console.error('Error al buscar cliente:', err);
            });
    }
})

let tablaCustomers;

$(document).on('keydown', '#codigo_remitente', function (e) {
    const inputCodigoDestinatario = this;

    if (e.key === 'F1') {
        e.preventDefault();

        const component = document.querySelector('[wire\\:id]');
        const componentId = component?.getAttribute('wire:id');

        Livewire.find(componentId).call('openCustomersModal');

        setTimeout(() => {
            if (!$.fn.DataTable.isDataTable('#tablaClientes')) {
                tablaCustomers = $('#tablaClientes').DataTable({
                    dom: 'f',
                    paging: true,
                    pageLength: 10,
                    info: false,
                    ordering: false,
                    select: {
                        style: 'single'
                    },
                    keys: {
                        columns: [0], // Solo permite moverse en la columna 0
                        //focus: ':eq(0)', El foco comienza en la primera celda
                    },
                    ajax: {
                        url: '/shipment-entries/listar-clientes',
                        dataSrc: ''
                    },
                    columns: [
                        { data: 'code' },
                        { data: 'name' },
                        { data: 'address' },
                        { data: 'phone' }
                    ]
                });

                $('#tablaClientes tbody').on('click', 'tr', function (e) {
                    const data = tablaCustomers.row(this).data();
                    const code = data.code.split("-")[1];

                    $(inputCodigoDestinatario).val(code);

                    Livewire.find(componentId).call('closeCustomersModal');

                    tablaCustomers.destroy();

                    $('#tablaClientes').empty();
                });

                /* let code
 
                tablaCustomers.on('key-focus', function (e, datatable, cell) {
                    // Obtener la fila correspondiente a la celda con foco
                    const rowIdx = cell.data();
                    code = rowIdx.split("-")[1];
 
                });
 
                $(document).on('keypress', '#tablaClientes', function (e) {
                    console.log(e.key)
                }); */

                // Foco en el input de búsqueda
                setTimeout(() => {
                    $('input[aria-controls="tablaClientes"]').focus();
                }, 200);
            } else {
                tablaCustomers.ajax.reload();
            }
        }, 1200);
    }
});

$(document).on('keydown', '#codigo_destinatario', function (e) {
    const inputCodigoDestinatario = this;

    if (e.key === 'F1') {
        e.preventDefault();

        const component = document.querySelector('[wire\\:id]');
        const componentId = component?.getAttribute('wire:id');

        Livewire.find(componentId).call('openCustomersModal');

        setTimeout(() => {
            if (!$.fn.DataTable.isDataTable('#tablaClientes')) {
                tablaCustomers = $('#tablaClientes').DataTable({
                    dom: 'f',
                    paging: false,
                    info: false,
                    ordering: false,
                    select: {
                        style: 'single'
                    },
                    keys: {
                        columns: [0], // Solo permite moverse en la columna 0
                        //focus: ':eq(0)', El foco comienza en la primera celda
                    },
                    ajax: {
                        url: '/shipment-entries/listar-clientes',
                        dataSrc: ''
                    },
                    columns: [
                        { data: 'code' },
                        { data: 'name' },
                        { data: 'address' },
                        { data: 'phone' }
                    ]
                });

                $('#tablaClientes tbody').on('click', 'tr', function (e) {
                    const data = tablaCustomers.row(this).data();
                    const code = data.code.split("-")[1];

                    $(inputCodigoDestinatario).val(code);

                    Livewire.find(componentId).call('closeCustomersModal');

                    tablaCustomers.destroy();

                    $('#tablaClientes').empty();
                });

                /* let code
 
                tablaCustomers.on('key-focus', function (e, datatable, cell) {
                    // Obtener la fila correspondiente a la celda con foco
                    const rowIdx = cell.data();
                    code = rowIdx.split("-")[1];
 
                });
 
                $(document).on('keypress', '#tablaClientes', function (e) {
                    console.log(e.key)
                }); */

                // Foco en el input de búsqueda
                setTimeout(() => {
                    $('input[aria-controls="tablaClientes"]').focus();
                }, 200);
            } else {
                tablaCustomers.ajax.reload();
            }
        }, 800);
    }
});

$(document).on('blur', '#codigo_destinatario', function () {
    if ($(this).val() != '') {
        const codigo = $(this).val();

        fetch(`/shipment-entries/buscar-cliente?code=${codigo}`)
            .then(res => res.json())
            .then(data => {


                if (Object.keys(data).length !== 0) {
                    if (Object.keys(data).length !== 0) {
                        $('#receiver_name').val(data.name);
                        $('#receiver_address').val(data.address);
                        $('#receiver_phone').val(data.phone);
                    } else {
                        $('#sender_name').val('');
                        $('#sender_address').val('');
                        $('#sender_phone').val('');
                    }
                }
            })
            .catch(err => {
                console.error('Error al buscar cliente:', err);
            });
    }
})

window.addEventListener('toggle-changed', (event) => {
    // También actualizar en Livewire (clave)
    const component = document.querySelector('[wire\\:id]');
    const componentId = component?.getAttribute('wire:id');

    Livewire.find(componentId).set(`link_child_later`, event.detail);
});

$(document).on('change', '#town_id', function () {
    const town_id = $(this).val();

    fetch(`/shipment-entries/buscar-ruta?town_id=${town_id}`)
        .then(res => res.json())
        .then(data => {
            const input = document.getElementById('route_destino');

            input.value = data;

        })
        .catch(err => {
            console.error('Error al buscar ruta:', err);
        });
})

$(document).on('blur', '#product_id_input', function () {
    const codigoIngresado = $(this).val();

    fetch(`/shipment-entries/buscar-producto?code=${codigoIngresado}`)
        .then(res => res.json())
        .then(data => {
            $('#product_description_input').val(data.description);
            $('#unit_price').val(data.price);

            const component = document.querySelector('[wire\\:id]');
            const componentId = component?.getAttribute('wire:id');

            Livewire.find(componentId).set(`newProduct.product_id`, codigoIngresado);
            Livewire.find(componentId).set(`newProduct.product_description`, data.description);
            Livewire.find(componentId).set(`newProduct.unit_price`, data.price);
        })
        .catch(err => {
            console.error('Error:', err);
        });
});

$(document).on('input', '#pieces_input, #unit_price', function () {
    const pieces = parseFloat($('#pieces_input').val());
    const unitPrice = parseFloat($('#unit_price').val());

    if (!isNaN(pieces) && !isNaN(unitPrice)) {
        const subtotal = pieces * unitPrice;

        const component = document.querySelector('[wire\\:id]');
        const componentId = component?.getAttribute('wire:id');

        Livewire.find(componentId).set(`newProduct.pieces`, pieces);
        Livewire.find(componentId).set(`newProduct.unit_price`, unitPrice);
        Livewire.find(componentId).set(`newProduct.subtotal`, subtotal.toFixed(2));
    }
});

$(document).on('blur', '#total', function () {
    $('.saveShipment').focus();
})

$(document).on('keydown', '#total', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        e.stopImmediatePropagation(); // ← evita que se dispare el handler global
        $('.saveShipment').focus();
    }
});

$(document).on('keydown', '.saveShipment', function (e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        e.stopImmediatePropagation(); // ← para evitar conflicto con el handler global
        $('.saveShipment').click(); // ← hace foco y clic
    }
});

$('.saveShipment').on('click', function () {
    const component = document.querySelector('[wire\\:id]');
    const componentId = component?.getAttribute('wire:id');


    // Agregar valor a variables en Livewir
    Livewire.find(componentId).set(`no_guide_user`, $('#guia_madre').val());
    Livewire.find(componentId).set(`manifest_code`, $('#manifest_code').val());
    Livewire.find(componentId).set(`date_guide`, $('#date_guide').val());
    Livewire.find(componentId).set(`payment_method_id`, $('#forma_pago').val());
    Livewire.find(componentId).set(`sender_total`, $('#sender_total').val());
    Livewire.find(componentId).set(`receiver_total`, $('#receiver_total').val());
    Livewire.find(componentId).set(`total`, $('#total').val());

    Livewire.find(componentId).set(`sender_code`, $('#codigo_remitente').val());
    Livewire.find(componentId).set(`sender_name`, $('#sender_name').val());
    Livewire.find(componentId).set(`sender_address`, $('#sender_address').val());
    Livewire.find(componentId).set(`sender_phone`, $('#sender_phone').val());

    Livewire.find(componentId).set(`receiver_code`, $('#codigo_destinatario').val());
    Livewire.find(componentId).set(`receiver_name`, $('#receiver_name').val());
    Livewire.find(componentId).set(`receiver_address`, $('#receiver_address').val());
    Livewire.find(componentId).set(`receiver_phone`, $('#receiver_phone').val());

    Livewire.find(componentId).set(`prefix_destination`, $('#prefix_destino').val());
    Livewire.find(componentId).set(`town_id`, $('#town_id').val());

    // Livewire.find(componentId).call('verifyData');
});


// Obtener información de guía en Consulta de Guías
$(document).on('click', '#searchGuideBtn', function () {
    const guide = document.getElementById('search_guide').value.trim();

    if (!guide) return;

    fetch(`/shipment-entries/buscar-guia?guide=${guide}`)
        .then(res => res.json())
        .then(data => {
            /* $('#sender_name_consult').text(data.sender_name);
            $('#sender_address_consult').text(data.sender_address);
            $('#sender_phone_consult').text(data.sender_phone);
            $('#receiver_name_consult').text(data.receiver_name);
            $('#receiver_address_consult').text(data.receiver_address);
            $('#receiver_phone_consult').text(data.receiver_phone);
            $('#product_consult').text(data.product_description);
            $('#pieces_consult').text(data.pieces);
            $('#unit_price_consult').text(data.unit_price);
            $('#total_consult').text(data.total);
            $('#date_guide_consult').text(data.date_guide);
            $('#payment_method_consult').text(data.payment_method);
            $('#manifest_no_consult').text(data.no_manifest); */

            // Foreach para guide_data, guide_incomes y guide_outgos
            if (data.guide_data) {
                const guide = data.guide_data;
                const tbody = document.getElementById('tracking_table_body');
                const row = document.createElement('tr');

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

                tbody.innerHTML = '';
                row.className = 'bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-50';

                row.innerHTML = `
                    <td class="text-center py-2 px-2">${guide.created_at}</td>
                    <td class="text-center py-2 px-2">${guide.departament_name}</td>
                    <td class="text-center py-2 px-2">Ingreso a Recepción</td>
                    <td class="text-center py-2 px-2">${guide.created_by}</td>
                    <td class="text-center py-2 px-2">
                        -
                    </td>
                `;

                tbody.appendChild(row);
            }

            if (data.guide_incomes && data.guide_incomes.length > 0) {
                const tbody = document.getElementById('tracking_table_body');
                tbody.innerHTML = '';

                data.guide_incomes.forEach(income => {
                    const row = document.createElement('tr');
                    row.className = 'bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-50';

                    row.innerHTML = `
                        <td class="text-center py-2 px-2">${income.scanned_at}</td>
                        <td class="text-center py-2 px-2">${income.warehouse_name}</td>
                        <td class="text-center py-2 px-2">Ingreso a Bodega</td>
                        <td class="text-center py-2 px-2">${income.user_name}</td>
                        <td class="py-2 px-2">
                            Manifiesto de Entrada: ${income.manifest_code} <br>
                            Ruta que recolecta: ${income.route_name} <br>
                            Placas del camión: ${income.route_plate}
                        </td>
                    `;

                    tbody.appendChild(row);
                });

            }

            if (data.guide_outgos && data.guide_outgos.length > 0) {
                const tbody = document.getElementById('tracking_table_body');

                data.guide_outgos.forEach(outgo => {
                    const row = document.createElement('tr');
                    row.className = 'bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-50';

                    row.innerHTML = `
                        <td class="text-center py-2 px-2">${outgo.scanned_at}</td>
                        <td class="text-center py-2 px-2">${outgo.destination_name}</td>
                        <td class="text-center py-2 px-2">Salida de Bodega</td>
                        <td class="text-center py-2 px-2">${outgo.user_name}</td>
                        <td class="py-2 px-2">
                            Manifiesto de Salida: ${outgo.manifest_code} <br>
                            Ruta que entrega: ${outgo.route_name} <br>
                            Placas del camión: ${outgo.route_plate} <br>
                            Origen: ${outgo.origin_name}
                        </td>
                    `;

                    tbody.appendChild(row);
                });

            }

        })
        .catch(err => {
            console.error('Error al buscar guía:', err);
        });
})

function addingSubtotal() {
    const component = document.querySelector('[wire\\:id]');
    const componentId = component?.getAttribute('wire:id');

    const productos = Livewire.find(componentId).get('productos');

    const codigo_remitente = document.getElementById('codigo_remitente');
    const codigo_destinatario = document.getElementById('codigo_destinatario');

    const sender_total = document.getElementById('sender_total');
    const receiver_total = document.getElementById('receiver_total');
    const totalMount = document.getElementById('total');

    const forma_pago = document.getElementById('forma_pago').value;

    sender_total.value = '0.00';
    receiver_total.value = '0.00';

    let total = 0;

    productos.forEach(producto => {
        const value = parseFloat(producto.subtotal);
        if (!isNaN(value)) {
            total += value;
        }
    });

    if (forma_pago == 1) {
        receiver_total.value = total.toFixed(2);
    } else if (forma_pago == 2) {
        sender_total.value = total.toFixed(2);
    } else if (forma_pago == 3 || forma_pago == 4) {
        if (codigo_remitente.value) {
            sender_total.value = total.toFixed(2);
        } else if (codigo_destinatario.value) {
            receiver_total.value = total.toFixed(2);
        }
    }

    totalMount.value = (parseFloat(sender_total.value) + parseFloat(receiver_total.value)).toFixed(2);
}

function restartFocus() {
    $("#guia_madre").val('');
    $("#guia_madre").focus();
}

document.addEventListener('livewire:initialized', () => {
    Livewire.on('focus-product-input', () => {
        setTimeout(() => {
            document.getElementById('product_id_input')?.focus();
            addingSubtotal();
        }, 50);
    });

    Livewire.on('restartFocus', () => {
        setTimeout(() => {
            restartFocus();
        }, 50);
    });
});
