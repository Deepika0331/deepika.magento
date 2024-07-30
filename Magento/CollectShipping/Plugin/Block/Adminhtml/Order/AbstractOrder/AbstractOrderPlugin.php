<?php

declare(strict_types=1);

namespace Meticulosity\CollectShipping\Plugin\Block\Adminhtml\Order\AbstractOrder;

use Magento\Sales\Block\Adminhtml\Order\AbstractOrder;

/**
 * Class AbstractOrderPlugin
 * @package Meticulosity\CollectShipping\Plugin\Block\Adminhtml\Order\AbstractOrder
 */
class AbstractOrderPlugin
{
    /**
     * @param AbstractOrder $subject
     * @param \Closure $proceed
     * @param $code
     * @param false $strong
     * @param string $separator
     */
    public function aroundDisplayPriceAttribute(AbstractOrder $subject, \Closure $proceed, $code, $strong = false, $separator = '<br/>')
    {
        $dataObject = $subject->getPriceDataObject();
        if ($dataObject instanceof \Magento\Sales\Model\Order) {
            $order = $dataObject;
        } else {
            $order = $dataObject->getOrder();
        }
        if ($order->getShippingMethod() == 'collect_shipping_collect_shipping') {
            return '';
        }
        return $proceed($code, $strong, $separator);
    }
}
