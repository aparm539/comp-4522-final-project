<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Containers Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Containers Report</h1>
        <p>Generated on: {{ now()->setTimezone('America/Denver')->format('Y-m-d H:i:s') }} (Mountain Time)</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Barcode</th>
                <th>CAS #</th>
                <th>Chemical Name</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Room</th>
                <th>Cabinet</th>
                <th>Hazardous</th>
            </tr>
        </thead>
        <tbody>
            @foreach($containers as $container)
                <tr>
                    <td>{{ $container->barcode }}</td>
                    <td>{{ $container->chemical->cas }}</td>
                    <td>{{ $container->chemical->name }}</td>
                    <td>{{ $container->quantity }}</td>
                    <td>{{ $container->unitOfMeasure->abbreviation }}</td>
                    <td>{{ $container->storageCabinet->location->room_number }}</td>
                    <td>{{ $container->storageCabinet->name }}</td>
                    <td>{{ $container->chemical->ishazardous ? 'Yes' : 'No' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Total Containers: {{ count($containers) }}</p>
    </div>
</body>
</html> 