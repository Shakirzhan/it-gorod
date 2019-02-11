<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arServices = Array(
    "main" => Array(
        "NAME" => GetMessage("SERVICE_MAIN_SETTINGS"),
        "STAGES" => Array(
            "search.php", //Indexing files
            "files.php", // Copy bitrix files           
            "template.php", // Install template
            "menu.php", // Set menu types
            "group.php", // Install group
            "settings.php",
        ),
    ),

    "iblock" => Array(
        "NAME" => GetMessage("SERVICE_IBLOCK"),
        "STAGES" => Array(
            "types.php", //IBlock types
            "md_bitcorp_advertbig.php",
            "md_bitcorp_contacts.php",
            "md_bitcorp_faq.php",
            "md_bitcorp_gallery.php",
            "md_bitcorp_licenses.php",
            "md_bitcorp_news.php",
            "md_bitcorp_price.php",
            "md_bitcorp_reviews.php",            
            "md_bitcorp_stuff.php",
            "md_bitcorp_partners.php",
            "md_bitcorp_teasers.php",
            "md_bitcorp_vacancy.php",
            "md_bitcorp_services.php",
            "md_bitcorp_projects.php",
            "md_bitcorp_catalog.php",                       
            "md_bitcorp_requests_callback.php",
            "md_bitcorp_requests_contacts.php",            
            "md_bitcorp_requests_projects.php",
            "md_bitcorp_requests_services.php",
            "md_bitcorp_requests_catalog.php",
            "md_bitcorp_requests_questions.php",
        ),
    ),

);

//$themeId = $_REQUEST["__wiz_bitcorp_themeID"]; //corporate

?>