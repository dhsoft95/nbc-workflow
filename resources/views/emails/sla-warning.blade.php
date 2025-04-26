<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLA Warning Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #ff9800;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 20px;
            border-left: 1px solid #ddd;
            border-right: 1px solid #ddd;
        }
        .footer {
            background-color: #eee;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-radius: 0 0 5px 5px;
            border: 1px solid #ddd;
        }
        .warning {
            background-color: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 5px solid #ffc107;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table td, .info-table th {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .info-table th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>⚠️ SLA Warning</h1>
    </div>
    <div class="content">
        <div class="warning">
            <strong>Attention Required:</strong> An integration request has been awaiting your review for longer than the recommended timeframe.
        </div>

        <p>Hello,</p>

        <p>An integration request requires your attention. According to our Service Level Agreement (SLA), this integration has been in the current approval stage for longer than expected:</p>

        <table class="info-table">
            <tr>
                <th>Integration Name</th>
                <td>{{ $integration->name }}</td>
            </tr>
            <tr>
                <th>Current Stage</th>
                <td>{{ $stageNames[$stage] ?? ucfirst($stage) }}</td>
            </tr>
            <tr>
                <th>Time in Current Stage</th>
                <td><strong>{{ round($hoursInStage, 1) }} hours</strong> (SLA target: {{ $slaConfig->warning_hours }} hours)</td>
            </tr>
            <tr>
                <th>Department</th>
                <td>{{ $integration->department }}</td>
            </tr>
            <tr>
                <th>Priority</th>
                <td>{{ ucfirst($integration->priority) }}</td>
            </tr>
            <tr>
                <th>Created By</th>
                <td>{{ $integration->creator->name ?? 'Unknown' }}</td>
            </tr>
        </table>

        <p>Please review this integration request at your earliest convenience. Timely processing of integration requests helps maintain our workflow efficiency.</p>

        <p><a href="{{ route('integrations.show', $integration) }}" class="btn">Review Integration</a></p>

        <p>Thank you for your attention to this matter.</p>

        <p>Best regards,<br>Integration Management System</p>
    </div>
    <div class="footer">
        <p>This is an automated message from the Integration Management System. Please do not reply to this email.</p>
        <p>If you believe you've received this in error, please contact your system administrator.</p>
    </div>
</div>
</body>
</html>
