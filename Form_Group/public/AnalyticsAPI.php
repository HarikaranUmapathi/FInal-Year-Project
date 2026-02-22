<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';
session_start();

// 1. Authorization Check
if (!isset($_SESSION['User'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Role-based access control (Basic implementation)
$role = $_SESSION['Role'] ?? '';
$allowed_roles = ['Faculty', 'Admin', 'Principal']; 
if (!in_array($role, $allowed_roles)) {
    echo json_encode(['error' => 'Access Denied']);
    exit;
}

// Role-based data filtering
$user_dept = $_SESSION['dept'] ?? '';
if ($role === 'Faculty' && $user_dept) {
    // Force filter to faculty's department
    $dept = $user_dept;
}

// 2. Request Parameters
$request_type = $_GET['type'] ?? 'aggregated'; // 'aggregated' or 'individual'
$feedback_form = $_GET['form'] ?? 'facilities'; // 'facilities', 'alumni', 'course_end', 'guest', 'parent', 'exit'
$dept = $_GET['dept'] ?? '';
$year = $_GET['year'] ?? '';
$section = $_GET['section'] ?? '';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';

// Map form type to table name
$table_map = [
    'facilities' => 'feedback_facilities',
    'alumni' => 'alumni_feedback',
    'course_end' => 'course_end_survey',
    'guest' => 'guest_feedback',
    'parent' => 'parent_feedback',
    'exit' => 'student_exit_feedback'
];

if (!array_key_exists($feedback_form, $table_map)) {
    echo json_encode(['error' => 'Invalid feedback form type']);
    exit;
}

$table = $table_map[$feedback_form];

// 3. Build Query
$where_clauses = [];
$params = [];
$types = '';

// Filter logic
if ($dept) {
    // Note: Column names might vary. Standardizing on 'branch' or 'department'. 
    // Need to check specific tables if column names differ.
    // facilities: branch, alumni: branch, course_end: department/branch?, guest: subject?, parent: branch_batch, exit: branch
    $col = ($table === 'course_end_survey') ? 'department' : (($table === 'parent_feedback') ? 'branch_batch' : 'branch');
    $where_clauses[] = "$col = ?";
    $params[] = $dept;
    $types .= 's';
}

if ($year) {
    // facilities: year, alumni: pass_year, course_end: year_sem, guest: N/A, parent: N/A, exit: year
    $col = ($table === 'alumni_feedback') ? 'pass_year' : (($table === 'course_end_survey') ? 'year_sem' : 'year');
    // Guest/Parent might not have year. Check schema if needed.
    if ($table !== 'guest_feedback' && $table !== 'parent_feedback') {
         $where_clauses[] = "$col = ?";
         $params[] = $year;
         $types .= 's'; // Year might be int or string. Using string to be safe.
    }
}

if ($section) {
    // Only if table has section
    if ($table !== 'guest_feedback') {
        $where_clauses[] = "section = ?";
        $params[] = $section;
        $types .= 's';
    }
}

// Date Range
if ($start_date) {
    $where_clauses[] = "submitted_at >= ?";
    $params[] = $start_date . ' 00:00:00';
    $types .= 's';
}
if ($end_date) {
    $where_clauses[] = "submitted_at <= ?";
    $params[] = $end_date . ' 23:59:59';
    $types .= 's';
}


$where_sql = '';
if (!empty($where_clauses)) {
    $where_sql = 'WHERE ' . implode(' AND ', $where_clauses);
}

// 4. Fetch Data
$response = [];

if ($request_type === 'aggregated') {
    // Calculate averages for question columns (q1, q2, ...)
    // Need to know how many questions.
    $q_counts = [
        'facilities' => 12,
        'alumni' => 22,
        'course_end' => 10,
        'guest' => 4,
        'parent' => 5,
        'exit' => 12
    ];
    
    $num_q = $q_counts[$feedback_form] ?? 0;
    $select_parts = [];
    for ($i=1; $i<=$num_q; $i++) {
        $select_parts[] = "AVG(q$i) as q$i";
    }
    $select_sql = implode(', ', $select_parts);
    
    $stmt = $conn->prepare("SELECT $select_sql FROM $table $where_sql");
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    // Format for Chart.js
    $labels = [];
    $data = [];
    for ($i=1; $i<=$num_q; $i++) {
        $labels[] = "Q$i";
        $data[] = round((float)($row["q$i"] ?? 0), 2);
    }
    
    $response = [
        'labels' => $labels,
        'data' => $data,
        'count' => 0 // TODO: Get count
    ];
    
} elseif ($request_type === 'individual') {
    // 1. Define Columns & Max Questions per Table
    // Standard Output: id, name, department, year, section, reg_no, date, score
    
    $cols = "";
    $date_col = "submitted_at"; // Default
    
    switch ($table) {
        case 'feedback_facilities': // StudentFeedBackFormFaculty.php
            $cols = "id, name, branch as department, year, section, reg_no, submitted_at, 'Facilities' as type";
            break;
        case 'alumni_feedback':
            $cols = "id, name, branch as department, pass_year as year, section, reg_no, submitted_at, 'Alumni' as type";
            break;
        case 'course_end_survey':
            $cols = "id, student_name as name, department, year_sem as year, section, reg_no, submitted_at, 'CourseEnd' as type";
            break;
        case 'student_exit_feedback':
            $cols = "id, name, branch as department, year, section, reg_no, submitted_at, 'Exit' as type";
            break;
        case 'guest_feedback': // Assuming simplified columns
            $cols = "id, guest_name as name, 'N/A' as department, 'N/A' as year, section, reg_no, created_at as submitted_at, 'Guest' as type";
            break;
        case 'parent_feedback':
           $cols = "id, student_name as name, branch_batch as department, 'N/A' as year, section, reg_no, submitted_at, 'Parent' as type";
           break;
        default:
            $cols = "*";
    }

    // Question Counts for Score Calculation
    $q_counts = [
        'facilities' => 12,
        'alumni' => 22,
        'course_end' => 10,
        'guest' => 4, // Verify if needed
        'parent' => 5,
        'exit' => 12
    ];
    $num_q = $q_counts[$feedback_form] ?? 0;
    
    // Select Questions
    $q_select = [];
    for ($i=1; $i<=$num_q; $i++) $q_select[] = "q$i";
    $q_sql_part = !empty($q_select) ? ', ' . implode(',', $q_select) : '';

    $sql = "SELECT $cols $q_sql_part FROM $table $where_sql ORDER BY id DESC LIMIT 200";

    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $rows = [];
    while ($r = $result->fetch_assoc()) {
        // Calculate Score
        $sum = 0;
        $count = 0;
        for ($i=1; $i<=$num_q; $i++) {
            if (isset($r["q$i"]) && is_numeric($r["q$i"])) {
                $sum += (float)$r["q$i"];
                $count++;
            }
            unset($r["q$i"]); // Remove q cols from final output to keep it clean
        }
        
        // Score = (Sum / (Count * 5)) * 100
        $score_pct = ($count > 0) ? round(($sum / ($count * 5)) * 100, 1) : 0;
        $r['score'] = $score_pct;
        
        // Format Date
        $r['date'] = isset($r['submitted_at']) ? date('Y-m-d H:i', strtotime($r['submitted_at'])) : 'N/A';
        unset($r['submitted_at']);

        $rows[] = $r;
    }
    $response = ['submissions' => $rows];
}

echo json_encode($response);
