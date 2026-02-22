<?php
function generateFacultyDashboard($department, $year, $section = '') {
    $yearText = '';
    switch($year) {
        case '2F': $yearText = 'Second Year'; break;
        case '3F': $yearText = 'Third Year'; break;
        case '4F': $yearText = 'Fourth Year'; break;
        case 'HOD': $yearText = 'HOD'; break;
        default: $yearText = $year; break;
    }

    $title = "$department Department - $yearText Faculty Dashboard";

    $feedbackLinks = [
        ['title' => 'Student Feedback on Faculty', 'url' => '../../GraphPage/StudentFeedBackFormFaculty.php', 'description' => 'View feedback from students about faculty performance'],
        ['title' => 'Student Feedback on Facilities', 'url' => '../../GraphPage/StudentFeedbackOnFacilities.php', 'description' => 'Check feedback regarding campus facilities and infrastructure'],
        ['title' => 'Guest Lecture Feedback', 'url' => '../../GraphPage/GuestLectureFeedbackFromGuest.php', 'description' => 'Review feedback from guest lectures'],
        ['title' => 'Course End Survey', 'url' => '../../GraphPage/CourseEndSurvey.php', 'description' => 'Analyze course completion surveys'],
        ['title' => 'Parent Feedback', 'url' => '../../GraphPage/ParentsFeedback.php', 'description' => 'View feedback from parents'],
        ['title' => 'Alumni Feedback', 'url' => '../../GraphPage/AluminiFeedBackForm.php', 'description' => 'Check alumni feedback and suggestions']
    ];

    $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . $title . '</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2.5rem 2rem;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .header-title {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .header-title h1 {
            font-size: 2rem;
            font-weight: 700;
        }

        .header-title p {
            font-size: 1rem;
            opacity: 0.95;
        }

        .header-actions {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .login-btn, .student-btn {
            background: white;
            color: #667eea;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }

        .login-btn:hover, .student-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .section-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 2rem;
            text-align: center;
        }

        .feedback-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .feedback-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            transition: all 0.3s;
            border: 1px solid #e5e7eb;
        }

        .feedback-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        .card-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .card-icon svg {
            width: 30px;
            height: 30px;
            color: white;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .card-description {
            color: #6b7280;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }

        .card-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .card-link:hover {
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .analytics-section {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            border: 1px solid #e5e7eb;
        }

        .analytics-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 1rem;
            text-align: center;
        }

        .analytics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .analytics-card {
            background: #f9fafb;
            border-radius: 8px;
            padding: 1.5rem;
            text-align: center;
            border: 1px solid #e5e7eb;
        }

        .analytics-value {
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 0.5rem;
        }

        .analytics-label {
            color: #6b7280;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .header {
                padding: 2rem 1rem;
            }

            .header-title h1 {
                font-size: 1.5rem;
            }

            .header-actions {
                flex-direction: column;
                width: 100%;
            }

            .container {
                padding: 1rem;
            }

            .feedback-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .analytics-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="header-title">
                <h1>' . $department . ' Department</h1>
                <p>' . $yearText . ' Faculty Dashboard</p>
            </div>
            <div class="header-actions">
                <a href="../Login.php" class="login-btn">Logout</a>
                <a href="../Department.html" class="student-btn">Back to Selection</a>
            </div>
        </div>
    </header>

    <div class="container">
        <h2 class="section-title">Feedback Analytics Dashboard</h2>

        <div class="feedback-grid">';

    foreach ($feedbackLinks as $link) {
        $html .= '
            <div class="feedback-card">
                <div class="card-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="card-title">' . $link['title'] . '</h3>
                <p class="card-description">' . $link['description'] . '</p>
                <a href="' . $link['url'] . '" class="card-link">
                    View Analytics
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" width="16" height="16">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>';
    }

    $html .= '
        </div>

        <div class="analytics-section">
            <h3 class="analytics-title">Quick Stats</h3>
            <div class="analytics-grid">
                <div class="analytics-card">
                    <div class="analytics-value">6</div>
                    <div class="analytics-label">Feedback Types</div>
                </div>
                <div class="analytics-card">
                    <div class="analytics-value">' . $department . '</div>
                    <div class="analytics-label">Department</div>
                </div>
                <div class="analytics-card">
                    <div class="analytics-value">' . $yearText . '</div>
                    <div class="analytics-label">Faculty Level</div>
                </div>
                <div class="analytics-card">
                    <div class="analytics-value">Real-time</div>
                    <div class="analytics-label">Data Updates</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>';

    return $html;
}
?>