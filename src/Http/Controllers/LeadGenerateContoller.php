<?php

namespace LaravelCake\Lead\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Session;
use LaravelCake\Lead\Contact as LeadContact;
use LaravelCake\Lead\Services\ContactService;
use LaravelCake\Lead\Http\Traits\Leadscore;
use LaravelCake\Lead\Http\Traits\Leadscore2;
use LaravelCake\Lead\Http\Traits\InfutorApi;
use LaravelCake\Lead\Models\Contact;
use LaravelCake\Lead\Models\ScrambleAffilateId;
use LaravelCake\Lead\Models\ScrambleSubId;
use LaravelCake\Lead\Models\ScrambleSubId2;
use LaravelCake\Lead\Services\ContactInfutorSummaryService;

class LeadGenerateContoller extends Controller
{
    use Leadscore, Leadscore2, InfutorApi;
    protected $contact, $contactModel, $contactInfutorSummary;
    /**
     * __construct
     *
     * @param ContactService $contact
     * @return void
     */
    public function __construct(ContactService $contact, Contact $contactModel,ContactInfutorSummaryService $contactInfutorSummary)
    {
        $this->contact = $contact;
        $this->contactInfutorSummary = $contactInfutorSummary;
        $this->contactModel = $contactModel;
    }
    /**
     * Display a listing of the resource.
     */
    public function index( Request $request)
    {
        try {
            $list = $this->contact->list();
            return view('cakelead.index', compact('list'));
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $inputs = $request->all();
        $config = config('leadgenerate.inputs');
        return view('cakelead.create', compact('inputs', 'config'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $arr = [
                'first_name' => 'Alex',
                'last_name' => 'Hell',
            ];
            $b = new LeadContact;
            $b->store($arr);
            $inputs = $request->all();
            $fieldData = [];
            $commonFields = $this->contactModel::commonFields;
            $configFields = $this->contactModel::configFields;
            $fields = array_merge($commonFields, $configFields);
            if(is_null($inputs['tax_debt']) || is_null($inputs['email_address']) || is_null($inputs['primary_phone'])){
                if (!is_null($request->header('referer'))) {
                    $this->contact->createRedirectUrl($request, $inputs);
                }
                else{
                    dd('redirect');
                }
            }
            if($request->ip() == '14.102.161.106'){
                $inputs['state'] = 'CA';
            }
            if(isset($inputs['current_situation']) && is_array($inputs['current_situation'])){
                $inputs['current_situation']=implode(",",$inputs['current_situation']);
            }

            foreach ($fields as $key =>  $field) {
                if (!empty($inputs[$field])) {
                    $fieldData[$field] = $inputs[$field];
                    continue;
                }
                // this lets us handle values that we want but didn't get for some reason
                switch ($field) {
                    // this handles pages that submit 'ckm_request_id' instead of 'reqid'
                    case 'reqid':
                        if (!empty($inputs['ckm_request_id']))
                            $fieldData[$field] = $inputs['ckm_request_id'];
                        break;
                    default:
                        break;
                }
                // end empty field processing
            }
            
            $fieldData['melissa'] = "";
            $fieldData['neustar'] = "";

            if(!isset($inputs['zipcode'])){
                $inputs['zipcode']="";
            }

            if ($inputs['tax_debt'] > 9999){
                $check = $this->validatePhone1($inputs['primary_phone'],$inputs['first_name'],$inputs['last_name'],$inputs['email_address'],$inputs['state'],$inputs['zipcode'], true);
                if($check!=0) {
                    $fieldData['neustar_disposition'] = $check['response'];
                }
            }

            $nd_odlv = "";
            if ($inputs['tax_debt'] > 9999)
            {
                if(!isset($inputs['zipcode']))
                {
                    $inputs['zipcode']="";
                }
                $check = $this->validatePhone2($inputs['primary_phone'],$inputs['first_name'],$inputs['last_name'],$inputs['email_address'],$inputs['state'],$inputs['zipcode'], true);

                if($check!=0) {
                    $nd_odlv = $check['response'];
                }

                $fieldData['neustar'] = 'pass';

                if(isset($fieldData['neustar_disposition']) && trim($fieldData['neustar_disposition'],",")!="") {
                    $fieldData['neustar'] = 'fail';
                    $ns_disposition = $fieldData['neustar_disposition'];
                    if(strpos($ns_disposition,"I")!== FALSE || strpos($ns_disposition,",1") !== FALSE || strpos($ns_disposition,",2") !== FALSE || strpos($ns_disposition,",3") !== FALSE || strpos($ns_disposition,",4") !== FALSE || $nd_odlv == 26 || $nd_odlv == 31) {
                        $fieldData['neustar'] = 'fail';
                    } else {
                        $fieldData['neustar'] = 'pass';
                    }
                }else if(isset($fieldData['neustar_disposition']) && $fieldData['neustar_disposition']==",,,,") {
                    $fieldData['neustar'] = 'fail';
                }
            }
            $infutorResponce = $this->infutor_API($inputs);

            $xml = new \SimpleXMLElement($infutorResponce);
            $infutorResponceArray = json_decode(json_encode($xml), true);

            $infutorData = Contact::getInfutorData($infutorResponceArray,$inputs);
            $fieldData = array_merge($fieldData, $infutorData['fieldData']);

            // Infutor implemnet end
            setcookie('tax_debt', $inputs['tax_debt'], 0, "/");
            setcookie('first_name', $inputs['first_name'], 0, "/");
            setcookie('last_name', $inputs['last_name'], 0, "/");
            setcookie('primary_phone', $inputs['primary_phone'], 0, "/");
            setcookie('email_address', $inputs['email_address'], 0, "/");
            setcookie('state', $inputs['state'], 0, "/");

            if(isset($inputs['opt_special_offers']) && $inputs['opt_special_offers']=="checked") {
                $fieldData['email_optin_offers']='checked';
            }

            if(isset($inputs['opt_in']) && ($inputs['opt_in']=="checked" || $inputs['opt_in']=="on")) {
                $fieldData['opt_in']='checked';
                if(!isset($inputs['opt_special_offers'])) {
                    $fieldData['email_optin_offers']='checked';
                }
            }
            $fieldData['opt_in']='checked';
            $fieldData['TCPA_checkbox']='checked';
            // $fieldData['zip_code']= $inputs['zip_code'];
            $fieldData['user_agent'] = $request->header('User-Agent');
            $fieldData['ip_address'] = ip2long($request->ip());
            $page = parse_url($request->header('referer'), PHP_URL_PATH);
            $fieldData['page'] = $page;
            $query = parse_url($request->header('referer'), PHP_URL_QUERY);
            $fieldData['query_string'] = $query;
            if($fieldData['neustar'] == 'fail' && $fieldData['infutor_status'] == 'fail'){
                $fieldData['neustar_infutor_score'] = 'fail';
            } else {
              $fieldData['neustar_infutor_score'] = 'pass';
            }
            $fieldData['nodl_flag'] = 'pass';
            if((!empty($nd_odlv) && ($nd_odlv == 11 || $nd_odlv == 30 || $nd_odlv == 3 || $nd_odlv == 28)) && $fieldData['neustar'] == 'fail' ){
                $fieldData['nodl_flag'] = 'fail';
            }
            if(!empty($inputs['universal_leadid'])) {
                $this->contact->universalLeadData($inputs);
            }
            $fieldData['debt_type']=$inputs['debt_type'] ?? "";
            $fieldData['back_taxes_years']=$inputs['back_taxes_years'] ?? "";
            $fieldData['employment']=$inputs['employment'] ?? "";
            $fieldData['payment_plan']=$inputs['payment_plan'] ?? "";
            $fieldData['domain']= 'FSI';
            $fieldData['submitted'] = 1;
            if($inputs['LeadRouting']=="fsi22") {
                if(($fieldData['enrolled_irs'] == 'yes') && ($fieldData['employment'] == 'unemployed' || $fieldData['employment'] == 'retired' || $fieldData['employment'] == 'employed')){
                    $fieldData['lead_status'] = 'fail';
                } else {
                  $fieldData['lead_status'] = 'pass';
                }
            }
            if($inputs['LeadRouting']=="fsi-lf1") {
                if(($fieldData['payment_plan'] == 'Yes') && ($fieldData['employment'] == 'Other' || $fieldData['employment'] == 'Unemployed')){
                    $fieldData['lead_status'] = 'fail';
                } else {
                  $fieldData['lead_status'] = 'pass';
                }
            }
            
            //session code issue
            // if (!isset($_SESSION['insertID'])) {
            //     $result = $DB->insert('contacts', $fieldData);
            //     $last_insert_id = $DB->insertID;
            //     $_SESSION['insertID'] = $last_insert_id;
            // } else {
            //     $fieldData['RAW'] = 'submit_attempts = submit_attempts + 1';
            //     $DB->update('contacts', $fieldData, ' id=' . $_SESSION['insertID']);
            // }
            
            // $Infutor_data['contact_id'] = $_SESSION['insertID'];
            $Infutor_data['email_address'] = $fieldData['email_address'];
            $Infutor_data['primary_phone'] = $fieldData['primary_phone'];
            $Infutor_data['summary'] = $infutorData['infutorResponce'];
            // $contactInfutorSummaryStore = $this->contactInfutorSummary->store($Infutor_data);
            // setcookie('insertID', $_SESSION['insertID'], 0, "/");

            $fieldData['nd_odlv'] = $nd_odlv;

            if(isset($inputs['page']) && $inputs['page']!="") {
                $fieldData['page'] = $inputs['page'];
            }else {
                $page = $request->header('referer');
                $fieldData['page'] = $page;
            }
            $fieldData['ip_address'] = $request->ip();
            $passThruFields = $this->contactModel::passThruFields;
            foreach ($passThruFields as $field) {
                if (!empty($inputs[$field])) {
                    $fieldData[$field] = $inputs[$field];
                    continue;
                }
            }
            
            unset($fieldData['RAW']);
            $fieldData['TCPA_checkbox'] = 'checked';
            $fieldData['landing_page'] = 'fsi';
            if($inputs['tax_debt'] <= 9999)
            $fieldData['ckm_bp'] = '1';
            if(isset($fieldData['neustar']) && $fieldData['neustar']=="fail")
            $fieldData['ckm_bp'] = '1';
            $fieldData['ckm_bp'] = '1';
            if($inputs['tax_debt'] > 9999) {
                $fieldData['cr_price'] = '0.00';
            }
            if($inputs['LeadRouting']=='fsi14'){
                $fieldData['cr_price'] = '3';
           }

            if (isset($inputs['opt_special_offers']) && $inputs['opt_special_offers'] == "checked") {
                $fieldData['email_optin_offers'] = 'checked';
            }
            if(isset($inputs['opt_in']) && ($inputs['opt_in']=="checked" || $inputs['opt_in']=="on")) {
                $fieldData['opt_in']='checked';
                if(!isset($inputs['opt_special_offers'])) {
                    $fieldData['email_optin_offers']='checked';
                }
            }
            $fieldData['alt_subid'] = 100;
            if(!empty($inputs['s1'])) {
                $arrsubid = ScrambleSubId::where('sub_id', $inputs['s1'])->orderBy('id')->get()->toArray();
                if(!empty($arrsubid)){
                    $fieldData['alt_subid'] = $arrsubid[0]['scramble_sub_id'];
                }
            }
            $fieldData['alt_affid'] = 100;
            if(!empty($inputs['affid'])) {
                $arrsubid = ScrambleAffilateId::where('sub_id', $inputs['affid'])->orderBy('id')->get()->toArray();
                if(!empty($arrsubid)){
                    $fieldData['alt_affid'] = $arrsubid[0]['scramble_affilate_id'];
                }
            }
            $fieldData['alt_s2'] = 100;
            if(!empty($inputs['s2'])) {
                $arrsubid = ScrambleSubId2::where('sub_id2', $inputs['s2'])->orderBy('id')->get()->toArray();
                if(!empty($arrsubid)){
                    $fieldData['alt_s2'] = $arrsubid[0]['scramble_sub_id2'];
                }
            }
            
            // $affidSubidScore = file_get_contents("http://www.freshstart-initiative.net/drips/get_affid_subid_score.php?aff_id=".$inputs['affid']."&sub_id=".$inputs['s1']);

            if (isset($affidSubidScore) && !empty($affidSubidScore)) {
                $fieldData['affid_subid_score'] = $affidSubidScore;
            }
            
            $fieldData['first_name_capital']= ucwords(strtolower($inputs['first_name']));
            $fieldData['last_name_capital']= ucwords(strtolower($inputs['last_name']));

            $ckmData = Contact::getCkmData($inputs);
            $fieldData = array_merge($fieldData, $ckmData);
            
            dd($fieldData);
            // $postString = http_build_query($fieldData);
            // $postURL = 'http://flmtrk.com/d.ashx';
            // $ch = curl_init($postURL);
            // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            // curl_setopt($ch, CURLOPT_POST, count($fieldData));
            // curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // $curlInfo = curl_getinfo($ch);
            // $curlResult = curl_exec($ch);

            // $this->centralLeadCall($fieldData);

            /* Maildrill send thank you mail start here */
            if($inputs['LeadRouting']=="FSI_20"){ }else{
                $visitor_data['email_address']=$inputs['email_address'];
                $visitor_data['recepient_name']=$inputs['first_name'];
                $this->_sendthank_mail($visitor_data);    
            }
            /* Maildrill send thank you mail start here */

            // $updateCakeResponse = array();
            // $updateCakeResponse['cake_response'] = $curlResult;
            // $this->contact->update($_SESSION['insertID'], $updateCakeResponse);

            if (stripos($curlResult, 'html') !== false) {
                preg_match('/<a href="(.+)">/', $curlResult, $match);
                $newUrl = str_replace('#', '', $match[1]);
                $url_components = parse_url($newUrl);
                parse_str($url_components['query'], $params);
                $dom = new \DOMDocument;
                $dom->loadHTML($curlResult);
                $updateCakeResponse = array();
                $updateCakeResponse['cake_status'] = "error";
                $updateCakeResponse['cake_id'] = $params['amp;leadid'];
                if(isset($fieldData['ckm_campaign_id']) && !empty($fieldData['ckm_campaign_id'])
                    && isset($fieldData['ckm_key']) && !empty($fieldData['ckm_key'])
                ){
                    $updateCakeResponse['ckm_campaign_id'] = $fieldData['ckm_campaign_id'];
                    $updateCakeResponse['ckm_key'] = $fieldData['ckm_key'];
                }
                // $this->contact->update($_SESSION['insertID'], $updateCakeResponse);
                foreach ($dom->getElementsByTagName('a') as $node) {
                    $redirect = str_replace('&amp;', '&', $node->getAttribute("href"));
                    unset($_SESSION['insertID']);
                    unset($_SESSION['ERROR']);
                    // header("Location: $redirect");
                    // exit;
                    dd('redirect');
                }
            } 
            elseif (stripos($curlResult, 'xml') !== false) {
                $xml = new \SimpleXMLElement($curlResult);
                if ($xml->msg == 'success') {
                    $updateCakeResponse = array();
                    $updateCakeResponse['cake_status'] = "success";
                    $updateCakeResponse['cake_id'] = $xml->leadid;
                    if(isset($fieldData['ckm_campaign_id']) && !empty($fieldData['ckm_campaign_id'])
                        && isset($fieldData['ckm_key']) && !empty($fieldData['ckm_key'])
                    ){
                        $updateCakeResponse['ckm_campaign_id'] = $fieldData['ckm_campaign_id'];
                        $updateCakeResponse['ckm_key'] = $fieldData['ckm_key'];
                    }
                    // $this->contact->update($_SESSION['insertID'], $updateCakeResponse);
                    $redirect = $xml->redirect;
                    $redirect = str_replace('&amp;', '&', $redirect);
                    $leadID = $xml->leadid;
                    // unset($_SESSION['insertID']);
                    // unset($_SESSION['ERROR']);
                    if($redirect!=""){
                        dd('redirect');
                    } else {
                        $arrSearch = array("#first_name#","#last_name#","#email_address#","#primary_phone#","#tax_debt#");
                        $arrReplace = array($inputs['first_name'],$inputs['last_name'],$inputs['email_address'],$inputs['primary_phone'],$inputs['tax_debt']);
                        $queryString = str_replace($arrSearch, $arrReplace, '?fname=#first_name#&lname=&email=#email_address#&phone=#primary_phone#&debt=#tax_debt#');
                        dd('redirect');
                        // header("Location: /thank_you.php" . $queryString);
                    }
                } else {
                    $error = $xml->errors->error;
                    $updateCakeResponse = array();
                    $updateCakeResponse['cake_status'] = "error";
                    $updateCakeResponse['cake_errorcode'] = $error;
                    // $this->contact->update($_SESSION['insertID'], $updateCakeResponse);
                    $SESSION['error'] = $error;
                    $arrSearch = array("#first_name#","#last_name#","#email_address#","#primary_phone#","#tax_debt#");
                    $arrReplace = array($inputs['first_name'],$inputs['last_name'],$inputs['email_address'],$inputs['primary_phone'],$inputs['tax_debt']);
                    $queryString = str_replace($arrSearch, $arrReplace, '?fname=#first_name#&lname=&email=#email_address#&phone=#primary_phone#&debt=#tax_debt#');
                    // header("Location: /thank_you.php" . $queryString);
                    // exit;
                    dd('redirect');
                }
            }

            // dd($fieldData);
            // $contactStore = $this->contact->store($inputs);
            // if($contactStore){
            //     return redirect()->route('index');
            // }
            return redirect()->route('create');
        } catch (Exception $ex) {
            dd($ex->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function centralLeadCall($data)
    {
        $url = 'https://flmreporting.com/api/index.php';
        $host = request()->getHost();
        $requestUrl = request()->getRequestUri();
        $baseUrl = "https://".$host.$requestUrl;
        $customData['lead_timestamp']   = date('Y-m-d H:i:s');
        $customData['first_name']       = $data['first_name'];
        $customData['last_name']        = $data['last_name'];
        $customData['email_address']    = $data['email_address'];
        $customData['primary_phone']    = $data['primary_phone'];
        $customData['opt_in_domain']    = trim($baseUrl, '/process/');
        $customData['aff_id']           =  (isset($data['affid']) && !empty($data['affid'])) ? $data['affid'] : '';
        $customData['sub_id']           =  (isset($data['sub_id']) && !empty($data['sub_id'])) ? $data['sub_id'] : '';
        $customData['universal_leadid'] =  (isset($data['universal_leadid']) && !empty($data['universal_leadid'])) ? $data['universal_leadid'] : '';
        $customData['ckm_offer_id']     = $data["ckm_offer_id"];
        $customData['ip_address']       = request()->ip();

        $dataPassed = http_build_query($customData);
        $headers = array(
            'X-API-TOKEN:ZnJlc2gtc3RhcnQtaW5pdGlhdGl2ZS5jb20tN2U5MjNjY2FjYTJiMzg5NWY3ZmJiZTRlNzE5NTg3NTc=',
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataPassed);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $rs = curl_exec($ch);
        $info = curl_getinfo($ch);
    }

    public function _sendthank_mail($visitor_data)
    {
        $name = $visitor_data['recepient_name'];
        $email = $visitor_data['email_address'];
        $subject = "You're Confirmed for a Free Consultation";
        
        try {
            $mandrill = new \Mandrill('TExZc7TrxEwZfBixCELI7g');
            $template_name = 'fresh-start-initiative-com';

            $commonMessage = array(
                'from_email' => 'gethelp@fresh-start-initiative.com',
                'from_name' => 'Fresh Start Initiative',
                'headers' => array('Reply-To' => 'gethelp@fresh-start-initiative.com'),
                'important' => false,
                'track_opens' => null,
                'track_clicks' => null,
                'auto_text' => null,
                'auto_html' => null,
                'inline_css' => null,
                'url_strip_qs' => null,
                'preserve_recipients' => null,
                'view_content_link' => null,
                'tracking_domain' => null,
                'signing_domain' => null,
                'return_path_domain' => null,
                'merge' => true,
                'merge_language' => 'mailchimp',
                'global_merge_vars' => array(
                    array(
                        'name' => 'logo_img',
                        'content' => 'http://www.fresh-start-initiative.com/images/logo.png',
                    ),
                    array(
                        'name' => 'body_img',
                        'content' => 'http://www.fresh-start-initiative.com/images/body_img.jpg',
                    ),
                    array(
                        'name' => 'first',
                        'content' => 'http://www.fresh-start-initiative.com/images/customer_img/1.jpg',
                    ),
                    array(
                        'name' => 'second',
                        'content' => 'http://www.fresh-start-initiative.com/images/customer_img/2.jpg',
                    ),
                    array(
                        'name' => 'third',
                        'content' => 'http://www.fresh-start-initiative.com/images/customer_img/3.jpg',
                    ),
                    array(
                        'name' => 'fourth',
                        'content' => 'http://www.fresh-start-initiative.com/images/customer_img/4.jpg',
                    ),
                    array(
                        'name' => 'fifth',
                        'content' => 'http://www.fresh-start-initiative.com/images/customer_img/5.jpg',
                    ),
                    array(
                        'name' => 'unsubcribe',
                        'content' => 'http://www.byetrk.info/o-qkvl-d21-5b37de34590ff08a67e782a2e5f85096',
                    ),
                    array(
                        'name' => 'terms',
                        'content' => 'http://www.fresh-start-initiative.com/terms.php',
                    ),
                    array(
                        'name' => 'recepient_name',
                        'content' => $name,
                    ),
                ),
            );

            $message = array(
                'subject' => $subject,
                'to' => array(
                    array(
                        'email' => $email,
                        'name' => ucfirst($name),
                        'type' => 'to'
                    )
                    
                ),
                'merge_vars' => array(
                    array(
                        'rcpt' => $email,
                        'vars' => array(
                            array(
                                'name' => 'SUBJECT',
                                'content' => $subject
                            ),
                            array(
                                'name' => 'USER_EMAIL',
                                'content' => $email
                            ),
                            array(
                                'name' => 'SERVER_NAME',
                                'content' => request()->getHost()
                            )
                        )
                    )
                ),
                'tags' => array('ty-fsi'),
            );

            $finalmessage = array_merge($commonMessage, $message);
            $template_content = array();
            $async = true;
            $ip_pool = 'Main Pool';
            $send_at = date('Y-m-d H:i:s');
            $result = $mandrill->messages->sendTemplate($template_name, $template_content, $finalmessage);
            // print_r($result);
        } catch (\Exception $e) {
            // Mandrill errors are thrown as exceptions
            // echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();
            // A mandrill error occurred: Mandrill_Unknown_Subaccount - No subaccount exists with the id 'customer-123'
            // throw $e;
        }
    }
}
