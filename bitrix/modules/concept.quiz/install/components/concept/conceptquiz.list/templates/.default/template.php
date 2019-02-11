<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>

<div class="wqec-setting main-set">
    <div class="wqec-inner">
    
        <div class="wqec-head-wrap">

            <div class="name-hameleon hidden-xs"></div>
            <a class="wqec-setting-close" data-button="main-close-list"></a>
            
        </div>

        <div class="wqec-setting-content no-margin-top-bot">
            <div class="wqec-title bold"><?=GetMessage("CWIZ_SETTINGS_LIST_NAME")?></div>

            <ul class="wqec-list">
                
                <?foreach($arResult["SECTIONS"] as $key=>$arSection):?>
                        
                    <li class="wqec-parent">
                        <span class="list-name">
                            <?=$arSection["NAME"]?>
                        </span>
                        <a target="_blank" href="/bitrix/admin/iblock_section_edit.php?IBLOCK_ID=<?=$arResult["IBLOCK_ID"]?>&type=<?=$arResult["IBLOCK_TYPE_ID"]?>&ID=<?=$arSection["ID"]?>&lang=ru&find_section_section=0" class="edit-icon"><span><?=GetMessage("QUIZ_SET_EDIT_QUIEST")?></span></a>
                        <div class="wqec-options-wrap">
                            <div class="wqec-tbl">
                                <div class="wqec-cell wqec-left">

                                    <table>
                                        <tr>
                                            <td class="wqec-btn-preview"><a class="call-wqec-menu" data-wqec-section-id="<?=$arSection["ID"]?>"><?=GetMessage("CWIZ_SETTINGS_LIST_PREVIEW_BUTTON")?></a></td>
                                            <td class="wqec-btn-copy quiz_copy_parent"><a data-clipboard-text='<a class="call-wqec" style="background: #2d7fd4; font-size: 16px; line-height: 16px; color: #fff; padding: 17px 40px; margin-top: 15px; margin-bottom: 15px; border-radius: 4px;" data-wqec-section-id="<?=$arSection["ID"]?>"><?=GetMessage("CWIZ_SETTINGS_LIST_OPEN")?></a>' class="wqec-copy"><?=GetMessage("CWIZ_SETTINGS_LIST_COPY_BUTTON")?></a><span class="wqec-copy-ready quiz_copy_complited"><?=GetMessage("CWIZ_SETTINGS_LIST_COPY")?></span></td>
                                        </tr>
                                    </table>
                                    
                                    <div class="wqec-code">
                                        <textarea name="wqec-code" disabled><a class="call-wqec" style="background: #2d7fd4; font-size: 16px; line-height: 16px; color: #fff; padding: 17px 40px; margin-top: 15px; margin-bottom: 15px; border-radius: 4px;" data-wqec-section-id="<?=$arSection["ID"]?>"><?=GetMessage("CWIZ_SETTINGS_LIST_OPEN")?></a></textarea>
                                    </div>

                                    <div class="quiz_block_code">
                                        <div class="info"><?=GetMessage("CWIZ_INFO_QUIZ_BLOCK")?></div>
                                        <?/*<textarea disabled><div class="areaBlockquiz quiz_block" data-wqec-section-id="<?=$arSection["ID"]?>"></div></textarea>*/?>

                                        <div class="btn_quiz_block quiz_copy_parent">
                                            <a class="wqec-copy" data-clipboard-text='<div class="areaBlockquiz quiz_block" id="cquiz_<?=$arSection["ID"]?>"></div>' class="wqec-copy"><?=GetMessage("CWIZ_SETTINGS_LIST_BUILD_BUTTON")?></a><span class="wqec-copy-ready quiz_copy_complited"><?=GetMessage("CWIZ_SETTINGS_LIST_BUILD")?></span>
                                        </div>
                                        <div class="under_btn_quiz_block">
                                            <div class="under_btn_quiz_block_inner">&lt;div class="areaBlockquiz quiz_block" id="cquiz_<?=$arSection["ID"]?>"&gt;&lt;/div&gt;</div>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="wqec-cell wqec-right">
                                    <ul class="wqec-instruct">
                                        <li><?=GetMessage("CWIZ_SETTINGS_LIST_INSTR_1")?></li>
                                        <li><?=GetMessage("CWIZ_SETTINGS_LIST_INSTR_2")?></li>
                                        <li><?=GetMessage("CWIZ_SETTINGS_LIST_INSTR_3")?></li>
                                        <li><?=GetMessage("CWIZ_SETTINGS_LIST_INSTR_4")?></li>
                                    </ul>
                                     <a href="https://goo.gl/hwiAkj" target="_blank" class="wqec-more-instr"><?=GetMessage("CWIZ_SETTINGS_LIST_MORE_INSTRUCTION")?></a>
                                </div>
                               
                            </div>
                        </div>
                    </li>
                
                <?endforeach;?>
                
            </ul>

            <div class="button-wrap">
                <a target="_blank" href="/bitrix/admin/iblock_section_edit.php?IBLOCK_ID=<?=$arResult["IBLOCK_ID"]?>&type=<?=$arResult["IBLOCK_TYPE_ID"]?>&ID=0&lang=ru&IBLOCK_SECTION_ID=0&find_section_section=0&from=iblock_list_admin" class="btn-set"><?=GetMessage("CWIZ_SETTINGS_LIST_ADD_BUTTON")?></a>
            </div>

        </div>


    </div>

</div> 

<div class="wqec-setting-btn">
    <div class="wqec-btn">
        <span><?=GetMessage("CWIZ_SETTINGS_LIST_BUTTON_TIP")?></span>
    </div>
</div>
