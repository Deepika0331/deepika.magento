<?php


namespace Meticulosity\ViewCart\Block;
use Magento\Checkout\Model\Session as CheckoutSession;

class QuoteId extends \Magento\Framework\View\Element\Template
{
 
    //protected $configProvider;

    protected $checkoutSession;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        //\Magento\Checkout\Model\CompositeConfigProvider $configProvider,
        CheckoutSession $checkoutSession,
        array $data = []
    ) {
        parent::__construct($context, $data); 
        //$this->configProvider = $configProvider;
        $this->checkoutSession = $checkoutSession;
    }


    // /**
    //  * Retrieve checkout configuration
    //  *
    //  * @return array
    //  * @codeCoverageIgnore
    //  */
    // public function getCheckoutConfig()
    // {
    //     return $this->configProvider->getConfig();
    // }

    public function getQuoteId(): int
    {
        return (int)$this->checkoutSession->getQuote()->getId();
    }

}