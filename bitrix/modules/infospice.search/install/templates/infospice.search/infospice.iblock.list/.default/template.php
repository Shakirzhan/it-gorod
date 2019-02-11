<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<div class="category-links category-links2">
<?
	$iblockId    = ( !empty( $_GET[ "iblock" ]  )) ? ( int )$_GET[ "iblock" ]  : 0;
	$sectionId   = ( !empty( $_GET[ "section" ] )) ? ( int )$_GET[ "section" ] : 0;
	$userQuery   = ( !empty( $_GET[ "q" ] ))       ? trim( $_GET[ "q" ])       : "";
	$pageType    = ( !empty( $_GET[ "page" ] ))    ? trim( $_GET[ "page" ])    : "";
	$isStatic    = ( boolean )( $pageType == "static" );
	
	

if ( !empty( $arResult[ 'BACK_URL' ] )) 
{ 
	?>
	<div class="head">
		<a class="backurl" href="<?=$arResult['BACK_URL']?>"><?=GetMessage('BACK_URL_LABEL');?></a>
		<?
		if ( count( $arResult[ "MODULE" ]) == 1 ) 
			echo '<h1 class="title">'.$arResult[ "MODULE" ][ "0" ][ "NAME" ]."</h1>"; 
		?>
	</div>
	<?
} 
?>
	<div class="list-holder">
		<ul>
			<?
			foreach($arResult["SECTION"] as $items) 
			{
			
				$active = "";
				if($arResult["SECTION_URL"] == "Y"){
					if($items['ID'] == $sectionId){
						$active = "active";
					}
				} else {
					if($items['ID'] == $iblockId){
						$active = "active";
					}
				}
				
				
				$url  = $APPLICATION->GetCurPage();
				$url .= "?q=".$userQuery;  
				$name = $items['NAME'];
				
				
				if ( $arResult["SECTION_URL"] == "Y" ) {
					if ( $iblockId > 0 )       $url   .= "&iblock=".$iblockId;
					if ( $items['ID'] > 0 )    $url   .= "&section=".$items['ID'];
				}
				else {
					if ( $items['ID'] > 0 )    $url   .= "&iblock=".$items['ID'];	
				}
				
				if ( $items['COUNT'] > 0 )     $count  = " (".$items['COUNT'].")";
				
				?>
				<li class="<?echo $active?>"><span class="co-r"><span class="co-l">
					<a href="<?=$url?>"><?=$name?></a><?=$count?>
					</span></span>
				</li>
				<?
			}
			
			
			
			if ( $arParams["STATIC"] == "Y" ) 
			{ 
				$active = ( $isStatic ) ? "active" : "";
				
				$url  = $APPLICATION->GetCurPage();
				$url .= "?q=".$userQuery; 
				$url .= "&page=static";
				$name = GetMessage('STATIC_PAGE');
				
				if ( $arParams["STATIC_COUNT"] > 0 )     $count  = " (".$arParams["STATIC_COUNT"].")";
				
				?>
				<li class="<?echo $active?>"><span class="co-r"><span class="co-l">
					<a href="<?=$url?>"><?=$name?></a><?=$count?>
					</span></span>
				</li>
				<?
			}
			?>
		</ul>
	</div>
	<?
	if (false)
	{
		if (!empty( $_REQUEST["section"])) {
			?><p><a href="/biblioteka/?SECTION_ID=<?=$_REQUEST["section"]?>"><?=GetMessage("INFOSPICE_SEARCH_PEREYTI_V_RAZDEL")?></a></p><?
		}
	}
	?>

</div>

