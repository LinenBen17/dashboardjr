//focus guia madre cuando se carga la pagina
$(window).on('load', function () {
    $("input").on("keypress", function () {
        $input = $(this);
        setTimeout(function () {
            $input.val($input.val().toUpperCase());
        }, 50);
    })

    restartFocus();

    $("#forma_pago").val(1); // Establecer el valor por defecto de forma_pago a 1
    $("#forma_pago").blur(function () {
        $("#sender_total").focus();
        addingSubtotal();
    });

    $("#prefix_origen").focus(function () {
        $("#prefix_destino").focus();
    });

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

    $(document).on('keydown', '.addProduct', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();

            $('#forma_pago').focus();
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
    })

    document.addEventListener('print-guide', function (e) {
        const datos = e.detail[0];

        console.log('Datos a imprimir:', datos);

        // Limpiar campos de destino
        const select = document.getElementById('town_id');
        select.innerHTML = '<option value="">Seleccione una opción</option>';
        document.getElementById('route_destino').value = '';

        fetch('http://localhost:9000/print', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datos)
        })
            .then(response => response.json())
            .then(data => {
                console.log('Respuesta de impresión:', data);
            })
            .catch(error => {
                console.error('Error al conectar con el servidor local:', error);
            });
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
});


function restartFocus() {
    $("#codigo_remitente").focus();
}

$(document).on('blur', '#codigo_remitente', function () {
    if ($(this).val() != '') {
        const codigo = $(this).val();

        fetch(`/shipment-entries/buscar-cliente?code=${codigo}`)
            .then(res => res.json())
            .then(data => {
                if (Object.keys(data).length !== 0) {
                    $('#sender_name').val(data.name);
                    $('#sender_address').val(data.address);
                    $('#sender_phone').val(data.phone);

                    $('#codigo_destinatario').focus();
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
                $('#receiver_name').val(data.name);
                $('#receiver_address').val(data.address);
                $('#receiver_phone').val(data.phone);
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

$(document).on('blur', '.product_index-input', function () {
    const codigoIngresado = $(this).val();
    const indexInput = this.dataset.index;

    fetch(`/shipment-entries/buscar-producto?code=${codigoIngresado}`)
        .then(res => res.json())
        .then(data => {
            $('#product_description_' + indexInput).val(data.description);
            $('#unit_price_' + indexInput).val(data.price);

            // También actualizar en Livewire (clave)
            const component = document.querySelector('[wire\\:id]');
            const componentId = component?.getAttribute('wire:id');

            Livewire.find(componentId).set(`productos.${indexInput}.product_id`, $(this).val());
            Livewire.find(componentId).set(`productos.${indexInput}.pieces`, $('#pieces_' + indexInput).val());
            Livewire.find(componentId).set(`productos.${indexInput}.product_description`, data.description);
            Livewire.find(componentId).set(`productos.${indexInput}.unit_price`, data.price);
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

function addingSubtotal() {
    const codigo_remitente = document.getElementById('codigo_remitente');
    const codigo_destinatario = document.getElementById('codigo_destinatario');

    const sender_total = document.getElementById('sender_total');
    const receiver_total = document.getElementById('receiver_total');
    const totalMount = document.getElementById('total');

    const subtotals = document.querySelectorAll('.subtotal_index-input');

    const forma_pago = document.getElementById('forma_pago').value;

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