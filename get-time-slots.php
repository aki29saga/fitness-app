<?php
require 'includes/config.php';
require 'includes/db.php';
require 'includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit();
}

$programId = intval($_GET['program_id'] ?? 0);
$locationId = intval($_GET['location_id'] ?? 0);
$date = $_GET['date'] ?? '';

if ($programId <= 0 || $locationId <= 0 || empty($date)) {
    echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
    exit();
}

try {
    $slots = getAvailableTimeSlots($programId, $locationId, $date);
    
    $formattedSlots = array_map(function($slot) {
        return [
            'id' => $slot['schedule_id'],
            'time' => date('h:i A', strtotime($slot['start_time'])) . ' - ' . 
                     date('h:i A', strtotime($slot['end_time'])),
            'trainer' => $slot['first_name'] . ' ' . $slot['last_name'],
            'available' => $slot['available_slots']
        ];
    }, $slots);
    
    echo json_encode(['success' => true, 'slots' => $formattedSlots]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error']);
}