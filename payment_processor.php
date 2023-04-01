<?php
require_once 'storage.php';
require_once 'communication.php';

class PaymentProcessor
{
    private $storage;

    public function __construct()
    {
        $this->storage = new Storage();
    }

    public function paymentProcess($payment_data): int
    {
        $communication = new Communication();
        $validation = $this->validatePaymentData($payment_data);
        if ($validation !== 0) {
            $communication->sendFailedMessageToSupport('Error code: ' . $validation . '. Payment reference = ' . $payment_data['payment_reference'] . '.');
            return $validation;
        }

        $payment_data['status'] = 'UNASSIGNED';

        $this->storage->savePayment($payment_data);


        $loan = $this->getLoan($payment_data);
        if (!$loan) {
            $this->storage->createRefundPaymentOrder($payment_data, $payment_data['amount']);
        } elseif ($payment_data['amount'] == $loan['amount_to_pay']) {
            $this->storage->markLoanAsPaid($loan['loan_number'], $payment_data['amount']);
            $this->storage->markPaymentStatus($payment_data['payment_reference'], 'ASSIGNED');

            $communication->sendMessageToCustomer($loan['customer_id'], 'Payment received', 'Payment received. Amount of payment = ' . $payment_data['amount'] . '.');
            $communication->sendMessageToCustomer($loan['customer_id'], 'Loan fully paid', 'Loan fully paid.');
        } elseif ($payment_data['amount'] > $loan['amount_to_pay']) {
            $this->storage->markLoanAsPaid($loan['loan_number'], $payment_data['amount']);
            $this->storage->markPaymentStatus($payment_data['payment_reference'], 'PARTIALLY_ASSIGNED');
            $refund_amount = $payment_data['amount'] - $loan['amount_to_pay'];
            $this->storage->createRefundPaymentOrder($payment_data, $refund_amount);

            $communication->sendMessageToCustomer($loan['customer_id'], 'Loan fully paid', 'Loan fully paid.');
            $communication->sendMessageToCustomer($loan['customer_id'], 'Payment received', 'Payment received. Amount of payment = ' . $payment_data['amount'] . '. Refund amount = ' . $refund_amount . '.');
        } else {
            $this->storage->updateLoanAmounts($loan['loan_number'], $payment_data['amount']);
            $this->storage->markPaymentStatus($payment_data['payment_reference'], 'ASSIGNED');

            $amount_to_pay = $loan['amount_to_pay'] - $payment_data['amount'];
            $communication->sendMessageToCustomer($loan['customer_id'], 'Payment received', 'Payment received. Amount of payment = ' . $payment_data['amount'] . '. Amount to pay for loan = ' . $amount_to_pay . '.');
        }
        return 0;
    }

    private function getLoan($payment_data)
    {
        preg_match('/LN[0-9]{8}/', $payment_data['description'], $matches);
        $loan_number = $matches[0] ?? null;
        if (!$loan_number) {
            return null;
        }
        return $this->storage->getLoanByNumber($loan_number);
    }

    private function validatePaymentData($payment_data): int
    {
        //check duplicate payment reference
        $exists_payment = $this->storage->getPaymentByReference($payment_data['payment_reference']);
        if ($exists_payment) {
            return 1;//duplicate entry
        }

        //check negative amount
        if ($payment_data['amount'] < 0) {
            return 2;//negative amount
        }

        //check valid date format
        $timestamp = strtotime($payment_data['paymentDate']);
        if (!$timestamp) {
            return 3;//invalid date
        }

        return 0;//All fine
    }
}