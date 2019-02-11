<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);
use Bitrix\Main\Localization\Loc;
?>
<span class="jqmClose top-close"><i class="fa fa-times"></i></span>
<div class="modal--header">
	<?if(strlen($arResult["IBLOCK"]["NAME"])):?>
		<h2><?=$arResult["IBLOCK"]["NAME"]?></h2>
	<?endif;?>

	<?if($arResult["IBLOCK"]["DESCRIPTION"]):?>
		<div class="modal--descr">
			<?if($arResult["IBLOCK"]["DESCRIPTION_TYPE"] == "html"):?>
				<?=$arResult["IBLOCK"]["DESCRIPTION"]?>
			<?else:?>
				<p><?=$arResult["IBLOCK"]["DESCRIPTION"]?></p>
			<?endif;?>
		</div>
	<?endif;?>
	
</div>

<form action="<?=POST_FORM_ACTION_URI?>" method="POST" id="<?=$arResult['ELEMENT_AREA_ID']?>_form" class="form--data form">
	<?=bitrix_sessid_post()?>
	<?if(!empty($arResult["SUCCESS_MESSAGE"])):?>		
		<div class="alert alert-success">
			<?foreach($arResult["SUCCESS_MESSAGE"] as $message):?>
				<?=$message?>	
			<?endforeach?>
		</div>
	<?endif;?>

	<?if(!empty($arResult["ERROR_MESSAGE"])):?>		
		<div class="form-group">									
			<div class="alert alert-danger">
				<?foreach($arResult["ERROR_MESSAGE"] as $error):?>
					<?=$error?>	
				<?endforeach?>
			</div>
		</div>		
	<?endif;?>				

	<?foreach($arResult["IBLOCK"]["PROPERTIES"] as $arProp):?>
		<?$bHidden = ($arProp["CODE"] == "SEND_FROM" ? true : false);?>		
		<div class="form-group" <?= ($bHidden ? 'style="display:none"' : '')?> >
			<div class="fluid-label">
				<?=$arProp["HTML_CODE"]?>
		    </div>
	    </div>			
	<?endforeach;?>								

	<?//CAPTCHA?>
	<?if($arParams["USE_CAPTCHA"] == "Y"):?>		
		<div class="form-group">
			<input required class="captcha" type="text" name="CAPTCHA_WORD" maxlength="5" value="" placeholder="<?=Loc::getMessage("FORMS_CAPTCHA")?>*" autocomplete="off"/>
	    </div>
	    <div class="form-group">
	    	<img src="" width="180" height="40" alt="CAPTCHA" style="display:none;" />
	    	<input type="hidden" name="CAPTCHA_SID" value="" class="CAPTCHA_SID" />
	    </div>							
	<?endif;?>				

	<?if($arParams["SHOW_PERSONAL_DATA"] == "Y"):?>		
		<div class="form-group font-xs">
			<input type="checkbox" required  name="licenses_agreement_popup" id="licenses_agreement_popup" value="Y"/>
			<label for="licenses_agreement_popup">
				<?$APPLICATION->IncludeFile(SITE_DIR."include/license.php", Array(), Array("MODE" => "html", "NAME" => "LICENSES")); ?>									
			</label>
	    </div>			
	<?endif;?>
	
	<div class="form-group form-group_last">
		<button type="submit" id="<?=$arResult['ELEMENT_AREA_ID']?>_btn" class="btn btn-primary"><?=($arParams["BUTTON_TITLE"] ? $arParams["BUTTON_TITLE"] : Loc::getMessage("FORMS_SEND"))?></button>
		<input type="hidden" name="form_submit" value="<?=Loc::getMessage("FORMS_SEND")?>">		
    </div>	
</form>

		

<script type="text/javascript">
	$(document).ready(function () {
		$('#<?=$arResult['ELEMENT_AREA_ID']?>_form').validate({
			errorElement: "div",
			errorClass: "input-error",
			highlight: function( element ){
				$(element).removeClass('correct');
				$(element).addClass('error');
			},
			unhighlight: function( element ){
				$(element).removeClass('error');
				$(element).addClass('correct');
			},
			submitHandler: function( form ){
				if( $('#<?=$arResult['ELEMENT_AREA_ID']?>_form').valid() ){				
					$(form).find('button[type="submit"]').attr('disabled', 'disabled');				
					form.submit();
				}
			},
			errorPlacement: function( error, element ){			
				if($(element).attr('id') == 'licenses_agreement_popup'){
					error.insertAfter('label[for="licenses_agreement_popup"]');
				} else{
					error.insertAfter(element);	
				}
			},
			messages:{
		      licenses_agreement_popup: {
		        required : BX.message('JS_REQUIRED_LICENSES')
		      },
		      CAPTCHA_WORD: {
		        required : BX.message('JS_REQUIRED_CAPTCHA')
		      }
			}
		});	
		
		var phoneMask = '<?=$arParams['PHONE_MASK']?>';

		if(phoneMask.length){
			var base_mask = phoneMask.replace( /(\d)/g, '_' );
			$('#<?=$arResult["ELEMENT_AREA_ID"]?>_form input[name="PHONE"]').inputmask('mask', {'mask': phoneMask, 'showMaskOnHover': false });

			$('#<?=$arResult["ELEMENT_AREA_ID"]?>_form input[name="PHONE"]').blur(function(){			
				if( $(this).val() == base_mask || $(this).val() == '' ){							
					if( $(this).hasClass('required') ){					
						$(this).parent().find('div.input-error').html(BX.message('JS_REQUIRED'));
					}
				}
			});
		}
		//add to close button jqmClose behavior
		$('.jqmClose').closest('.jqmWindow').jqmAddClose('.jqmClose');

		/*Form input*/	
		$('.fluid-label').fluidLabel({
			focusClass: 'focused'
		});	
		/*Form input*/
	});	
</script>