<?php
 /**
 * @author Oliver Giles <oliver.giles@jarlssen.de>
 * @copyright Copyright © 2014, Jarlssen GmbH
 * @license Proprietary. All rights reserved.
 * @date First created 29.04.14
 */

class Jarlssen_EmailTesting_Block_Adminhtml_Customer_Chooser extends Mage_Adminhtml_Block_Customer_Grid
{

    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        // disable exporting
        $this->_exportTypes = array();
        return $this;
    }
    public function canDisplayContainer(){
        return true;
    }

    private function getActionUrl($storeId)
    {
        $templateIds = $this->getRequest()->getParam('template_id');
        return $this->getUrl('*/*/sendEmails', array('template_id' => $templateIds, 'store_id' => $storeId));
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('customer');
        $this->getMassactionBlock()->setUseAjax(true);
        foreach(Mage::getSingleton('adminhtml/system_store')->getStoreCollection() as $s) {
            $this->getMassactionBlock()->addItem($s->getCode(), array(
                'label'    => $s->getName(),
                'url'      => $this->getActionUrl($s->getStoreId()),
                'complete' => '(function(grid,opt,tx){eval(tx.responseText);})',
            ));

        }
        return $this;
    }

    // disable the row links
    public function getGridUrl()
    {
        return "";
    }

    public function getRowUrl($row)
    {
        return "";
    }
}