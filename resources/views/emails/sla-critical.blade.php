<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRITICAL SLA Alert</title>
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
            background-color: #dc3545;
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
        .critical {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 5px solid #dc3545;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 10px;
            font-weight: bold;
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
        .hours-exceeded {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🚨 CRITICAL SLA ALERT</h1>
    </div>
    <div class="content">
        <div class="critical">
            <strong>URGENT ACTION REQUIRED:</strong> An integration request has significantly exceeded the SLA timeframe and requires immediate attention.
        </div>

        <p>Hello,</p>

        <p>This is an <strong>urgent notification</strong> regarding an integration request that has critically exceeded our Service Level Agreement (SLA) timeframe:</p>

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
                <td class="hours-exceeded"><strong>{{ round($hoursInStage, 1) }} hours</strong> (Critical threshold: {{ $slaConfig->critical_hours }} hours)</td>
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
            <tr>
                <th>Created Date</th>
                <td>{{ $integration->created_at->format('Y-m-d H:i') }}</td>
            </tr>
        </table>

        <p><strong>This integration has been awaiting approval for significantly longer than our agreed service level.</strong> Please take immediate action to review and process this request to prevent further delays.</p>

        <p>This alert has also been escalated to appropriate management personnel.</p>

        <p><a href="{{ route('integrations.show', $integration) }}" class="btn">REVIEW IMMEDIATELY</a></p>

        <p>If you are unable to process this request, please contact your supervisor or delegate this task to someone who can attend to it promptly.</p>

        <p>Thank you for your immediate attention to this critical matter.</p>

        <p>Integration Management System</p>
    </div>
    <div class="footer">
        <p>This is an automated message from the Integration Management System. Please do not reply to this email.</p>
        <p>If you believe you've received this in error, please contact your system administrator.</p>
    </div>
</div>
</body>
</html>
