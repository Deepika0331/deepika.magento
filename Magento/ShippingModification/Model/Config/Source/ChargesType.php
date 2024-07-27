<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Meticulosity\ShippingModification\Model\Config\Source;

class ChargesType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'F',
                'label' => __('Fixed'),
            ],
            [
                'value' => 'P',
                'label' => __('Percent')
            ]
        ];
    }
}
