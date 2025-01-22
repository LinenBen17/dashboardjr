<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('assets/css/loan-format.css') }}">
    <title>Solicitud Préstamo</title>
</head>

<body>
    <div class="header">
        <div class="logo">
            <img src="{{ asset('assets/images/jrico.png') }}" alt="logo">
            <p>Transportes JR<br>
                2 Avenida, 2-43, Zona 3, Guatemala</p>
        </div>
        <div class="titleContainer">
            <div class="title">
                <h1>&nbsp;&nbsp; SOLICITUD DE ANTICIPO DE SALARIO &nbsp;&nbsp;<br>EN CALIDAD DE PRESTAMOS</h1>
            </div>
        </div>
    </div>
    <div class="body">
        <div class="info">
            <p class="no_loan">No. de Prestamo: &nbsp;&nbsp; {{ $id }}</p>
            <p class="date">Guatemala, {{ $created_at }}</p>
        </div><br>
        <div class="text">
            Yo, <b>{{ $employee_name }}</b>, con Documento de Identificación Personal (DPI) <b>{{ $employee_dpi }}</b>,
            me permito solicitar a la empresa <b>Transportes JR</b>, un anticipo de salario en calidad de préstamo
            por la cantidad de <b>Q{{ $amount_loan }}</b>, el cual me comprometo a reembolzar a partir del próximo
            pago de salario, quedando detallado de la siguiente manera: <br><br>
            <b>1. Monto del Préstamo:</b> Q{{ $amount_loan }} <br>
            <b>2. Plazo:</b> {{ $no_share / 2 }} meses<br>
            <b>3. Cuota en cada Pago:</b> Q{{ $amount_share }} quincenales<br>
            <b>4. Motivo del Préstamo:</b> {{ $comments }}<br><br>

            Autorizo a dicha empresa para que dichos pagos sean descontados de mi salario en la forma y plazo indicado
            anteriormente, aceptando desde ya como buenas y validas las deducciones que se efectúen en mi salario.
            <br><br>

            Sin otro particular me suscribo.<br><br>

            Atentamente,<br><br>

            <div class="signature">
                <p>Nombre:_____________________________________________________</p><br>
                <p>Firma: ______________________________________________________</p><br>
                <p>DPI:________________________________________________________</p>
            </div>
        </div>
</body>

</html>
