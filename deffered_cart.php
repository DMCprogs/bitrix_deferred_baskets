<?
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

\Bitrix\Main\Loader::IncludeModule('iblock');
\Bitrix\Main\Loader::includeModule('sale');
\Bitrix\Main\Loader::includeModule("catalog");
$fuserId = \Bitrix\Sale\Fuser::getId();
   $nowDate=date('Y-m-d H:i:s');
   $basket = \Bitrix\Sale\Basket::loadItemsForFUser(\Bitrix\Sale\Fuser::getId(), \Bitrix\Main\Context::getCurrent()->getSite());
   $array_items=json_decode($_REQUEST["item_array"],true);
  
   $basketItems = [];
   foreach ($basket as $basketItem) {
    if(!empty($array_items)){
foreach ($array_items as $key => $value) {
  if($basketItem->getField('PRODUCT_ID')==$value){
    $array_items["BASKET_ID"][]=$basketItem->getId();
    $basketItems[] = [
        'ID' => $basketItem->getId(),
        'NAME' => $basketItem->getField('NAME'),
        'PRICE' => $basketItem->getPrice(),
        'QUANTITY' => $basketItem->getQuantity(),
        'CURRENCY' => $basketItem->getCurrency(),
        'PRODUCT_ID'=>$basketItem->getField('PRODUCT_ID'),
        'CATALOG_XML_ID'=>$basketItem->getField('PRODUCT_XML_ID'),
       
    ]; 

  }
}
    }
    else {
        $basketItems[] = [
            'ID' => $basketItem->getId(),
            'NAME' => $basketItem->getField('NAME'),
            'PRICE' => $basketItem->getPrice(),
            'QUANTITY' => $basketItem->getQuantity(),
            'CURRENCY' => $basketItem->getCurrency(),
            'PRODUCT_ID'=>$basketItem->getField('PRODUCT_ID'),
            'CATALOG_XML_ID'=>$basketItem->getField('PRODUCT_XML_ID'),
           
        ];  
    }
      
   }
   $basketItemsJson = json_encode($basketItems, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    $el = new CIBlockElement;
    $arFields = [
        'IBLOCK_SECTION_ID' => false,
        'IBLOCK_ID' => 276,
        'PROPERTY_VALUES' => [
            "USER_ID"=>$fuserId,
            "ARRAY_CART"=>$basketItemsJson,
        ],
        'NAME' => 'Корзина от '. $nowDate,
        'ACTIVE' => 'Y',
        'PREVIEW_TEXT' => '',
        'DETAIL_TEXT' =>  '',
    ];

    if($ELEMENT_ID = $el->Add($arFields)) {
        if(!empty($array_items)){
            
         foreach ($array_items["BASKET_ID"] as $key => $value_id) {
         
            $basket->getItemById($value_id)->delete();
            $basket->save();
         }
        }
        else {
            $result=CSaleBasket::DeleteAll(CSaleBasket::GetBasketUserID());
        }
         
        echo json_encode(["status" => "success", "ELEMENT_ID" => $ELEMENT_ID]);
    } else {
        echo json_encode(["status" => "error", "message" => $el->LAST_ERROR]);
    }

?>