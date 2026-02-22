<?php
// Shared styles and utilities for feedback forms

function getHeadStyles() {
    return <<<'HTML'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .header h1 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .header p {
            font-size: 0.95rem;
            opacity: 0.9;
        }
        
        .container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
            font-family: inherit;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background-color: #f9f7ff;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .rating-group {
            background: #f9fafb;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            border-left: 4px solid #667eea;
        }
        
        .rating-group label {
            margin-bottom: 1rem;
        }
        
        .rating-options {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .rating-options input[type="radio"] {
            display: none;
        }
        
        .rating-options label {
            margin: 0;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            font-weight: 500;
        }
        
        .rating-options input[type="radio"]:checked + span {
            color: #667eea;
            font-weight: 700;
        }
        
        .rating-badge {
            display: inline-block;
            width: 40px;
            height: 40px;
            border: 2px solid #e5e7eb;
            border-radius: 50%;
            text-align: center;
            line-height: 36px;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 600;
        }
        
        .rating-options input[type="radio"]:checked + .rating-badge {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        
        .rating-badge:hover {
            border-color: #667eea;
            transform: scale(1.05);
        }
        
        .btn {
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            background: #e5e7eb;
            color: #333;
        }
        
        .btn-secondary:hover {
            background: #d1d5db;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 500;
            border-left: 4px solid;
        }
        
        .alert-success {
            background: #d1fae5;
            border-color: #10b981;
            color: #047857;
        }
        
        .alert-error {
            background: #fee2e2;
            border-color: #ef4444;
            color: #b91c1c;
        }
        
        .section-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #333;
            margin: 2rem 0 1.5rem 0;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #667eea;
        }
        
        .filter-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
        }
        
        .stat-box .number {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .stat-box .label {
            font-size: 0.85rem;
            opacity: 0.9;
        }
        
        .chart-container {
            position: relative;
            height: 400px;
            margin-bottom: 2rem;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
        }
        
        .data-table thead {
            background: #f3f4f6;
            border-bottom: 2px solid #667eea;
        }
        
        .data-table th {
            padding: 1rem;
            text-align: left;
            font-weight: 700;
            color: #333;
            font-size: 0.9rem;
        }
        
        .data-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .data-table tbody tr:hover {
            background: #f9fafb;
        }
        
        .data-table tbody tr:nth-child(even) {
            background: #fafbfc;
        }
        
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .badge-success {
            background: #d1fae5;
            color: #047857;
        }
        
        .badge-warning {
            background: #fef3c7;
            color: #b45309;
        }
        
        .badge-info {
            background: #dbeafe;
            color: #0369a1;
        }
        
        .back-link {
            display: inline-block;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            margin-top: 1rem;
            transition: all 0.2s;
        }
        
        .back-link:hover {
            color: #764ba2;
        }
        
        @media (max-width: 768px) {
            .header h1 {
                font-size: 1.5rem;
            }
            
            .card {
                padding: 1.5rem;
            }
            
            .filter-group {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .table-responsive {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
HTML;
}

function getFooter() {
    return <<<'HTML'
</body>
</html>
HTML;
}

?>
