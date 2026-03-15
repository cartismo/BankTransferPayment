<?php

namespace Modules\BankTransferPayment\Services;

use App\Contracts\AbstractPaymentMethod;

class BankTransferPaymentService extends AbstractPaymentMethod
{
    /**
     * Get icon identifier for this payment method
     */
    public function getIcon(): string
    {
        return $this->settings['icon'] ?? 'building-library';
    }

    /**
     * Get payment type: always offline for bank transfer
     */
    public function getType(): string
    {
        return self::TYPE_OFFLINE;
    }

    /**
     * Get default order status for bank transfer orders (pending until confirmed)
     */
    public function getOrderStatus(): string
    {
        return $this->settings['order_status'] ?? 'pending';
    }

    /**
     * Get bank details from settings
     */
    public function getBankDetails(): array
    {
        return [
            'bank_name' => $this->settings['bank_name'] ?? '',
            'account_holder' => $this->settings['account_holder'] ?? '',
            'iban' => $this->settings['iban'] ?? '',
            'bic' => $this->settings['bic'] ?? '',
            'additional_info' => $this->settings['additional_info'] ?? '',
        ];
    }

    /**
     * Override getPaymentMethod to include bank_details
     */
    public function getPaymentMethod(float $orderTotal = 0): ?array
    {
        $base = parent::getPaymentMethod($orderTotal);

        if ($base === null) {
            return null;
        }

        $base['bank_details'] = $this->getBankDetails();

        return $base;
    }
}