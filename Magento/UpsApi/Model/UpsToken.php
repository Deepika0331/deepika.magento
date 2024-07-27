<?php
namespace Meticulosity\UpsApi\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
class UpsToken extends AbstractModel
{
    protected function _construct()
    {
        $this->_init('Meticulosity\UpsApi\Model\ResourceModel\UpsToken');
    }
}