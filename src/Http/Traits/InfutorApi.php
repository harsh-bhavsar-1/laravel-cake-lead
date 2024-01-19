<?php
namespace LaravelCake\Lead\Http\Traits;

trait InfutorApi
{
    public function infutor_API($params) {
        $curl = curl_init();
        $username = 'for86849';
        $password = '43SDXR3SK$MB8Ct';

        $query = http_build_query(
                array(
                'Login' => $username,
                'Password'=> $password,
                'FullName'=> $params['first_name']." ".$params['last_name'],
                'FName'=> $params['first_name'],
                'LName'=> $params['last_name'],
                'Phone'=> $params['primary_phone'],
                'Email'=> $params['email_address']
                )
            );
        curl_setopt_array($curl, array(
            //CURLOPT_URL => "https://api.yourdatadelivery.com/service/rest/IDProfileProperty?".$query."&=",
            CURLOPT_URL => "https://api.yourdatadelivery.com/IDComplete?".$query."&=",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
}

?>
