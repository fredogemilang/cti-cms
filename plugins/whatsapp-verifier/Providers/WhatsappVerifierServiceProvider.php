<?php

namespace Plugins\WhatsappVerifier\Providers;

use App\Models\FormField;
use App\Providers\CmsPluginServiceProvider;
use App\Services\SettingsRegistry;
use App\Support\Filter;
use Plugins\WhatsappVerifier\Services\WhatsappGatewayService;

class WhatsappVerifierServiceProvider extends CmsPluginServiceProvider
{
    protected string $pluginSlug = 'whatsapp-verifier';

    /**
     * Register bindings in the container.
     */
    protected function registerBindings(): void
    {
        $this->app->singleton(WhatsappGatewayService::class, function () {
            return new WhatsappGatewayService;
        });
    }

    /**
     * Bootstrap plugin services.
     */
    public function boot(): void
    {
        parent::boot();

        // 1. Register field type into Form Studio
        $custom = $this->app->bound('theme_custom_field_types') ? $this->app->make('theme_custom_field_types') : [];
        $hasWaType = collect($custom)->contains('type', 'wa_verified_phone');
        if (! $hasWaType) {
            $custom[] = [
                'type' => 'wa_verified_phone',
                'label' => 'WA Verified Phone',
                'icon' => 'verified_user',
                'category' => 'advanced',
            ];
            $this->app->instance('theme_custom_field_types', $custom);
        }

        // 2. Hook into server-side form submission validation
        Filter::add('form.validate_field', function ($validation, FormField $field, $value, array $data, $request) {
            if ($validation !== true) {
                return $validation; // Built-in validation failed already
            }

            // Target phone fields: 'tel', 'wa_verified_phone', or field_id 'phone'
            $isPhoneField = in_array($field->type, ['tel', 'wa_verified_phone'], true)
                || $field->field_id === 'phone'
                || str_contains(strtolower($field->field_id), 'phone')
                || str_contains(strtolower($field->field_id), 'whatsapp');

            if (! $isPhoneField) {
                return true;
            }

            // Follow core form field requirement setting (user preference: "ikut settingan form sekarang")
            $isEmpty = empty($value);
            if ($field->is_required && $isEmpty) {
                return "{$field->label} is required.";
            }

            // If empty and not required, pass validation
            if ($isEmpty) {
                return true;
            }

            // If value is provided (or if required), verify that it was verified via WhatsApp OTP
            $service = app(WhatsappGatewayService::class);
            $token = $data['_wa_verification_token'] ?? null;

            if (! $service->isPhoneVerified((string) $value, $token, $request)) {
                return t('kouventa.Please verify your WhatsApp phone number with the OTP code.', 'Please verify your WhatsApp phone number with the OTP code.');
            }

            return true;
        }, 15);
    }

    /**
     * Register settings in Admin Panel (/admin/settings/whatsapp-verifier).
     */
    protected function registerSettings(SettingsRegistry $registry): void
    {
        $registry->registerGroup('whatsapp-verifier', [
            'label' => 'WhatsApp Verifier',
            'icon' => 'chat',
            'order' => 115,
            'description' => 'Configure WhatsApp OTP Gateway integration for form phone verification.',
            'fields' => [
                [
                    'key' => 'wa_verifier_gateway_url',
                    'label' => 'Gateway Endpoint URL',
                    'type' => 'text',
                    'default' => 'https://kouventa-becore.helios.id/openApi/chat/sends',
                    'rules' => ['required', 'url'],
                    'help' => 'Helios Open API chat sends endpoint.',
                    'order' => 10,
                ],
                [
                    'key' => 'wa_verifier_jwt_token',
                    'label' => 'Authorization JWT Token',
                    'type' => 'textarea',
                    'default' => WhatsappGatewayService::DEFAULT_JWT,
                    'rules' => ['required', 'string'],
                    'help' => 'Bearer token for authorization header.',
                    'order' => 20,
                ],
                [
                    'key' => 'wa_verifier_template_code',
                    'label' => 'Template Code',
                    'type' => 'text',
                    'default' => '1627',
                    'rules' => ['required', 'string'],
                    'help' => 'Template code registered in WhatsApp Gateway.',
                    'order' => 30,
                ],
                [
                    'key' => 'wa_verifier_otp_message',
                    'label' => 'Message Parameter Template',
                    'type' => 'text',
                    'default' => 'Use the following OTP to complete your Sign Up procedures.',
                    'rules' => ['required', 'string'],
                    'help' => 'Parameter text sent in parameter index 1.',
                    'order' => 40,
                ],
                [
                    'key' => 'wa_verifier_otp_expiry',
                    'label' => 'OTP Expiry (Minutes)',
                    'type' => 'number',
                    'default' => 5,
                    'rules' => ['required', 'integer', 'min:1'],
                    'help' => 'How long the OTP is valid before expiring.',
                    'order' => 50,
                ],
                [
                    'key' => 'wa_verifier_cooldown',
                    'label' => 'Resend Cooldown (Seconds)',
                    'type' => 'number',
                    'default' => 60,
                    'rules' => ['required', 'integer', 'min:10'],
                    'help' => 'Countdown timer before user can request another OTP.',
                    'order' => 60,
                ],
                [
                    'key' => 'wa_verifier_simulation_mode',
                    'label' => 'Simulation Mode (Dev / Testing)',
                    'type' => 'boolean',
                    'default' => false,
                    'rules' => ['nullable', 'boolean'],
                    'help' => 'If enabled, OTP code will be logged to server logs without calling the real WhatsApp Gateway (useful for testing).',
                    'order' => 70,
                ],
            ],
        ]);
    }
}
