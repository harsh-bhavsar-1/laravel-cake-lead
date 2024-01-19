<?php

namespace LaravelCake\Lead\Repositories;

use LaravelCake\Lead\Models\Contact;
use LaravelCake\Lead\Models\ContactDetails;
use LaravelCake\Lead\Repositories\BaseRepository;
use Exception;

/**
 * Class ContactRepository
 *
 * @package LaravelCake\Lead\Repositories
 */
class ContactRepository extends BaseRepository
{
    protected $detailModel;
    /**
     * __construct
     *
     * @param  Contact  $model
     * @param  ContactDetails $detailModel
     * @return void
     */

    public function __construct(Contact $model, ContactDetails $detailModel)
    {
        $this->model = $model;
        $this->detailModel = $detailModel;
    }

    public function list(){
        try {
            $response = $this->model->with(['details'])->get();
            return $response;
        } catch (Exception $ex) {
            return false;
        }
    }

    public function store(array $data){
        try {
            $store = $this->model->create($data);
            if($store){
                $storeDetails = $store->details()->create($data);
                return $store;
            }
            return false;
        } catch (Exception $ex) {
            return false;
        }
    }

    public function update($id, $data){
        try {
            $contact = $this->model->findorFail($id);
            if(!$contact){
                return false;
            }
            $update = $contact->update($data);
            return $update;
        } catch (Exception $ex) {
            return false;
        }
    }

    public function createRedirectUrl($request, $inputs)
    {
        try{
            $URL = $request->header('referer');
            $RefererHost = parse_url($URL, PHP_URL_HOST);
            $RefererURI  = parse_url($URL, PHP_URL_PATH);
            $RefererQueryParam = parse_url($URL, PHP_URL_QUERY);
            $arrQueryParams    = array();
            parse_str($RefererQueryParam,$arrQueryParams);

            $phoneNumber = preg_replace('/\D+/', '', $inputs['primary_phone']);
            $arrParams   = [
                'tax_debt'      => $inputs['tax_debt'],
                'first_name'    => $inputs['first_name'],
                'last_name'     => $inputs['last_name'],
                'email_address' => $inputs['email_address'],
                'state'         => $inputs['state'],
                'primary_phone' => $phoneNumber,
                'error_post'=>1
            ];
            $arrQueryParams = array_merge($arrQueryParams,$arrParams);
            $postString     = http_build_query($arrQueryParams);
            $redirectURL    = "http://".$RefererHost.$RefererURI."?".$postString;
            return $redirectURL;
        } catch (Exception $ex) {
            return false;
        }
    }

    public function universalLeadData($inputs)
    {
        try{
            $fieldData['jornaya_leadid'] = $inputs['universal_leadid'];
            
            $data = "f_name;".$inputs['first_name']."|l_name;".$inputs['last_name']."|phone1;".$inputs['primary_phone']."|email;".$inputs['email_address'];
        
            $ch = curl_init('https://api.leadid.com/SingleQuery?lac=581E5A37-7A2C-A742-C313-6F515B2D3222&id='.$inputs['universal_leadid'].'&lak=DC38B41E-20A2-558B-9AF0-44E6A69452CB&lpc=03D25297-91B2-7DEA-48A9-88CEE9696E12&data='.$data.'&format=json');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            $result = curl_exec($ch);
            curl_close($ch);
            /*$arrJornayaResponse = json_decode($result,true);
            if(json_last_error() == JSON_ERROR_NONE){
                $fieldData['jornaya_leadid'] = $inputs['universal_leadid'];
            }*/
            if($result){
                return $result;
            }
            return false;
        }catch (Exception $ex) {
            return false;
        }
    }
}
