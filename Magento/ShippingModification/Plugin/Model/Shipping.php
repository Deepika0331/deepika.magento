<?php

namespace Meticulosity\ShippingModification\Plugin\Model;

use Magento\Backend\Model\Session\Quote as BackendModelSession;


class Shipping
{
   
    /**
     * @var \Magento\Quote\Model\Quote\Address\RateRequest|null
     */
    private $request = null;

    protected $scopeConfig;
    protected $backendModelSession;


   
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
         BackendModelSession $backendModelSession
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->backendModelSession = $backendModelSession;

    }

    /**
     * before plugin
     *
     * @param \Magento\Shipping\Model\Shipping $subject
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return void
     */
    // public function beforeCollectRates(
    //     \Magento\Shipping\Model\Shipping $subject,
    //     \Magento\Quote\Model\Quote\Address\RateRequest $request
    // ) {
    //     $this->request = $request;
    // }

    /**
     * after plugin
     *
     * @param \Magento\Shipping\Model\Shipping $subject
     * @param \Magento\Shipping\Model\Rate\Result] $result
     * @return void
     */
    public function afterCollectRates(\Magento\Shipping\Model\Shipping $subject, $result)
    {
        $result = $subject->getResult();
        $rates = $result->getAllRates();
        $result->reset();

        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();        
        $storeManager  = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $storeID       = $storeManager->getStore()->getStoreId();

        $subTotal= 0;

        $ShippingEnable = $this->scopeConfig->getValue(
                'shipping_modification/general/enable',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeID
            );

        $value_of_charges = $this->scopeConfig->getValue(
                'shipping_modification/general/value_of_charges',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeID
            );
      
        $min_cart_subtotal = $this->scopeConfig->getValue(
                'shipping_modification/general/min_cart_subtotal',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeID
            );

        $priceCurrencyFactory = $objectManager->get('Magento\Directory\Model\CurrencyFactory');
        $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
         
        $currencyCodeFrom = $storeManager->getStore()->getCurrentCurrency()->getCode();
        $currencyCodeTo = $storeManager->getStore()->getBaseCurrency()->getCode();

        $c_rate = $priceCurrencyFactory->create()->load($currencyCodeFrom)->getAnyRate($currencyCodeTo);

        $new_value_of_charges = $value_of_charges * $c_rate;


        $state = $objectManager->get('Magento\Framework\App\State');

        if($state->getAreaCode() == "adminhtml"){
            $quote = $this->backendModelSession->getQuote();
            $subTotal = $quote->getSubtotal();
        }else{

            $session = $objectManager->get('\Magento\Checkout\Model\Session');
            $quote_repository = $objectManager->get('\Magento\Quote\Api\CartRepositoryInterface');
            $qid = $session->getQuoteId();
            $quote = $quote_repository->get($qid);    
            $subTotal = $quote->getSubtotal();

        }
        
        $subTolat_percent = $subTotal * $new_value_of_charges / 100;

        $newprice = 0;
        foreach ($rates as $rate) {
               
            if($ShippingEnable && $subTotal >= $min_cart_subtotal ){

                if($rate->getCarrier() == 'ups'){

                    $price = $rate->getPrice();
                    if($price > 0){
                        $newprice = $price + $subTolat_percent;
                    }
                    $rate->setCost($newprice);
                    $rate->setPrice($newprice);
                    $result->append($rate);
                }   
                else{
                  

                    $price = $rate->getPrice();
                    $rate->setCost($price);
                    $rate->setPrice($price);
                    $result->append($rate);

                }  

            }else{

                    $price = $rate->getPrice();
                    $rate->setCost($price);
                    $rate->setPrice($price);
                    $result->append($rate);

            } 

        }
        
        return $subject;
    }


    
}