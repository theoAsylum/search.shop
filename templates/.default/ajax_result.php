<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
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
?>
<?if($arResult['HOW'] != 3){?>
    <div class="modal fade error-message" role="dialog" id="search-error" data-backdrop="static">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?=$APPLICATION->GetCurPage();?>" method="post">
                    <div class="modal-header">
                        <div class="modal-title"><?echo GetMessage('T_SEARCH_ERROR_TITLE_'.$arResult['HOW']).$arResult['NAME']?></div>
                    </div>
                    <div class="modal-body">
                        <p class="text-center"><?=GetMessage('T_SEARCH_ERROR_MESSAGE_'.$arResult['HOW'])?></p>
                    </div>
                    <div class="modal-footer text-center">
                        <button type="submit" class="submit-clean">OK</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        $('#search-error').modal();
        $('.submit-clean').on('click',function(e){
            e.preventDefault();
            $('#search-error').modal( 'hide' );
            setTimeout(function(){$('#search-error').remove()},500);

        });
    </script>
<?}else{?>
    <?$APPLICATION->IncludeComponent(
        "bitrix:form.result.new",
        "button",
        Array(
            "CACHE_TIME" => "3600",
            "CACHE_TYPE" => "N",
            "CHAIN_ITEM_LINK" => "",
            "CHAIN_ITEM_TEXT" => "",
            "EDIT_URL" => "result_edit.php",
            "IGNORE_CUSTOM_TEMPLATE" => "N",
            "LIST_URL" => "result_list.php",
            "SEF_MODE" => "N",
            "SUCCESS_URL" => "",
            "USE_EXTENDED_ERRORS" => "N",
            "VARIABLE_ALIASES" => array("RESULT_ID"=>"RESULT_ID","WEB_FORM_ID"=>"WEB_FORM_ID"),
            "WEB_FORM_ID" => $arParams['WEBFORM_ID'],
            "BUTTON_NAME" => GetMessage('T_SEARCH_ERROR_BUTTON_3'),
            "DEFAULT_FIELD_VALUE" => [
                "SITE_URL" => $arResult["NAME"],
                "EMAIL" => $USER->GetEmail()
            ],
            "TEMPLATE_PARAMS" => [
                "BUTTON_STYLE" => "readmore"
            ]
        )
    );?>
    <script>
        var recaptchaCallback = function() {
            grecaptcha.render('WEB_FORM_<?=$arParams['WEBFORM_ID']?>_recaptcha', {
                'sitekey': "<?=RE_CAPTCHA_PUBLIC_KEY?>"
            });
         };
        $('#WEB_FORM_<?=$arParams['WEBFORM_ID']?>').modal();
    </script>
    <script src="https://www.google.com/recaptcha/api.js?onload=recaptchaCallback&render=explicit" async defer></script>
<?}?>
