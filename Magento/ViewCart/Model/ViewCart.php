<?php
namespace Meticulosity\ViewCart\Model;
class ViewCart extends \Magento\Framework\Model\AbstractModel {

    protected function _construct()
    {
        $this->_init('Meticulosity\ViewCart\Model\ResourceModel\ViewCart');
    }
}