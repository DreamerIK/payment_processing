<?php
require_once 'payment_processor.php';
require_once 'logger.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo 'Method not allowed';
    exit();
}

if (!$_POST) {
    http_response_code(400);
    echo 'Invalid payment data';
    exit();
}

$payment_data['firstname'] = $_POST['firstname'];
$payment_data['surname'] = $_POST['lastname'];
$payment_data['paymentDate'] = $_POST['paymentDate'];
$payment_data['payment_date'] = date('Y-m-d H:i:s', strtotime($payment_data['paymentDate'] ?? ''));
$payment_data['amount'] = $_POST['amount'];
$payment_data['description'] = $_POST['description'];
$payment_data['payment_reference'] = $_POST['refId'];


$payment_processor = new PaymentProcessor();
$result = $payment_processor->paymentProcess($payment_data);

if ($result == 0) {
    http_response_code(201);
    echo 'Payment successfully precessed.';
} elseif ($result == 1) {
    http_response_code(409);
    echo 'Duplicate entry.';
} else {
    http_response_code(400);
    echo 'Invalid payment data';
}

$logger = new Logger();
$logger->log("Status code: " . $result . ". Data:" . implode(',', $payment_data)).";";