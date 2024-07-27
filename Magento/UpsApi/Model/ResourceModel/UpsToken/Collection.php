<?php
namespace Meticulosity\UpsApi\Model\ResourceModel\UpsToken;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('Meticulosity\UpsApi\Model\UpsToken', 'Meticulosity\UpsApi\Model\ResourceModel\UpsToken');
    }
}