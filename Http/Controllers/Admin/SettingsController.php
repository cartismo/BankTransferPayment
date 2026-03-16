<?php

namespace Modules\BankTransferPayment\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\HasMultiStoreModuleSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\BankTransferPayment\Services\BankTransferPaymentService;

class SettingsController extends Controller
{
    use HasMultiStoreModuleSettings;

    protected function getModuleSlug(): string
    {
        return 'bank-transfer-payment';
    }

    protected function getDefaultSettings(): array
    {
        return BankTransferPaymentService::defaultSettings();
    }

    public function index(): Response
    {
        $data = $this->getMultiStoreData();
        $data['translations'] = __('banktransferpayment::settings');

        return Inertia::render('BankTransferPayment::Admin/Settings', $data);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'is_enabled' => 'boolean',
            'settings.enabled' => 'boolean',
            'settings.title' => 'required|string|max:255',
            'settings.description' => 'nullable|string|max:1000',
            'settings.instructions' => 'nullable|string|max:2000',
            'settings.bank_name' => 'nullable|string|max:255',
            'settings.account_holder' => 'nullable|string|max:255',
            'settings.iban' => 'nullable|string|max:50',
            'settings.bic' => 'nullable|string|max:20',
            'settings.additional_info' => 'nullable|string|max:1000',
            'settings.order_status' => 'string|max:50',
            'settings.sort_order' => 'integer|min:0',
        ]);

        return $this->saveStoreSettings($request);
    }
}