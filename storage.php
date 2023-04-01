<?php
class Storage{
    private $db;

    public function __construct()
    {
        $this->db = new SQLite3('payments.db');
    }

    public function savePayment($payment_data)
    {
        $sql_query = "INSERT INTO payments(firstname, surname, payment_date, amount, description, payment_reference, status)
                        VALUES (:firstname, :surname, :payment_date, :amount, :description, :payment_reference, :status)";
        $stmt = $this->db->prepare($sql_query);
        $stmt->bindValue('firstname', $payment_data['firstname']);
        $stmt->bindValue('surname', $payment_data['surname'] );
        $stmt->bindValue('payment_date', $payment_data['payment_date']);
        $stmt->bindValue('amount', $payment_data['amount']);
        $stmt->bindValue('description', $payment_data['description']) ?? '';
        $stmt->bindValue('payment_reference', $payment_data['payment_reference']);
        $stmt->bindValue('status', $payment_data['status']);
        $stmt->execute();
    }

    public function getPaymentByReference($payment_reference)
    {
        $stmt = $this->db->prepare('SELECT * FROM payments WHERE payment_reference = :payment_reference');
        $stmt->bindValue(':payment_reference', $payment_reference);
        $result = $stmt->execute();
        return $result->fetchArray();
    }

    public function getLoanByNumber($loan_number)
    {
        $stmt = $this->db->prepare('SELECT * FROM loans WHERE loan_number = :loan_number');
        $stmt->bindValue('loan_number', $loan_number);
        $result = $stmt->execute();
        return $result->fetchArray();
    }

    public function createRefundPaymentOrder($payment_data, $refund_amount)
    {
        $sql_query = "INSERT INTO payments(firstname, surname, payment_date, amount, description, payment_reference, status)
                        VALUES (:firstname, :surname, :payment_date, :amount, :description, :payment_reference, :status)";
        $stmt = $this->db->prepare($sql_query);
        $stmt->bindValue('firstname', $payment_data['firstname']);
        $stmt->bindValue('surname', $payment_data['surname'] );
        $stmt->bindValue('payment_date', $payment_data['payment_date']);
        $stmt->bindValue('amount', $refund_amount);
        $stmt->bindValue('description', 'Refund for '. $payment_data['payment_reference']);
        $stmt->bindValue('payment_reference', $payment_data['payment_reference'].'_refund');
        $stmt->bindValue('status', 'REFUND');
        $stmt->execute();
    }

    public function markLoanAsPaid($loan_number, $amount)
    {
        $stmt = $this->db->prepare("UPDATE loans SET status = 'PAID', amount_issued = :amount, amount_to_pay = 0 WHERE loan_number = :loan_number");
        $stmt->bindValue('loan_number', $loan_number);
        $stmt->bindValue('amount', $amount);
        $stmt->execute();
    }

    public function updateLoanAmounts($loan_number, $amount)
    {
        $stmt = $this->db->prepare("UPDATE loans SET amount_issued = amount_issued + :amount, amount_to_pay = amount_to_pay - :amount WHERE loan_number = :loan_number");
        $stmt->bindValue('loan_number', $loan_number);
        $stmt->bindValue('amount', $amount);
        $stmt->execute();
    }

    public function markPaymentStatus($payment_reference, $status)
    {
        $stmt = $this->db->prepare("UPDATE payments SET status = :status WHERE payment_reference = :payment_reference");
        $stmt->bindValue('payment_reference', $payment_reference);
        $stmt->bindValue('status', $status);
        $stmt->execute();
    }

    public function getPaymentsByDate($date)
    {
        $stmt = $this->db->prepare('SELECT * FROM payments WHERE date(payment_date) = :date');
        $stmt->bindValue('date', $date);
        $result = $stmt->execute();

        $payments = [];
        while ($payment = $result->fetchArray()){
            $payments[] = $payment;
        }
        return $payments;
    }

    public function getCustomerInfo($customer_id)
    {
        $sql_query = 'SELECT * FROM customers WHERE id = :id';
        $stmt = $this->db->prepare($sql_query);
        $stmt->bindValue('id', $customer_id);
        $result = $stmt->execute();
        return $result->fetchArray();
    }
}