<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Integration Status Update</title>
    <style>
        /* Base styling with modern typography */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #374151;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }

        .wrapper {
            width: 100%;
            max-width: 100%;
            table-layout: fixed;
            background-color: #f3f4f6;
            padding: 30px 10px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        }

        /* Header with RED + OFF-BLUE color scheme */
        .header {
            background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
            padding: 25px 30px;
            text-align: center;
            color: white;
        }

        .logo-container {
            margin-bottom: 15px;
            text-align: center;
        }

        .logo {
            width: 160px;
            height: auto;
            max-height: 60px;
        }

        .header h1 {
            color: #ffffff;
            font-size: 22px;
            font-weight: 700;
            margin: 0;
            padding: 0;
            letter-spacing: -0.01em;
        }

        /* Content area */
        .content {
            padding: 30px;
        }

        /* Typography with updated color scheme */
        h2 {
            color: #1e3a8a; /* Off-blue color */
            font-size: 20px;
            margin-top: 30px;
            margin-bottom: 15px;
            font-weight: 600;
            padding-bottom: 8px;
            position: relative;
        }

        h2:after {
            content: "";
            position: absolute;
            left: 0;
            bottom: 0;
            width: 40px;
            height: 3px;
            background: #dc2626; /* Red accent */
            border-radius: 3px;
        }

        p {
            margin: 0 0 15px;
            font-size: 15px;
        }

        .meta-info {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 25px;
            border-left: 4px solid #1e3a8a; /* Off-blue border */
        }

        .meta-info p {
            margin: 5px 0;
        }

        strong {
            font-weight: 600;
        }

        /* Status indicators with updated color scheme */
        .status-container {
            border-radius: 10px;
            padding: 20px;
            margin: 25px 0;
        }

        .status-approved {
            background-color: #f0fdf4;
            border-left: 4px solid #16a34a;
        }

        .status-rejected {
            background-color: #fef2f2;
            border-left: 4px solid #dc2626; /* Red accent */
        }

        .status-returned {
            background-color: #fffbeb;
            border-left: 4px solid #f59e0b;
        }

        .status-info {
            background-color: #eff6ff;
            border-left: 4px solid #1e3a8a; /* Off-blue accent */
        }

        .status-icon {
            font-size: 20px;
            margin-right: 8px;
            vertical-align: middle;
        }

        .status-title {
            font-weight: 600;
            font-size: 17px;
            margin: 0 0 10px 0;
            display: block;
        }

        /* Quote box with elegant styling */
        .quote {
            background-color: #f9fafb;
            border-radius: 8px;
            margin: 15px 0;
            padding: 15px 20px;
            font-style: italic;
            position: relative;
            border-left: 4px solid #9ca3af;
        }

        .quote:before {
            content: """;
            position: absolute;
            top: 0;
            left: 10px;
            font-size: 40px;
            color: #e5e7eb;
            font-family: Georgia, serif;
            line-height: 1;
        }

        /* Separator */
        hr {
            border: 0;
            height: 1px;
            background-color: #e5e7eb;
            margin: 30px 0;
        }

        /* Table styling with red/blue color scheme */
        table.info-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin: 20px 0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        table.info-table th,
        table.info-table td {
            text-align: left;
            padding: 12px 15px;
            border-bottom: 1px solid #e5e7eb;
        }

        table.info-table th {
            background-color: #1e3a8a; /* Off-blue header */
            font-weight: 600;
            color: #ffffff;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.05em;
        }

        table.info-table tr:last-child td {
            border-bottom: none;
        }

        table.info-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        /* Action list with improved styling */
        .action-section {
            background-color: #f9fafb;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #dc2626; /* Red accent */
        }

        .action-section p {
            margin-top: 0;
            font-weight: 600;
        }

        ol {
            margin: 15px 0 5px;
            padding-left: 20px;
        }

        li {
            margin-bottom: 10px;
            padding-left: 5px;
        }

        /* Button with red/blue color scheme */
        .button-container {
            text-align: center;
            margin: 30px 0;
        }

        .button {
            display: inline-block;
            background-color: #dc2626; /* Default red button */
            color: white;
            padding: 12px 28px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            transition: transform 0.15s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .button-approved {
            background-color: #16a34a;
        }

        .button-rejected {
            background-color: #dc2626;
        }

        .button-returned {
            background-color: #f59e0b;
        }

        .button-default {
            background-color: #1e3a8a; /* Off-blue button */
        }

        /* Support section */
        .support {
            background-color: #f9fafb;
            border-radius: 8px;
            padding: 15px 20px;
            margin: 25px 0 15px;
            text-align: center;
            border-top: 2px solid #1e3a8a; /* Off-blue accent */
        }

        .support a {
            color: #dc2626; /* Red link */
            text-decoration: none;
            font-weight: 500;
        }

        /* Footer with red/blue branding */
        .footer {
            background-color: #1e3a8a; /* Off-blue background */
            padding: 25px 30px;
            color: #f3f4f6;
            font-size: 14px;
            text-align: center;
        }

        .footer p {
            margin: 5px 0;
        }

        .company-name {
            font-weight: 600;
            color: #ffffff;
        }

        .copyright {
            margin-top: 15px;
            font-size: 13px;
            color: #cbd5e1;
        }

        /* Responsive adjustments */
        @media screen and (max-width: 600px) {
            .email-container {
                width: 100% !important;
            }

            .header, .content, .footer {
                padding: 20px !important;
            }

            .status-container, .action-section, .support {
                padding: 15px !important;
            }

            h1 {
                font-size: 20px !important;
            }

            h2 {
                font-size: 18px !important;
            }

            .button {
                display: block !important;
                width: 100% !important;
            }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="email-container">
        <div class="header">
            <div class="logo-container">
                <!-- Logo placeholder - replace with your actual logo -->
                <img class="logo" src="{{ config('app.url') }}/images/logo-light.png" alt="{{ config('app.name') }} Logo">
            </div>
            <h1>Integration Status Update: {{ $integration->name }}</h1>
        </div>

        <div class="content">
            <div class="meta-info">
                <p><strong>Integration ID:</strong> #{{ $integration->id }}</p>
                <p><strong>Type:</strong> {{ ucfirst($integration->type) }}</p>
                <p><strong>Department:</strong> {{ $integration->department }}</p>
            </div>

            @if($action === 'approved')
                @if($integration->status === 'approved')
                    <div class="status-container status-approved">
                        <span class="status-title"><span class="status-icon">✅</span> Full Approval Complete</span>
                        <p>This integration has been fully approved and will move to implementation immediately.</p>
                    </div>
                @else
                    <div class="status-container status-approved">
                        <span class="status-title"><span class="status-icon">🔄</span> Stage Approval Completed</span>
                        <p>Approved at <strong>{{ $stage }}</strong> stage. Awaiting review from <strong>{{ $returnToStage }}</strong> team.</p>
                    </div>
                @endif
            @elseif($action === 'rejected')
                <div class="status-container status-rejected">
                    <span class="status-title"><span class="status-icon">❌</span> Request Rejected</span>
                    <p>Approval halted at <strong>{{ $stage }}</strong> stage.</p>
                </div>

                @if(!empty($integration->approvalHistories->first()?->comments))
                    <p><strong>Feedback from Reviewer:</strong></p>
                    <div class="quote">
                        {!! nl2br(e($integration->approvalHistories->first()->comments)) !!}
                    </div>
                @endif
            @elseif($action === 'returned')
                <div class="status-container status-returned">
                    <span class="status-title"><span class="status-icon">📝</span> Revision Required</span>
                    <p>Returned from <strong>{{ $stage }}</strong> to <strong>{{ $returnToStage }}</strong> stage for modifications.</p>
                </div>

                @if(!empty($integration->approvalHistories->first()?->comments))
                    <p><strong>Revision Notes:</strong></p>
                    <div class="quote">
                        {!! nl2br(e($integration->approvalHistories->first()->comments)) !!}
                    </div>
                @endif
            @else
                <div class="status-container status-info">
                    <span class="status-title"><span class="status-icon">ℹ️</span> Status Update</span>
                    <p>{{ ucfirst(str_replace('_', ' ', $integration->status)) }}</p>
                </div>
            @endif

            <hr>

            <h2>Integration Overview</h2>
            <table class="info-table">
                <tr>
                    <th>Detail</th>
                    <th>Information</th>
                </tr>
                <tr>
                    <td>Current Status</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $integration->status)) }}</td>
                </tr>
                <tr>
                    <td>Priority Level</td>
                    <td>{{ ucfirst($integration->priority) }}</td>
                </tr>
                <tr>
                    <td>Timeline Estimate</td>
                    <td>{{ $integration->estimated_timeline ? \Carbon\Carbon::parse($integration->estimated_timeline)->format('M j, Y') : 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Requestor</td>
                    <td>{{ optional($integration->createdBy)->name ?? 'System Generated' }}</td>
                </tr>
            </table>

            <hr>

            @if($action === 'approved' && $integration->status !== 'approved')
                <div class="action-section">
                    <p>Next Phase</p>
                    <p>The <strong>{{ $returnToStage }}</strong> team should review and approve before {{ \Carbon\Carbon::parse($integration->estimated_timeline)->format('M j') }} deadline.</p>
                </div>
            @endif

            @if($action === 'returned')
                <div class="action-section">
                    <p>Required Action</p>
                    <ol>
                        <li>Address feedback from revision notes</li>
                        <li>Resubmit for {{ $stage }} review</li>
                        <li>Maintain target implementation date: {{ \Carbon\Carbon::parse($integration->estimated_timeline)->format('M j, Y') }}</li>
                    </ol>
                </div>
            @endif

            <div class="button-container">
                <a href="{{ $viewUrl }}" class="button{{ $action === 'approved' ? ' button-approved' : ($action === 'rejected' ? ' button-rejected' : ($action === 'returned' ? ' button-returned' : ' button-default')) }}">
                    {{ $action === 'approved' ? 'View Approved Integration' : 'Review Request' }}
                </a>
            </div>

            <div class="support">
                Need assistance? Contact <a href="mailto:support@example.com">Support Team</a>
            </div>
        </div>

        <div class="footer">
            <p>Best regards,</p>
            <p class="company-name">{{ config('app.name') }} Integration Team</p>
            <p>🏢 Digital Transformation Department</p>
            <p>📞 (255) 123-4567</p>
            <p class="copyright">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</div>
</body>
</html>
