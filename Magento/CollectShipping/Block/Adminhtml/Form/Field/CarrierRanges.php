<?php

declare(strict_types=1);

namespace Meticulosity\CollectShipping\Block\Adminhtml\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;

/**
 * Class Carrier Ranges
 * @package Meticulosity\CollectShipping\Block\Adminhtml\Form\Field
 */
class CarrierRanges extends AbstractFieldArray
{
    /**
     * Prepare rendering the new field by adding all the needed columns
     */
    protected function _prepareToRender()
    {
        $this->addColumn('carrier', ['label' => __('Carrier'), 'class' => 'required-entry']);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     */
    protected function _prepareArrayRow(DataObject $row): void
    {
        $row->setData('option_extra_attrs', []);
    }
}
