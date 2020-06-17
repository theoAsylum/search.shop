<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arTypes = CIBlockParameters::GetIBlockTypes();

$arIBlocks=array();
$db_iblock = CIBlock::GetList(array("SORT"=>"ASC"), array("SITE_ID"=>$_REQUEST["site"], "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")));
while($arRes = $db_iblock->Fetch())
	$arIBlocks[$arRes["ID"]] = "[".$arRes["ID"]."] ".$arRes["NAME"];

$arProperty_LNS = array();
$rsProp = CIBlockProperty::GetList(array("sort"=>"asc", "name"=>"asc"), array("ACTIVE"=>"Y", "IBLOCK_ID"=>(isset($arCurrentValues["IBLOCK_ID"])?$arCurrentValues["IBLOCK_ID"]:$arCurrentValues["ID"])));
while ($arr=$rsProp->Fetch())
{
	$arProperty[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	if (in_array($arr["PROPERTY_TYPE"], array("L", "N", "S")))
	{
		$arProperty_LNS[$arr["CODE"]] = "[".$arr["CODE"]."] ".$arr["NAME"];
	}
}

$arHL = [];
if(\Bitrix\Main\Loader::IncludeModule("highloadblock")){

    if ($hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList()->fetch()) {
        $arHL[$hlblock['NAME']] = $hlblock['NAME'];
    }
}

if(CModule::IncludeModule("form")){  
    $arWebforms = [];
    $rsForms = CForm::GetList($by="s_id", $order="desc", [], $is_filtered);
    while ($arForm = $rsForms->Fetch())
    {
        $arWebforms[$arForm['ID']] =  "[".$arForm["ID"]."] ".$arForm["NAME"];
    }
}
$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_LIST_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => $arTypes,
			"DEFAULT" => "news",
			"REFRESH" => "Y",
		),
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_DESC_LIST_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => '8',
			"ADDITIONAL_VALUES" => "Y",
			"REFRESH" => "Y",
		),
        "PROPERTY_CODE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_IBLOCK_PROPERTY"),
			"TYPE" => "LIST",
			"MULTIPLE" => "N",
			"VALUES" => $arProperty_LNS,
		),
        "HL_NAME" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_HL_NAME"),
			"TYPE" => "LIST",
			"VALUES" => $arHL,
			"DEFAULT" => '',
		),
        "HL_PROPERTY_NAME" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_HL_PROPERTY_NAME"),
			"TYPE" => "STRING",
			"DEFAULT" => 'UF_SITE_URL',
		),
        "MIN_QUERY_LEN" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_MIN_QUERY_LEN"),
			"TYPE" => "STRING",
			"DEFAULT" => "4",
		),
        "RESULT_COUNT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_RESULT_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => "4",
		),
        "WEBFORM_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("T_WEBFORM_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arWebforms,
			"DEFAULT" => '8',
		),
        "INPUT" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("T_INPUT"),
            "TYPE" => "STRING",
            "DEFAULT" => "b-where__search-text",
        ),
        "CONTAINER_RESULT" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("T_CONTAINER_RESULT"),
            "TYPE" => "STRING",
            "DEFAULT" => "b-where__search-list",
        ),
        "BUTTON" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("T_BUTTON"),
            "TYPE" => "STRING",
            "DEFAULT" => "b-where__search-submit",
        ),
		"CACHE_TIME"  =>  array("DEFAULT"=>36000000),
	),
);
