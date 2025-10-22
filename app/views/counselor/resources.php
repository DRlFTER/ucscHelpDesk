<?php
// view_resources.php
session_start();
require_once('../../core/config.php');
$conn = new mysqli(DBHOST, DBUSER, DBPASSWORD, DBNAME, DBPORT);

if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

$counselor_name = "Mrs. Nalani Perera"; // Replace with session data

// Sample resources data (replace with database queries)
$resources = [
    [
        'id' => 1,
        'title' => 'Student Mental Health Guidelines',
        'description' => 'Comprehensive guide for handling student mental health issues and providing appropriate counseling support.',
        'category' => 'mental_health',
        'type' => 'pdf',
        'file_path' => '/resources/mental-health-guidelines.pdf',
        'created_at' => '2024-01-15',
        'created_by' => 'Dr. Sarah Williams',
        'downloads' => 45
    ],
    [
        'id' => 2,
        'title' => 'Academic Stress Management Techniques',
        'description' => 'Evidence-based techniques for helping students manage academic stress and improve study habits.',
        'category' => 'academic',
        'type' => 'video',
        'file_path' => 'https://youtube.com/watch?v=example',
        'created_at' => '2024-01-12',
        'created_by' => 'Prof. John Anderson',
        'downloads' => 32
    ],
    [
        'id' => 3,
        'title' => 'Crisis Intervention Protocols',
        'description' => 'Step-by-step protocols for handling crisis situations and emergency counseling procedures.',
        'category' => 'crisis',
        'type' => 'document',
        'file_path' => '/resources/crisis-intervention-protocols.docx',
        'created_at' => '2024-01-10',
        'created_by' => 'Dr. Maria Rodriguez',
        'downloads' => 28
    ],
    [
        'id' => 4,
        'title' => 'Peer Counseling Training Materials',
        'description' => 'Training materials for implementing peer counseling programs and managing student mentors.',
        'category' => 'training',
        'type' => 'zip',
        'file_path' => '/resources/peer-counseling-training.zip',
        'created_at' => '2024-01-08',
        'created_by' => 'Mrs. Nalani Perera',
        'downloads' => 19
    ],
    [
        'id' => 5,
        'title' => 'Cultural Sensitivity in Counseling',
        'description' => 'Best practices for providing culturally sensitive counseling services to diverse student populations.',
        'category' => 'diversity',
        'type' => 'pdf',
        'file_path' => '/resources/cultural-sensitivity-guide.pdf',
        'created_at' => '2024-01-05',
        'created_by' => 'Dr. Amith Kumar',
        'downloads' => 37
    ],
    [
        'id' => 6,
        'title' => 'Student Assessment Forms',
        'description' => 'Collection of assessment forms for evaluating student mental health and counseling needs.',
        'category' => 'assessment',
        'type' => 'zip',
        'file_path' => '/resources/assessment-forms.zip',
        'created_at' => '2024-01-03',
        'created_by' => 'Dr. Priya Sharma',
        'downloads' => 52
    ]
];

// Filter resources based on category
$selected_category = $_GET['category'] ?? 'all';
$search_query = $_GET['search'] ?? '';

$filtered_resources = $resources;

if ($selected_category !== 'all') {
    $filtered_resources = array_filter($resources, function($resource) use ($selected_category) {
        return $resource['category'] === $selected_category;
    });
}

if (!empty($search_query)) {
    $filtered_resources = array_filter($filtered_resources, function($resource) use ($search_query) {
        return stripos($resource['title'], $search_query) !== false || 
               stripos($resource['description'], $search_query) !== false;
    });
}

// Get categories for filter
$categories = [
    'all' => 'All Resources',
    'mental_health' => 'Mental Health',
    'academic' => 'Academic Support',
    'crisis' => 'Crisis Intervention',
    'training' => 'Training Materials',
    'diversity' => 'Diversity & Inclusion',
    'assessment' => 'Assessment Tools'
];

// Function to get file type icon
function getFileTypeIcon($type) {
    $icons = [
        'pdf' => '📄',
        'video' => '🎥',
        'document' => '📝',
        'zip' => '📁',
        'image' => '🖼️',
        'audio' => '🎵'
    ];
    return $icons[$type] ?? '📎';
}

// Function to get category badge color
function getCategoryColor($category) {
    $colors = [
        'mental_health' => '#e8f5e8',
        'academic' => '#e3f2fd',
        'crisis' => '#ffebee',
        'training' => '#fff3e0',
        'diversity' => '#f3e5f5',
        'assessment' => '#e0f2f1'
    ];
    return $colors[$category] ?? '#f5f5f5';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Resources - UCSC Help Desk</title>
    <link rel="stylesheet" href="../common/css/components.css">
    <link rel="stylesheet" href="coun.css">
    <link rel="stylesheet" href="counselor_dashboard.css">
    <style>
        .resources-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .search-filter-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .search-row {
            display: grid;
            grid-template-columns: 2fr 1fr auto;
            gap: 20px;
            align-items: end;
        }

        .search-group {
            display: flex;
            flex-direction: column;
        }

        .search-group label {
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }

        .search-input {
            padding: 12px 16px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        .search-input:focus {
            outline: none;
            border-color: #4285f4;
        }

        .category-select {
            padding: 12px 16px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 14px;
            background: white;
            cursor: pointer;
        }

        .search-btn {
            background: #4285f4;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .search-btn:hover {
            background: #3367d6;
        }

        .resources-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
        }

        .resource-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .resource-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .resource-header {
            padding: 20px 25px 15px;
            border-bottom: 1px solid #f0f0f0;
        }

        .resource-title {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
            line-height: 1.3;
        }

        .resource-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .file-type-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            background: #f8f9fa;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .category-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: #333;
        }

        .resource-description {
            color: #666;
            line-height: 1.5;
            font-size: 14px;
        }

        .resource-body {
            padding: 0 25px 20px;
        }

        .resource-footer {
            padding: 15px 25px;
            background: #fafbfc;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .resource-info {
            font-size: 12px;
            color: #666;
        }

        .resource-actions {
            display: flex;
            gap: 10px;
        }

        .download-btn {
            background: #34a853;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.2s;
        }

        .download-btn:hover {
            background: #2d8e47;
        }

        .view-btn {
            background: #4285f4;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.2s;
        }

        .view-btn:hover {
            background: #3367d6;
        }

        .stats-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #4285f4;
            margin-bottom: 8px;
        }

        .stat-label {
            color: #666;
            font-weight: 500;
        }

        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .no-results h3 {
            margin-bottom: 10px;
            color: #333;
        }

        .add-resource-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #ff4444;
            color: white;
            width: 60px;
            height: 60px;
            border: none;
            border-radius: 50%;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(255, 68, 68, 0.3);
            transition: transform 0.2s;
        }

        .add-resource-btn:hover {
            transform: scale(1.1);
        }

        @media (max-width: 768px) {
            .search-row {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .resources-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-section {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <!-- Include Navbar -->
    <?php include 'coun_navbar.html'; ?>

    <header>
        <h2>📚 Counseling Resources</h2>
        <p>Access and manage counseling materials and resources</p>
    </header>

    <div class="resources-container">
        <!-- Statistics Section -->
        <div class="stats-section">
            <div class="stat-card">
                <div class="stat-number"><?= count($resources) ?></div>
                <div class="stat-label">Total Resources</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= count($filtered_resources) ?></div>
                <div class="stat-label">Filtered Results</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= array_sum(array_column($resources, 'downloads')) ?></div>
                <div class="stat-label">Total Downloads</div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="search-filter-section">
            <form method="GET" action="">
                <div class="search-row">
                    <div class="search-group">
                        <label for="search">Search Resources</label>
                        <input type="text" id="search" name="search" class="search-input" 
                               placeholder="Search by title or description..." 
                               value="<?= htmlspecialchars($search_query) ?>">
                    </div>
                    
                    <div class="search-group">
                        <label for="category">Filter by Category</label>
                        <select id="category" name="category" class="category-select">
                            <?php foreach ($categories as $value => $label): ?>
                                <option value="<?= $value ?>" <?= $selected_category === $value ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="search-btn">🔍 Search</button>
                </div>
            </form>
        </div>

        <!-- Resources Grid -->
        <?php if (empty($filtered_resources)): ?>
            <div class="no-results">
                <h3>No Resources Found</h3>
                <p>Try adjusting your search criteria or browse all resources.</p>
                <a href="view_resources.php" style="color: #4285f4; text-decoration: none; font-weight: 600;">← Back to All Resources</a>
            </div>
        <?php else: ?>
            <div class="resources-grid">
                <?php foreach ($filtered_resources as $resource): ?>
                    <div class="resource-card">
                        <div class="resource-header">
                            <h3 class="resource-title"><?= htmlspecialchars($resource['title']) ?></h3>
                            <div class="resource-meta">
                                <span class="file-type-badge">
                                    <?= getFileTypeIcon($resource['type']) ?> <?= strtoupper($resource['type']) ?>
                                </span>
                                <span class="category-badge" style="background-color: <?= getCategoryColor($resource['category']) ?>">
                                    <?= ucfirst(str_replace('_', ' ', $resource['category'])) ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="resource-body">
                            <p class="resource-description"><?= htmlspecialchars($resource['description']) ?></p>
                        </div>
                        
                        <div class="resource-footer">
                            <div class="resource-info">
                                <div>By <?= htmlspecialchars($resource['created_by']) ?></div>
                                <div><?= date('M j, Y', strtotime($resource['created_at'])) ?> • <?= $resource['downloads'] ?> downloads</div>
                            </div>
                            <div class="resource-actions">
                                <a href="<?= htmlspecialchars($resource['file_path']) ?>" class="view-btn" target="_blank">👁️ View</a>
                                <a href="download_resource.php?id=<?= $resource['id'] ?>" class="download-btn">⬇️ Download</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Add Resource Button -->
        <button class="add-resource-btn" onclick="window.location.href='add_resource.php'" title="Add New Resource">
            ➕
        </button>
    </div>

    <script>
        // Search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search');
            const categorySelect = document.getElementById('category');
            
            // Auto-submit form when category changes
            categorySelect.addEventListener('change', function() {
                this.form.submit();
            });
            
            // Handle Enter key in search input
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    this.form.submit();
                }
            });
        });

        // Download tracking (you can implement this)
        function trackDownload(resourceId) {
            fetch('track_download.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({resource_id: resourceId})
            });
        }
    </script>
</body>
</html>