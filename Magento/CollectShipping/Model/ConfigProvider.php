<?php

declare(strict_types=1);

namespace Meticulosity\CollectShipping\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class ConfigProvider
 * @package Meticulosity\CollectShipping\Model
 */
class ConfigProvider implements ConfigProviderInterface
{
    const CARRIERS_OPTIONS_PATH = 'carriers/collect_shipping/carriers';

    /**
     * @var ScopeConfigInterface
     */
    private $config;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * ConfigProvider constructor.
     * @param ScopeConfigInterface $config
     * @param SerializerInterface $serializer
     */
    public function __construct(ScopeConfigInterface $config, SerializerInterface $serializer)
    {
        $this->config = $config;
        $this->serializer = $serializer;
    }

    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        return [
            'collect_shipping' => [
                'carrier_options' => $this->serializer->unserialize($this->config->getValue(self::CARRIERS_OPTIONS_PATH)),
            ],
        ];
    }
}
