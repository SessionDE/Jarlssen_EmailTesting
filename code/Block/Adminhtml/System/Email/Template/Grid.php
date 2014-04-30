<?php
 /**
 * @author Oliver Giles <oliver.giles@jarlssen.de>
 * @copyright Copyright © 2014, Jarlssen GmbH
 * @license Proprietary. All rights reserved.
 * @date First created 29.04.14
 */

class Jarlssen_EmailTesting_Block_Adminhtml_System_Email_Template_Grid extends Mage_Adminhtml_Block_System_Email_Template_Grid
{
    protected function _prepareLayout()
    {
        if ($head = $this->getLayout()->getBlock('head')) {
            $head->addItem('js', 'prototype/window.js')
                ->addItem('js_css', 'prototype/windows/themes/default.css')
                ->addCss('lib/prototype/windows/themes/magento.css');
        }
        return parent::_prepareLayout();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('template_id');
        $this->getMassactionBlock()->setFormFieldName('template_id');
        $this->getMassactionBlock()->setUseAjax(true);
        $this->getMassactionBlock()->addItem('test', array(
            'label'=> Mage::helper('tax')->__('Test'),
            'url' => $this->getUrl('*/*/dialog'),
            'complete' => '(function(grid,opt,tx){eval(tx.responseText);})',
            'selected' => true
        ));

        return $this;
    }

}