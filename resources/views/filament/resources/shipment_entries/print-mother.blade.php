<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Guía #{{ $shipment->mother }}</title>
    <link rel="stylesheet" href="{{ mix('css/print.css') }}">
</head>

<body onload="window.print();">
    {{-- tu diseño de etiqueta / ticket aquí --}}
    <p>Hola soy una impresion de guias madres</p>

    <script>
        /* Cierra la ventana cuando el cuadro de impresión se cierra */
        window.onafterprint = () => window
            .close(); // moderno: Chrome, Edge, Firefox, Safari :contentReference[oaicite:0]{index=0}

        /* Fallback para navegadores que lanzan afterprint antes de tiempo */
        window.matchMedia?.('print')
            ?.addEventListener('change', e => {
                if (!e.matches) window.close();
            });

        /* Seguridad extra: por si todo falla, cierra en 1 s */
        setTimeout(() => window.close(), 1000);
    </script>
</body>

</html>
