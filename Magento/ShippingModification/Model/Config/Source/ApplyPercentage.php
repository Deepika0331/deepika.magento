<?php

namespace Meticulosity\ShippingModification\Model\Config\Source;

/**
 * Used in creating options for commetns config value selection
 *
 */
class ApplyPercentage implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Cart Subtotal amount')],
            ['value' => 2, 'label' => __('Shipping Charges from shipperHQ')],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach($this->toOptionArray() as $item) {
            $array[$item['value']] = $item['label'];
        }
        return $array;
    }
}
