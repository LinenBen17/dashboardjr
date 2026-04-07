<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Manifiesto de Entrada</title>
    <style>
        @page {
            size: A4;
            margin: 0;
            /* O usa margin-top, margin-right, margin-bottom, margin-left individuales */
        }
    </style>
    <script>
        window.onload = function() {
            window.print();
            setTimeout(() => window.close(), 1000);
        }
    </script>

</head>

<body class="p-4 text-[12px] font-sans">
    <!-- Encabezado principal -->
    <div class="flex justify-between items-start mb-2">
        <div>
            <img src="{{ asset('images/jrico.png') }}" alt="Logo" class="w-24 mb-1">
        </div>
        <div class="text-center flex-1">
            <h1 class="text-lg uppercase">Manifiesto de Entrega</h1>
        </div>
        <div class="text-center">
            <h1 class="text-lg uppercase">{{ $manifest_code }}</h1>
        </div>
    </div>

    @php
        function splitIntoColumns($collection, $rowsPerColumn)
        {
            return $collection->chunk($rowsPerColumn);
        }

        $rowsPerColumn = 45;
        if ($motherGuides->count() > 0 || $childGuides->count() > 0) {
            $motherColumns = splitIntoColumns($motherGuides, $rowsPerColumn);
        }
    @endphp

    <!-- Datos generales -->
    <div class="grid grid-cols-3 text-xs gap-y-1 mb-3">
        <p>Agencia Genera: {{ $agency_origin_name }} </p>
        <p>Piloto: {{ $driver_name }}</p>
        <p>Total Guías Por Cobrar: {{ 'Q.' . number_format($totalPorCobrar, 2) }}</p>
        <p>Agencia Destino: {{ $agency_destination_name }} </p>
        <p>Placa: {{ $plates }}</p>
        <p>Total Guías Prepago: {{ 'Q.' . number_format($totalPrepago, 2) }}</p>
        <p>Fecha: {{ $date }}</p>
        <p></p>
        <p>Total Piezas: {{ $totalPiezas }} </p>
        <p>Manifiesto: {{ $manifest_code }}</p>
        <p></p>
        <p>Total Guías: {{ $totalGuias }}</p>
    </div>
    <hr>

    <!-- Guías Madres -->
    @isset($motherColumns)
        <div class="w-full flex gap-x-4">
            @foreach ($motherColumns as $column)
                <div class="w-1/2">
                    <!-- Encabezados -->
                    <div class="grid grid-cols-4 font-bold mb-1 text-xs gap-x-2">
                        <span>No. Guía</span>
                        <span>Piezas</span>
                        <span>Destinatario</span>
                        <span>xCobrar</span>
                    </div>

                    <!-- Filas -->
                    <div class="space-y-0.5 text-xs">
                        @foreach ($column as $guide)
                            <div class="grid grid-cols-4 gap-x-2">
                                <span>{{ \Illuminate\Support\Str::limit($guide->mother, 8) }}</span>
                                <span>{{ $guide->pieces }}</span>
                                <span>{{ \Illuminate\Support\Str::limit($guide->receiver_name, 15) }}</span>
                                <span>
                                    @if (strtoupper($guide->payment_method_name) == 'POR COBRAR')
                                        {{ $guide->total > 0 ? 'Q.' . number_format($guide->total, 2) : '-' }}
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endisset
</body>

</html>
