<?php
/**
 * Dashboard Generator - Creates responsive student and faculty dashboards
 * Run this script to regenerate all department dashboards
 */

$base_path = __DIR__ . '/College_Department';

// Department mappings
$departments = [
    'AIDS' => 'AIDS',
    'AIML' => 'AIML',
    'Civil' => 'Civil',
    'CSE' => 'CSE',
    'ECE' => 'ECE',
    'EEE' => 'EEE',
    'FirstYear' => 'First Year',
    'IT' => 'IT',
    'MBA' => 'MBA',
    'MCA' => 'MCA',
    'Mechanical' => 'Mechanical'
];

$years = [
    '2' => ['year' => 'Second Year', 'year_num' => 2],
    '3' => ['year' => 'Third Year', 'year_num' => 3],
    '4' => ['year' => 'Fourth Year', 'year_num' => 4],
    '1' => ['year' => 'First Year', 'year_num' => 1],
];

// FirstYear only has specific files
$firstyear_pattern = ['2FirstYear' => 2, '3FirstYear' => 3, '4FirstYear' => 4];
$mba_pattern = ['1MBA' => 1, '2MBA' => 2, '1FMBA' => 1, '2FMBA' => 2];
$mca_pattern = ['1MCA' => 1, '2MCA' => 2, '1FMCA' => 1, '2FMCA' => 2];

function generate_dashboard_html($dept_display, $dept_folder, $year_text, $faculty_link) {
    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$dept_display} Department - {$year_text} Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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

        .login-btn, .faculty-btn {
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

        .login-btn:hover, .faculty-btn:hover {
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
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .feedback-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-top: 4px solid #667eea;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
        }

        .feedback-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 30px rgba(0, 0, 0, 0.15);
            border-top-color: #764ba2;
        }

        .card-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .card-description {
            font-size: 0.95rem;
            color: #666;
            margin-bottom: 1.5rem;
            flex-grow: 1;
        }

        .card-link {
            color: #667eea;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        .card-link:hover {
            color: #764ba2;
            transform: translateX(4px);
        }

        .faculty-section {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            text-align: center;
            margin-top: 2rem;
        }

        .faculty-section h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1rem;
        }

        .faculty-section p {
            color: #666;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                text-align: center;
            }

            .header-title h1 {
                font-size: 1.5rem;
            }

            .header-actions {
                width: 100%;
                justify-content: center;
            }

            .feedback-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .container {
                padding: 1.5rem 1rem;
            }

            .section-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <div class="header-title">
                <h1>{$dept_display} Department Dashboard</h1>
                <p>{$year_text} - Student Feedback Portal</p>
            </div>
            <div class="header-actions">
                <a href="../../Login.php" class="login-btn">Login</a>
                <a href="./{$faculty_link}" class="faculty-btn">Faculty Dashboard</a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <h2 class="section-title">📋 Available Feedback Forms</h2>

        <!-- Feedback Cards Grid -->
        <div class="feedback-grid">
            <!-- Faculty Feedback -->
            <a href="../../FeedBack_Form/StudentFeedBackFormFaculty.php" class="feedback-card">
                <div class="card-icon">👨‍🏫</div>
                <h3 class="card-title">Faculty Feedback</h3>
                <p class="card-description">Rate and provide feedback on faculty teaching quality, support, and effectiveness.</p>
                <span class="card-link">Fill Form →</span>
            </a>

            <!-- Facilities Feedback -->
            <a href="../../FeedBack_Form/StudentFeedbackOnFacilities.php" class="feedback-card">
                <div class="card-icon">🏫</div>
                <h3 class="card-title">Campus Facilities</h3>
                <p class="card-description">Share your feedback on campus infrastructure, amenities, and facilities.</p>
                <span class="card-link">Fill Form →</span>
            </a>

            <!-- Guest Lecture Feedback -->
            <a href="../../FeedBack_Form/GuestLectureFeedbackFromGuest.php" class="feedback-card">
                <div class="card-icon">🎤</div>
                <h3 class="card-title">Guest Lecture Feedback</h3>
                <p class="card-description">Rate guest lectures and share your learning experience and suggestions.</p>
                <span class="card-link">Fill Form →</span>
            </a>

            <!-- Course End Survey -->
            <a href="../../FeedBack_Form/CourseEndSurvey.php" class="feedback-card">
                <div class="card-icon">📊</div>
                <h3 class="card-title">Course End Survey</h3>
                <p class="card-description">Evaluate course content, delivery, and overall learning outcomes.</p>
                <span class="card-link">Fill Form →</span>
            </a>

            <!-- Parent Feedback -->
            <a href="../../FeedBack_Form/ParentFeedbackForm.php" class="feedback-card">
                <div class="card-icon">👨‍👩‍👧</div>
                <h3 class="card-title">Parent Feedback</h3>
                <p class="card-description">Parents can share feedback on college discipline, infrastructure, and placement support.</p>
                <span class="card-link">Fill Form →</span>
            </a>

            <!-- Alumni Feedback -->
            <a href="../../FeedBack_Form/AluminiFeedbackForm.php" class="feedback-card">
                <div class="card-icon">🎓</div>
                <h3 class="card-title">Alumni Feedback</h3>
                <p class="card-description">Alumni can provide feedback on career success and institutional effectiveness.</p>
                <span class="card-link">Fill Form →</span>
            </a>
        </div>

        <!-- Faculty Section -->
        <div class="faculty-section">
            <h2>Faculty Dashboard Access</h2>
            <p>Faculty members can access their dashboard to view student feedback and analytics.</p>
            <a href="./{$faculty_link}" class="faculty-btn" style="display: inline-block; margin: 0;">Go to Faculty Dashboard</a>
        </div>
    </div>
</body>
</html>
HTML;
}

// Generate dashboards for each department
foreach ($departments as $folder => $display_name) {
    $path = "$base_path/$folder";
    
    if (!is_dir($path)) {
        continue;
    }

    // Handle special cases for MBA, MCA, FirstYear
    if ($folder === 'MBA') {
        $patterns = $mba_pattern;
    } elseif ($folder === 'MCA') {
        $patterns = $mca_pattern;
    } elseif ($folder === 'FirstYear') {
        $patterns = $firstyear_pattern;
    } else {
        // Standard 2, 3, 4 years
        $patterns = [
            '2' => 2, '2F' => 2,
            '3' => 3, '3F' => 3,
            '4' => 4, '4F' => 4
        ];
    }

    // Generate files for each year pattern
    if ($folder === 'FirstYear') {
        // FirstYear: 2FirstYear.html, 3FirstYear.html, 4FirstYear.html
        foreach ([2, 3, 4] as $year) {
            $filename = $year . 'FirstYear.html';
            $filepath = "$path/$filename";
            $year_text = match($year) {
                2 => 'Second Year',
                3 => 'Third Year',
                4 => 'Fourth Year'
            };
            
            file_put_contents($filepath, generate_dashboard_html($display_name, $folder, $year_text, "${year}FirstYear.html"));
            echo "Generated: $filename<br>";
        }
    } elseif ($folder === 'MBA' || $folder === 'MCA') {
        // MBA/MCA: 1COURSE.html, 2COURSE.html, 1FCOURSE.html, 2FCOURSE.html
        $course_code = substr($folder, 0, 3);
        $configs = [
            "1$course_code" => ['year' => 'First Year', 'faculty' => "1F$course_code"],
            "2$course_code" => ['year' => 'Second Year', 'faculty' => "2F$course_code"],
            "1F$course_code" => ['year' => 'First Year (Female)', 'faculty' => "1$course_code"],
            "2F$course_code" => ['year' => 'Second Year (Female)', 'faculty' => "2$course_code"],
        ];
        
        foreach ($configs as $file_prefix => $config) {
            $filename = $file_prefix . '.html';
            $filepath = "$path/$filename";
            $faculty_link = $config['faculty'] . '.html';
            
            file_put_contents($filepath, generate_dashboard_html($display_name, $folder, $config['year'], $faculty_link));
            echo "Generated: $filename<br>";
        }
    } else {
        // Standard departments: 2DEPT, 2FDEPT, 3DEPT, 3FDEPT, 4DEPT, 4FDEPT
        $dept_code = substr($folder, 0, 3);
        if ($folder === 'EEE') $dept_code = 'EEE';
        if ($folder === 'CSE') $dept_code = 'CSE';
        
        $configs = [
            "2$dept_code" => ['year' => 'Second Year', 'faculty' => "2F$dept_code"],
            "2F$dept_code" => ['year' => 'Second Year (Female)', 'faculty' => "2$dept_code"],
            "3$dept_code" => ['year' => 'Third Year', 'faculty' => "3F$dept_code"],
            "3F$dept_code" => ['year' => 'Third Year (Female)', 'faculty' => "3$dept_code"],
            "4$dept_code" => ['year' => 'Fourth Year', 'faculty' => "4F$dept_code"],
            "4F$dept_code" => ['year' => 'Fourth Year (Female)', 'faculty' => "4$dept_code"],
        ];
        
        foreach ($configs as $file_prefix => $config) {
            $filename = $file_prefix . '.html';
            $filepath = "$path/$filename";
            $faculty_link = $config['faculty'] . '.html';
            
            file_put_contents($filepath, generate_dashboard_html($display_name, $folder, $config['year'], $faculty_link));
            echo "Generated: $filename<br>";
        }
    }
}

echo "<h2>✅ Dashboard generation complete!</h2>";
?>