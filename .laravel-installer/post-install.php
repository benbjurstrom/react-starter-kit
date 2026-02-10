<?php

require getenv('LARAVEL_INSTALLER_AUTOLOADER');

use Laravel\InstallerTools\PostInstall;

$kit = PostInstall::in(dirname(__DIR__))
    ->withAnswers($argv[1]);

// ── Email Verification ──────────────────────────────────────────────

$kit->selected('auth_features', 'email-verification', function ($kit) {
    // Strip markers, keep all code
    $kit->stripBlock('resources/js/pages/settings/profile.tsx', 'email-verification');
    $kit->stripBlock('app/Providers/FortifyServiceProvider.php', 'email-verification');
}, function ($kit) {
    // User model: remove interface and import
    $kit->php('app/Models/User.php')
        ->removeImport('Illuminate\Contracts\Auth\MustVerifyEmail')
        ->removeInterface('MustVerifyEmail');

    // Fortify config and service provider
    $kit->deleteLinesContaining('config/fortify.php', 'Features::emailVerification()');
    $kit->removeBlock('app/Providers/FortifyServiceProvider.php', 'email-verification');

    // JS/TS blocks
    $kit->removeBlock('resources/js/pages/settings/profile.tsx', 'email-verification');

    // Delete feature files
    $kit->delete(
        'resources/js/components/email-verification-notice.tsx',
        'resources/js/pages/auth/verify-email.tsx',
        'tests/Feature/Auth/EmailVerificationTest.php',
        'tests/Feature/Auth/VerificationNotificationTest.php',
    );
});

// ── Two-Factor Authentication ───────────────────────────────────────

$kit->selected('auth_features', '2fa', function ($kit) {
    // Strip markers, keep all code
    $kit->stripBlock('resources/js/layouts/settings/layout.tsx', '2fa');
    $kit->stripBlock('resources/js/types/auth.ts', '2fa');
    $kit->stripBlock('config/fortify.php', '2fa');
    $kit->stripBlock('app/Providers/FortifyServiceProvider.php', '2fa');
    $kit->stripBlock('routes/settings.php', '2fa');
}, function ($kit) {
    // User model: remove 2FA trait, hidden attrs, cast
    $kit->php('app/Models/User.php')
        ->removeImport('Laravel\Fortify\TwoFactorAuthenticatable')
        ->removeTrait('TwoFactorAuthenticatable')
        ->removeFromArray('hidden', 'two_factor_secret')
        ->removeFromArray('hidden', 'two_factor_recovery_codes')
        ->removeFromArray('casts', 'two_factor_confirmed_at');

    // Factory: remove 2FA fields and method
    $kit->php('database/factories/UserFactory.php')
        ->removeFromArray('definition', 'two_factor_secret')
        ->removeFromArray('definition', 'two_factor_recovery_codes')
        ->removeFromArray('definition', 'two_factor_confirmed_at')
        ->removeMethod('withTwoFactor');

    // Fortify config, service provider, and routes
    $kit->removeBlock('config/fortify.php', '2fa');
    $kit->removeBlock('app/Providers/FortifyServiceProvider.php', '2fa');
    $kit->php('routes/settings.php')
        ->removeImport('App\Http\Controllers\Settings\TwoFactorAuthenticationController');
    $kit->removeBlock('routes/settings.php', '2fa');

    // JS/TS blocks
    $kit->removeBlock('resources/js/layouts/settings/layout.tsx', '2fa');
    $kit->removeBlock('resources/js/types/auth.ts', '2fa');

    // Remove npm dependency
    $kit->npm('remove', 'input-otp');

    // Delete feature files
    $kit->delete(
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
});
