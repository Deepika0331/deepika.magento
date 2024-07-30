<?php

declare(strict_types=1);

namespace Meticulosity\CollectShipping\Plugin\Model\Order;

use Magento\Sales\Model\Order;

/**
 * Class OrderPlugin
 * @package Meticulosity\CollectShipping\Plugin\Model\Order
 */
class OrderPlugin
{
    /**
     * @param Order $subject
     * @param \Closure $proceed
     * @return string|null
     */
    public function aroundGetShippingDescription(Order $subject, \Closure $proceed): ?string
    {
        if ($subject->getShippingMethod() == 'collect_shipping_collect_shipping') {
            return __(
                '%1 - Account #%2',
                $subject->getCustomCarrier(),
                $subject->getCustomAccountNumber()
            )->__toString();
        }
        return $proceed();
    }
}
