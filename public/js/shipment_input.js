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

    totalMount.value = (parseFloat(sender_total.value) + parseFloat(receiver_total.value)).toFixed(2);
}


/*************************************************
 * 5. EVENTOS PRINCIPALES (WINDOW LOAD)
 *************************************************/
$(window).on('load', function () {

    // Uppercase automático
    $("input").on("keypress", function () {
        let input = $(this);
        setTimeout(() => input.val(input.val().toUpperCase()), 50);
    });

    restartFocus();
    $("#forma_pago").val(1);

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
        focusables.eq(idx + 1).focus();
    }
});


/*************************************************
 * 7. EVENTOS DE INPUTS (AGRUPADOS)
 *************************************************/

// CHILD
$(document).on('keydown', '#child', function (e) {
    if (e.key !== 'Enter') return;

    e.preventDefault();

    const valor = this.value.trim();
    this.value = '';

    if (!valor || !valor.startsWith('H')) return;

    getLivewireComponent()?.call('addChildGuide', valor);
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

/*************************************************
 * 8. LIVEWIRE EVENTS
 *************************************************/
document.addEventListener('livewire:initialized', () => {
    Livewire.on('focus-product-input', () => {
        setTimeout(() => {
            $('#product_id_input').focus();
            addingSubtotal();
        }, 50);
    });

    Livewire.on('restartFocus', () => {
        setTimeout(restartFocus, 50);
    });

    initTomSelects();
});