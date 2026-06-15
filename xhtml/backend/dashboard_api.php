<?php
header('Content-Type: application/json');
require_once __DIR__ . '/functions.php';

$pdo = pdo();
$data = [];

function getGrowth($pdo, $sqlCurrent, $sqlPrevious) {
    $curr = (float) $pdo->query($sqlCurrent)->fetchColumn();
    $prev = (float) $pdo->query($sqlPrevious)->fetchColumn();
    if ($prev == 0) return $curr > 0 ? 100 : 0;
    $growth = (($curr - $prev) / $prev) * 100;
    return min(100, max(-100, round($growth)));
}

try {
    // 1. Total Enquiries
    $data['total_enquiries'] = (int) $pdo->query("SELECT COUNT(*) FROM enquiries")->fetchColumn();
    $data['enquiries_growth'] = getGrowth($pdo,
        "SELECT COUNT(*) FROM enquiries WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
        "SELECT COUNT(*) FROM enquiries WHERE created_at >= DATE_SUB(NOW(), INTERVAL 60 DAY) AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)"
    );

    // 2. Media Gallery (replaces Active Classes)
    $data['total_gallery'] = (int) $pdo->query("SELECT COUNT(*) FROM gallery")->fetchColumn();
    $data['gallery_growth'] = getGrowth($pdo,
        "SELECT COUNT(*) FROM gallery WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
        "SELECT COUNT(*) FROM gallery WHERE created_at >= DATE_SUB(NOW(), INTERVAL 60 DAY) AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)"
    );

    // 3. Upcoming Events
    $data['total_events'] = (int) $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
    $data['events_growth'] = getGrowth($pdo,
        "SELECT COUNT(*) FROM events WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
        "SELECT COUNT(*) FROM events WHERE created_at >= DATE_SUB(NOW(), INTERVAL 60 DAY) AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)"
    );

    // 4. Published Blogs
    $data['total_blogs'] = (int) $pdo->query("SELECT COUNT(*) FROM blogs")->fetchColumn();
    $data['blogs_growth'] = getGrowth($pdo,
        "SELECT COUNT(*) FROM blogs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)",
        "SELECT COUNT(*) FROM blogs WHERE created_at >= DATE_SUB(NOW(), INTERVAL 60 DAY) AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)"
    );

    $data['enquiries_progress'] = $data['enquiries_growth'] == 0 && $data['total_enquiries'] > 0 ? 50 : abs($data['enquiries_growth']);
    $data['gallery_progress'] = $data['gallery_growth'] == 0 && $data['total_gallery'] > 0 ? 50 : abs($data['gallery_growth']);
    $data['events_progress'] = $data['events_growth'] == 0 && $data['total_events'] > 0 ? 50 : abs($data['events_growth']);
    $data['blogs_progress'] = $data['blogs_growth'] == 0 && $data['total_blogs'] > 0 ? 50 : abs($data['blogs_growth']);

    // 5. Recent Enquiries
    $stmt = $pdo->query("SELECT id, name, email, phone, created_at as booking_date, status, subject as class_name FROM enquiries ORDER BY id DESC LIMIT 5");
    $data['recent_registrations'] = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $parts = explode(' | ', $row['class_name']);
        
        // Handle "Event Registration: ..."
        $shortName = trim($parts[0] ?? 'General');
        if (strpos($shortName, 'Event Registration: ') === 0) {
            $shortName = substr($shortName, 20); // remove prefix
        }
        
        // If it's still too long, truncate it
        if (strlen($shortName) > 30) {
            $shortName = substr($shortName, 0, 27) . '...';
        }
        
        $row['class_name'] = $shortName;
        $data['recent_registrations'][] = $row;
    }

    // 6. Chart: Enquiries by Class (Donut)
    $stmt = $pdo->query("SELECT subject FROM enquiries");
    $class_counts = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $parts = explode(' | ', $row['subject']);
        $class_name = trim($parts[0] ?? 'General');
        
        // Handle "Event Registration: ..."
        if (strpos($class_name, 'Event Registration: ') === 0) {
            $class_name = substr($class_name, 20); // remove prefix
        }
        
        if (!isset($class_counts[$class_name])) $class_counts[$class_name] = 0;
        $class_counts[$class_name]++;
    }
    
    $data['chart_donut'] = [];
    foreach ($class_counts as $name => $count) {
        $data['chart_donut'][] = ['label' => $name, 'value' => $count];
    }
    if (empty($data['chart_donut'])) {
        $data['chart_donut'] = [['label' => 'Yogasanas', 'value' => 5], ['label' => 'Anandam', 'value' => 8], ['label' => 'Surya Kriya', 'value' => 3]];
    }

    // 7. Chart: Enquiries Trend (Bar)
    $data['chart_bar'] = [['y' => 'Jan', 'a' => 5, 'b' => 2], ['y' => 'Feb', 'a' => 8, 'b' => 5], ['y' => 'Mar', 'a' => 12, 'b' => 8]];
    $stmt = $pdo->query("SELECT DATE_FORMAT(created_at, '%b') as m, COUNT(*) as c FROM enquiries GROUP BY m");
    $dynamic_bar = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $dynamic_bar[] = ['y' => $row['m'], 'a' => $row['c'], 'b' => $row['c'] * 0.8];
    }
    if (count($dynamic_bar) > 0) $data['chart_bar'] = $dynamic_bar;

    echo json_encode(['success' => true, 'data' => $data]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
