<?php

namespace Meticulosity\ViewCart\Block\Adminhtml\Buttons;


class BackButton extends \Magento\Catalog\Block\Adminhtml\Product\Edit\Button\Generic
{
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'class' => 'back',
            'on_click' =>  sprintf("location.href = '%s';", $this->getBackUrl()),
            'sort_order' => 10
        ];

    }


    public function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }
}