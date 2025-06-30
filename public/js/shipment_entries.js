//focus guia madre cuando se carga la pagina
$(window).on('load', function () {
    $("#codigo_remitente").focus();

    $("#forma_pago").blur(function () {
        $("#codigo_remitente").focus();
    });

    $(document).on('keydown', 'input, select, textarea, button', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();

            // Cualquier elemento focuseable que esté visible y habilitado
            const focusables = $(
                'input:not([type=hidden]):enabled:visible,' +   // <input>
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
                            <p class="remitente">${e.detail[0]['sender_name']}</p>
                            <p class="dirRemitente">${e.detail[0]['sender_address']}</p>
                            <p class="telRemitente">${e.detail[0]['sender_phone']}</p>
                            <p class="origen">${e.detail[0]['prefix_origin']}</p>
                        </div>
                        <div class="datosDestinatario">
                            <p class="destinatario">${e.detail[0]['receiver_name']}</p>
                            <p class="dirDestinatario">${e.detail[0]['receiver_address']}</p>
                            <p class="telDestinatario">${e.detail[0]['receiver_phone']}</p>
                            <p class="destino">${e.detail[0]['prefix_destination']}</p>
                        </div>
                    </div>
                    <div class="codigoCliente">
                        <p>${(e.detail[0]['sender_code'] + ' - ' + e.detail[0]['sender_code']) ?? 0}</p>
                    </div>
                </div>
            `
        );
        ventana.document.write('</body></html>');
        ventana.document.close();
        setTimeout(() => {
            ventana.print();
            ventana.close();
            document.imp.submit()
        }, 1000)
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