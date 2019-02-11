<?
function CheckCurlResponse($code)
{
  $code=(int)$code;
  $errors=array(
    301=>'Moved permanently',
    400=>'Bad request',
    401=>'Unauthorized',
    403=>'Forbidden',
    404=>'Not found',
    500=>'Internal server error',
    502=>'Bad gateway',
    503=>'Service unavailable'
  );
  try
  {
    
    // if($code!=200 && $code!=204)
    //   throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undescribed error',$code);
  }
  
  catch(Exception $E)
  {
    die();
  }
}  
?>