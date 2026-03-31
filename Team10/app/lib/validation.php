<?php

function validateLogin($email, $password) {
    $errors = [];

    // Sanitize and validate email
    $email = trim($email);
    if (empty($email)) {
        $errors['loginEmail'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['loginEmail'] = "Please enter a valid email address.";
    }

    // Validate password presence
    if (empty(trim($password))) {
        $errors['loginPassword'] = "Password is required.";
    }

    return $errors; // Returns an array of errors, empty if valid
}

function validateContact($name, $email) {
    $errors = [];

    // Validate name (required field)
    if (empty(trim($name))) {
        $errors['contactName'] = "Contact name is required.";
    }

    // Validate email (optional, but must be valid if provided)
    $email = trim($email);
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['contactEmail'] = "If provided, the email must be valid.";
    }

    return $errors;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Check which form was submitted
    $formType = $_POST['formType'] ?? '';

    if ($formType === 'login') {
        $email = $_POST['loginEmail'] ?? '';
        $password = $_POST['loginPassword'] ?? '';
        
        $errors = validateLogin($email, $password);
        
        if (empty($errors)) {
            // Validation passed. Next: Check database for user.
            echo json_encode(['success' => true, 'message' => 'Login validated successfully']);
        } else {
            // Validation failed. Return errors to frontend.
            echo json_encode(['success' => false, 'errors' => $errors]);
        }
        exit;
    }

    if ($formType === 'contact') {
        $name = $_POST['contactName'] ?? '';
        $email = $_POST['contactEmail'] ?? '';
        
        $errors = validateContact($name, $email);
        
        if (empty($errors)) {
            // Validation passed. Next: Insert contact into database.
            echo json_encode(['success' => true, 'message' => 'Contact validated successfully']);
        } else {
            // Validation failed. Return errors to frontend.
            echo json_encode(['success' => false, 'errors' => $errors]);
        }
        exit;
    }
}
?>