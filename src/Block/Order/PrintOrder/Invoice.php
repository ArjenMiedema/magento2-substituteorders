<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Dealer4Dealer\SubstituteOrders\src\Block\Order\PrintOrder;

use Dealer4Dealer\SubstituteOrders\Block\Order\PrintOrder\Address;
use Magento\Framework\View\Element\AbstractBlock;
use function Dealer4Dealer\SubstituteOrders\Block\Order\PrintOrder\__;

/**
 * Sales order details block
 */
class Invoice extends \Magento\Sales\Block\Items\AbstractItems
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Payment\Helper\Data
     */
    protected $_paymentHelper;

    /**
     * @var \Magento\Sales\Model\Order\Address\Renderer
     */
    protected $addressRenderer;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Payment\Helper\Data $paymentHelper
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context                 $context,
        \Magento\Framework\Registry                                      $registry,
        \Magento\Payment\Helper\Data                                     $paymentHelper,
        \Dealer4Dealer\SubstituteOrders\src\Model\Order\Address\Renderer $addressRenderer,
        array                                                            $data = []
    ) {
        $this->addressRenderer = $addressRenderer;
        $this->_paymentHelper = $paymentHelper;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Order # %1', $this->getOrder()->getRealOrderId()));
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/history');
    }

    /**
     * @return string
     */
    public function getPrintUrl()
    {
        return $this->getUrl('*/*/print');
    }

    /**
     * @return string
     */
    public function getPaymentInfoHtml()
    {
        return $this->getChildHtml('payment_info');
    }

    /**
     * @return array|null
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    /**
     * @return array|null
     */
    public function getInvoice()
    {
        return $this->_coreRegistry->registry('current_invoice');
    }

    /**
     * @param AbstractBlock $renderer
     * @return $this
     */
    protected function _prepareItem(AbstractBlock $renderer)
    {
        $renderer->setPrintStatus(true);
        return parent::_prepareItem($renderer);
    }

    /**
     * Get html of invoice totals block
     *
     * @param   \Magento\Sales\Model\Order\Invoice $invoice
     * @return  string
     */
    public function getInvoiceTotalsHtml($invoice)
    {
        $html = '';
        $totals = $this->getChildBlock('invoice_totals');
        if ($totals) {
            $totals->setInvoice($invoice);
            $html = $totals->toHtml();
        }
        return $html;
    }

    /**
     * Returns string with formatted address
     *
     * @param Address $address
     * @return null|string
     */
    public function getFormattedAddress(\Dealer4Dealer\SubstituteOrders\src\Api\Data\OrderAddressInterface $address)
    {
        return $this->addressRenderer->format($address, 'html');
    }
}
