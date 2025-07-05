//focus guia madre cuando se carga la pagina
$(window).on('load', function () {
    $("input").on("keypress", function () {
        $input = $(this);
        setTimeout(function () {
            $input.val($input.val().toUpperCase());
        }, 50);
    })

    $("#codigo_remitente").focus();

    $("#forma_pago").blur(function () {
        $("#codigo_remitente").focus();
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
    document.addEventListener('print-guide', function (e) {
        console.log(e.detail);
        var ventana = window.open(' ', 'popimpr');
        ventana.document.write('<html><head><title>' + document.title + '</title>');
        ventana.document.write('<link rel="stylesheet" href="' + e.detail[0]['style'] + '">'); //Aquí agregué la hoja de estilos
        ventana.document.write('</head><body >');
        //ventana.document.write('<div class="canvas"></div>')
        ventana.document.write(
            `
                <div class="guia">
                    <div class="formapago">
                        <p class="">${e.detail[0]['payment_method']}</p>
                    </div>
                    <div class="datos">
                        <div class="datosRemitente">
                            <p class="remitente">${e.detail[0]['sender_name']}</p><br>
                            <p class="dirRemitente" id="dirRemitente">${e.detail[0]['sender_address']}</p>
                            <p class="telRemitente">${e.detail[0]['sender_phone']}</p>
                            <p class="origen">${e.detail[0]['prefix_origin']}</p>
                        </div>
                        <div class="datosDestinatario">
                            <p class="destinatario">${e.detail[0]['receiver_name']}</p><br>
                            <p class="dirDestinatario" id="dirDestinatario">${e.detail[0]['receiver_address']}</p>
                            <p class="telDestinatario">${e.detail[0]['receiver_phone']}</p>
                            <p class="destino">${e.detail[0]['prefix_destination']}</p>
                        </div>
                    </div><br><br>
                    <div class="codigoCliente">
                        <p class="descripcionProducto">${e.detail[0]['product_description']}</p>
                        <p class="tarifa">${e.detail[0]['total']}</p>
                        <p class="codigo">${((e.detail[0]['sender_code'] ?? 0) + ' - ' + (e.detail[0]['sender_code'] ?? 0))}</p>
                    </div><br>
                    <div class="inferiorGuia">
                        <p class="piezas">${e.detail[0]['pieces']}</p>
                        <p class="usuario"></p>
                        <div class="fecha">
                            <p>${e.detail[0]['dia']}</p>
                            <p>${e.detail[0]['mes']}</p>
                            <p>${e.detail[0]['anio']}</p>
                        </div>
                    </div>
                </div>
                <script>
                    const text = document.getElementById('dirDestinatario').textContent;
                    const truncated = text.length > 75 ? text.slice(0, 75) + '...' : text;

                    const textRemitente = document.getElementById('dirRemitente').textContent;
                    const truncatedRemitente = textRemitente.length > 75 ? textRemitente.slice(0, 75) + '...' : textRemitente;

                    document.getElementById('dirDestinatario').textContent = truncated;
                    document.getElementById('dirRemitente').textContent = truncatedRemitente;
                </script>
            `
        );
        ventana.document.write('</body></html>');
        ventana.document.close();
        setTimeout(() => {
            ventana.print();
            ventana.close();
        }, 1500)
        $("#codigo_remitente").focus();
    });
});
function addGuide(event) {
    const input = document.getElementById('child');

    if (event.key === 'Enter') {
        event.preventDefault();

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
}
window.addGuide = addGuide;