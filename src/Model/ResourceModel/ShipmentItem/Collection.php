<?php

declare(strict_types=1);

// phpcs:disable GlobalCommon.NamingConventions.ValidVariableName.IllegalVariableNameUnderscore
// phpcs:disable PSR2.Methods.MethodDeclaration.Underscore

namespace Dealer4Dealer\SubstituteOrders\Model\ResourceModel\ShipmentItem;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct(): void
    {
        $this->_init(
            'Dealer4Dealer\SubstituteOrders\Model\ShipmentItem',
            'Dealer4Dealer\SubstituteOrders\Model\ResourceModel\ShipmentItem'
        );
    }
}
