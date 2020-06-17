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

<script>
	BX.ready(function(){
		new FPShopSearch({
            'ITEMS': <?echo CUtil::PhpToJSObject($arResult)?>,
			'CONTAINER_RESULT': "<?echo $arParams['CONTAINER_RESULT']?>",
			'INPUT': "<?echo $arParams['INPUT']?>",
			'MIN_QUERY_LEN': "<?echo $arParams['MIN_QUERY_LEN']?>",
			'RESULT_COUNT': "<?echo $arParams['RESULT_COUNT']?>",
            'BUTTON': "<?echo $arParams['BUTTON']?>",
		});
	});
</script>