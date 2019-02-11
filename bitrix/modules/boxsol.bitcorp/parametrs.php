<?
$moduleClass = "CBitcorp";
$moduleID = "boxsol.bitcorp";
IncludeModuleLangFile(__FILE__);

//module default parametrs array
$moduleClass::initParametrs(
	array(
		"MAIN" => array(
			"TITLE" => GetMessage("MAIN_OPTIONS"),
			"OPTIONS" => array(
				"SHOW_SETTINGS_PANEL" => array(
					"TITLE" => GetMessage("SHOW_SETTINGS_PANEL"),
					"TYPE" => "checkbox",
					"DEFAULT" => "N",
					"IN_SETTINGS_PANEL" => "N",
					"HINT" => GetMessage("SHOW_SETTINGS_PANEL_HINT"),
				),
				"COLOR_SCHEME" => array(
					"TITLE" => GetMessage("COLOR_SCHEME"), 
					"TYPE" => "selectbox", 
					"LIST" => array(					
						"BLUE_RED" => array("COLOR_FIRST" => "#0c4da2", "COLOR_SECOND" => "#ed1c24", "TITLE" => GetMessage("COLOR_SCHEME_BLUE_RED")),
						"ORANGE_BLUE" => array("COLOR_FIRST" => "#ff6800", "COLOR_SECOND" => "#0097ff", "TITLE" => GetMessage("COLOR_SCHEME_ORANGE_BLUE")),
						"GREEN_RED" => array("COLOR_FIRST" => "#37b28d", "COLOR_SECOND" => "#f15207", "TITLE" => GetMessage("COLOR_SCHEME_GREEN_RED")),	
						"BLUE_ORANGE" => array("COLOR_FIRST" => "#075ae8", "COLOR_SECOND" => "#ff7f00", "TITLE" => GetMessage("COLOR_SCHEME_BLUE_ORANGE")),
						"BLUE" => array("COLOR_FIRST" => "#1331db", "COLOR_SECOND" => "#1331db", "TITLE" => GetMessage("COLOR_SCHEME_BLUE")),						
						"RED" => array("COLOR_FIRST" => "#ed1c24", "COLOR_SECOND" => "#ed1c24", "TITLE" => GetMessage("COLOR_SCHEME_RED")),									
						"CUSTOM" => array("COLOR" => "", "TITLE" => GetMessage("COLOR_SCHEME_CUSTOM"))
					),
					"DEFAULT" => "BLUE_RED",
					"IN_SETTINGS_PANEL" => "Y",				
				),
				"COLOR_SCHEME_CUSTOM_FIRST" => array(
					"TITLE" => GetMessage("COLOR_SCHEME_CUSTOM_FIRST"), 
					"TYPE" => "text", 
					"DEFAULT" => "#0c4da2",
					"IN_SETTINGS_PANEL" => "Y",
					"HINT" => GetMessage("COLOR_SCHEME_CUSTOM_FIRST_HINT"),
				),
				"COLOR_SCHEME_CUSTOM_SECOND" => array(
					"TITLE" => GetMessage("COLOR_SCHEME_CUSTOM_SECOND"), 
					"TYPE" => "text", 
					"DEFAULT" => "#ed1c24",
					"IN_SETTINGS_PANEL" => "Y",
					"HINT" => GetMessage("COLOR_SCHEME_CUSTOM_SECOND_HINT"),
				),
				"LOGO_WITHBG" => array(
					"TITLE" => GetMessage("LOGO_WITHBG"),
					"TYPE" => "checkbox",
					"DEFAULT" => "Y",
					"IN_SETTINGS_PANEL" => "N"
				),
				"LOGO_IMAGE" => array(
					"TITLE" => GetMessage("LOGO_IMAGE"),
					"TYPE" => "file",
					"DEFAULT" => "",				
					"IN_SETTINGS_PANEL" => "N"
				),
				"FAVICON_IMAGE" => array(
					"TITLE" => GetMessage("FAVICON_IMAGE"),
					"TYPE" => "file",
					"DEFAULT" => "",				
					"IN_SETTINGS_PANEL" => "N",
					"HINT" => GetMessage("FAVICON_IMAGE_HINT"),
				),
				"HEADER_TYPE" => array(
					"TITLE" => GetMessage("HEADER_TYPE"), 
					"TYPE" => "selectbox", 
					"LIST" => array(					
						"FIRST" => GetMessage("HEADER_TYPE_FIRST"),
						"SECOND" => GetMessage("HEADER_TYPE_SECOND"),
					),
					"DEFAULT" => "FIRST",
					"IN_SETTINGS_PANEL" => "Y"
				),
				"FIX_TOP_MENU" => array(
					"TITLE" => GetMessage("FIX_TOP_MENU"),
					"TYPE" => "checkbox",
					"DEFAULT" => "Y",
					"IN_SETTINGS_PANEL" => "N"
				),
				"MENU_TYPE" => array(
					"TITLE" => GetMessage("MENU_TYPE"), 
					"TYPE" => "selectbox", 
					"LIST" => array(					
						"WHITE" => GetMessage("MENU_TYPE_WHITE"),
						"COLOR" => GetMessage("MENU_TYPE_COLOR"),
						"DARK" => GetMessage("MENU_TYPE_DARK"),						
					),
					"DEFAULT" => "COLOR",
					"IN_SETTINGS_PANEL" => "Y",
					"HINT" => GetMessage("MENU_TYPE_HINT"),
				),							
				"SERVICES_TYPE" => array(
					"TITLE" => GetMessage("SERVICES_TYPE"),
					"TYPE" => "selectbox",
					"LIST" => array(
						"SERVICES" => GetMessage("SERVICES_TYPE_SERVICES_FIRST"),
						"SERVICES_SECOND" => GetMessage("SERVICES_TYPE_SERVICES_SECOND"),						
					),
					"DEFAULT" => "SERVICES",
					"IN_SETTINGS_PANEL" => "Y",					
				),
				"PROJECTS_TYPE" => array(
					"TITLE" => GetMessage("PROJECTS_TYPE"),
					"TYPE" => "selectbox",
					"LIST" => array(
						"PROJECTS" => GetMessage("SERVICES_TYPE_PROJECTS_FIRST"),
						"PROJECTS_SECOND" => GetMessage("SERVICES_TYPE_PROJECTS_SECOND"),						
					),
					"DEFAULT" => "PROJECTS",
					"IN_SETTINGS_PANEL" => "Y",					
				),
				"HOME_PAGE" => array(
					"TITLE" => GetMessage("HOME_PAGE"),
					"TYPE" => "multiselectbox",
					"LIST" => array(
						"ADVANTAGES" => GetMessage("HOME_PAGE_ADVANTAGES"),
						"SERVICES" => GetMessage("HOME_PAGE_SERVICES"),
						"CATALOG" => GetMessage("HOME_PAGE_CATALOG"),
						"CATALOG_SECTIONS" => GetMessage("HOME_PAGE_CATALOG_SECTIONS"),
						"PROJECTS" => GetMessage("HOME_PAGE_PROJECTS"),
						"COMPANY" => GetMessage("HOME_PAGE_COMPANY"),
						"TEAM" => GetMessage("HOME_PAGE_TEAM"),
						"REVIEWS" => GetMessage("HOME_PAGE_REVIEWS"),
						"NEWS" => GetMessage("HOME_PAGE_NEWS"),
						"PARTNERS" => GetMessage("HOME_PAGE_PARTNERS")
					),
					"DEFAULT" => array("ADVANTAGES","SERVICES", "CATALOG", "CATALOG_SECTIONS", "PROJECTS", "COMPANY", "TEAM", "REVIEWS", "NEWS", "PARTNERS"),
					"IN_SETTINGS_PANEL" => "Y",
					"HINT" => GetMessage("HOME_PAGE_HINT"),
				),		
				
			)
		),	
		"FORMS" => array(
			"TITLE" => GetMessage("FORMS_OPTIONS"),
			"OPTIONS" => array(			
				"FORMS_USE_CAPTCHA" => array(
					"TITLE" => GetMessage("FORMS_USE_CAPTCHA"),
					"TYPE" => "checkbox",
					"DEFAULT" => "Y",
					"IN_SETTINGS_PANEL" => "N"
				),
				"FORMS_PHONE_MASK" => array(
					"TITLE" => GetMessage("FORMS_PHONE_MASK"),				
					"TYPE" => "text",
					"DEFAULT" => "+7 (999) 999-99-99",
					"IN_SETTINGS_PANEL" => "N"				
				),
				"FORMS_VALIDATE_PHONE_MASK" => array(
					"TITLE" => GetMessage("FORMS_VALIDATE_PHONE_MASK"),
					"TYPE" => "text",
					"SIZE" => "40",
					"DEFAULT" => "^[+][0-9] [(][0-9]{3}[)] [0-9]{3}[-][0-9]{2}[-][0-9]{2}$",
					"IN_SETTINGS_PANEL" => "N"
				),			
			)
		),
		"PERSONAL_DATA" => array(
			"TITLE" => GetMessage("PERSONAL_DATA"),
			"OPTIONS" => array(
				"SHOW_PERSONAL_DATA" => array(
					"TITLE" => GetMessage("PERSONAL_DATA_SHOW_PERSONAL_DATA"),
					"TYPE" => "checkbox",
					"DEFAULT" => "Y",
					"IN_SETTINGS_PANEL" => "N"
				),
				"TEXT_PERSONAL_DATA" => array(
					"TITLE" => GetMessage("PERSONAL_DATA_TEXT_PERSONAL_DATA"),				
					'TYPE' => 'includefile',
					'INCLUDEFILE' => '#SITE_DIR#include/license.php',
					"COLS" => "50",
					"ROWS" => "5",
					"DEFAULT" => GetMessage("DEFAULT_PERSONAL_DATA_TEXT"),
					"IN_SETTINGS_PANEL" => "N"
				)
			)
		)
	)
);?>