<?php
namespace Meticulosity\CouponGrid\Model;
class CouponGrid extends \Magento\Framework\Model\AbstractModel {

    protected function _construct()
    {
        $this->_init('Meticulosity\CouponGrid\Model\ResourceModel\CouponGrid');
    }
}