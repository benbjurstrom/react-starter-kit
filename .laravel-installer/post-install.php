<?php

require getenv('LARAVEL_INSTALLER_AUTOLOADER');

use Laravel\InstallerTools\PostInstall;

$tool = PostInstall::in(dirname(__DIR__))
    ->withAnswers($argv[1] ?? null)
    ->multiselect('auth_features', 'Which authentication features would you like to enable?', [
        'email-verification' => 'Email verification',
        '2fa' => 'Two-factor authentication',
    ], hint: 'Use space to select, enter to confirm.');

// ── Email Verification ──────────────────────────────────────────────

$tool->selected('auth_features', 'email-verification',
    then: function ($tool) {
        $tool->removeSectionMarkers('email-verification')->from(
            'resources/js/pages/settings/profile.tsx',
            'app/Providers/FortifyServiceProvider.php',
        );
    },
    else: function ($tool) {
        $tool->php('app/Models/User.php')
            ->removeImport('Illuminate\Contracts\Auth\MustVerifyEmail')
            ->removeInterface('MustVerifyEmail');

        $tool->deleteLinesContaining('config/fortify.php', 'Features::emailVerification()');

        $tool->removeSection('email-verification')->from(
            'app/Providers/FortifyServiceProvider.php',
            'resources/js/pages/settings/profile.tsx',
        );

        $tool->deleteFiles(
            'resources/js/components/email-verification-notice.tsx',
            'resources/js/pages/auth/verify-email.tsx',
            'tests/Feature/Auth/EmailVerificationTest.php',
            'tests/Feature/Auth/VerificationNotificationTest.php',
        );
    },
);

// ── Two-Factor Authentication ───────────────────────────────────────

$tool->selected('auth_features', '2fa',
    then: function ($tool) {
        $tool->removeSectionMarkers('2fa')->from(
            'resources/js/layouts/settings/layout.tsx',
            'resources/js/types/auth.ts',
            'config/fortify.php',
            'app/Providers/FortifyServiceProvider.php',
            'routes/settings.php',
        );
    },
    else: function ($tool) {
        $tool->php('app/Models/User.php')
            ->removeImport('Laravel\Fortify\TwoFactorAuthenticatable')
            ->removeTrait('TwoFactorAuthenticatable')
            ->removeFromPropertyArray('hidden', 'two_factor_secret')
            ->removeFromPropertyArray('hidden', 'two_factor_recovery_codes')
            ->removeFromPropertyArray('casts', 'two_factor_confirmed_at');

        $tool->php('database/factories/UserFactory.php')
            ->removeFromMethodArray('definition', 'two_factor_secret')
            ->removeFromMethodArray('definition', 'two_factor_recovery_codes')
            ->removeFromMethodArray('definition', 'two_factor_confirmed_at')
            ->removeMethod('withTwoFactor');

        $tool->removeSection('2fa')->from(
            'config/fortify.php',
            'app/Providers/FortifyServiceProvider.php',
            'routes/settings.php',
            'resources/js/layouts/settings/layout.tsx',
            'resources/js/types/auth.ts',
        );

        $tool->php('routes/settings.php')
            ->removeImport('App\Http\Controllers\Settings\TwoFactorAuthenticationController');

        $tool->npm()->remove('input-otp');

        $tool->deleteFiles(
            'app/Http/Controllers/Settings/TwoFactorAuthenticationController.php',
            'app/Http/Requests/Settings/TwoFactorAuthenticationRequest.php',
            'resources/js/pages/settings/two-factor.tsx',
            'resources/js/pages/auth/two-factor-challenge.tsx',
            'resources/js/components/two-factor-setup-modal.tsx',
            'resources/js/components/two-factor-recovery-codes.tsx',
            'resources/js/components/ui/input-otp.tsx',
            'resources/js/hooks/use-two-factor-auth.ts',
            'database/migrations/2025_08_14_170933_add_two_factor_columns_to_users_table.php',
            'tests/Feature/Settings/TwoFactorAuthenticationTest.php',
            'tests/Feature/Auth/TwoFactorChallengeTest.php',
        );
    },
);
