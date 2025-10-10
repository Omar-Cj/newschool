<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guardian List Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        .routine_wrapper {
            max-width: 900px;
            margin: auto;
            background: #ECECEC;
        }

        .routine_wrapper_header {
            background: #392C7D;
            padding: 20px 30px;
            border-radius: 8px 8px 0 0;
            display: flex;
            align-items: center;
        }

        .routine_wrapper_header_logo .header_logo {
            height: 50px;
        }

        .vertical_seperator {
            border-right: 1px solid #FFFFFF;
            height: 60px;
            margin: 0 20px;
        }

        .routine_wrapper_header h3 {
            font-weight: 500;
            font-size: 24px;
            color: #FFFFFF;
            margin: 0 0 5px 0;
        }

        .routine_wrapper_header p {
            font-size: 14px;
            color: #FFFFFF;
            margin: 0;
        }

        .routine_wrapper_body {
            padding: 30px;
            background: #fff;
        }

        .markseet_title h5 {
            color: #242424;
            font-weight: 600;
            font-size: 20px;
            margin: 20px 0;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #EAEAEA;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        tfoot td {
            background-color: #f9f9f9;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="routine_wrapper">
        <!-- Header -->
        <div class="routine_wrapper_header">
            @if(setting('light_logo'))
            <div class="routine_wrapper_header_logo">
                <img class="header_logo" src="{{ public_path(setting('light_logo')) }}" alt="logo">
            </div>
            <div class="vertical_seperator"></div>
            @endif
            <div class="routine_wrapper_header_content">
                <h3>{{ setting('application_name') }}</h3>
                <p>{{ setting('address') }}</p>
            </div>
        </div>

        <div class="routine_wrapper_body">
            <!-- Report Title -->
            <div class="markseet_title">
                <h5>Guardian List Report</h5>
            </div>

            <!-- Report Table -->
            <table>
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="25%">Guardian Name</th>
                        <th width="15%">Mobile</th>
                        <th width="30%">Address</th>
                        <th width="12%">Total Students</th>
                        <th width="13%">Relation Type</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data['reportData']['guardians'] as $index => $guardian)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $guardian->guardian_name }}</td>
                        <td>{{ $guardian->guardian_mobile }}</td>
                        <td>{{ $guardian->guardian_address }}</td>
                        <td>{{ $guardian->total_students }}</td>
                        <td>{{ $guardian->relation_type }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">No data found</td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-right">Total Guardians:</td>
                        <td>{{ $data['reportData']['total_count'] }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <!-- routine_wrapper_body end -->
    </div>
    <!-- routine_wrapper end -->
</body>
</html>
