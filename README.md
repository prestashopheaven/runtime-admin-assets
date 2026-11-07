# prestashopheaven/runtime-admin-assets

Loads a PrestaHaven module's admin scripts on its own back-office pages only (after the admin theme), and the TypeScript helpers that start core admin components when they exist.

## Use when

A module adds JavaScript to its own back-office pages (actionAdminControllerSetMedia, HK-05/HK-11), or its admin TypeScript waits for window.prestashop.component and starts core components (Grid, MultistoreConfigField, TranslatableInput).

## Install

```
composer require prestashopheaven/runtime-admin-assets
```

## Usage

<!-- Examples of the public API. -->

---

Read-only split of [`prestashopheaven/mono-runtime-backoffice`](https://github.com/prestashopheaven/mono-runtime-backoffice): changes,
issues and pull requests go there. MIT License.
