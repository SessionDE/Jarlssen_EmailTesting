<?php
 /**
 * @author Oliver Giles <oliver.giles@jarlssen.de>
 * @copyright Copyright © 2014, Jarlssen GmbH
 * @license Proprietary. All rights reserved.
 * @date First created 29.04.14
 */

class Jarlssen_EmailTesting_Adminhtml_System_Email_TemplateController extends Mage_Adminhtml_Controller_Action
{
    public function chooserAction()
    {
        echo $this->getLayout()->createBlock('jarlssen_emailtesting/adminhtml_customer_chooser')->toHtml();
    }

    public function sendEmailsAction()
    {
        $templateIds = Mage::getModel('adminhtml/session')->getEmailTemplates();
        $customerIds = $this->getRequest()->getParam('customer');

        $exception = null;

        try {
            foreach($customerIds as $c) {
                $customer = Mage::getModel('customer/customer')->load($c);
                foreach($templateIds as $t) {

                    $mailer = Mage::getModel('core/email_template_mailer');
                    $emailInfo = Mage::getModel('core/email_info');
                    $emailInfo->addTo($c->getEmail(), $c->getName());
                    $mailer->addEmailInfo($emailInfo);

                    // Set all required params and send emails
                    $mailer->setSender(array(
                        'name' => 'name',
                        'email' => 'snth@snth.com'
                    ));
                    //$mailer->setStoreId($storeId);
                    $mailer->setTemplateId((int)$t);
                    $order = Mage::getResourceModel('sales/order_collection')
                        ->addFieldToSelect('*')
                        ->addFieldToFilter('customer_id', $customer->getId())
                        ->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
                        ->setOrder('created_at', 'desc')
                        ->getFirstItem();
                    Mage::log($order->debug());
                    $mailer->setTemplateParams(array(
                        'customer' => $customer,
                        'order' => $order
                    ));
                    $mailer->send();
                }

            }
        } catch(Exception $e) {
            Mage::log("got an exception!");
            $exception = $e;
        }

        $message = is_null($exception) ? "Emails successfully sent" : $exception->getMessage();

        echo <<<EOD
    Dialog.info('$message', {
        closable: true,
        className: "magento",
        title: "Done",
        width: 400,
        height: 100,
        onClose: function() { Windows.closeAll(); }
    });
EOD;

    }

    public function dialogAction()
    {
        $templateIds = $this->getRequest()->getParam('template_id');
        Mage::getModel('adminhtml/session')->setEmailTemplates($templateIds);

        /** @var Jarlssen_EmailTesting_Block_Adminhtml_Customer_Chooser $block */
        $block = $this->getLayout()->createBlock('jarlssen_emailtesting/adminhtml_customer_chooser');
        // force preparation of block
        $block->toHtml();

        $childBlockJs = <<<EOD
{$block->getJsObjectName()} = new varienGrid('{$block->getId()}', '{$block->getGridUrl()}', '{$block->getVarNamePage()}', '{$block->getVarNameSort()}', '{$block->getVarNameDir()}', '{$block->getVarNameFilter()}');
{$block->getJsObjectName()}.useAjax = '{$block->getUseAjax()}';
{$block->getMassactionBlock()->getJavaScript()}
window.{$block->getMassactionBlock()->getJsObjectName()} = {$block->getMassactionBlock()->getJsObjectName()};
EOD;

        echo <<<EOD
        text = new Ajax.Request('{$this->getUrl('*/*/chooser', array('template_id'=>$templateIds))}', {
            onComplete:function(transport) {
                Dialog.confirm(transport.responseText, {
                    draggable: true,
                    resizable: true,
                    closable: true,
                    className: "magento",
                    //windowClassName: 'popup-window',
                    title: "Select customers to receive email",
                    width: 900,
                    height: 470,
                    //zIndex: 2100,
                    //recenterAuto: false,
                    //hideEffect: Element.hide,
                    //showEffect: Element.show,
                    //id: "translate-inline",
                    //buttonClass: "form-button button",
                    //okLabel: "Submit",
                    //ok: this.formOk.bind(this),
                    //cancel: this.formClose.bind(this),
                    //onClose: this.formClose.bind(this)
                });
                $childBlockJs
            }
        });
EOD;

    }
}