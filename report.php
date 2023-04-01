<?php
require_once 'storage.php';

$options = getopt('', ['date:']);
if (!isset($options['date'])){
    print("Usage: php report.php --date=YYYY-MM-DD");
    exit();
}
$date = $options['date'];
$date_obj = DateTime::createFromFormat('Y-m-d', $date);
if (!$date_obj || $date_obj->format('Y-m-d') !== $date) {
    print("Error: Wrong date format");
    exit();
}
$storage = new Storage();
$payments = $storage->getPaymentsByDate($date);

if (!$payments){
    print ("No payments for date: " . $date);
}else{
    print ("Payments for " . $date . ": \n");
    print ("______________________________ \n");

    foreach ($payments as $payment){
        print ("Payment reference: " . $payment['payment_reference'] . "\n");
        print ("Amount: " . $payment['amount'] . "\n");
        print ("Date: " . $payment['payment_date'] . "\n");
        print ("Description: " . $payment['description'] . "\n");
        print ("Status: " . $payment['status'] . "\n");
        print ("______________________________ \n");
    }
}