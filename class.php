    <?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Loader,
    \Bitrix\Main\Application;

class SearchShop extends CBitrixComponent
{

    public function onPrepareComponentParams($arParams)
    {

        if ($arParams['CACHE_TYPE'] == 'Y') {
            $arParams['CACHE_TIME'] = 3600;
        }

        return $arParams;
    }

    public function executeComponent()
    {
        global $APPLICATION;

        if($_REQUEST["ajax_start"] === "y"){

            if(!$this->arParams['PROPERTY_CODE'] || !$this->arParams['IBLOCK_ID']) return false;

            $this->arResult = [];
            $prop_site = $this->arParams['PROPERTY_CODE'];

            $cache = \Bitrix\Main\Data\Cache::createInstance();
            $cacheId = md5(serialize($this->arParams));
            $cacheInitDir = str_replace([':', '.'], ['__', '_'], $this->getName());

            if (($this->arParams['CACHE_TYPE'] == 'Y')
                && $cache->initCache($this->arParams["CACHE_TIME"], $cacheId, $cacheInitDir)
            ) {

                $this->arResult = $cache->getVars();

            } elseif ($cache->startDataCache()) {

                if (! Loader::includeModule('iblock')) {
                    return false;
                }

                if (file_exists($_SERVER["DOCUMENT_ROOT"].'/local/php_interface/include/idna_convert.class.php')) {
                    require_once($_SERVER["DOCUMENT_ROOT"].'/local/php_interface/include/idna_convert.class.php');
                    function coderurl($url) {
                        $idn = new idna_convert(array('idn_version' => 2008));
                        $url = $idn->decode($url);
                        return $url;
                    }
                }

                $arFilter['IBLOCK_ID'] = intval($this->arParams['IBLOCK_ID']);
                $arFilter['ACTIVE'] = 'Y';
                $arFilter['!PROPERTY_'.$prop_site] = false;

                $arElements = [];

                if ($rsElements = \CIBlockElement::GetList(
                        ['name'=>'asc'],
                        $arFilter,
                        false,
                        false,
                        ['ID','NAME','IBLOCK_ID','PROPERTY_'.$prop_site]
                    )
                ) {
                    while ($rsElement = $rsElements->Fetch()) {
                        if(function_exists(coderurl)){
                            $site_url = (stripos($rsElement['PROPERTY_'.$prop_site.'_VALUE'], 'xn--') !== false) ? coderurl($rsElement['PROPERTY_'.$prop_site.'_VALUE']) : $rsElement['PROPERTY_'.$prop_site.'_VALUE'];
                            $arElements[$site_url] = [
                                'NAME' => $rsElement['NAME'],
                                'SITE' => $site_url,
                                'HOW' => '1'
                            ];
                        }else{
                             $arElements[$rsElement['PROPERTY_'.$prop_site.'_VALUE']] = [
                                'NAME' => $rsElement['NAME'],
                                'SITE' => $rsElement['PROPERTY_'.$prop_site.'_VALUE'],
                                'HOW' => '1'
                            ];
                        }
                    }
                }

                if(\Bitrix\Main\Loader::IncludeModule("highloadblock") && $this->arParams['HL_NAME'] && $this->arParams['HL_PROPERTY_NAME']){

                    if($hlblock = \Bitrix\Highloadblock\HighloadBlockTable::getList(["filter" => ["NAME" => $this->arParams['HL_NAME']] ])->fetch()){

                        $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($hlblock);
                        $entityDataClass = $entity->getDataClass();

                        $rsData = $entityDataClass::getList([]);

                        while($arElem = $rsData->fetch()){
                            if(function_exists(coderurl)){

                                $arElements[$arElem['ID']] = [
                                    'SITE' => (stripos($arElem[$this->arParams['HL_PROPERTY_NAME']], 'xn--') !== false) ? coderurl($arElem[$this->arParams['HL_PROPERTY_NAME']]) : $arElem[$this->arParams['HL_PROPERTY_NAME']],
                                    'HOW' => '2'
                                ];

                            }else{

                                 $arElements[$arElem['ID']] = [
                                    'SITE' => $arElem[$this->arParams['HL_PROPERTY_NAME']],
                                    'HOW' => '2'
                                ];

                            }
                        }

                    }

                }

                $this->arResult = $arElements;

                $cache->endDataCache($this->arResult);
            }

            $APPLICATION->RestartBuffer();

            $this->IncludeComponentTemplate('ajax_start');

            CMain::FinalActions();

            die();

        }elseif($_REQUEST["ajax_result"] === "y"){

            $this->arResult['HOW'] = $_REQUEST["how"];

            if($_REQUEST["name"]) $this->arResult['NAME'] = $_REQUEST["name"];

            $APPLICATION->RestartBuffer();

            $this->IncludeComponentTemplate('ajax_result');

            CMain::FinalActions();

            die();

        }else{

            $APPLICATION->AddHeadScript($this->GetPath().'/script.js');
            CUtil::InitJSCore(array('ajax'));
            if (! is_null($this->getTemplateName()) ) {
                $this->includeComponentTemplate();
            }

        }

    }


}