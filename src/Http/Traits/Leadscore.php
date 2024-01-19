<?php
namespace LaravelCake\Lead\Http\Traits;

trait Leadscore {

    private $url = 'http://leadscoreapi.px.com/px';
    private $PartnerToken = '7924B009-411A-4ECE-B91D-B54828E988FB';

    // public function __construct() {
    //     $this->PartnerToken = '7924B009-411A-4ECE-B91D-B54828E988FB';
    // }

    public function validatePhone1($phoneNumber,$first_name,$last_name,$email,$state,$zipcode,$fullresponse) {
        $response = $this->makeRequest1($phoneNumber,$first_name,$last_name,$email,$state,$zipcode);
        if ($response) {
        	if ($fullresponse === false) {
            	$result = $this->decodeResponse1($response);
                return $this->testResult1($result);
            } else {
            	$result = $this->decodeResponse1($response);
                $final_result = true;//$this->testResult($result);
                $response = $result[2];
                return compact('final_result', 'response');
            }
        }
        return 0;
    }

    private function decodeResponse1($response) {
        $xml = simplexml_load_string($response);

        if (!empty($xml->response->result->value)) {
            $result = explode(',', $xml->response->result->value);
            return $result;
        }
        return 0;
    }

    private function testResult1($result) {
        // We're going to hard-code our pass/fail for now
        // Decisions based on values found in Element_ID_1320.pdf

        /*   if($result[2] != 'B' && strpos($result[3], 'A') !== false) {
          return 1;
          } */
        return true;
    }

    private function makeRequest1($phoneNumber,$first_name,$last_name,$email,$state,$zipcode) {

        // initiate curl and set options
        $fields = array(
            "PartnerToken" => $this->PartnerToken,
            //"Type" => "jsonwsp/request",
            "BaeReplyType"=>"json",
            "Version" => "1.0",
            "MethodName" => "Lead.GetLeadScore",
            "IsRawResponse" =>1,
            "FirstName" => $first_name,
            "LastName" => $last_name,
            "Address" => "2700 Madonna Dr",
            "City"=>"Fullerton",
            "State" => $state!=""?$state:"CA",
            "ZipCode"=>$zipcode!=""?$zipcode:"92835",
            "EmailAddress" => $email,
            "PhoneNumber" => $phoneNumber,
            "Country" => "US",
            "IpAddress" => "255.255.255.255"
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($fields)))
        );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $data = curl_exec($ch);
        $headers = curl_getinfo($ch);

        // close curl
        curl_close($ch);

        // return XML data
        if ($headers['http_code'] != '200') {
            return false;
        } else {
            return($data);
        }
    }

}
