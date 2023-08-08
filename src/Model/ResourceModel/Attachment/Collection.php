<?php


namespace Dealer4Dealer\SubstituteOrders\src\Model\ResourceModel\Attachment;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Dealer4Dealer\SubstituteOrders\src\Model\Attachment',
            'Dealer4Dealer\SubstituteOrders\src\Model\ResourceModel\Attachment'
        );
    }
}
