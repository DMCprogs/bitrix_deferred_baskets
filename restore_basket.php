<?
include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

\Bitrix\Main\Loader::IncludeModule('iblock');
\Bitrix\Main\Loader::includeModule('sale');
\Bitrix\Main\Loader::includeModule("catalog");
$fuserId = \Bitrix\Sale\Fuser::getId();

/** int $productId ID товара */
/** int $quantity количество */



// Идентификатор элемента
$productID = $_REQUEST['elementId']; // Укажите ID товара
$UserOpt=$_REQUEST['user'];
$selectProduct=json_decode($_REQUEST['item_id'],true);
// Получение свойств элемента
$res = CIBlockElement::GetProperty(
    "",      // ID инфоблока
    $productID,      // ID товара
    array("sort" => "asc"), // Сортировка (по умолчанию 'asc')
    array("CODE " => "ARRAY_CART")  // Дополнительные фильтры
);


if($ob = $res->GetNext()){
    
    $textOrder=$ob["~VALUE"]["TEXT"];
    $orderArray=json_decode($textOrder,true);
   
}

$result=CSaleBasket::DeleteAll(CSaleBasket::GetBasketUserID());



$basket = \Bitrix\Sale\Basket::loadItemsForFUser(\Bitrix\Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());
foreach ($orderArray as $key => $value) {
    $ar_res = CPrice::GetBasePrice($value['PRODUCT_ID']);
    if ($item = $basket->getExistsItem('catalog', $value['PRODUCT_ID'])&&!empty($UserOpt)) {
        
       
        if(!empty($selectProduct)){
            foreach ($selectProduct as $key => $value_id) {
                if($value_id==$value["PRODUCT_ID"]){
                    $item->setField('QUANTITY', $item->getQuantity() + $value["QUANTITY"]);
                }
            }
            }
            else{
                $item->setField('QUANTITY', $item->getQuantity() + $value["QUANTITY"]);
            }
    }
    else {
        if(!empty($selectProduct)){
        foreach ($selectProduct as $key => $value_id) {
            if($value_id==$value["PRODUCT_ID"]){
                $item = $basket->createItem('catalog', $value["PRODUCT_ID"]);
            // Если вы хотите добавить товар с произвольной ценой, нужно сделать так:
            $item->setFields(array(
                'QUANTITY' => $value["QUANTITY"],
                'CURRENCY' => Bitrix\Currency\CurrencyManager::getBaseCurrency(),
                'LID' => Bitrix\Main\Context::getCurrent()->getSite(),
                'PRICE' => !empty($UserOpt)?$value["PRICE_1C"]:$ar_res['PRICE'],
                'CUSTOM_PRICE' => 'Y',
                'NAME'=>$value["NAME"],
                "PRODUCT_XML_ID"=>$value["CATALOG_XML_ID"],
    
           ));
            }
        }
        }
        else{
            $item = $basket->createItem('catalog', $value["PRODUCT_ID"]);
            // Если вы хотите добавить товар с произвольной ценой, нужно сделать так:
            $item->setFields(array(
                'QUANTITY' => $value["QUANTITY"],
                'CURRENCY' => Bitrix\Currency\CurrencyManager::getBaseCurrency(),
                'LID' => Bitrix\Main\Context::getCurrent()->getSite(),
                'PRICE' => !empty($UserOpt)?$value["PRICE_1C"]:$ar_res['PRICE'],
                'CUSTOM_PRICE' => 'Y',
                'NAME'=>$value["NAME"],
                "PRODUCT_XML_ID"=>$value["CATALOG_XML_ID"],
    
           ));
        }
       
       
    }
    if ($basket->save()) {
        $success = true;
    }
}
if(empty($selectProduct)){
    CIBlockElement::Delete($_REQUEST['elementId']);
    }
    else {
            foreach ($orderArray as $keyorder => $order) {
               foreach ($selectProduct as $keyid => $value_id_last) {
                if($value_id_last==$order["PRODUCT_ID"]){

                    unset($orderArray[$keyorder]);
                }
               }
            }


        $orderArrayLast = json_encode($orderArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $el = new CIBlockElement;
        $PROP = array();
        $PROP["ARRAY_CART"] = $orderArrayLast;  
        $PROP["USER_ID"] = $fuserId;          
        $arLoadProductArray = Array(
            "MODIFIED_BY"    => $fuserId, // элемент изменен текущим пользователем
            "IBLOCK_SECTION" => false,          // элемент лежит в корне раздела
            "PROPERTY_VALUES"=> $PROP,
        
        );
          
        
        if($res = $el->Update($productID, $arLoadProductArray)) {
          
        } 
    }



    // Проверка успешности выполнения и возврат JSON-ответа
if ($success) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Could not process the order or save the basket."]);
}
    

   
?>