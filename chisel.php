<?php

require getenv('LARAVEL_INSTALLER_AUTOLOADER');

use Laravel\Chisel\Chisel;

$c = Chisel::in(__DIR__)
    ->withAnswers($argv[1] ?? null)
    ->multiselect('auth_features', 'Which authentication features would you like to enable?', [
        'email-verification' => 'Email verification',
        '2fa' => 'Two-factor authentication',
        'passkeys' => 'Passkeys',
    ], hint: 'Use space to select, enter to confirm.');

// ── Email Verification ──────────────────────────────────────────────

$c->selected('auth_features', 'email-verification',
    then: function (Chisel $c) {
        $c->files(
            'resources/js/pages/settings/profile.tsx',
            'app/Providers/FortifyServiceProvider.php',
        )->removeSectionMarkers('email-verification');
    },
    else: function (Chisel $c) {
        $c->phpFile('app/Models/User.php')
            ->removeImport('Illuminate\Contracts\Auth\MustVerifyEmail')
            ->removeInterface('MustVerifyEmail');

        $c->file('config/fortify.php')->removeLinesContaining('Features::emailVerification()');

        $c->files(
            'app/Providers/FortifyServiceProvider.php',
            'resources/js/pages/settings/profile.tsx',
        )->removeSection('email-verification');

        $c->files(
            'resources/js/components/email-verification-notice.tsx',
            'resources/js/pages/auth/verify-email.tsx',
            'tests/Feature/Auth/EmailVerificationTest.php',
            'tests/Feature/Auth/VerificationNotificationTest.php',
        )->delete();
    },
);

// ── Two-Factor Authentication ───────────────────────────────────────

$c->selected('auth_features', '2fa',
    then: function (Chisel $c) {
        $c->files(
            'app/Models/User.php',
            'database/factories/UserFactory.php',
            'resources/js/layouts/settings/layout.tsx',
            'resources/js/types/auth.ts',
            'config/fortify.php',
            'app/Providers/FortifyServiceProvider.php',
            'routes/settings.php',
        )->removeSectionMarkers('2fa');
    },
    else: function (Chisel $c) {
        $c->phpFile('app/Models/User.php')
            ->removeImport('Laravel\Fortify\TwoFactorAuthenticatable')
            ->removeTrait('TwoFactorAuthenticatable');

        $c->files(
            'app/Models/User.php',
            'database/factories/UserFactory.php',
            'config/fortify.php',
            'app/Providers/FortifyServiceProvider.php',
            'routes/settings.php',
            'resources/js/layouts/settings/layout.tsx',
            'resources/js/types/auth.ts',
        )->removeSection('2fa');

        $c->phpFile('routes/settings.php')
            ->removeImport('App\Http\Controllers\Settings\TwoFactorAuthenticationController');

        $c->npm()->remove('input-otp');

        $c->files(
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
        )->delete();
    },
);

// ── Passkeys ───────────────────────────────────────────────────────

$c->selected('auth_features', 'passkeys',
    then: function (Chisel $c) {
        $c->files(
            'config/fortify.php',
            'app/Providers/FortifyServiceProvider.php',
            'routes/settings.php',
            'resources/js/layouts/settings/layout.tsx',
            'resources/js/pages/auth/login.tsx',
            'resources/js/pages/auth/confirm-password.tsx',
        )->removeSectionMarkers('passkeys');
    },
    else: function (Chisel $c) {
        $c->phpFile('app/Models/User.php')
            ->removeImport('Laravel\Fortify\PasskeyAuthenticatable')
            ->removeImport('Laravel\Fortify\Contracts\PasskeyUser')
            ->removeTrait('PasskeyAuthenticatable')
            ->removeInterface('PasskeyUser');

        $c->phpFile('app/Providers/FortifyServiceProvider.php')
            ->removeImport('App\Models\User')
            ->removeImport('Illuminate\Validation\ValidationException');

        $c->files(
            'config/fortify.php',
            'app/Providers/FortifyServiceProvider.php',
            'routes/settings.php',
            'resources/js/layouts/settings/layout.tsx',
            'resources/js/pages/auth/login.tsx',
            'resources/js/pages/auth/confirm-password.tsx',
        )->removeSection('passkeys');

        $c->phpFile('routes/settings.php')
            ->removeImport('App\Http\Controllers\Settings\PasskeysController');

        $c->npm()->remove('@laravel/passkeys');

        $c->files(
            'app/Http/Controllers/Settings/PasskeysController.php',
            'resources/js/components/passkey-item.tsx',
            'resources/js/components/passkey-register.tsx',
            'resources/js/components/passkey-verify.tsx',
            'resources/js/pages/settings/passkeys.tsx',
            'database/migrations/2024_01_01_000000_create_passkeys_table.php',
        )->delete();
    },
);
