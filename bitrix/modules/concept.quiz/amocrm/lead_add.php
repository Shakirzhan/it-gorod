<?

    $lead = array(
        "name" => $title,
        //"status_id" => 17004579,
        //"responsible_user_id" => 238032,
        "price" => 0,
        "custom_fields" => array(
			
            
        ),     
    );
   
    $set['request']['leads']['add'][] = $lead;
     
    
    $link=$crmUrl.'private/api/v2/json/leads/set';
    $curl=curl_init();
    

    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($set));
    curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
     
    $out=curl_exec($curl);
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
    CheckCurlResponse($code);
     

    $Response=json_decode($out,true);
    $Response=$Response['response']['leads']['add'];
    
    
     
    $output=''.PHP_EOL;
    
    foreach($Response as $v)
    {
        if(is_array($v))
        {
            $output.=$v['id'].PHP_EOL;
            $lead_id = $v["id"];
            
            //echo $lead_id;
        }
    }
    
    
    $notes['request']['notes']['add']=array(
        array(
            'element_id'=>$lead_id,
            'last_modified'=> time(),
            'element_type'=>2,
            'note_type'=>4,  
            'text' => $mess
        )
    );

    $link=$crmUrl.'private/api/v2/json/notes/set';
    $curl=curl_init(); 
    

    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-API-client/1.0');
    curl_setopt($curl,CURLOPT_URL,$link);
    curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
    curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($notes));
    curl_setopt($curl,CURLOPT_HTTPHEADER,array('Content-Type: application/json'));
    curl_setopt($curl,CURLOPT_HEADER,false);
    curl_setopt($curl,CURLOPT_COOKIEFILE,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_COOKIEJAR,__DIR__.'/cookie.txt'); #PHP>5.3.6 dirname(__FILE__) -> __DIR__
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);
     
    $out=curl_exec($curl); 
    $code=curl_getinfo($curl,CURLINFO_HTTP_CODE);
    CheckCurlResponse($code);
     
     
    $Response=json_decode($out,true);
    $Response=$Response['response']['notes']['add'];
    
    
 
    
?>