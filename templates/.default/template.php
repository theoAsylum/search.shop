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

<form action="<?= $APPLICATION->GetCurPage()?>" method="get" class="b-where__search b-where__search_right">
    <input type="text" class="b-where__search-text" placeholder="Поиск" name="search_shop" value="<?= $_REQUEST['search_shop'] ? $_REQUEST['search_shop'] : ''?>" autocomplete="off">
    <button class="b-where__search-submit" disabled='disabled'><i class="fa fa-search" aria-hidden="true"></i></button>
    <div class="b-where__search-list"></div>
</form>
<script>
	BX.ready(function(){
		new FPShopSearchStart({
			'INPUT': "<?echo $arParams['INPUT']?>",
            'CONTAINER_RESULT': "<?echo $arParams['CONTAINER_RESULT']?>"
		});
	});
</script>