<?php

namespace Modules\BankTransferPayment\Tests\Unit;

use App\Contracts\AbstractPaymentMethod;
use App\Models\Currency;
use App\Models\InstalledModule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

require_once dirname(__DIR__, 2) . '/Services/BankTransferPaymentService.php';

use Modules\BankTransferPayment\Services\BankTransferPaymentService;

class BankTransferPaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_exposes_offline_payment_contract(): void
    {
        Currency::query()->create([
            'name' => 'Euro',
            'code' => 'EUR',
            'symbol' => 'EUR',
            'symbol_left' => 'EUR ',
            'symbol_right' => null,
            'decimal_places' => 2,
            'exchange_rate' => 1,
            'is_base' => true,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $module = new InstalledModule([
            'slug' => 'bank-transfer-payment',
            'name' => 'Bank Transfer',
            'settings' => [
                'enabled' => true,
                'title' => 'Bank Transfer',
                'description' => 'Pay via bank transfer',
                'bank_name' => 'Test Bank',
                'account_holder' => 'Test Company',
                'iban' => 'BG12STSA93000012345678',
                'bic' => 'STSABGSF',
                'order_status' => 'pending',
            ],
        ]);

        $service = new BankTransferPaymentService($module);
        $method = $service->getPaymentMethod(100);

        $this->assertSame(AbstractPaymentMethod::TYPE_OFFLINE, $service->getType());
        $this->assertSame('building-library', $service->getIcon());
        $this->assertSame('pending', $service->getOrderStatus());
        $this->assertNotNull($method);
        $this->assertSame(AbstractPaymentMethod::TYPE_OFFLINE, $method['type']);
        $this->assertArrayHasKey('bank_details', $method);
        $this->assertSame('BG12STSA93000012345678', $method['bank_details']['iban']);
        $this->assertSame('STSABGSF', $method['bank_details']['bic']);
    }

    public function test_default_settings_returns_expected_keys(): void
    {
        $defaults = BankTransferPaymentService::defaultSettings();

        $this->assertArrayHasKey('enabled', $defaults);
        $this->assertArrayHasKey('title', $defaults);
        $this->assertArrayHasKey('bank_name', $defaults);
        $this->assertArrayHasKey('iban', $defaults);
        $this->assertArrayHasKey('bic', $defaults);
        $this->assertArrayHasKey('order_status', $defaults);
        $this->assertArrayHasKey('sort_order', $defaults);
    }
}