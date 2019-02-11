<?
$add = "Y";

$link=$crmUrl.'private/api/v2/json/contacts/list?query='.$phoneamo;
$curl=curl_init(); 

curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
curl_setopt($curl,CURLOPT_URL,$link);
curl_setopt($curl,CURLOPT_HEADER,false);
curl_setopt($curl,CURLOPT_COOKIEFILE,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
curl_setopt($curl,CURLOPT_COOKIEJAR,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
 
$out=curl_exec($curl); 
$code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
curl_close($curl);
CheckCurlResponse($code);

if($out)
{
    /*$Response=json_decode($out,true);
    $Response=$Response['response']['contacts'];
    
    foreach($Response as $contact_val)
    {
        foreach($contact_val["custom_fields"] as $field)
        {
            if($field["code"] == "PHONE")
            {
                foreach($field["values"] as $val)
                {
                    if($val["value"] == $phoneamo)
                    {
                        //$add = "N";
                        //$contact_id = $contact_val["id"];
                    }
                }
            }
        }
    }
    

    $n = $contact_val["name"];
    
    $Leads_id = $contact_val["linked_leads_id"];
    $Leads_id[] = $lead_id;
    
    
    $contact=array(
      'id'=>$contact_id,
      'name' => $n,
      'linked_leads_id' => $Leads_id,
      'last_modified' => time()
    );*/
    
    
}
?>