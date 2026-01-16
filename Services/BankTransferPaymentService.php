<?php

namespace Modules\BankTransferPayment\Services;

use App\Models\InstalledModule;

class BankTransferPaymentService
{
    protected ?array $settings = null;

    /**
     * Get module settings
     */
    public function getSettings(): array
    {
        if ($this->settings === null) {
            $module = InstalledModule::where('slug', 'bank-transfer-payment')->first();

            $defaultSettings = [
                'enabled' => true,
                'title' => 'Bank Transfer',
                'description' => 'Pay via bank transfer. You will receive bank account details after placing your order.',
                'instructions' => 'Please make the payment to the following bank account and include your order number as reference.',
                'bank_name' => '',
                'account_holder' => '',
                'iban' => '',
                'bic' => '',
                'additional_info' => '',
                'order_status' => 'pending',
                'sort_order' => 0,
            ];

            $this->settings = array_replace_recursive($defaultSettings, $module?->settings ?? []);
        }

        return $this->settings;
    }

    /**
     * Check if module is enabled
     */
    public function isEnabled(): bool
    {
        return $this->getSettings()['enabled'] ?? false;
    }

    /**
     * Get payment method title
     */
    public function getTitle(): string
    {
        return $this->getSettings()['title'] ?? 'Bank Transfer';
    }

    /**
     * Get payment method description
     */
    public function getDescription(): string
    {
        return $this->getSettings()['description'] ?? '';
    }

    /**
     * Get payment instructions
     */
    public function getInstructions(): string
    {
        return $this->getSettings()['instructions'] ?? '';
    }

    /**
     * Get bank details
     */
    public function getBankDetails(): array
    {
        $settings = $this->getSettings();

        return [
            'bank_name' => $settings['bank_name'] ?? '',
            'account_holder' => $settings['account_holder'] ?? '',
            'iban' => $settings['iban'] ?? '',
            'bic' => $settings['bic'] ?? '',
            'additional_info' => $settings['additional_info'] ?? '',
        ];
    }

    /**
     * Get payment method data for checkout
     */
    public function getPaymentMethod(): ?array
    {
        if (!$this->isEnabled()) {
            return null;
        }

        return [
            'id' => 'bank-transfer-payment',
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'instructions' => $this->getInstructions(),
            'bank_details' => $this->getBankDetails(),
            'type' => 'offline',
        ];
    }

    /**
     * Get order status after placing order with this payment method
     */
    public function getOrderStatus(): string
    {
        return $this->getSettings()['order_status'] ?? 'pending';
    }

    /**
     * Format bank details for display
     */
    public function getFormattedBankDetails(): string
    {
        $details = $this->getBankDetails();
        $lines = [];

        if (!empty($details['bank_name'])) {
            $lines[] = "Bank: {$details['bank_name']}";
        }
        if (!empty($details['account_holder'])) {
            $lines[] = "Account Holder: {$details['account_holder']}";
        }
        if (!empty($details['iban'])) {
            $lines[] = "IBAN: {$details['iban']}";
        }
        if (!empty($details['bic'])) {
            $lines[] = "BIC/SWIFT: {$details['bic']}";
        }
        if (!empty($details['additional_info'])) {
            $lines[] = $details['additional_info'];
        }

        return implode("\n", $lines);
    }
}