# Laravel + React Starter Kit — Passkey Demo

## Setup

```bash
git clone https://github.com/laravel/react-starter-kit.git react-starter-kit-passkey-demo
cd react-starter-kit-passkey-demo
git checkout fortify-passkeys
composer setup
herd link --secure
```

> Passkeys require a secure (HTTPS) connection. The `herd link --secure` command provisions a local SSL certificate for your site.

## Dependencies

This demo depends on two in-progress branches:

- [benbjurstrom/fortify@add-passkey-support](https://github.com/benbjurstrom/fortify/tree/add-passkey-support) — Adds passkey support to Laravel Fortify
- [laravel/passkeys-server](https://github.com/laravel/passkeys-server) — Server-side passkey (WebAuthn) implementation for Laravel
- [laravel/passkeys](https://github.com/laravel/passkeys) — Client-side React hooks for passkey authentication
