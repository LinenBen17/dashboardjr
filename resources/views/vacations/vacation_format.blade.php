<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/css/vacation-format.css') }}">
    <title>Formato Vacaciones</title>
    <script>
        window.print();
    </script>
</head>

<body>
    <div class="header">
        <div class="logo">
            <img src="{{ asset('assets/images/jrico.png') }}" alt="logo">
        </div>
        <div class="titleContainer">
            <div class="title">
                <h1>&nbsp;&nbsp; SOLICITUD Y AUTORIZACIÓN DE &nbsp;&nbsp;<br>VACACIONES</h1>
            </div>
        </div>
    </div>
    <table border="1" style="border-collapse: collapse; width: 100%; text-align: left;">
        <tr>
            <th>Código Empleado</th>
            <th>Fecha ingreso</th>
            <th colspan="3">Nombres y Apellidos</th>
        </tr>
        <tr>
            <td>{{ $id }}</td>
            <td>{{ $employee_entry_date }}</td>
            <td colspan="3">{{ $employee_name }}</td>
        </tr>
        <tr>
            <th colspan="2">Cargo</th>
            <th>Del Periodo</th>
            <th>Rango a Gozar</th>
            <th>Días a disfrutar</th>
        </tr>
        <tr>
            <td colspan="2">{{ $charge }}</td>
            <td>{{ $year }}</td>
            <td>Del: {{ $start_date }} &nbsp;&nbsp;&nbsp; Al: {{ $end_date }}</td>
            <td>15</td>
        </tr>
    </table>
    <br>
    <div class="comments">
        <p>&nbsp;<b>Observaciones:</b> {{ $comments }}</p>
    </div>
    <div class="signature">
        <div class="employeeSignature">
            <div class="">
                <p>_____________________________________________</p>
                <p>Firma empleado</p>
            </div>
        </div>
        <div class="rhSignature">
            <div>
                <p>_____________________________________________</p>
                <p>Vo. Bo. Recursos Humanos</p>
            </div>
        </div>
    </div>
</body>

</html>
