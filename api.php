<?php
require_once 'db.php';

header('Content-Type: application/json');

// 1. Handle Vote Submission
if (isset($_POST['action']) && $_POST['action'] == 'vote') {
    $poll_id = intval($_POST['poll_id']);
    
    $stmt = $pdo->prepare("UPDATE event_polls SET votes = votes + 1 WHERE id = ?");
    if ($stmt->execute([$poll_id])) {
        // Fetch updated votes to send back to UI
        $stmt = $pdo->prepare("SELECT votes FROM event_polls WHERE id = ?");
        $stmt->execute([$poll_id]);
        $result = $stmt->fetch();
        echo json_encode(['status' => 'success', 'new_votes' => $result['votes']]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to register vote.']);
    }
    exit;
}

// 2. Handle Intake Form Submission
if (isset($_POST['action']) && $_POST['action'] == 'register_intake') {
    $name = strip_tags(trim($_POST['student_name']));
    $email = strip_tags(trim($_POST['student_email']));
    $club_id = intval($_POST['club_id']);
    $reason = strip_tags(trim($_POST['reason']));

    if (empty($name) || empty($email) || empty($club_id) || empty($reason)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    // Basic College Email Validation Guardrail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email address.']);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO intake_applications (student_name, student_email, club_id, reason) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$name, $email, $club_id, $reason])) {
        echo json_encode(['status' => 'success', 'message' => 'Application submitted successfully!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error. Please try again.']);
    }
    exit;
}