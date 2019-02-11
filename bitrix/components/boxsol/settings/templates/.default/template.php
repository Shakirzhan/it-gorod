<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(false);

use Bitrix\Main\Localization\Loc,
	Bitrix\Main\Config\Option;?>
<!-- preloader -->	
<div id="hellopreloader_preload"></div>
<!-- preloader/ -->

<div class="style-switcher <?=$_COOKIE['styleSwitcher'] == 'open' ? 'active' : ''?>">
	<div class="header">
		<?=Loc::getMessage("THEME_SETTINGS")?><span class="switch"><i class="fa fa-sliders"></i></span>		
	</div>

    <div class="form-wrapper">
    	<form action="<?=$APPLICATION->GetCurPage()?>" method="POST" name="style-switcher">
            <div class="settings-blocks-wrapper">
        		<?=bitrix_sessid_post();?>
        		<?$i = 1;?>
        		<?foreach($arResult as $optionCode => $arOption):?>		
        			<?if($arOption["IN_SETTINGS_PANEL"] == "Y"):?>
        				<?if($optionCode == "COLOR_SCHEME_CUSTOM_FIRST" || $optionCode == "COLOR_SCHEME_CUSTOM_SECOND" || ($arResult["HEADER_TYPE"]["LIST"]["SECOND"]["CURRENT"] == 'Y' && $optionCode == "MENU_TYPE")):?>
        					<?continue;?>
        				?>
        				<?else:?>
        					<div class="block">					
        						<div class="block-title">
        							<span><?=$arOption["TITLE"]?></span>    							
        						</div>
        						<div class="options options-count-<?=count($arOption['LIST'])?>" id="options-<?=$optionCode?>" style="display:none;">							
        							<?$k = 1;?>
        							<?if($optionCode == "COLOR_SCHEME"):?>
        								<?foreach($arOption["LIST"] as $colorCode => $arColor):?>
        									<?if($colorCode !== "CUSTOM"):?>
        										<div class="custom-forms main-colors" data-color="<?=$arColor['COLOR']?>">

        											<input type="radio" id="option-<?=$i?>-<?=$k?>" name="<?=$optionCode?>" <?=$arColor["CURRENT"] == "Y" ? "checked=\"checked\"" : ""?> value="<?=$colorCode?>" />

        											<label for="option-<?=$i?>-<?=$k?>" title="<?=$arColor['TITLE']?>">
        												<div class="color-sheme cs-first" style="background:<?=$arColor['COLOR_FIRST']?>;"></div>
        												<div class="color-sheme cs-second" style="background:<?=$arColor['COLOR_SECOND']?>;"></div>
        											</label>

        										</div>
        										<?$k++;?>

        									<?endif;?>
        								<?endforeach;?>
        								
                                        <div class="block-title">
                                            <span><?=Loc::getMessage("CUTSOM_COLORS_TITLE")?></span>                                
                                        </div>
        								<div class="color-scheme-custom-first">
        									<?foreach($arOption["LIST"] as $colorCode => $arColor):?>
        										<?if($colorCode == "CUSTOM"):?>

                                                    <!-- curent custom color source-->
                                                    <div class="cur-first-custom-color" data-color="<?=(strlen($arResult['COLOR_SCHEME_CUSTOM_FIRST']['VALUE']) > 0) ? $arResult['COLOR_SCHEME_CUSTOM_FIRST']['VALUE'] : $arResult['COLOR_SCHEME_CUSTOM_FIRST']['DEFAULT']?>" style="display: none;"></div>   											
                                                    <input type="radio" id="option-<?=$i?>-<?=$k?>" name="<?=$optionCode?>" <?=$arColor["CURRENT"] == "Y" ? "checked=\"checked\"" : ""?> value="<?=$colorCode?>" style="display: none;"/>
        											<input
                                                        type="text"
                                                        id="option-color-scheme-custom-first"
                                                        name="COLOR_SCHEME_CUSTOM_FIRST"
                                                        maxlength="7"
                                                        value="<?=(strlen($arResult['COLOR_SCHEME_CUSTOM_FIRST']['VALUE']) > 0) ? $arResult['COLOR_SCHEME_CUSTOM_FIRST']['VALUE'] : $arResult['COLOR_SCHEME_CUSTOM_FIRST']['DEFAULT']?>"
                                                        style="<?=(strlen($arResult['COLOR_SCHEME_CUSTOM_FIRST']['VALUE']) > 0) ? 'border-color:'.$arResult['COLOR_SCHEME_CUSTOM_FIRST']['VALUE'].';' : 'border-color:'.$arResult['COLOR_SCHEME_CUSTOM_FIRST']['DEFAULT'].';'?>" 
                                                    />

        											<button type="button" name="palette_button" class="btn btn-primary first-custom-color-button">
        												<i class="fa fa-eyedropper"></i>
        												<span><?=Loc::getMessage("PALETTE")?> 1</span>
        											</button>

        											<?$k++;?>

        										<?endif;?>
        									<?endforeach;?>									
        								</div>	

        								
        								<div class="color-scheme-custom-second">
        								    <?foreach($arOption["LIST"] as $colorCode => $arColor):?>
        								        <?if($colorCode == "CUSTOM"):?>    								           

                                                    <!-- curent custom color source-->
                                                    <div class="cur-second-custom-color" data-color="<?=(strlen($arResult['COLOR_SCHEME_CUSTOM_SECOND']['VALUE']) > 0) ? $arResult['COLOR_SCHEME_CUSTOM_SECOND']['VALUE'] : $arResult['COLOR_SCHEME_CUSTOM_SECOND']['DEFAULT']?>" style="display: none;"></div>
        								            <input 
                                                        type="text"
                                                        id="option-color-scheme-custom-second"
                                                        name="COLOR_SCHEME_CUSTOM_SECOND"
                                                        maxlength="7" 
                                                        value="<?=(strlen($arResult['COLOR_SCHEME_CUSTOM_SECOND']['VALUE']) > 0) ? $arResult['COLOR_SCHEME_CUSTOM_SECOND']['VALUE'] : $arResult['COLOR_SCHEME_CUSTOM_SECOND']['DEFAULT']?>" 
                                                        style="<?=(strlen($arResult['COLOR_SCHEME_CUSTOM_SECOND']['VALUE']) > 0) ? 'border-color:'.$arResult['COLOR_SCHEME_CUSTOM_SECOND']['VALUE'].';' : 'border-color:'.$arResult['COLOR_SCHEME_CUSTOM_SECOND']['DEFAULT'].';'?>"
                                                    />

        								            <button type="button" name="palette_button" class="btn btn-primary second-custom-color-button">
        								                <i class="fa fa-eyedropper"></i>
        								                <span><?=Loc::getMessage("PALETTE")?> 2</span>
        								            </button>
        								            <?$k++;?>

        								        <?endif;?>
        								    <?endforeach;?>								   
        								</div>						
        							<?else:?>
        								<?if($arOption["TYPE"] == "selectbox"):?>                                            
        									<?foreach($arOption["LIST"] as $variantCode => $arVariant):?>					
        										<div class="custom-forms">
        											<input type="radio" id="option-<?=$i?>-<?=$k?>" name="<?=$optionCode?>" <?=$arVariant["CURRENT"] == "Y" ? "checked=\"checked\"" : ""?> value="<?=$variantCode?>" />
        											<label for="option-<?=$i?>-<?=$k?>"><?=$arVariant["TITLE"]?></label>
        										</div>
        										<?$k++;?>
        									<?endforeach;?>                                           
        								<?elseif($arOption["TYPE"] == "multiselectbox"):?>							
        									<?foreach($arOption["LIST"] as $variantCode => $arVariant):?>							
        										<div class="custom-forms option">
        											<input type="checkbox" id="option-<?=$i?>-<?=$k?>" name="<?=$optionCode?>[]" <?=$arVariant["CURRENT"] == "Y" ? "checked=\"checked\"" : ""?> value="<?=$variantCode?>" />
        											<label for="option-<?=$i?>-<?=$k?>">											
        												<?=$arVariant["TITLE"]?>
        											</label>
        										</div>
        										<?$k++;?>
        									<?endforeach;?>
        								<?endif;?>
        							<?endif;?>
        						</div>						
        					</div>
        					<?$i++;?>
        				<?endif;?>
        			<?else:?>
        				<input type="hidden" name="<?=$optionCode?>" value="<?=$arOption["VALUE"]?>" />
        			<?endif;?>			
        		<?endforeach;?>
            </div><!-- /.settings-blocks-wrapper -->
    		<div class="reset">    			
    			<button type="button" name="reset_button" class="btn btn-primary">				
    				<?=Loc::getMessage("THEME_RESET")?>
    			</button>
    		</div>
    	</form>
    </div><!-- /.form-wrapper -->
</div>

<script type="text/javascript">
    
    $(function() {
        if($.cookie("styleSwitcher") == "open") {
            $(".style-switcher").addClass("active");
        }        
        
        $(".style-switcher .switch").click(function(e) {
            e.preventDefault();
            var styleswitcher = $(this).closest(".style-switcher");
            if(styleswitcher.hasClass("active")) {
                styleswitcher.animate({left: "-" + styleswitcher.outerWidth() + "px"}, 300).removeClass("active");
                $.removeCookie("styleSwitcher", {path: "/"});
            } else {
                styleswitcher.animate({left: "0"}, 300).addClass("active");
                var pos = styleswitcher.offset().top;
                if($(window).scrollTop() > pos){
                    $("html, body").animate({scrollTop: pos}, 500);
                }
                $.cookie("styleSwitcher", "open", {path: "/"});
            }
        });
        
        <?foreach($arResult as $optionCode => $arOption):?>
            <?if($arOption["IN_SETTINGS_PANEL"] == "Y"):?>
                if($.cookie("plus-minus-<?=$optionCode?>") == "open") {
                    $("#plus-minus-<?=$optionCode?>").removeClass().addClass("minus");
                    $(".style-switcher .block #options-<?=$optionCode?>").show();
                }   
                    
                $("#plus-minus-<?=$optionCode?>").click(function() {
                    var clickitem = $(this);
                    if(clickitem.hasClass("plus")) {
                        clickitem.removeClass().addClass("minus");
                        $.cookie("plus-minus-<?=$optionCode?>", "open", {path: "/"});
                    } else {
                        clickitem.removeClass().addClass("plus");
                        $.removeCookie("plus-minus-<?=$optionCode?>", {path: "/"});
                    }
                    $(".style-switcher .block #options-<?=$optionCode?>").slideToggle();
                });
            <?endif;?>
        <?endforeach;?>           

        //first coolor swither
        var curColor = $(".cur-first-custom-color").data("color");              
            customColorInput = $(".color-scheme-custom-first input[id=option-color-scheme-custom-first]"),
            paletteButton = $(".color-scheme-custom-first button[name=palette_button]"),
            formSwitcher = $("form[name=style-switcher]");
            preloader = $("#hellopreloader_preload");

        paletteButton.spectrum({                
            clickoutFiresChange: false,
            cancelText: "<i class='fa fa-times'></i>",
            chooseText: "<?=Loc::getMessage('PALETTE_CHOOSE_COLOR')?>",
            containerClassName:"palette_cont",              
            move: function(color) {
                var colorCode = color.toHexString();                
                customColorInput.val(colorCode);
                customColorInput.css("border-color", colorCode);
            },
            hide: function(color) {
                var colorCode = color.toHexString();                
                customColorInput.val(colorCode);
                customColorInput.css("border-color", colorCode);
            },
            change: function(color) {
                preloader.show(); 
                customColorInput.parent().find("input").attr("checked", "checked");                            
                formSwitcher.append("<input type='hidden' name='CHANGE_THEME' value='Y' />");                
                formSwitcher.submit();
            }
        });         
                
        if(curColor != undefined && curColor.length > 0) {
            paletteButton.spectrum("set", curColor);            
            customColorInput.val(curColor);
        }
        
        customColorInput.change(function() {                
            var colorCode = $(this).val();
            if(colorCode.length > 0) {
                colorCode = colorCode.replace(/#/g, "");
                if(colorCode.length < 3) {
                    for($i = 0, $l = 6 - colorCode.length; $i < $l; ++$i) {
                        colorCode = colorCode + "0";
                    }                   
                }
                colorCode = "#" + colorCode;
                $(this).val(colorCode);
                customColorInput.css("color", colorCode).css("border-color", colorCode);
            } else {
                if(curColor != undefined && curColor.length > 0) {
                    $(this).val(curColor);
                    customColorInput.css("color", colorCode).css("border-color", colorCode);
                }
            }
        });

        //second coolor swither
        var curColorSecond = $(".cur-second-custom-color").data("color");            
            customColorInputSecond = $(".color-scheme-custom-second input[id=option-color-scheme-custom-second]"),
            paletteButtonSecond = $(".color-scheme-custom-second button[name=palette_button]"),
            formSwitcher = $("form[name=style-switcher]");
            preloader = $("#hellopreloader_preload");

        paletteButtonSecond.spectrum({                
            clickoutFiresChange: false,
            cancelText: "<i class='fa fa-times'></i>",
            chooseText: "<?=Loc::getMessage('PALETTE_CHOOSE_COLOR')?>",
            containerClassName:"palette_cont",              
            move: function(color) {
                var colorCode = color.toHexString();                
                customColorInputSecond.val(colorCode);
                customColorInputSecond.css("border-color", colorCode);
            },
            hide: function(color) {
                var colorCode = color.toHexString();                
                customColorInputSecond.val(colorCode);
                customColorInputSecond.css("border-color", colorCode);
            },
            change: function(color) {
                preloader.show();
                customColorInput.parent().find("input").attr("checked", "checked");                 
                formSwitcher.append("<input type='hidden' name='CHANGE_THEME' value='Y' />");
                formSwitcher.submit();
            }
        });         
                
        if(curColorSecond != undefined && curColorSecond.length > 0) {
            paletteButtonSecond.spectrum("set", curColorSecond);           
            customColorInputSecond.val(curColorSecond);
        }

        customColorInputSecond.change(function() {                
            var colorCode = $(this).val();
            if(colorCode.length > 0) {
                colorCode = colorCode.replace(/#/g, "");
                if(colorCode.length < 3) {
                    for($i = 0, $l = 6 - colorCode.length; $i < $l; ++$i) {
                        colorCode = colorCode + "0";
                    }                   
                }
                colorCode = "#" + colorCode;
                $(this).val(colorCode);
                customColorInputSecond.attr("style", "background:" + colorCode + ";");
            } else {
                if(curColorSecond != undefined && curColorSecond.length > 0) {
                    $(this).val(curColorSecond);
                    customColorInputSecond.attr("style", "background:" + curColorSecond + ";");
                }
            }
        });

        //sortable main blocks
        /*$( "#options-HOME_PAGE" ).sortable({              
            placeholder: "ui-state-highlight",
            update: function( event, ui ) {
                preloader.show();                   
                formSwitcher.append("<input type='hidden' name='CHANGE_THEME' value='Y' />");
                formSwitcher.submit();}
        });         
        $( "#options-HOME_PAGE" ).disableSelection();*/

        
        $(".color-scheme-custom-first").on("keypress", "input[id=option-color-scheme-custom-first]", function(e) {
            if(e.keyCode == 13){    
                e.preventDefault();
                $(this).parent().find("input").attr("checked", "checked");
                formSwitcher.append("<input type='hidden' name='CHANGE_THEME' value='Y' />");
                formSwitcher.submit();
            }
        });

        $(".color-scheme-custom-first").on("blur", "input[id=option-color-scheme-custom-first]", function(e) {             
            e.preventDefault();
            $(this).parent().find("input").attr("checked", "checked");
            formSwitcher.append("<input type='hidden' name='CHANGE_THEME' value='Y' />");
            formSwitcher.submit();           
        });

        $(".color-scheme-custom-second").on("keypress", "input[id=option-color-scheme-custom-second]", function(e) {
            if(e.keyCode == 13){    
                e.preventDefault();
                $(this).parent().find(".colors.custom-forms input").attr("checked", "checked");
                formSwitcher.append("<input type='hidden' name='CHANGE_THEME' value='Y' />");
                formSwitcher.submit();
            }
        });

        $(".color-scheme-custom-second").on("blur", "input[id=option-color-scheme-custom-second]", function(e) {               
            e.preventDefault();
            $(this).parent().find(".colors.custom-forms input").attr("checked", "checked");
            formSwitcher.append("<input type='hidden' name='CHANGE_THEME' value='Y' />");
            formSwitcher.submit();
           
        });
        
        $(".style-switcher .reset button[name=reset_button]").click(function(e) {
            preloader.show();
            formSwitcher.append("<input type='hidden' name='CHANGE_THEME' value='Y' />");
            formSwitcher.append("<input type='hidden' name='THEME' value='default' />");
            formSwitcher.submit();
        });
        
        $(".style-switcher .options input[type=radio], .style-switcher .options input[type=checkbox]").click(function(e) {
            preloader.show();       
            formSwitcher.append("<input type='hidden' name='CHANGE_THEME' value='Y' />");                   
            formSwitcher.submit();
        });
    });
</script>