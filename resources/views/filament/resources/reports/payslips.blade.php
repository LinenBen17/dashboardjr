<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <link rel="stylesheet" href="{{ asset('assets/css/payslips-report.css') }}">
    <title>Reporte Boletas de Pago</title>
    <script>
        window.print();
    </script>
</head>
<body>
    @foreach ($payrollData['data'] as $item)
        @for ($i = 1; $i <= 2; $i++)
            <div class="container">
                <div class="title">
                    <h2 style="text-align: center;">RECIBO DE PAGO</h2>
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
                            <td>{{ $item['ctaBancaria'] }}</td>
                            <td>{{ $item['fechaIngreso'] }}</td>
                            <td>{{ $item['agencia'] }}</td>
                            <td>{{ $item['cargo'] }}</td>
                        </tr>
                    </tbody>
                </table>
        
                <table class="datosMonetarios">
                    <thead>
                        <tr>
                            <th colspan="2" class="section-title">REMUNERACIONES</th>
                            <th colspan="2" class="section-title">RETENCIONES / DESCUENTOS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Sueldo Ordinario</td>
                            <td>{{ $item['sueldo'] }}</td>
                            <td>IGSS</td>
                            <td>{{ $item['igss'] }}</td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td></td>
                            <td>Préstamos</td>
                            <td>{{ $item['installments'] }}</td>
                        </tr>
                        <tr>
                            <td>Bonificación Decreto Ley 37-2001</td>
                            <td>{{ $item['bonoLey'] }}</td>
                            <td>Adelantos</td>
                            <td>{{ $item['anticipos'] }}</td>
                        </tr>
                        <tr>
                            <td>Bonificación Incentivo</td>
                            <td>{{ $item['bonoIncentivo'] }}</td>
                            <td>Ausencias</td>
                            <td>{{ $item['ausencias'] }}</td>
                        </tr>
                        <tr>
                            <td>Otros Ingresos</td>
                            <td>{{ $item['bonoMonto'] }}</td>
                            <td>Otros</td>
                            <td>{{ $item['otros'] }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: center;"><b>Total Remuneraciones</b></td>
                            <td><b>{{ $item['totalDevengado'] }}</b></td>
                            <td style="text-align: center;"><b>Total Descuentos</b></td>
                            <td><b>{{ $item['totalDescuento'] }}</b></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="no-border"></td>
                            <td style="text-align: center;"><b>Neto a Pagar</b></td>
                            <td><b>{{ $item['liquido'] }}</b></td>
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