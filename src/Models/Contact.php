<?php

namespace LaravelCake\Lead\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'contact';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email_address',
        'primary_phone',
        'alt_phone',
        'state',
        'zip_code',
        'enrolled_irs',
        'affid',
        'submit_attempts',
        'user_agent',
        'ip_address',
        'geo_lookup',
        'cake_response',
        'cake_status',
        'cake_id',
        'ckm_campaign_id',
        'ckm_key',
        'cake_errorcode',
    ];

    const commonFields = [
        'affid',
        'ckm_offer_id',
        'oc',
        'reqid',
        'page',
        'query_string',
        's1',
        's2',
        's3',
        'subid',
        'referrer',
        'neustar',
        'melissa',
        'current_situation',
        'current_situation_reason'
    ];

    const configFields = [
        'tax_debt',
        'first_name',
        'last_name',
        'email_address',
        'primary_phone',
        'state',
        'enrolled_irs',
        'alt_phone'
    ];

    const passThruFields = [
        'ckm_request_id',
        'cpAFID',
        'cpSID',
        'cpSID2',
        'universal_leadid',
        'xxTrustedFormToken',
        'xxTrustedFormCertUrl',
        'cr_price',
        'opt_in'
    ];

    public function details()
    {
        return $this->hasOne(ContactDetails::class);
    }

    public static function getInfutorData($infutorResponceArray, $inputs)
    {
        $fieldData = [];
        if (!empty($infutorResponceArray['Response']['Detail']['Identity']['Address']['State'])) {
            $fieldData['infutor_state'] = $infutorResponceArray['Response']['Detail']['Identity']['Address']['State'];
        }
        if (!empty($infutorResponceArray['Response']['Detail']['Identity']['Address']['City'])) {
            $fieldData['infutor_city'] = $infutorResponceArray['Response']['Detail']['Identity']['Address']['City'];
        }
        if (!empty($infutorResponceArray['Response']['Detail']['Identity']['PreviousAddress1'])) {
            $fieldData['infutor_address1'] = $infutorResponceArray['Response']['Detail']['Identity']['PreviousAddress1'];
        }
        if (!empty($infutorResponceArray['Response']['Detail']['Identity']['PreviousAddress2'])) {
            $fieldData['infutor_address2'] = $infutorResponceArray['Response']['Detail']['Identity']['PreviousAddress2'];
        }
        if (!empty($infutorResponceArray['Response']['Detail']['Identity']['Address']['Zip'])) {
            $fieldData['infutor_zip'] = @$infutorResponceArray['Response']['Detail']['Identity']['Address']['Zip'];
        }
        if (!empty($infutorResponceArray['Response']['Detail']['IDScores']['NameToPhone'])) {
            $fieldData['infutor_nametophone'] = @$infutorResponceArray['Response']['Detail']['IDScores']['NameToPhone'];
        }
        if (!empty($infutorResponceArray['Response']['Detail']['IDScores']['ValidationSummary'])) {
            $fieldData['infutor_validation_summury'] = @$infutorResponceArray['Response']['Detail']['IDScores']['ValidationSummary'];
        }
        $infutorResponce = json_encode($infutorResponceArray);
        $fieldData['infutor_status'] = "pass";

        if (!empty($fieldData['infutor_validation_summury']) && !empty($fieldData['infutor_nametophone']) && (strtoupper($fieldData['infutor_validation_summury']) =="INCONCLUSIVE" ||  strtoupper($fieldData['infutor_validation_summury']) =="FAIL") && $fieldData['infutor_nametophone'] == "1") {
            $fieldData['infutor_status']= "fail";
        }
        if(isset($fieldData['infutor_zip']) && !empty($fieldData['infutor_zip'])) {
            $fieldData['zip_pass']= "yes";
        }
        if ($inputs['tax_debt'] < 9999) {
            if (!empty($fieldData['infutor_validation_summury']) && !empty($fieldData['infutor_nametophone']) && (strtoupper($fieldData['infutor_validation_summury']) =="INCONCLUSIVE" ||  strtoupper($fieldData['infutor_validation_summury']) =="FAIL") && $fieldData['infutor_nametophone'] == "1") {
                $fieldData['infutor_under']= "fail";
            } else {
                $fieldData['infutor_under'] = "pass";
            }
        }
        return 
        [
            'fieldData' => $fieldData,
            'infutorResponce' => $infutorResponce
        ];
    } 

    public static function getCkmData($inputs)
    {
        $fieldData = [];
        if($inputs['LeadRouting']=="fsi41"){
            $fieldData['ckm_campaign_id'] = '3814';
            $fieldData['ckm_key'] = 'Ayqb5rZuwBI';
        }

        if($inputs['LeadRouting']=="fsi_sc"){
            if ($inputs['tax_debt'] < 10000){
                $fieldData['ckm_campaign_id'] = '2408';
                $fieldData['ckm_key'] = '9ncUsag7wRY';
            } else {
                $fieldData['ckm_campaign_id'] = '2407';
                $fieldData['ckm_key'] = 'L8f651yMHw';
            }
        }

        if($inputs['LeadRouting']=="fsi_sc2"){
            if ($inputs['tax_debt'] < 10000){
                $fieldData['ckm_campaign_id'] = '2742';
                $fieldData['ckm_key'] = 'ckVVXzN8a8';
            } else {
                $fieldData['ckm_campaign_id'] = '2741';
                $fieldData['ckm_key'] = '8GWZObZbywA';
            }
        }

        if ($inputs['LeadRouting'] == "fsi22") {
            $fieldData['ckm_campaign_id'] = '2979';
            $fieldData['ckm_key']         = 'Zz64cT6MBCo';
        }

        if ($inputs['LeadRouting'] == "fsi-lf1-bj") {
            $fieldData['ckm_campaign_id'] = '3699';
            $fieldData['ckm_key']         = 'oC1fXAsQA4';
            $fieldData['ckm_subid']       = 'tiktok';
        }

        if ($inputs['LeadRouting'] == "fsi-lf1-bj-fb") {
            $fieldData['ckm_campaign_id'] = '3483';
            $fieldData['ckm_key']         = 'oC1fXAsQA4';
            $fieldData['ckm_subid']       = 'fb';
            $fieldData['affid_subid_score'] = 'N';
        }

        if ($inputs['LeadRouting'] == "fsi-lf1") {
            $fieldData['ckm_campaign_id'] = '3397';
            $fieldData['ckm_key']         = 'oC1fXAsQA4';
            $fieldData['ckm_subid']       = 'youtube';
            $fieldData['affid_subid_score'] = 'N';
        }

        if ($inputs['LeadRouting'] == "fsi-lf1-bj-google") {
            $fieldData['ckm_campaign_id'] = '3484';
            $fieldData['ckm_key']         = 'oC1fXAsQA4';
            $fieldData['ckm_subid']       = 'google';
            $fieldData['affid_subid_score'] = 'N';
        }

        if ($inputs['LeadRouting'] == "fsi-lf1-bj-snap") {
            $fieldData['ckm_campaign_id'] = '3594';
            $fieldData['ckm_key']         = 'oC1fXAsQA4';
            $fieldData['ckm_subid']       = 'snapchat';
            $fieldData['affid_subid_score'] = 'N';
        }

        $fieldData['opt_in']='checked';
        $fieldData['TCPA_checkbox']='checked';
        $fieldData['ABt'] = $inputs['ABt'];
        $fieldData['remarket_buyer'] = $inputs['remarket_buyer'] ?? '';
      
        if ($inputs['LeadRouting'] == 'fsi4' || $inputs['LeadRouting'] == 'fsi6' || $inputs['LeadRouting'] == 'fsi_ca2' || $inputs['LeadRouting'] == 'fsi14_ab' || $inputs['LeadRouting'] == 'fsi19') {
            $arrDetails = array("FNAME" => $inputs['first_name'],
                                "LNAME" => $inputs['last_name'],
                                "PHONE" => $inputs['primary_phone']
                            );
                $amount='Under10k';
                if ($inputs['tax_debt'] > 9999) {
                    $amount='Over10k';
                }
        }
        return $fieldData;
    }
}
