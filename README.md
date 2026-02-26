# Laravel + React Starter Kit — Passkey Demo

## Setup

```bash
git clone https://github.com/benbjurstrom/react-starter-kit.git react-starter-kit-passkey-demo
cd react-starter-kit-passkey-demo
git checkout fortify-passkeys
composer setup
herd link --secure
```

> Passkeys require HTTPS or localhost. Make sure your `APP_URL` in `.env` matches the URL you're using.

## Configuration

Passkeys can be toggled via the `Features::passkeys()` entry in `config/fortify.php`. All passkey UI elements are conditionally rendered based on this setting.

## Dependencies

This demo depends on several unpublished repos:

- [benbjurstrom/fortify@add-passkey-support](https://github.com/benbjurstrom/fortify/tree/add-passkey-support) — Adds passkey support to Laravel Fortify
- [laravel/passkeys-server](https://github.com/laravel/passkeys-server) — Server-side passkey implementation for Laravel
- [laravel/passkeys](https://github.com/laravel/passkeys) — Client-side helpers
