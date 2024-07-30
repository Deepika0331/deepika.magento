<?php

declare(strict_types=1);

namespace Meticulosity\CollectShipping\Plugin\Quote;

use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Model\ShippingInformationManagement;
use Magento\Quote\Model\QuoteRepository;

/**
 * Class SaveToQuote
 * @package Meticulosity\CollectShipping\Plugin\Quote
 */
class SaveToQuote
{
    protected $quoteRepository;

    public function __construct(QuoteRepository $quoteRepository)
    {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param ShippingInformationManagement $subject
     * @param $cartId
     * @param ShippingInformationInterface $addressInformation
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeSaveAddressInformation(
        ShippingInformationManagement $subject,
        $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        if (!$extAttributes = $addressInformation->getExtensionAttributes()) {
            return;
        }

        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getCustomCarrier()) {
            $quote->setCustomCarrier($extAttributes->getCustomCarrier());
            $quote->setCustomAccountNumber($extAttributes->getCustomAccountNumber());
        }
    }
}
