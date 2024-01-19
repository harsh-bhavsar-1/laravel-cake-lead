<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Test</title>
</head>
<body>
    <h2>Form</h2>
    <form action="{{ route('store') }}" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="ckm_offer_id" value="{{ $inputs['ckm_offer_id'] ?? $config['ckm_offer_id'] }}">
        <input type="hidden" name="page" value="{{ request()->fullUrl() }}">
        <input type="hidden" name="ckm_request_id" value="{{ request()->input('reqid') }}">
        <input type="hidden" name="s1" value="{{ request()->input('s1') }}" id="s1" />
        <input type="hidden" name="s2" value="{{ request()->input('s2') }}" id="s2" />
        <input type="hidden" name="s3" value="{{ request()->input('s3') }}" id="s3" />
        <input type="hidden" name="subid" value="{{ request()->input('subid') }}" id="subid" />
        <input type="hidden" name="neustar" id="neustar" value="">
        <input type="hidden" name="referrer" value="{{ $_SERVER['HTTP_REFERER'] ?? '' }}" id="referrer" />
        <input type="hidden" id="melissa" name="melissa" value=""/>
        <input type="hidden" name="LeadRouting" value="{{ $config['LeadRouting'] }}" />
        <input type="hidden" name="universal_leadid" id="leadid_token" value=""/>
        <input type="hidden" id="tax_debt" name="tax_debt" value=""/>
        <input type="hidden" id="enrolled_irs" name="enrolled_irs" value=""/>
        <input type="hidden" id="ABt" name="ABt" value=""/>

        <!---CallerReady Hidden Fields - Start--->
        <input type="hidden" id="UrlRefer" name="UrlRefer" value="{{ request()->Url() }}"/>
        <input type="hidden" id="PathLabel" name="PathLabel" value="FSI-FSI4"/>
        <input type="hidden" id="Tags" name="Tags" value="FSI"/>
        <!---CallerReady Hidden Fields - END--->

        <input type="text" name="first_name" id="first_name" placeholder="first name" > <br/>
        <input type="text" name="last_name" id="last_name" placeholder="last name"><br/>
        <input type="email" name="email_address" id="email_address" placeholder="email address"> <br/>

        <select name="state" id="state" class="lf4-step3 dis-ib form-control signchange" tabindex="9">
            <option value="" >Select Your State</option>
        </select> <br/>

        <input type="checkbox" name="opt_special_offers" id="opt_special_offers" checked="checked" value="checked"> <br/>
        <input type="tel" name="primary_phone" id="primary_phone" placeholder="Phone Number"> <br/>
        <input type="text" name="tax_debt" id="tax_debt" placeholder="tax_debt"> <br/> <br/>
        <button>Submit</button>
    </form>
</body>
</html>
