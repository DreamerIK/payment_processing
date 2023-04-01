<?php
require_once 'storage.php';
class Communication
{
    private $storage;
    public function __construct()
    {
        $this->storage = new Storage();
    }

    public function sendMessageToCustomer($customer_id, $title, $message = '')
    {
        $customer_info = $this->storage->getCustomerInfo($customer_id);
        if ($customer_info){
            if ($customer_info['email']){
                $this->sendMail($customer_info['email'], $title, $message);
            }
            if ($customer_info['phone']){
                $this->sendMessageToPhone($customer_info['phone'], $message);
            }
        }
    }

    public function sendFailedMessageToSupport($message = '')
    {
        $this->sendMail('support@example.com', 'Failed payment', $message);
    }

    private function sendMail($to, $subject, $message){
        $headers = 'From: system@example.com' . "\r\n" .
            'Reply-To: system@example.com' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        //mail($to, $subject, $message, $headers);
    }

    private function sendMessageToPhone($to, $message){
        //Data for telephone mailing of any service
        $sid = "ACCOUNT_SID";
        $token = "AUTH_TOKEN";

        $from = "COMPANY_PHONE_NUMBER";
        //send message to php=one
    }
}