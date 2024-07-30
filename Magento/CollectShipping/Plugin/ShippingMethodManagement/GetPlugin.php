<?php

declare(strict_types=1);

namespace Meticulosity\CollectShipping\Plugin\ShippingMethodManagement;

use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\ShippingMethodManagement;
use Magento\Quote\Api\Data\ShippingMethodExtensionFactory;

/**
 * Class GetPlugin
 * @package Meticulosity\CollectShipping\Plugin\ShippingMethodManagement
 */
class GetPlugin
{
    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;
    /**
     * @var ShippingMethodExtensionFactory
     */
    private $extensionFactory;

    /**
     * GetPlugin constructor.
     * @param CartRepositoryInterface $quoteRepository
     * @param ShippingMethodExtensionFactory $extensionFactory
     */
    public function __construct(CartRepositoryInterface $quoteRepository, ShippingMethodExtensionFactory $extensionFactory)
    {
        $this->quoteRepository = $quoteRepository;
        $this->extensionFactory = $extensionFactory;
    }

    public function aroundGet(ShippingMethodManagement $subject, \Closure $proceed, $cartId)
    {
        $result = $proceed($cartId);
        if (!$result) {
            return $result;
        }
        /** @var Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);

        $extensionAttribute = $result->getExtensionAttributes() ?: $this->extensionFactory->create();
        $extensionAttribute->setData('custom_carrier', $quote->getCustomCarrier());
        $extensionAttribute->setData('custom_account_number', $quote->getCustomAccountNumber());
        $result->setExtensionAttributes($extensionAttribute);

        return $result;
    }
}
