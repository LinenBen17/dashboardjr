<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <link rel="stylesheet" href="{{ asset('assets/css/payroll-report.css') }}">
    <title>PLANILLA DE {{ $payrollData['benefits'][$benefit] }}</title>
</head>
<body>
    <div class="page">
        <div class="head">
            <div class="headtop">
                <div class="title">
                    <img src="{{asset('assets/images/jrico.png')}}">
                    <h1>PLANILLA DE {{ $payrollData['benefits'][$benefit] }}</h1>
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
                    </tr>
                </thead>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Cta. Bancaria</th>
                        <th>Nombre Empleado</th>
                        <th>Días trabajados</th>
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
                                            <td> {{ date_diff(date_create($row['fechaIngreso']), date_create($to))->format('%a') > 365 ? 365 : date_diff(date_create($row['fechaIngreso']), date_create($to))->format('%a')}} </td>
                                            <td class="liquido"> {{ date_diff(date_create($row['fechaIngreso']), date_create($to))->format('%a') > 365 ? number_format($row['sueldo'] * 2 , 2) : number_format($row['sueldo'] * 2 * date_diff(date_create($row['fechaIngreso']), date_create($to))->format('%a') / 365, 2) }}</td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                    <tr class="totals">
                        <td colspan="4">TOTAL</td>
                        <td class="totalLiquido"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        console.log(@json($payrollData));
        var tdLiquido = document.querySelectorAll('.liquido');
        var liquido = 0;
        for (let i = 0; i < tdLiquido.length; i++) {
            liquido = liquido + parseFloat(tdLiquido[i].textContent.replace(/,/g, ''));
        }
        
        document.querySelector('.totalLiquido').textContent = liquido.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    </script>
</body>
</html>