<?php

declare(strict_types=1);

namespace Meticulosity\CollectShipping\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class SaveCustomFieldsInOrder
 * @package Meticulosity\CollectShipping\Observer
 */
class SaveCustomFieldsInOrder implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();

        $order->setCustomCarrier($quote->getCustomCarrier());
        $order->setCustomAccountNumber($quote->getCustomAccountNumber());

        return $this;
    }
}
