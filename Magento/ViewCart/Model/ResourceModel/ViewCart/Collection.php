<?php
namespace Meticulosity\ViewCart\Model\ResourceModel\ViewCart;
 
/* use required classes */
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    protected $_logger;
 
    /**
     * @param EntityFactoryInterface $entityFactory,
     * @param LoggerInterface        $logger,
     * @param FetchStrategyInterface $fetchStrategy,
     * @param ManagerInterface       $eventManager,
     * @param StoreManagerInterface  $storeManager,
     * @param AdapterInterface       $connection,
     * @param AbstractDb             $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->_logger = $logger;
        $this->_init('Meticulosity\ViewCart\Model\ViewCart', 'Meticulosity\ViewCart\Model\ResourceModel\ViewCart');
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->storeManager = $storeManager;
    }
    
    protected function _initSelect()
    {
        
        parent::_initSelect();

                $query = $this->getSelect()->reset(\Zend_Db_Select::COLUMNS)
                //my main table fields
                ->columns('main_table.entity_id')
                ->columns("CONCAT (main_table.customer_firstname, ' ' , main_table.customer_lastname) AS customer_firstname")
                ->columns('main_table.customer_lastname')
                ->columns('main_table.customer_email')
                //->columns("TRUNCATE(CONCAT('$', main_table.subtotal),2) AS subtotal")
                ->columns("CONCAT ('$',TRUNCATE(main_table.subtotal, 2)) AS subtotal")
                //->columns("CONCAT ('$', main_table.grand_total) AS grand_total")
                ->columns("CONCAT ('$',TRUNCATE(main_table.grand_total, 2)) AS grand_total")
                ->columns('TRUNCATE(main_table.items_qty,0) AS items_qty')
                ->columns('main_table.is_active')
                ->columns('main_table.created_at')
                ->columns('main_table.updated_at');

                $this->addFilterToMap('created_at', 'main_table.created_at');

                $this->addFieldToFilter('main_table.is_active', ['eq' => 1]);

                $this->addFieldToFilter('main_table.subtotal', ['gt' => 0]);

                $this->setOrder('updated_at','DESC');


                $this->_logger->error("Query: " . $query->__toString());


                return $this;

    }
    
}