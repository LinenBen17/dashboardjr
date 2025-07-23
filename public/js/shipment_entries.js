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
        const datos = e.detail[0];

        console.log('Datos a imprimir:', datos);

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
                alert('Guía enviada a la impresora.');
            })
            .catch(error => {
                console.error('Error al conectar con el servidor local:', error);
                alert('No se pudo imprimir. Asegúrate de tener el servidor local corriendo.');
            });
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