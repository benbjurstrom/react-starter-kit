# React Starter Kit — Post-Install Options

This branch adds optional post-install customization to the React starter kit. The kit ships with all features enabled, and a post-install script removes anything the user didn't select during `laravel new`.

## Currently supported options

- **Email verification** — `MustVerifyEmail` interface, verify-email page, verification notice component, related tests
- **Two-factor authentication** — `TwoFactorAuthenticatable` trait, 2FA setup/challenge pages, OTP input, related routes, migrations, and tests

## Local setup

```bash
git clone -b post-install-options https://github.com/benbjurstrom/react-starter-kit.git
cd react-starter-kit
composer setup
```

## Testing the post-install script

The `install:features` artisan command lets you run the post-install flow locally without going through `laravel new`:

```bash
php artisan install:features
```

This reads the manifest at `.laravel-installer/manifest.json`, prompts you to select which features to keep, runs the post-install script, then rebuilds npm dependencies and assets.

You can also pass answers non-interactively with the `--answers` flag:

```bash
php artisan install:features --answers='{"auth_features": ["email-verification"]}'
```

## CI

The `post-install.yml` workflow runs on every push and PR. It tests all permutations of the post-install options as a matrix — currently the 4 combinations of email verification and 2FA. Each job runs the post-install script with a given set of answers, then runs the full PHPUnit suite to verify nothing is broken.

## How it works

The `.laravel-installer/` directory contains:

- **`manifest.json`** — Defines the prompts shown to the user. These map to [Laravel Prompts](https://laravel.com/docs/prompts) functions.
- **`post-install.php`** — The script that modifies the project based on the user's answers. It uses the [installer-tools](https://github.com/benbjurstrom/installer-tools) package.

The approach is subtractive: the scaffold includes all feature code by default, wrapped in block markers like `/* @2fa */` / `/* @end-2fa */`. When a feature is selected, the markers are stripped and the code stays. When a feature is not selected, the markers and the code between them are removed, along with related files, imports, traits, and dependencies.

See the [installer-tools README](https://github.com/benbjurstrom/installer-tools) for the full API reference.
