<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/css/payroll-report.css') }}">
    <script>
        //window.print();
        console.log({{ Js::from($payrollData) }});
    </script>
    <title>Reporte Planilla</title>
</head>
<body>
    <div class="page">
        <div class="head">
            <div class="headtop">
                <div class="title">
                    <img src="{{asset('assets/images/jrico.png')}}">
                    <h1>PLANILLA DE SUELDOS</h1>
                </div>
            </div>
            <hr>
        </div>
        <div class="payroll">
            <table>
                <thead class="headerTable">
                    <tr>
                        <th colspan="14">
                            <h2 style="text-align: left;">Período planilla: Del {{ $from }} Al {{ $to }}</h2>
                        </th>
                        <th colspan="2">
                            
                        </th>
                    </tr>
                </thead>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Cta. Bancaria</th>
                        <th>Nombre Empleado</th>
                        {{-- <th>Puesto</th> --}}
                        {{-- <th>Agencia</th> --}}
                        <th>Sueldo</th>
                        <th>Bonific. Ley</th>
                        <th>Bono Incentivo</th>
                        <th>Otros Ingresos</th>
                        <th>Total Devengado</th>
                        <th>IGSS</th>
                        <th>Anticipo</th>
                        <th>Ausencias</th>
                        <th>Otros Descuentos</th>
                        <th>Préstamos</th>
                        <th>Total Descuento</th>
                        <th>Líquido a Recibir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payrollData['agencies'] as $agency)
                        @php
                            // Filtrar empleados que pertenezcan a la agencia actual
                            $agencyEmployees = array_filter($payrollData['data'], function ($row) use ($agency) {
                                return $row['agencia'] == $agency;
                            });
                        @endphp

                        @if (count($agencyEmployees) > 0) <!-- Mostrar solo agencias con empleados -->
                            <tr>
                                <td class="titleTD" colspan="100%">{{ $agency }}</td>
                            </tr>
                            @foreach ($payrollData['charges'] as $charge)
                                @php
                                    // Filtrar empleados de la agencia actual y del cargo actual
                                    $filteredData = array_filter($agencyEmployees, function ($row) use ($charge) {
                                        return $row['cargo'] == $charge;
                                    });
                                @endphp

                                @if (count($filteredData) > 0) <!-- Mostrar solo cargos con empleados -->
                                    <tr>
                                        <td class="sub titleTD" colspan="100%">{{ $charge }}</td>
                                    </tr>
                                    @foreach ($filteredData as $row)
                                        <tr>
                                            <td> {{ $row['id'] }} </td>
                                            <td> {{ $row['ctaBancaria'] }} </td>
                                            <td> {{ $row['empleado'] }} </td>
                                            {{-- <td> {{ $row['cargo'] }} </td> --}}
                                            {{-- <td> {{ $row['agencia'] }} </td> --}}
                                            <td> {{ number_format($row['sueldo'], 2) }} </td>
                                            <td> {{ number_format($row['bonoLey'], 2) }} </td>
                                            <td> {{ number_format($row['bonoIncentivo'], 2) }} </td>
                                            <td> {{ number_format($row['bonoMonto'], 2) }} </td>
                                            <td> {{ number_format($row['totalDevengado'], 2) }} </td>
                                            <td> {{ number_format($row['igss'], 2) }} </td>
                                            <td> {{ number_format($row['anticipos'], 2) }} </td>
                                            <td> {{ number_format($row['ausencias'], 2) }} </td>
                                            <td> {{ number_format($row['otros'], 2) }} </td>
                                            <td> {{ number_format($row['installments'], 2) }} </td>
                                            <td> {{ number_format($row['totalDescuento'], 2) }} </td>
                                            <td> {{ number_format($row['liquido'], 2) }} </td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                    <tr class="totals">
                        <td colspan="3">TOTAL</td>
                        @foreach ($payrollData['totals'] as $total)
                            <td>{{ number_format($total) }}</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>