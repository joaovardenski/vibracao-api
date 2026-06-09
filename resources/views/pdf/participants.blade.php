<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Participantes - Vibração Jovem 2026</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #2d3748;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #1a365d;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 22px;
            color: #1a365d;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header p {
            margin: 5px 0 0 0;
            font-size: 12px;
            color: #718096;
            font-weight: bold;
        }

        .header .diocese {
            font-size: 10px;
            color: #d69e2e;
            text-transform: uppercase;
            margin-top: 2px;
            letter-spacing: 0.5px;
        }

        /* Container para os metadados ficarem organizados */
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            font-size: 10px;
            color: #4a5568;
        }

        .info-table td {
            border: none;
            padding: 0;
        }

        .info-right {
            text-align: right;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        thead {
            background: #1a365d;
        }

        th {
            color: #ffffff;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 8px 6px;
            border: 1px solid #1a365d;
        }

        td {
            font-size: 9px;
            padding: 7px 6px;
            border: 1px solid #e2e8f0;
            color: #2d3748;
        }

        /* Efeito Zebra para facilitar a leitura de listas grandes */
        tbody tr:nth-child(even) {
            background-color: #f7fafc;
        }

        .check-column {
            width: 30px;
            text-align: center;
        }

        .index-column {
            width: 30px;
            text-align: center;
            color: #718096;
        }

        .ticket-column {
            width: 65px;
            font-weight: bold;
        }

        .phone-column {
            width: 90px;
        }

        .city-column {
            width: 110px;
        }

        .check-box {
            width: 13px;
            height: 13px;
            border: 1px solid #a0aec0;
            border-radius: 2px;
            margin: 0 auto;
            background: #fff;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #a0aec0;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }

        @page {
            margin: 30px 25px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Vibração Jovem 2026</h1>
        <div class="diocese">Diocese de União da Vitória</div>
        <p>Lista Oficial de Participantes</p>
    </div>

    <table class="info-table">
        <tr>
            <td><strong>Total de Inscritos:</strong> {{ $participants->count() }}</td>
            <td class="info-right"><strong>Gerado em:</strong> {{ now()->format('d/m/Y H:i') }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th class="check-column">Conf.</th>
                <th class="index-column">#</th>
                <th class="ticket-column">Ticket</th>
                <th>Nome</th>
                <th class="city-column">Cidade</th>
                <th>Paróquia</th>
                <th class="phone-column">Telefone</th>
            </tr>
        </thead>

        <tbody>
            @foreach($participants as $index => $order)
                <tr>
                    <td class="check-column">
                        <div class="check-box"></div>
                    </td>

                    <td class="index-column">
                        {{ $index + 1 }}
                    </td>

                    <td class="ticket-column">
                        {{ $order->ticket_number }}
                    </td>

                    <td>
                        {{ $order->participant->full_name }}
                    </td>

                    <td class="city-column">
                        {{ $order->participant->city }}
                    </td>

                    <td>
                        {{ $order->participant->parish }}
                    </td>

                    <td class="phone-column">
                        {{ $order->participant->phone }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Documento gerado automaticamente pelo sistema de inscrições - Vibração Jovem 2026
    </div>

</body>
</html>