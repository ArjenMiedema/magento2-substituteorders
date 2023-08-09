<?php

declare(strict_types=1);

namespace Dealer4Dealer\SubstituteOrders\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class RealOrderId implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'magento', 'label' => __('Magento')],
            ['value' => 'external', 'label' => __('External')]
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
