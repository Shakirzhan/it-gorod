<?IncludeModuleLangFile(__FILE__);

if($GLOBALS['APPLICATION']->GetGroupRight($moduleID) >= 'R')
{
	$aMenu = array(
		"parent_menu" => "global_menu_services",
		"text" => GetMessage("CONCEPT_QUIZ_SERVICES_MENU"),
		"icon" => "rating_menu_icon",
		"title" => "",
		"url" => "concept_quiz.php",
		"sort" => 9900,
		"module_id" => "concept.quiz"
	);

	return $aMenu;
}
?>