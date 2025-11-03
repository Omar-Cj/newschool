<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report - {{ setting('school_name') ?? 'School Management System' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            background: #fff;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .print-wrapper {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        /* School Header */
        .school-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #392C7D;
        }

        .school-header .logo {
            max-width: 120px;
            height: auto;
            margin-bottom: 15px;
        }

        .school-header h1 {
            color: #392C7D;
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .school-header h2 {
            color: #FF5170;
            font-size: 22px;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .school-header .contact-info {
            font-size: 12px;
            color: #666;
            margin-top: 8px;
        }

        /* Report Header */
        .report-header {
            background: #F8F8F8;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .report-header .report-info {
            flex: 1;
        }

        .report-header h3 {
            color: #1A1A21;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .report-header .date-range {
            color: #666;
            font-size: 13px;
        }

        .report-header .legend {
            text-align: right;
            font-size: 12px;
        }

        .report-header .legend span {
            display: inline-block;
            margin-left: 15px;
            padding: 3px 8px;
            border-radius: 3px;
        }

        .legend .present { color: #28a745; font-weight: 600; }
        .legend .late { color: #ffc107; font-weight: 600; }
        .legend .absent { color: #dc3545; font-weight: 600; }
        .legend .halfday { color: #007bff; font-weight: 600; }
        .legend .holiday { color: #6c757d; font-weight: 600; }

        /* Student Section */
        .student-section {
            margin-bottom: 40px;
            page-break-inside: avoid;
        }

        .student-info {
            background: #F5F5F5;
            border-radius: 8px;
            padding: 18px 20px;
            margin-bottom: 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 12px;
        }

        .student-info-item {
            display: flex;
            align-items: center;
        }

        .student-info-item .label {
            font-weight: 600;
            color: #424242;
            min-width: 120px;
            font-size: 13px;
        }

        .student-info-item .value {
            color: #1A1A21;
            font-size: 13px;
        }

        /* Attendance Table */
        .attendance-table-wrapper {
            margin-bottom: 20px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        table thead {
            background: #E6E6E6;
        }

        table thead th {
            padding: 10px 8px;
            text-align: center;
            font-weight: 600;
            font-size: 13px;
            color: #1A1A21;
            border: 1px solid #ddd;
        }

        table tbody td {
            padding: 8px 6px;
            text-align: center;
            font-size: 12px;
            border: 1px solid #ddd;
            vertical-align: middle;
        }

        table tbody tr:nth-child(odd) {
            background: #F8F8F8;
        }

        table tbody tr:nth-child(even) {
            background: #FFFFFF;
        }

        /* Attendance Status Colors */
        .status-present {
            color: #28a745;
            font-weight: 700;
            font-size: 14px;
        }

        .status-late {
            color: #ffc107;
            font-weight: 700;
            font-size: 14px;
        }

        .status-absent {
            color: #dc3545;
            font-weight: 700;
            font-size: 14px;
        }

        .status-halfday {
            color: #007bff;
            font-weight: 700;
            font-size: 14px;
        }

        .status-holiday {
            color: #6c757d;
            font-weight: 500;
            font-size: 14px;
        }

        /* Summary Section */
        .attendance-summary {
            background: #F8F8F8;
            border-radius: 8px;
            padding: 15px 20px;
            margin-top: 15px;
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 15px;
        }

        .summary-item {
            text-align: center;
            flex: 1;
            min-width: 100px;
        }

        .summary-item .label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .summary-item .value {
            font-size: 24px;
            font-weight: 700;
        }

        .summary-item.present .value { color: #28a745; }
        .summary-item.late .value { color: #ffc107; }
        .summary-item.absent .value { color: #dc3545; }
        .summary-item.halfday .value { color: #007bff; }

        /* Page Break */
        .page-break {
            page-break-after: always;
        }

        /* Print Footer */
        .print-footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 11px;
            color: #999;
        }

        /* Print-specific styles */
        @media print {
            /* Optimize page margins to minimize browser headers/footers */
            @page {
                margin: 0.5cm;
                size: auto;
            }

            /* Hide all navigation and UI elements */
            header, nav, .sidebar, .hamburger-menu, .menu-toggle,
            .user-profile, .navbar, .top-bar, .main-menu, .breadcrumb,
            footer, button, .btn, .no-print, .navigation, .header,
            .menu, .user-dropdown, .profile-dropdown, .navbar-nav,
            .offcanvas, .modal, .tooltip, .popover, .dropdown-menu,
            #sidebar, #header, #navbar, #menu, .top-header, .main-header {
                display: none !important;
                visibility: hidden !important;
            }

            body {
                margin: 0 !important;
                padding: 20px !important;
                background: white !important;
            }

            .print-wrapper {
                padding: 15px !important;
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
            }

            .page-break {
                page-break-after: always;
            }

            .student-section {
                page-break-inside: avoid;
            }

            table {
                page-break-inside: auto;
            }

            table tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            .no-print {
                display: none !important;
            }

            /* Ensure colors print correctly */
            .status-present,
            .status-late,
            .status-absent,
            .status-halfday,
            .status-holiday {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* Ensure only print content shows */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }

        /* Responsive adjustments */
        @media screen and (max-width: 768px) {
            .report-header {
                flex-direction: column;
                text-align: center;
            }

            .report-header .legend {
                text-align: center;
                margin-top: 10px;
            }

            .student-info {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 11px;
            }

            table thead th,
            table tbody td {
                padding: 5px 3px;
            }
        }
    </style>
</head>
<body>
    <div class="print-wrapper">
        <!-- School Header -->
        <div class="school-header">
            @if(setting('light_logo'))
                <img src="{{ asset(setting('light_logo')) }}" alt="{{ setting('application_name') }}" class="logo">
            @endif
            <h1>{{ setting('application_name') ?? 'School Management System' }}</h1>
            <h2>{{ ___('report.attendance_report') }}</h2>
            @if(setting('address') || setting('phone'))
                <div class="contact-info">
                    @if(setting('address'))
                        {{ setting('address') }}
                    @endif
                    @if(setting('phone'))
                        | {{ ___('common.Phone') }}: {{ setting('phone') }}
                    @endif
                </div>
            @endif
        </div>

        <!-- Report Header with Legend -->
        <div class="report-header">
            <div class="report-info">
                <h3>{{ ___('report.attendance_report') }}</h3>
                @if(isset($dateRange))
                    <div class="date-range">
                        <strong>{{ ___('common.Period') }}:</strong> {{ $dateRange }}
                    </div>
                @elseif(isset($month))
                    <div class="date-range">
                        <strong>{{ ___('common.Month') }}:</strong> {{ $month }}
                    </div>
                @elseif(isset($date))
                    <div class="date-range">
                        <strong>{{ ___('common.Date') }}:</strong> {{ $date }}
                    </div>
                @endif
                <div class="date-range">
                    <strong>{{ ___('common.Generated') }}:</strong> {{ now()->format('F d, Y - h:i A') }}
                </div>
            </div>
            <div class="legend">
                <div><strong>{{ ___('common.Legend') }}:</strong></div>
                <span class="present">{{ ___('common.P') }} = {{ ___('attendance.Present') }}</span>
                <span class="late">{{ ___('common.L') }} = {{ ___('attendance.Late') }}</span>
                <span class="absent">{{ ___('common.A') }} = {{ ___('attendance.Absent') }}</span>
                <span class="halfday">{{ ___('common.F') }} = {{ ___('attendance.half_day') }}</span>
                <span class="holiday">{{ ___('common.H') }} = {{ ___('attendance.Holiday') }}</span>
            </div>
        </div>

        @php
            // Handle both data structures from repository
            $studentsList = [];

            if (isset($data['results']) && is_array($data['results']) && isset($data['results'][0]['student'])) {
                // Multiple students structure from "all students" mode
                $studentsList = $data['results'];
            } elseif (isset($data['results']) && isset($data['student'])) {
                // Single student structure
                $studentsList = [
                    [
                        'student' => $data['student'],
                        'class_name' => $data['student']->sessionStudentDetails->class->name ?? 'N/A',
                        'section_name' => $data['student']->sessionStudentDetails->section->name ?? 'N/A',
                        'records' => $data['results'],
                        'summary' => null  // Will be calculated below
                    ]
                ];
            }
        @endphp

        @foreach($studentsList as $index => $studentData)
            @php
                $student = $studentData['student'] ?? null;
                $attendanceRecords = $studentData['records'] ?? $studentData['attendance'] ?? collect();

                if (!$student) continue;

                // Calculate summary statistics
                $summary = [
                    'present' => 0,
                    'late' => 0,
                    'absent' => 0,
                    'halfday' => 0
                ];

                foreach ($attendanceRecords as $record) {
                    if ($record->attendance == App\Enums\AttendanceType::PRESENT) {
                        $summary['present']++;
                    } elseif ($record->attendance == App\Enums\AttendanceType::LATE) {
                        $summary['late']++;
                    } elseif ($record->attendance == App\Enums\AttendanceType::ABSENT) {
                        $summary['absent']++;
                    } elseif ($record->attendance == App\Enums\AttendanceType::HALFDAY) {
                        $summary['halfday']++;
                    }
                }

                $totalDays = array_sum($summary);
                $attendancePercentage = $totalDays > 0 ? round(($summary['present'] + $summary['halfday'] * 0.5) / $totalDays * 100, 2) : 0;
            @endphp

            <div class="student-section">
                <!-- Student Information -->
                <div class="student-info">
                    <div class="student-info-item">
                        <span class="label">{{ ___('student_info.Student Name') }}:</span>
                        <span class="value">{{ $student->first_name }} {{ $student->last_name }}</span>
                    </div>
                    <div class="student-info-item">
                        <span class="label">{{ ___('academic.Class') }}:</span>
                        <span class="value">{{ $studentData['class_name'] ?? 'N/A' }}</span>
                    </div>
                    <div class="student-info-item">
                        <span class="label">{{ ___('academic.Section') }}:</span>
                        <span class="value">{{ $studentData['section_name'] ?? 'N/A' }}</span>
                    </div>
                    <div class="student-info-item">
                        <span class="label">{{ ___('common.Attendance Rate') }}:</span>
                        <span class="value">{{ $attendancePercentage }}%</span>
                    </div>
                </div>

                <!-- Attendance Calendar Table (Short View Style) -->
                <div class="attendance-table-wrapper">
                    @if(isset($view) && $view == '0' || !isset($view))
                        {{-- Short View: Calendar Grid --}}
                        @php
                            $daysInMonth = isset($month) ? date('t', strtotime($month)) : date('t');
                        @endphp
                        <table>
                            <thead>
                                <tr>
                                    @for ($i = 1; $i <= $daysInMonth; $i++)
                                        <th>{{ $i }}</th>
                                    @endfor
                                    <th class="status-present">{{ ___('common.P') }}</th>
                                    <th class="status-late">{{ ___('common.L') }}</th>
                                    <th class="status-absent">{{ ___('common.A') }}</th>
                                    <th class="status-halfday">{{ ___('common.F') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @for ($i = 1; $i <= $daysInMonth; $i++)
                                        <td>
                                            @php
                                                $dayRecord = $attendanceRecords->first(function($record) use ($i) {
                                                    return (int)substr($record->date, -2) == $i;
                                                });
                                            @endphp
                                            @if($dayRecord)
                                                @if ($dayRecord->attendance == App\Enums\AttendanceType::PRESENT)
                                                    <span class="status-present">{{ ___('common.P') }}</span>
                                                @elseif($dayRecord->attendance == App\Enums\AttendanceType::LATE)
                                                    <span class="status-late">{{ ___('common.L') }}</span>
                                                @elseif($dayRecord->attendance == App\Enums\AttendanceType::ABSENT)
                                                    <span class="status-absent">{{ ___('common.A') }}</span>
                                                @elseif($dayRecord->attendance == App\Enums\AttendanceType::HALFDAY)
                                                    <span class="status-halfday">{{ ___('common.F') }}</span>
                                                @else
                                                    <span class="status-holiday">{{ ___('common.H') }}</span>
                                                @endif
                                            @else
                                                <span>-</span>
                                            @endif
                                        </td>
                                    @endfor
                                    <td><span class="status-present">{{ $summary['present'] }}</span></td>
                                    <td><span class="status-late">{{ $summary['late'] }}</span></td>
                                    <td><span class="status-absent">{{ $summary['absent'] }}</span></td>
                                    <td><span class="status-halfday">{{ $summary['halfday'] }}</span></td>
                                </tr>
                            </tbody>
                        </table>
                    @else
                        {{-- Detailed View: Date List --}}
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 20%;">{{ ___('common.Date') }}</th>
                                    <th style="width: 20%;">{{ ___('attendance.Attendance') }}</th>
                                    <th style="width: 60%;">{{ ___('attendance.Note') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($attendanceRecords as $record)
                                    <tr>
                                        <td>{{ dateFormat($record->date) }}</td>
                                        <td>
                                            @if ($record->attendance == App\Enums\AttendanceType::PRESENT)
                                                <span class="status-present">{{ ___('common.Present') }}</span>
                                            @elseif($record->attendance == App\Enums\AttendanceType::LATE)
                                                <span class="status-late">{{ ___('common.Late') }}</span>
                                            @elseif($record->attendance == App\Enums\AttendanceType::ABSENT)
                                                <span class="status-absent">{{ ___('common.Absent') }}</span>
                                            @elseif($record->attendance == App\Enums\AttendanceType::HALFDAY)
                                                <span class="status-halfday">{{ ___('common.half_day') }}</span>
                                            @else
                                                <span class="status-holiday">{{ ___('common.Holiday') }}</span>
                                            @endif
                                        </td>
                                        <td style="text-align: left;">{{ $record->note ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" style="text-align: center; color: #999;">
                                            {{ ___('common.no_data_available') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    @endif
                </div>

                <!-- Attendance Summary -->
                <div class="attendance-summary">
                    <div class="summary-item present">
                        <div class="label">{{ ___('attendance.Present') }}</div>
                        <div class="value">{{ $summary['present'] }}</div>
                    </div>
                    <div class="summary-item late">
                        <div class="label">{{ ___('attendance.Late') }}</div>
                        <div class="value">{{ $summary['late'] }}</div>
                    </div>
                    <div class="summary-item absent">
                        <div class="label">{{ ___('attendance.Absent') }}</div>
                        <div class="value">{{ $summary['absent'] }}</div>
                    </div>
                    <div class="summary-item halfday">
                        <div class="label">{{ ___('attendance.half_day') }}</div>
                        <div class="value">{{ $summary['halfday'] }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="label">{{ ___('common.Total Days') }}</div>
                        <div class="value" style="color: #333;">{{ $totalDays }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="label">{{ ___('common.Attendance Rate') }}</div>
                        <div class="value" style="color: {{ $attendancePercentage >= 75 ? '#28a745' : '#dc3545' }};">
                            {{ $attendancePercentage }}%
                        </div>
                    </div>
                </div>
            </div>

            @if(!$loop->last)
                <div class="page-break"></div>
            @endif
        @endforeach

        <!-- Print Footer -->
        <div class="print-footer">
            <p>{{ setting('footer_text') ?? '© 2025 Telesom Enterprise Solutions. All rights reserved.' }}</p>
            <p>{{ ___('common.This is a computer-generated document') }}</p>
        </div>
    </div>

    <script>
        // Auto-print when page loads (optional - can be triggered manually)
        window.onload = function() {
            // Uncomment the line below to enable auto-print
            // window.print();
        }
    </script>
</body>
</html>
