<?php
/**
 * A Magento 2 module named Dealer4Dealer/SubstituteOrders
 * Copyright (C) 2017 Maikel Martens
 *
 * This file is part of Dealer4Dealer/SubstituteOrders.
 *
 * Dealer4Dealer/SubstituteOrders is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Dealer4Dealer\SubstituteOrders\src\Api\Data;

interface OrderInvoiceRelationInterface
{

    const ORDERINVOICERELATION_ID = 'orderinvoicerelation_id';
    const ORDER_ID = 'order_id';
    const INVOICE_ID = 'invoice_id';


    /**
     * Get orderinvoicerelation_id
     * @return string|null
     */

    public function getOrderinvoicerelationId();

    /**
     * Set orderinvoicerelation_id
     * @param string $orderinvoicerelation_id
     * @return \Dealer4Dealer\SubstituteOrders\Api\Data\OrderInvoiceRelationInterface
     */

    public function setOrderinvoicerelationId($orderinvoicerelationId);

    /**
     * Get order_id
     * @return string|null
     */

    public function getOrderId();

    /**
     * Set order_id
     * @param string $order_id
     * @return \Dealer4Dealer\SubstituteOrders\Api\Data\OrderInvoiceRelationInterface
     */

    public function setOrderId($order_id);

    /**
     * Get invoice_id
     * @return string|null
     */

    public function getInvoiceId();

    /**
     * Set invoice_id
     * @param string $invoice_id
     * @return \Dealer4Dealer\SubstituteOrders\Api\Data\OrderInvoiceRelationInterface
     */

    public function setInvoiceId($invoice_id);
}
