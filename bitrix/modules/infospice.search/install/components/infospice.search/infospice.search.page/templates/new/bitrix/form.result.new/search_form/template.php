<?
if ( !defined( "B_PROLOG_INCLUDED" ) || B_PROLOG_INCLUDED !== true ) die();
?>
<? if ( $arResult["isFormErrors"] == "Y" ): ?><?= $arResult["FORM_ERRORS_TEXT"]; ?><? endif; ?>

<?
if ( $arResult["FORM_NOTE"] )
{
    ?>
    <div class="infospice-search-request-result" >
        <h3 ><?= GetMessage( "INFOSPICE_SEARCH_THANKS" ) ?></h3 >

        <p ><?= $arParams["SUCCESS_TEXT"] ?></p >
    </div >
    <?
}
else
{
    $arResult["QUESTIONS"]["INFOSPICE_SEARCH_FORM_4"]["HTML_CODE"] = '<input type="hidden" value="' . htmlspecialchars(
            $_REQUEST["q"]
        ) . '" name="form_hidden_' . $arResult["QUESTIONS"]["INFOSPICE_SEARCH_FORM_4"]["STRUCTURE"][0]["ID"] . '">';
    if ( $arResult["isFormNote"] != "Y" )
    {
        ?>
        <?= $arResult["FORM_HEADER"] ?>

        <?
        /***********************************************************************************
         * form questions
         ***********************************************************************************/
        ?>
        <fieldset class="infospice-search-request-form" >
            <?
            foreach ( $arResult["QUESTIONS"] as $FIELD_SID => $arQuestion )
            {
                if ( is_array( $arResult["FORM_ERRORS"] )
                     && array_key_exists(
                         $FIELD_SID ,
                         $arResult['FORM_ERRORS']
                     )
                )
                { ?>
                    <span class="error-fld" title="<?= $arResult["FORM_ERRORS"][$FIELD_SID] ?>" ></span >
                    <?
                };

                echo $arQuestion["HTML_CODE"];
            }
            ?>
            <?
            if ( $arResult["isUseCaptcha"] == "Y" )
            {
                ?>
                <input type="hidden" name="captcha_sid" value="<?= htmlspecialcharsbx( $arResult["CAPTCHACode"] ); ?>" />
                <img src="/bitrix/tools/captcha.php?captcha_sid=<?= htmlspecialcharsbx( $arResult["CAPTCHACode"] ); ?>"
                     width="180" height="40" />
                <?= GetMessage( "FORM_CAPTCHA_FIELD_TITLE" ) ?><?= $arResult["REQUIRED_SIGN"]; ?>
                <input type="text" name="captcha_word" size="30" maxlength="50" value="" class="inputtext" />
                <?
            }
            ?>
            <input <?= (intval( $arResult["F_RIGHT"] ) < 10 ? "disabled=\"disabled\"" : ""); ?>
                type="submit"
                name="web_form_submit"
                value="<?= htmlspecialcharsbx(
                    strlen(
                        trim(
                            $arResult["arForm"]["BUTTON"]
                        )
                    ) <= 0 ? GetMessage(
                        "FORM_ADD"
                    ) : $arResult["arForm"]["BUTTON"]
                ); ?>" />
        </fieldset >
        <?= $arResult["FORM_FOOTER"] ?>
        <?
    }
}
?>