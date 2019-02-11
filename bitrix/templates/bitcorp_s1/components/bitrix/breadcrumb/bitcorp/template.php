<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(empty($arResult))
    return "";

$strReturn = '<div class="row"><div class="col-md-12"><div class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">';

for($index = 0, $itemSize = count($arResult); $index < $itemSize; ++$index){
    $title = htmlspecialcharsex($arResult[$index]["TITLE"]);
    $arrow = ($index > 0? '<i class="fa fa-angle-right"></i>' : '');
    if(strlen($arResult[$index]["LINK"]) && $arResult[$index]['LINK'] != GetPagePath() && $arResult[$index]['LINK']."index.php" != GetPagePath())
        $strReturn .= '
	    	<div class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
	    		'.$arrow.'
	    		<a href="'.$arResult[$index]["LINK"].'" title="'.$title.'" itemprop="item">
	    			<span>'.$title.'</span>
	    			<meta itemprop="name" content="' . $title . '" />
	    			<meta itemprop="position" content="' . ($index+1) . '" />
	    		</a>
	    	</div>';    
    else{
        $strReturn .= '
        	<div class="breadcrumb-item" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
        		<i class="fa fa-angle-right"></i>
        		<span itemprop="title">'.$title.'</span>
        	</div>';
        break;
    }
}

$strReturn .= '</div></div></div>';
return $strReturn;?>