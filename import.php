<?php
require_once 'payment_processor.php';
require_once 'logger.php';

$options = getopt('', ['file:']);
if (!isset($options['file'])){
    print("Usage: php import.php --file=<path_to_cvs_file>");
    exit();
}

$filePath = $options['file'];

if (!file_exists($filePath)) {
    print('File not found');
    exit();
}

if (pathinfo($filePath, PATHINFO_EXTENSION) !== 'csv') {
    print('Wrong file format');
    exit();
}


$file = fopen($filePath, 'r');
$i = 0;
$keys = [];
$payments = [];

$payment_processor = new PaymentProcessor();
$logger = new Logger();
while ($data = fgetcsv($file)) {
    if ($i == 0) {
        $keys = $data;
        $i++;
        continue;
    }
    $payment_data = array_combine($keys, $data);

    $payment_data['firstname'] = $payment_data['payerName'];
    $payment_data['surname'] = $payment_data['payerSurname'];
    $payment_data['payment_date'] = date('Y-m-d H:i:s', strtotime($payment_data['paymentDate']));
    $payment_data['payment_reference'] = $payment_data['paymentReference'];

    $status_code = $payment_processor->paymentProcess($payment_data);
    $message = "Status code:" . $status_code . ". Data: " . implode(",", $payment_data);
    print($message . "\n");
    $logger->log($message);
}

fclose($file);

