<?php

namespace Modules\BankTransferPayment\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\HasMultiStoreModuleSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    use HasMultiStoreModuleSettings;

    protected function getModuleSlug(): string
    {
        return 'bank-transfer-payment';
    }

    protected function getDefaultSettings(): array
    {
        return [
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
    }

    public function index(): Response
    {
        return Inertia::render('BankTransferPayment::Admin/Settings', $this->getMultiStoreData());
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