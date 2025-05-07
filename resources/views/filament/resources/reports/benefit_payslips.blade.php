<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <link rel="stylesheet" href="{{ asset('assets/css/payslips-report.css') }}">
    <title>Reporte Boletas de Pago {{ $payrollData['benefits'][$benefit] }} </title>
    <script>
        // window.print();
        console.log(@json($payrollData));
        
    </script>
</head>
<body>
    @foreach ($payrollData['data'] as $item)
        @for ($i = 1; $i <= 2; $i++)
            <div class="container">
                <div class="title">
                    <h2 style="text-align: center;">RECIBO DE PAGO {{ $payrollData['benefits'][$benefit] }}</h2>
                    <p style="text-align: center;">DEL {{ $from }} AL {{ $to }}</p>
                </div>
                <table class="datosEmpresa">
                    <thead>
                        <tr>
                            <th>NIT</th>
                            <th>NOMBRE DE LA EMPRESA</th>
                            <th>ESTADO</th>
                            <th>DIRECCIÓN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>17888905</td>
                            <td>TRANSPORTES JR</td>
                            <td>FUERA DE PLANILLA</td>
                            <td>20 CALLE 2-43 ZONA 3 GUATEMALA</td>
                        </tr>
                    </tbody>
                </table>
        
                <table class="datosEmpleado">
                    <thead>
                        <tr>
                            <th>CODIGO</th>
                            <th colspan="2">EMPLEADO</th>
                            <th>DIAS</th>
                            <th>CTA. BANCARIA</th>
                            <th>FECHA INGRESO</th>
                            <th>AGENCIA</th>
                            <th>CARGO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $item['id'] }}</td>
                            <td colspan="2">{{ $item['empleado'] }}</td>
                            <td>{{ date_diff(date_create($item['fechaIngreso']), date_create($to))->format('%a') > 365 ? 365 : date_diff(date_create($item['fechaIngreso']), date_create($to))->format('%a')}}</td>
                            <td>{{ $item['ctaBancaria'] }}</td>
                            <td>{{ date_format(date_create($item['fechaIngreso']), 'd/m/Y') }}</td>
                            <td>{{ $item['agencia'] }}</td>
                            <td>{{ $item['cargo'] }}</td>
                        </tr>
                    </tbody>
                </table>
        
                <table class="datosMonetarios">
                    <thead>
                        <tr>
                            <th colspan="2" class="section-title">DESCRIPCIÓN DEL CONCEPTO</th>
                            <th class="section-title">CARGO</th>
                            <th class="section-title">ABONO</th>
                            <th class="section-title">LIQUIDO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="2" style="text-align:center;">POR PAGO DE <b>{{ $payrollData['benefits'][$benefit] }}</b></td>
                            <td style="text-align: right">{{ date_diff(date_create($item['fechaIngreso']), date_create($to))->format('%a') > 365 ? number_format($item['sueldo'] * 2 , 2) : number_format($item['sueldo'] * 2 * date_diff(date_create($item['fechaIngreso']), date_create($to))->format('%a') / 365, 2) }}</td>
                            <td>&nbsp;</td>
                            <td style="text-align: right">{{ date_diff(date_create($item['fechaIngreso']), date_create($to))->format('%a') > 365 ? number_format($item['sueldo'] * 2 , 2) : number_format($item['sueldo'] * 2 * date_diff(date_create($item['fechaIngreso']), date_create($to))->format('%a') / 365, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="2">&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="2">&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="2">&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="2">&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="no-border">&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td style="text-align: center"><b>{{ date_diff(date_create($item['fechaIngreso']), date_create($to))->format('%a') > 365 ? number_format($item['sueldo'] * 2 , 2) : number_format($item['sueldo'] * 2 * date_diff(date_create($item['fechaIngreso']), date_create($to))->format('%a') / 365, 2) }}</b></td>
                        </tr>
                    </tbody>
                </table>
        
                <table class="firma">
                    <tr>
                        <td style="text-align: center;">___________________________________<br>EMPLEADOR</td>
                        <td style="text-align: center;">___________________________________<br>TRABAJADOR</td>
                    </tr>
                </table>
            </div>
        @endfor
        <div class="break"></div>
    @endforeach
</body>
</html>