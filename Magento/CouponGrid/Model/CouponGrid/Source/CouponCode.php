<?php

namespace Meticulosity\CouponGrid\Model\CouponGrid\Source;

use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory as RuleCollection;


class CouponCode implements \Magento\Framework\Data\OptionSourceInterface
{
    protected $emp;
    protected $_ruleCollection;
    /**
     * @var array
     */
    protected $options;

    public function __construct(
        \Meticulosity\CouponGrid\Model\CouponGrid $emp,
        RuleCollection $ruleCollection
    ) {
        $this->emp = $emp;
        $this->_ruleCollection = $ruleCollection;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $availableOptions = $this->getOptionArray();
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }

    public function getOptionArray()
    {
        $rules = $this->_ruleCollection->create();


        $rulesListOptions = [];
        foreach ($rules as $rule) {
            //$rulesListOptions = [$rule['code'] => $rule['name']];
            //$rulesListName[] = $rule['name'];

            $code = $rule['code'];
            $name = $rule['name'];

            $rulesListOptions[$code] = $code;
        }

        return $rulesListOptions;
    }
}
