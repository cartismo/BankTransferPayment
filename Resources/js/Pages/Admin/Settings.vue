<script setup>
import { ref, computed } from 'vue';
оimport { Link, router, usePage } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import StoreSettingsTabs from '@/Components/Admin/StoreSettingsTabs.vue';
import { useConfirmDialog } from '@/Composables/useConfirmDialog.js';
import {
    BanknotesIcon,
    ArrowLeftIcon,
    ArrowPathIcon,
    CheckCircleIcon,
    XCircleIcon,
    BuildingLibraryIcon,
    Cog6ToothIcon,
    InformationCircleIcon,
    DocumentTextIcon,
    CreditCardIcon,
} from '@heroicons/vue/24/outline';
import { CheckIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    module: Object,
    stores: Array,
    storeSettings: Object,
    defaultSettings: Object,
});

const page = usePage();
const { confirm } = useConfirmDialog();
const storeTabsRef = ref(null);
const saving = ref(false);

const t = (key) => {
    const translations = page.props.translations?.admin?.payment?.['bank-transfer']?.settings ?? {};
    const keys = key.split('.');
    let value = translations;
    for (const k of keys) {
        if (value && typeof value === 'object' && k in value) {
            value = value[k];
        } else {
            return key;
        }
    }
    return typeof value === 'string' ? value : key;
};

const submit = () => {
    if (!storeTabsRef.value) return;
    saving.value = true;
    router.put(route('admin.payment.bank-transfer.settings.update'), {
        store_id: storeTabsRef.value.activeStoreId,
        is_enabled: storeTabsRef.value.isEnabled,
        settings: storeTabsRef.value.localSettings,
    }, {
        preserveScroll: true,
        onFinish: () => saving.value = false,
    });
};

const resetAll = async () => {
    const confirmed = await confirm({
        title: t('reset'),
        message: t('reset_confirm'),
        variant: 'warning',
    });
    if (confirmed && storeTabsRef.value) {
        Object.assign(storeTabsRef.value.localSettings, props.defaultSettings);
    }
};

const hasChanges = computed(() => {
    if (!storeTabsRef.value) return false;
    const currentStoreSettings = props.storeSettings[storeTabsRef.value.activeStoreId];
    if (!currentStoreSettings) return true;
    const original = { ...props.defaultSettings, ...(currentStoreSettings.settings || {}) };
    return JSON.stringify(storeTabsRef.value.localSettings) !== JSON.stringify(original) ||
           storeTabsRef.value.isEnabled !== currentStoreSettings.is_enabled;
});

const bankDetailsComplete = computed(() => {
    if (!storeTabsRef.value) return false;
    const s = storeTabsRef.value.localSettings;
    return s.bank_name && s.account_holder && s.iban;
});
</script>

<template>
    <AdminLayout :title="`${module.name} - ${t('title')}`">
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <Link :href="route('admin.modules.installed.index')" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <ArrowLeftIcon class="w-5 h-5" />
                    </Link>
                    <div class="flex items-center space-x-3">
                        <div class="p-3 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl shadow-lg">
                            <BanknotesIcon class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">{{ module.name }}</h1>
                            <p class="text-sm text-gray-500">{{ t('title') }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span v-if="hasChanges" class="text-sm text-amber-600 font-medium">{{ t('unsaved_changes') }}</span>
                    <button type="button" @click="resetAll" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                        <ArrowPathIcon class="w-4 h-4 inline mr-2" />{{ t('reset') }}
                    </button>
                    <button type="button" @click="submit" :disabled="saving || !hasChanges" class="px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-emerald-600 to-teal-600 rounded-xl hover:from-emerald-700 hover:to-teal-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-lg shadow-emerald-500/25">
                        <CheckIcon class="w-4 h-4 inline mr-2" />{{ saving ? t('saving') : t('save') }}
                    </button>
                </div>
            </div>
        </template>

        <StoreSettingsTabs ref="storeTabsRef" :stores="stores" :store-settings="storeSettings" :default-settings="defaultSettings" module-slug="bank-transfer-payment">
            <template #default="{ store, settings, updateSetting, isEnabled }">
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    <!-- Left Sidebar -->
                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="p-5 border-b border-gray-100"><h3 class="font-semibold text-gray-900">{{ t('module_status') }}</h3></div>
                            <div class="p-5 space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">{{ t('status') }}</span>
                                    <span :class="settings.enabled ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'" class="px-3 py-1 text-xs font-semibold rounded-full">{{ settings.enabled ? t('active') : t('inactive') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">{{ t('version') }}</span>
                                    <span class="text-sm font-mono text-gray-900">v{{ module.installed_version }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">{{ t('type') }}</span>
                                    <span class="text-sm text-gray-900">{{ t('type') }}</span>
                                </div>
                            </div>
                        </div>

                        <div :class="bankDetailsComplete ? 'bg-gradient-to-br from-emerald-500 to-teal-600' : 'bg-gradient-to-br from-amber-500 to-orange-600'" class="rounded-2xl shadow-lg p-5 text-white">
                            <div class="flex items-center space-x-3 mb-3">
                                <BuildingLibraryIcon class="w-8 h-8 opacity-80" />
                                <div>
                                    <p class="text-sm opacity-80">{{ t('bank_details_card') }}</p>
                                    <p class="text-lg font-bold">{{ bankDetailsComplete ? t('complete') : t('incomplete') }}</p>
                                </div>
                            </div>
                            <div class="space-y-2 pt-3 border-t border-white/20">
                                <div class="flex items-center space-x-2">
                                    <component :is="settings.bank_name ? CheckCircleIcon : XCircleIcon" class="w-4 h-4" />
                                    <span class="text-sm opacity-90">{{ t('bank_name') }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <component :is="settings.account_holder ? CheckCircleIcon : XCircleIcon" class="w-4 h-4" />
                                    <span class="text-sm opacity-90">{{ t('account_holder') }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <component :is="settings.iban ? CheckCircleIcon : XCircleIcon" class="w-4 h-4" />
                                    <span class="text-sm opacity-90">{{ t('iban') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-blue-50 rounded-2xl p-5 border border-blue-100">
                            <div class="flex items-start space-x-3">
                                <InformationCircleIcon class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" />
                                <div>
                                    <h4 class="text-sm font-medium text-blue-900">{{ t('how_it_works') }}</h4>
                                    <p class="text-sm text-blue-700 mt-1">{{ t('how_it_works_text') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side -->
                    <div class="lg:col-span-3 space-y-6">
                        <!-- Enable Toggle -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div :class="settings.enabled ? 'bg-green-100' : 'bg-gray-100'" class="p-3 rounded-xl transition-colors">
                                        <component :is="settings.enabled ? CheckCircleIcon : XCircleIcon" :class="settings.enabled ? 'text-green-600' : 'text-gray-400'" class="w-6 h-6" />
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ t('enable_title') }}</h3>
                                        <p class="text-sm text-gray-500">{{ t('enable_description').replace(':store', store?.name || '') }}</p>
                                    </div>
                                </div>
                                <button type="button" @click="updateSetting('enabled', !settings.enabled)" :class="settings.enabled ? 'bg-green-500' : 'bg-gray-300'" class="relative inline-flex h-7 w-12 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                    <span :class="settings.enabled ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow-lg ring-0 transition duration-200 ease-in-out" />
                                </button>
                            </div>
                        </div>

                        <!-- Display Settings -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                <div class="flex items-center space-x-3">
                                    <Cog6ToothIcon class="w-5 h-5 text-gray-400" />
                                    <h2 class="font-semibold text-gray-900">{{ t('display_settings') }}</h2>
                                </div>
                            </div>
                            <div class="p-6 space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('display_title') }} <span class="text-red-500">*</span></label>
                                        <input type="text" :value="settings.title" @input="updateSetting('title', $event.target.value)" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" :placeholder="t('display_title_placeholder')" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('sort_order') }}</label>
                                        <input type="number" :value="settings.sort_order" @input="updateSetting('sort_order', parseInt($event.target.value) || 0)" min="0" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" placeholder="0" />
                                        <p class="mt-1.5 text-xs text-gray-500">{{ t('sort_order_hint') }}</p>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('checkout_description') }}</label>
                                    <textarea :value="settings.description" @input="updateSetting('description', $event.target.value)" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors resize-none" :placeholder="t('checkout_description_placeholder')"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Account Details -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                <div class="flex items-center space-x-3">
                                    <BuildingLibraryIcon class="w-5 h-5 text-gray-400" />
                                    <h2 class="font-semibold text-gray-900">{{ t('bank_account_details') }}</h2>
                                </div>
                            </div>
                            <div class="p-6 space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('bank_name_label') }} <span class="text-red-500">*</span></label>
                                        <input type="text" :value="settings.bank_name" @input="updateSetting('bank_name', $event.target.value)" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" :placeholder="t('bank_name_placeholder')" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('account_holder_label') }} <span class="text-red-500">*</span></label>
                                        <input type="text" :value="settings.account_holder" @input="updateSetting('account_holder', $event.target.value)" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" :placeholder="t('account_holder_placeholder')" />
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="bg-gray-50 rounded-xl p-5">
                                        <label class="block text-sm font-medium text-gray-700 mb-3">
                                            <CreditCardIcon class="w-4 h-4 inline mr-1 text-gray-500" />{{ t('iban_label') }} <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" :value="settings.iban" @input="updateSetting('iban', $event.target.value)" class="w-full px-4 py-3 font-mono text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors bg-white" :placeholder="t('iban_placeholder')" />
                                    </div>
                                    <div class="bg-gray-50 rounded-xl p-5">
                                        <label class="block text-sm font-medium text-gray-700 mb-3">{{ t('bic_label') }}</label>
                                        <input type="text" :value="settings.bic" @input="updateSetting('bic', $event.target.value)" class="w-full px-4 py-3 font-mono text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors bg-white" :placeholder="t('bic_placeholder')" />
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('additional_info_label') }}</label>
                                    <textarea :value="settings.additional_info" @input="updateSetting('additional_info', $event.target.value)" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors resize-none" :placeholder="t('additional_info_placeholder')"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Instructions -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                                <div class="flex items-center space-x-3">
                                    <DocumentTextIcon class="w-5 h-5 text-gray-400" />
                                    <h2 class="font-semibold text-gray-900">{{ t('instructions_title') }}</h2>
                                </div>
                            </div>
                            <div class="p-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('instructions_label') }}</label>
                                    <textarea :value="settings.instructions" @input="updateSetting('instructions', $event.target.value)" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors resize-none" :placeholder="t('instructions_placeholder')"></textarea>
                                    <p class="mt-2 text-xs text-gray-500">{{ t('instructions_hint') }}</p>
                                </div>
                                <div v-if="settings.instructions" class="mt-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('preview') }}</label>
                                    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                                        <p class="text-sm text-emerald-800 whitespace-pre-wrap">{{ settings.instructions }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </StoreSettingsTabs>
    </AdminLayout>
</template>