<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Model;

use Dealer4Dealer\SubstituteOrders\Api\Data\OrderInvoiceRelationInterface;
use Dealer4Dealer\SubstituteOrders\Model\ResourceModel\OrderInvoiceRelation as ResourceModel;
use Magento\Framework\Model\AbstractModel;

class OrderInvoiceRelation extends AbstractModel implements OrderInvoiceRelationInterface
{
    protected function _construct(): void
    {
        $this->_init(ResourceModel::class);
    }

    public function getOrderId(): int
    {
        return $this->getData(self::ORDER_ID);
    }

    public function setOrderId(int $orderId): self
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    public function getInvoiceId(): int
    {
        return $this->getData(self::INVOICE_ID);
    }

    public function setInvoiceId(int $invoiceId): self
    {
        return $this->setData(self::INVOICE_ID, $invoiceId);
    }
}
