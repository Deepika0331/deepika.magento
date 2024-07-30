<?php

declare(strict_types=1);

namespace Meticulosity\CollectShipping\Setup\Patch\Schema;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Setup\SalesSetupFactory;

/**
 * Class OrderCustomAttribute
 * @package Meticulosity\CollectShipping\Setup\Patch\Schema
 */
class OrderCustomAttribute implements DataPatchInterface
{
    const ATTRIBUTE_CARRIER = 'custom_carrier';

    const ATTRIBUTE_ACCOUNT_NUMBER = 'custom_account_number';

    /**
     * @var SalesSetupFactory
     */
    private $salesSetupFactory;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var QuoteSetupFactory
     */
    private $quoteSetupFactory;

    /**
     * OrderCustomAttribute constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param QuoteSetupFactory $quoteSetupFactory
     * @param SalesSetupFactory $salesSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        QuoteSetupFactory $quoteSetupFactory,
        SalesSetupFactory $salesSetupFactory
    ) {
        $this->salesSetupFactory = $salesSetupFactory;
        $this->moduleDataSetup = $moduleDataSetup;
        $this->quoteSetupFactory = $quoteSetupFactory;
    }
    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $salesSetup = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $this->moduleDataSetup]);
        $quoteSetup = $this->quoteSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $salesSetup->addAttribute(Order::ENTITY, self::ATTRIBUTE_CARRIER, [
            'type' => Table::TYPE_TEXT,
            'label' => 'Carrier',
            'input' => 'text',
        ]);

        $salesSetup->addAttribute(Order::ENTITY, self::ATTRIBUTE_ACCOUNT_NUMBER, [
            'type' => Table::TYPE_TEXT,
            'label' => 'Account Number',
            'input' => 'text',
        ]);

        $quoteSetup->addAttribute('quote', self::ATTRIBUTE_CARRIER, [
            'type' => Table::TYPE_TEXT,
            'label' => 'Carrier',
            'input' => 'text',
        ]);

        $quoteSetup->addAttribute('quote', self::ATTRIBUTE_ACCOUNT_NUMBER, [
            'type' => Table::TYPE_TEXT,
            'label' => 'Account Number',
            'input' => 'text',
        ]);

        $this->moduleDataSetup->endSetup();
    }
}
