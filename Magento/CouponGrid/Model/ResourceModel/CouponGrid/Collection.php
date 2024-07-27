<?php
namespace Meticulosity\CouponGrid\Model\ResourceModel\CouponGrid;
 
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
        $this->_init('Meticulosity\CouponGrid\Model\CouponGrid', 'Meticulosity\CouponGrid\Model\ResourceModel\CouponGrid');
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->storeManager = $storeManager;
    }
    
    protected function _initSelect()
    {
        //my filters
        // $this->addFilterToMap('coupon_code', 'main_table.coupon_code');
        // $this->addFilterToMap('created_at', 'main_table.created_at');
        parent::_initSelect();

                $query = $this->getSelect()->reset(\Zend_Db_Select::COLUMNS)
                //my main table fields
                ->columns('main_table.entity_id')
                ->columns('main_table.increment_id')
                ->columns("CONCAT (main_table.customer_firstname, ' ' , main_table.customer_lastname) AS customer_firstname")
                ->columns('main_table.customer_lastname')
                ->columns('main_table.customer_email')
                ->columns("CONCAT ('$', main_table.subtotal) AS subtotal")
                ->columns("CONCAT ('$', main_table.grand_total) AS grand_total")
                //->columns('coupon_code')
                ->columns("LTRIM(RTRIM(UPPER(main_table.coupon_code))) AS coupon_code")
                ->columns('main_table.status')    
                ->columns('main_table.created_at');
                
                $this->addFilterToMap('coupon_code', 'main_table.coupon_code');
                $this->addFilterToMap('created_at', 'main_table.created_at');
                $this->_logger->error("Query: " . $query->__toString());


                return $this;

    }
    
}