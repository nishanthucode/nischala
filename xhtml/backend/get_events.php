<?php
require_once __DIR__ . '/functions.php';
header('Content-Type: application/json');

$events = backend_fetch_all('events');
$formattedEvents = [];

foreach ($events as $event) {
    $formattedEvents[] = [
        'id' => $event['id'],
        'title' => $event['title'],
        'start' => $event['event_date'] . ($event['event_time'] ? 'T' . $event['event_time'] : ''),
        'url' => 'edit-events.html?id=' . $event['id'],
        'className' => 'bg-primary'
    ];
}

echo json_encode($formattedEvents);
