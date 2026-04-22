/*************************************************
 * 1. VARIABLES GLOBALES
 *************************************************/
let tablaCustomers;
let tomClienteRemitente = null;
let tomClienteDestinatario = null;


/*************************************************
 * 2. HELPERS GENERALES
 *************************************************/
function getLivewireComponent() {
    const component = document.querySelector('[wire\\:id]');
    return component ? Livewire.find(component.getAttribute('wire:id')) : null;
}

function restartFocus() {
    $("#guia_madre").val('').focus();
}


/*************************************************
 * 3. TOM SELECT (REUTILIZABLE)
 *************************************************/
function createTomSelect({
    selector,
    url,
    valueField = "code",
    labelField = "code",
    searchField = ["code", "name"],
    livewireMethod = null,
    extraOnChange = null
}) {
    return new TomSelect(selector, {
        valueField,
        labelField,
        searchField,
        maxItems: 1,
        maxOptions: 5,

        load(query, callback) {
            if (!query.length) return callback();

            fetch(`${url}?code=${query}`)
                .then(res => res.json())
                .then(data => callback(data))
                .catch(() => callback());
        },
        render: {
            option(item) {
                return `
                    <div>
                        <strong>${item.code}</strong> - ${item.name}<br>
                        <small>${item.address ?? ''}</small>
                    </div>
                `;
            }
        },
        onChange(value) {
            const component = getLivewireComponent();

            if (livewireMethod && component) {
                component.call(livewireMethod, value);
            }

            if (extraOnChange) extraOnChange(value);



        },
        onBlur: function () {

        }
    });
}

function initTomSelects() {
    if (!tomClienteRemitente) {
        tomClienteRemitente = createTomSelect({
            selector: "#codigo_remitente",
            url: "/shipment-entries/listar-clientes",
            livewireMethod: "setClienteRemitente",
            extraOnChange: (value) => {
                fetch(`/shipment-entries/buscar-cliente?code=${value}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.special_rates.length > 0) {
                            getLivewireComponent()?.call('notificationJs', 'Tarifa Especial', 'Este cliente posee Tarifa Especial. Presione F1 en el campo "Código" para visualizarlas.', 'info');
                        }
                    });
            }
        });
    }

    if (!tomClienteDestinatario) {
        tomClienteDestinatario = createTomSelect({
            selector: "#codigo_destinatario",
            url: "/shipment-entries/listar-clientes",
            livewireMethod: "setClienteDestinatario",
            extraOnChange: (value) => {
                fetch(`/shipment-entries/buscar-cliente?code=${value}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.special_rates.length > 0) {
                            getLivewireComponent()?.call('notificationJs', 'Tarifa Especial', 'Este cliente posee Tarifa Especial. Presione F1 en el campo "Código" para visualizarlas.', 'info');
                        }
                    });
            }
        });
    }
}


/*************************************************
 * 4. LÓGICA DE NEGOCIO
 *************************************************/
function addingSubtotal() {
    const component = getLivewireComponent();
    const productos = component.get('productos');

    const sender_total = document.getElementById('sender_total');
    const receiver_total = document.getElementById('receiver_total');
    const totalMount = document.getElementById('total');

    const forma_pago = document.getElementById('forma_pago').value;
    const codigo_remitente = document.getElementById('codigo_remitente');
    const codigo_destinatario = document.getElementById('codigo_destinatario');

    sender_total.value = '0.00';
    receiver_total.value = '0.00';

    let total = 0;

    productos.forEach(p => {
        const val = parseFloat(p.subtotal);
        if (!isNaN(val)) total += val;
    });

    if (forma_pago == 1) receiver_total.value = total.toFixed(2);
    else if (forma_pago == 2) sender_total.value = total.toFixed(2);
    else if (forma_pago == 3 || forma_pago == 4) {
        if (codigo_remitente.value) sender_total.value = total.toFixed(2);
        else if (codigo_destinatario.value) receiver_total.value = total.toFixed(2);
    }
    else if (forma_pago == 5) receiver_total.value = total.toFixed(2);

    totalMount.value = (parseFloat(sender_total.value) + parseFloat(receiver_total.value)).toFixed(2);
}

function calcularCE() {
    console.log("dentro de funcion CE");

    let producto = parseFloat(document.getElementById('pce_amount').value) || 0;
    let piezas = parseInt(document.getElementById('pce_pieces').value) || 1;

    if (piezas <= 0) piezas = 1;

    let comision = producto * 0.05 * piezas || 0;
    let envio = parseFloat(document.getElementById('pce_shipment_price').value) * piezas || 0;

    document.getElementById('ce_results').innerHTML = `
        <div class="dark:text-gray-100">
            Comisión: Q${comision.toFixed(2)}<br>
            Envío: Q${envio.toFixed(2)}
        </div>
    `;
}

$(document).on('blur', '#pce_amount, #pce_pieces, #pce_shipment_price', calcularCE);

// eventos
document.addEventListener('input', function (e) {
    if (e.target.closest('.pceModal')) {
        calcularCE();
    }
});

// recalcularCE cuando se abre el modal
document.addEventListener('DOMContentLoaded', calcularCE);


/*************************************************
 * 5. EVENTOS PRINCIPALES (WINDOW LOAD)
 *************************************************/
$(window).on('load', function () {
    $("input").on("keypress", function () {
        $input = $(this);
        setTimeout(function () {
            $input.val($input.val().toUpperCase());
        }, 50);
    })

    restartFocus();

    $("#forma_pago").val(1); // Establecer el valor por defecto de forma_pago a 1

    $(document).on('keydown', '#child', function (e) {
        const input = document.getElementById('child');

        // Encuentra el componente Livewire
        const component = document.querySelector('[wire\\:id]');
        const componentId = component?.getAttribute('wire:id');

        if (e.key === 'Enter') {
            console.log("enter child")
            e.preventDefault();

            const valor = input.value.trim();

            input.value = ''; // Limpiar

            if (!valor) return;

            if (componentId) {
                if (valor.startsWith('H')) {
                    Livewire.find(componentId).call('addChildGuide', valor);
                }
                else {
                    let numero = valor.toString().padStart(10, '0');
                    let guia = 'H' + numero;

                    Livewire.find(componentId).call('addChildGuide', guia);
                }
            } else {
                console.error("No se encontró el componente Livewire.");
            }
        }

        if (e.key === 'F1') {
            e.preventDefault();

            Livewire.find(componentId).call('confirmChilds');
        }
    })

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

/*************************************************
 * 6. NAVEGACIÓN CON ENTER
 *************************************************/
$(document).on('keydown', 'input, select, textarea, button', function (e) {
    if (e.key !== 'Enter') return;

    e.preventDefault();

    const focusables = $(
        'input:not(#child):not([type=hidden]):enabled:visible,' +
        'select:enabled:visible,' +
        'textarea:enabled:visible,' +
        'button:enabled:visible,' +
        'input[type=button]:enabled:visible,' +
        'input[type=submit]:enabled:visible,' +
        '[tabindex]:not([tabindex="-1"]):enabled:visible'
    );

    let idx = focusables.index(this);

    if (idx > -1 && idx < focusables.length - 1) {
        let next = focusables.eq(idx + 1);

        next.focus();

        // 🔥 esto hace que se comporte como TAB
        setTimeout(() => {
            if (next.is('input, textarea')) {
                next.select();
            }
        }, 0);
    }
});

/*************************************************
 * 7. EVENTOS DE INPUTS (AGRUPADOS)
 *************************************************/

//town_id (ruta)
$(document).on('change', '#town_id', function () {
    const town_id = $(this).val();
    console.log("holachangin town");


    fetch(`/shipment-entries/buscar-ruta?town_id=${town_id}`)
        .then(res => res.json())
        .then(data => {
            console.log(data);
            const input = document.getElementById('route_destino');

            input.value = data;



        })
        .catch(err => {
            console.error('Error al buscar ruta:', err);
        });
})

window.addEventListener('toggle-changed', (event) => {
    // También actualizar en Livewire (clave)
    const component = document.querySelector('[wire\\:id]');
    const componentId = component?.getAttribute('wire:id');

    Livewire.find(componentId).set(`link_child_later`, event.detail);
});

// CLIENTE (REMITENTE)
$(document).on('blur', '#codigo_remitente', function () {
    const codigo = $(this).val();
    if (!codigo) return;

    fetch(`/shipment-entries/buscar-cliente?code=${codigo}`)
        .then(res => res.json())
        .then(data => {
            console.log(codigo);

            if (!data.customer_data) return;

            $('#sender_name').val(data.customer_data.name);
            $('#sender_address').val(data.customer_data.address);
            $('#sender_phone').val(data.customer_data.phone);
            $('#codigo_destinatario').focus();
        });
});

// CLIENTE (DESTINATARIO)
$(document).on('blur', '#codigo_destinatario', function () {
    const codigo = $(this).val();
    if (!codigo) return;

    fetch(`/shipment-entries/buscar-cliente?code=${codigo}`)
        .then(res => res.json())
        .then(data => {
            console.log(data);

            if (!data.customer_data) return;

            $('#receiver_name').val(data.customer_data.name);
            $('#receiver_address').val(data.customer_data.address);
            $('#receiver_phone').val(data.customer_data.phone);
        });
});

$(document).on('focus', '#codigo_remitente, #codigo_destinatario', function () {
    const ts = this.tomselect;
    if (ts) {
        setTimeout(() => ts.focus(), 0);
    }
});

// INPUT PRODUCT_ID
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

    const lw = Livewire.find(componentId);

    // Setear TODO
    lw.set(`no_guide_user`, $('#guia_madre').val());
    lw.set(`manifest_code`, $('#manifest_code').val());
    lw.set(`date_guide`, $('#date_guide').val());
    lw.set(`payment_method_id`, $('#forma_pago').val());
    lw.set(`sender_total`, $('#sender_total').val());
    lw.set(`receiver_total`, $('#receiver_total').val());
    lw.set(`total`, $('#total').val());

    lw.set(`sender_code`, $('#codigo_remitente').val());
    lw.set(`sender_name`, $('#sender_name').val());
    lw.set(`sender_address`, $('#sender_address').val());
    lw.set(`sender_phone`, $('#sender_phone').val());

    lw.set(`receiver_code`, $('#codigo_destinatario').val());
    lw.set(`receiver_name`, $('#receiver_name').val());
    lw.set(`receiver_address`, $('#receiver_address').val());
    lw.set(`receiver_phone`, $('#receiver_phone').val());

    lw.set(`prefix_destination`, $('#prefix_destino').val());
    lw.set(`town_id`, $('#town_id').val());

    // 🔥 IMPORTANTE: llamar después
    setTimeout(() => {
        lw.call('confirmSave');
    }, 50);
});

$(document).on('keydown', '#product_id_input', async function (e) {
    if (e.keyCode == 112) {
        e.preventDefault();

        let codigos = {
            codigo_remitente: $('#codigo_remitente').val(),
            codigo_destinatario: $('#codigo_destinatario').val()
        };

        const component = getLivewireComponent();

        let promises = Object.values(codigos)
            .filter(codigo => codigo)
            .map(async codigo => {
                try {
                    let res = await fetch(`/shipment-entries/buscar-cliente?code=${codigo}`);
                    return await res.json();
                } catch (e) {
                    return null;
                }
            });

        let customer_data_prices = (await Promise.all(promises)).filter(x => x);

        component?.set('customer_data_prices', customer_data_prices);
        component?.call('openSpecialRatesModal');
    }
});

/* $(document).on('keydown', '#guia_madre', function () {
    const guide = $(this).val().trim();

    if (!guide) return;

    fetch(`/shipment-entries/buscar-guia?guide=${guide}`)
        .then(res => res.json())
        .then(data => {
            if (data.guide_data) {
                $('#date_guide').val(data.guide_data.date_guide);
                $('#forma_pago').val(data.guide_data.payment_method_id);
                $('#manifest_code').val(data.guide_data.manifest_code);

                $('#codigo_remitente').val(data.guide_data.sender_code);
                $('#sender_name').val(data.guide_data.sender_name);
                $('#sender_address').val(data.guide_data.sender_address);
                $('#sender_phone').val(data.guide_data.sender_phone);

                $('#codigo_destinatario').val(data.guide_data.receiver_code);
                $('#receiver_name').val(data.guide_data.receiver_name);
                $('#receiver_address').val(data.guide_data.receiver_address);
                $('#receiver_phone').val(data.guide_data.receiver_phone);

                $('#prefix_origin').val(data.guide_data.prefix_origin);
                $('#prefix_destino').val(data.guide_data.prefix_destination);

                $('#town_id').val(data.guide_data.town_id).trigger('change');

                getLivewireComponent()?.call('buscarGuiaMadre', guide);
            }
        })
        .catch(err => {
            console.error('Error al buscar guía:', err);
        });
});
 */
//FUNCIONALIDADES CON F3 Y F4
$(document).on('keydown', function (e) {
    if (e.key === 'F3') {
        console.log("HOASIOSA");

        e.preventDefault();
        // obtener datos remitente ultima guía
        fetch('/shipment-entries/obtener-ultima-guia')
            .then(res => res.json())
            .then(data => {
                console.log(data);

                if (data) {
                    console.log("lololo");

                    $('#codigo_remitente').val(data.sender_code);
                    $('#sender_name').val(data.sender_name);
                    $('#sender_address').val(data.sender_address);
                    $('#sender_phone').val(data.sender_phone);

                    //Select
                    $('#forma_pago').val(data.payment_method_id);
                }
            })
            .catch(err => {
                console.error('Error al obtener última guía:', err);
            });
    } else if (e.key === 'F4') {
        e.preventDefault();
        //datos destinatario ultima guía
        fetch('/shipment-entries/obtener-ultima-guia')
            .then(res => res.json())
            .then(data => {
                if (data) {
                    $('#codigo_destinatario').val(data.receiver_code);
                    $('#receiver_name').val(data.receiver_name);
                    $('#receiver_address').val(data.receiver_address);
                    $('#receiver_phone').val(data.receiver_phone);

                    //Select
                    $('#forma_pago').val(data.payment_method_id);
                }
            })
            .catch(err => {
                console.error('Error al obtener última guía:', err);
            });
    }
});

$(document).on('blur', '#product_id_input', function () {
    if ($("#product_id_input").val() == "CE") {
        const component = getLivewireComponent();

        component?.call('openPCEModal');
    }
});

// Obtener información de guía en Consulta de Guías
$(document).on('click', '#searchGuideBtn', function () {
    const guide = document.getElementById('search_guide').value.trim();

    if (!guide) return;

    fetch(`/shipment-entries/buscar-guia?guide=${guide}`)
        .then(res => res.json())
        .then(data => {
            const tbody = document.getElementById('tracking_table_body');
            tbody.innerHTML = ''; // 🔥 limpiar una sola vez

            if (data.guide_data) {
                const guide = data.guide_data;
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
            if (data.guide_cod) {
                const cod = data.guide_cod;

                $('#cod_section').removeClass('hidden');

                $('#pce_no_consult').text(cod.no_pce ?? '-');
                $('#pce_amount_consult').text(cod.amount ?? '0.00');
                $('#pce_pieces_consult').text(cod.pieces ?? '1');
                $('#pce_shipment_price_consult').text(cod.shipment_price ?? '0.00');

                $('#pce_shipment_paid_by_consult').text(
                    cod.shipment_paid_by === 'receiver' ? 'Destinatario' : 'Remitente'
                );

                $('#pce_commission_consult').text(cod.commission_amount ?? '0.00');

                $('#pce_commission_paid_by_consult').text(
                    cod.commission_paid_by ? 'Destinatario' : 'Remitente'
                );

                $('#pce_total_receiver_consult').text(cod.total_receiver ?? '0.00');
                $('#pce_total_sender_consult').text(cod.total_sender ?? '0.00');
            } else {
                $('#cod_section').addClass('hidden'); // 👈 ocultar si no hay COD
            }

            if (data.guide_incomes && data.guide_incomes.length > 0) {
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

/*************************************************
 * 8. LIVEWIRE EVENTS
 *************************************************/
document.addEventListener('livewire:initialized', () => {
    Livewire.on('focus-product-input', () => {
        setTimeout(() => {
            $('#product_id_input').focus();
            $('#product_id_input').val('');
            addingSubtotal();
        }, 50);
    });

    Livewire.on('restartFocus', () => {
        setTimeout(restartFocus, 50);
    });

    // initTomSelects();
});