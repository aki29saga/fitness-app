<?php
require 'includes/config.php';
require 'includes/db.php';
require 'includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit();
}

// Get and validate POST data
$data = [
    'first_name' => trim($_POST['first_name'] ?? ''),
    'last_name' => trim($_POST['last_name'] ?? ''),
    'email' => filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL),
    'phone' => trim($_POST['phone'] ?? ''),
    'program_type_id' => intval($_POST['program_type_id'] ?? 0),
    'location_id' => intval($_POST['location_id'] ?? 0),
    'schedule_id' => intval($_POST['schedule_id'] ?? 0),
    'latitude' => floatval($_POST['latitude'] ?? 0),
    'longitude' => floatval($_POST['longitude'] ?? 0),
    'amount_paid' => floatval($_POST['amount_paid'] ?? 0)
];

// Basic validation
if (empty($data['first_name']) || empty($data['last_name']) || empty($data['email']) || 
    empty($data['phone']) || $data['program_type_id'] <= 0 || $data['location_id'] <= 0 || 
    $data['schedule_id'] <= 0) {
    echo json_encode(['success' => false, 'error' => 'All fields are required']);
    exit();
}

if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid email address']);
    exit();
}

// Process the booking
$result = processBooking($data);

// In a real application, you would send a confirmation email here
// sendBookingConfirmation($data['email'], $result['booking_id']);

echo json_encode($result);