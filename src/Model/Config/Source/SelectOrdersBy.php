<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class SelectOrdersBy implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'magento_customer_id', 'label' => __('Magento Customer ID')],
            ['value' => 'external_customer_id', 'label' => __('External Customer ID')]
        ];
    }

    public function toArray(): array
    {
        return array_column(
            $this->toOptionArray(),
            'label',
            'value'
        );
    }
}
