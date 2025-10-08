
<!DOCTYPE html>
<html>
<head>
    <title>Marksheet</title>
    <style>
body {
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        margin: 0;
        padding: 0;
        -webkit-print-color-adjust: exact !important;
}
.report {
    background: white;
}
.report_header {
    background: #392C7D;
    border-radius: 10px 10px 0 0;
    padding: 10px;
}
.report_header_logo {
    float: left;
    padding: 10px;
    border-right: #E6E6E6 3px solid;
    margin-right: 10px;
}
.report_header_logo img {
    height: 65px;
}
.report_header_content {
    color: white;
}
.report_header_content h3 {
    font-size: 24px;
    margin: 0;
}
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
.table{
    width: 100%;
}
.table_th {
    border-right: 0;
    border-color: transparent !important;
    text-align: left;
    background: #E6E6E6 !important;
    font-size: 16px;
    font-weight: 500;
    text-transform: capitalize;
    padding: 8px 4px;
}
.table_td {
    border-right: 0;
    border-color: transparent !important;
    text-align: left;
    font-size: 14px;
    padding: 8px 4px;
}
.table tr:nth-of-type(odd) {
    padding: 0;
    border-color: white;
    background: #F8F8F8;
}
.table tr:nth-of-type(even) {
    border: 0;
    border-color: white;
    background: #EFEFEF;
}
.footer {
    padding: 5px;
    text-align: center;
    background: #E6E6E6 !important;
    border-radius:  0 0 10px 10px;
}
.markseet_title {
    text-align: center;
    font-size: 24px;
}

</style>
</head>
<body>
    <div class="row">
        <div class="col-lg-12">
            <div class="report">
                <div class="report_header">
                    <div class="report_header_logo">
                        <img class="header_logo" src="{{ @globalAsset(setting('light_logo'), '154X38.webp') }}" alt="{{ __('light logo') }}">
                    </div>
                    <div class="report_header_content">
                        <h3>{{ setting('application_name') }}</h4>
                        <p>{{ setting('address') }}</p>
                    </div>
                </div>


                <table class="table">
                    <thead>
                    </thead>
                    <tbody>
                        <tr>
                            <th class="table_th">{{___('common.Student Name')}} :</th>
                            <td class="table_td">{{ @$data['student']->first_name }} {{ @$data['student']->last_name }}</td>
                            <th class="table_th">{{___('common.Guardian Phone')}} :</th>
                            <td class="table_td">{{ @$data['student']->parent->guardian_mobile }}</td>
                        </tr>
                        <tr>
                            <th class="table_th">{{___('common.Guardian Name')}} :</th>
                            <td class="table_td">{{ @$data['student']->parent->guardian_name }}</td>
                            <th class="table_th">{{___('common.Exam Type')}} :</th>
                            <td class="table_td">{{ @$data['examType']->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th class="table_th">{{___('common.Class(Section)')}} :</th>
                            <td class="table_td">{{ @$data['student']->session_class_student->class->name }}
                                ({{ @$data['student']->session_class_student->section->name }}) </td>
                            <th class="table_th">{{___('common.Result')}} :</th>
                            <td class="table_td">{{ @$data['resultData']['result'] }}</td>
                        </tr>
                        <tr>
                            <th class="table_th">{{___('common.DOB')}} :</th>
                            <td class="table_td" colspan="3">{{ dateFormat(@$data['student']->dob) }}</td>
                        </tr>
                    </tbody>
                </table>


                <div class="markseet_title">
                    <h5>{{___('common.Grade Sheet')}}</h5>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="table_th">{{___('common.Subject Name')}}</th>
                                <th class="table_th">{{___('common.Result Marks')}}</th>
                                <th class="table_th">{{___('common.Grade')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (@$data['resultData']['exam_results'] as $result)
                                <tr>
                                    <td class="table_td">
                                        {{ $result->subject_name }}
                                    </td>
                                    <td class="table_td">
                                        @if($result->is_absent)
                                            <span style="color: #dc3545;">{{ ___('examination.Absent') }}</span>
                                        @else
                                            {{ number_format($result->result, 2) }}
                                        @endif
                                    </td>
                                    <td class="table_td">
                                        {{ $result->grade }}
                                    </td>
                                </tr>
                                @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="footer">
                    <img src="{{ globalAsset(setting('favicon')) }}" alt="Icon">
                    <p>{{ setting('footer_text') }}</p>
                </div>
            </div>


        </div>
    </div>
</body>
</html>
