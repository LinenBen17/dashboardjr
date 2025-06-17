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

        /* O: */
        /* @media print { */
        /*     body { */
        /*         margin: 0; */
        /*     } */
        /*     body { */
        /*         margin-top: 0; */
        /*         margin-right: 0; */
        /*         margin-bottom: 0; */
        /*         margin-left: 0; */
        /*     } */
        /* } */
    </style>
</head>

<body class="p-4 text-[12px] font-sans">
    <!-- Encabezado principal -->
    <div class="flex justify-between items-start mb-2">
        <div>
            <img src="{{ asset('images/jrico.png') }}" alt="Logo" class="w-24 mb-1">
        </div>
        <div class="text-center flex-1">
            <h1 class="text-lg uppercase">Manifiesto de Descarga</h1>
            <p>{{ date('d/m/Y', strtotime($manifest_income['arrived_date'])) }}</p>
        </div>
        <div class="text-right text-xs">
            <p>Pag.: 1</p>
        </div>
    </div>

    @php
        function splitIntoColumns($array, $rowsPerColumn)
        {
            $columns = [];
            $chunks = array_chunk($array, $rowsPerColumn);
            foreach ($chunks as $chunk) {
                $columns[] = $chunk;
            }
            return $columns;
        }

        $rowsPerColumn = 45;
        if (count($motherGuides) > 0 || count($childGuides) > 0) {
            $motherColumns = splitIntoColumns($motherGuides, $rowsPerColumn);
            $childColumns = splitIntoColumns($childGuides, $rowsPerColumn);
        }

        if (count($reentryMotherGuides) > 0 || count($reentryChildGuides) > 0) {
            $reentryMotherColumns = splitIntoColumns($reentryMotherGuides, $rowsPerColumn);
            $reentryChildColumns = splitIntoColumns($reentryChildGuides, $rowsPerColumn);
        }
    @endphp

    <!-- Datos generales -->
    <div class="grid grid-cols-3 text-xs gap-y-1 mb-3">
        <p>Ruta ingreso: {{ $route['name'] }} </p>
        <p>Total Madres: {{ $manifest_income['total_guides'] }} </p>
        <p>Total Re-ingresos Madres: {{ isset($reentryMotherColumns) ? count($reentryMotherColumns) : 0 }}</p>
        <p>No. manifiesto: {{ $manifest_income['manifest_code'] }}</p>
        <p>Total Hijas: {{ $manifest_income['total_pieces'] - $manifest_income['total_guides'] }}</p>
        <p>Total Re-ingresos Hijas: {{ isset($reentryChildColumns) ? count($reentryChildColumns) : 0 }}</p>
        <p>Responsable: {{ $manifest_income['driver'] }}</p>
        <p>Total paquetes: {{ $manifest_income['total_pieces'] }}</p>
        <p></p>
        <p>Recibe: {{ $person_scans['name'] . ' ' . $person_scans['last_name'] }} </p>
        <p>Hora inicio: {{ $hora_inicio }} Hora fin: {{ $hora_fin }}</p>
        <p></p>
    </div>
    <hr>

    <!-- Guías Madres -->
    <div class="flex gap-8 mt-4">
        <!-- Guías Madres -->
        @isset($motherColumns)
            <div class="w-1/2">
                <h2 class="text-xs font-bold mb-1">Guías Madres</h2>
                <div class="grid grid-cols-{{ isset($motherColumns) ? count($motherColumns) : 0 }} gap-4 text-xs">
                    @foreach ($motherColumns as $column)
                        <div class="flex flex-col space-y-0.5">
                            @foreach ($column as $guide)
                                <p>{{ $guide }}</p>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        @endisset

        <!-- Guías Hijas -->
        @isset($childColumns)
            <div class="w-1/2">
                <h2 class="text-xs font-bold mb-1">Guías Hijas</h2>
                <div class="grid grid-cols-{{ isset($childColumns) ? count($childColumns) : 0 }} gap-4 text-xs">
                    @foreach ($childColumns as $column)
                        <div class="flex flex-col space-y-0.5">
                            @foreach ($column as $guide)
                                <p>{{ $guide }}</p>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        @endisset

        {{-- Guías Madres Re-ingresadas --}}
        @isset($reentryMotherColumns)
            <div class="w-1/2">
                <h2 class="text-xs font-bold mb-1">Guías Madres Re-ingresadas</h2>
                <div
                    class="grid grid-cols-{{ isset($reentryMotherColumns) ? count($reentryMotherColumns) : 0 }} gap-4 text-xs">
                    @foreach ($reentryMotherColumns as $column)
                        <div class="flex flex-col space-y-0.5">
                            @foreach ($column as $guide)
                                <p>{{ $guide }}</p>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        @endisset

        {{-- Guías Hijas Re-ingresadas --}}
        @isset($reentryChildColumns)
            <div class="w-1/2">
                <h2 class="text-xs font-bold mb-1">Guías Hijas Re-ingresadas</h2>
                <div
                    class="grid grid-cols-{{ isset($reentryChildColumns) ? count($reentryChildColumns) : 0 }} gap-4 text-xs">
                    @foreach ($reentryChildColumns as $column)
                        <div class="flex flex-col space-y-0.5">
                            @foreach ($column as $guide)
                                <p>{{ $guide }}</p>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        @endisset
</body>

</html>
