<?php
namespace Meticulosity\UpsApi\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
class UpsToken extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('ups_token', 'id');
    }
}