<?php

namespace Meticulosity\ViewCart\Block\Adminhtml;

use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory as QuoteFactoryData;
use Magento\Framework\DataObject;

class View extends \Magento\Backend\Block\Template
{

    protected $request;

    protected $quoteFactory;


    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\Request\Http $request,
        QuoteFactory $quoteFactory,
        QuoteFactoryData $quoteFactoryData

    )
    {
        parent::__construct($context);
        $this->request = $request;
        $this->quoteFactory = $quoteFactory;
        $this->quoteFactoryData = $quoteFactoryData;

    }

    public function getQuoteData()
    {


        $quoteId = $this->request->getParam('id');
        $quoteData = $this->quoteFactory->create()->load($quoteId);
        $customerData = [];
        $customerData['email'] = $quoteData->getCustomerEmail();
        $customerData['first_name'] = $quoteData->getCustomerFirstname();
        $customerData['last_name'] = $quoteData->getCustomerLastname();
        $customerData['qty'] = $quoteData->getItemsQty();
        $customerData['subtotal'] = $quoteData->getSubtotal();
        $customerData['grandtotal'] = $quoteData->getGrandTotal();
        $customerData['created'] = $quoteData->getCreatedAt();
        $customerData['updated'] = $quoteData->getUpdatedAt();


        return $customerData;
        
    }

    public function getQuoteItems(){

        $quoteId = $this->request->getParam('id');
        $quoteItemData = $this->quoteFactoryData->create()->addFieldToFilter('quote_id',$quoteId)->addFieldToFilter('parent_item_id', array('null' => true) );

        return $quoteItemData->getData();

    }


    public function getPersonlazationData($itemOptionId){

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('quote_item_option'); //gives table name with prefix
        //Select Data from table
        $sql = "Select * FROM " . $tableName." WHERE item_id = ". $itemOptionId." AND code = 'info_buyRequest'";
        $result = $connection->fetchAll($sql); 

        $personlazationData = $result[0]['value'];

        // echo "<pre>";
        // print_r($personlazationData);

        $buyRequest = json_decode($personlazationData,true);
        

        $components = [];

        if(isset($buyRequest['children_items'])){
            $personalizations = [];

            $children = $buyRequest['children_items'];

            $components = json_decode($buyRequest['components']);


            $isMultiPlate = false;

            
            if (reset($components)->type == 'Quantity') {
               $isMultiPlate = true;
            }

            $components = json_decode($buyRequest['components'],true);

            foreach ($children as $child) {
                if (($child['component_type'] == 'Personalization')) {

                    foreach ($components as $key=>$component) {
                        $sequence = 1;
                        $engravingData = [];
                        $engravings = isset($component['engravings'])?$component['engravings']:'';
                        if (($engravings != '') && ($key == $child['label'])) {
                            foreach ($engravings as $engraving) {
                                $haveEngravingText = false;
                                $engravingLinePos = 1;
                                foreach ($engraving['lines'] as $engravingLine) {
                                    if (isset($engravingLine['text']) && !empty($engravingLine['text'])) {
                                        $haveEngravingText = true;
                                        $engravingData[] =
                                            [
                                                'sequence' => $sequence,
                                                'position' => $engravingLinePos,
                                                'value' => $engravingLine['text'],
                                            ];
                                        
                                    }
                                    $engravingLinePos++;
                                }
                                if ($haveEngravingText) {
                                    $sequence++;
                                }
                            }
                            $personalization = 
                                [
                                    'sku'=> $child['product_sku'],
                                    'natural_id' => $child['natural_id'],
                                    'label' => $child['label'],
                                    'value' => $child['frontend_value'],
                                    'engraving' => $engravingData,
                                    'price' => sprintf('$%.2f', $child['price']),
                                    'multi_plate' => $isMultiPlate,
                                ];
                        
                            $personalizations[] = $personalization;
                            break;
                        }
                    }
                } else {
                    $temp = [
                        'sku'=> $child['product_sku'],
                        'natural_id' => $child['natural_id'],
                        'label' => $child['label'],
                        'value' => $child['frontend_value'],
                        'price' => sprintf('$%.2f', $child['price']),
                    ];

                    if (isset($child['logo'])) {
                        $temp = array_merge(
                            $temp,
                            ['logo'=>$child['logo']]
                        );
                    }
                    //$personalization = new DataObject($temp);
                    $personalizations[] = $temp;
                }
            }
            if (count($personalizations)) {
        
                return $personalizations;
            }

        }
        return false;

        

    }
}