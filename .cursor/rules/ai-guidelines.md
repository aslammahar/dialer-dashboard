---
description: Strict AI coding standards for Laravel + Inertia + Vue migration
alwaysApply: true
---

# AI Code Generation Rules (Laravel + Inertia + Vue)

## General
- Do NOT add unnecessary abstractions.
- Keep logic simple, explicit, and readable.
- Follow existing project structure and naming conventions strictly.
- Prefer incremental, low-risk changes over large rewrites.
- Do not modify unrelated files.

## Migration Safety (Production Protection)
- Existing Blade + Laravel flows are production-critical; do not break or replace them without explicit instruction.
- Build new SPA features in parallel (Inertia + Vue) and keep legacy Blade routes intact during migration.
- Use route prefixes or isolated route groups for new SPA pages unless instructed otherwise.
- Avoid destructive refactors that could affect current production behavior.
- Preserve backward compatibility in controllers, validation, and request/response contracts.

## Frontend Project Config (Vue)
- Frontend stack target is Vue 3 + TypeScript + Inertia.js (NOT React).
- Use Vite-compatible patterns and existing repo tooling.
- Use path alias `@` for `resources/js` imports when configured.
- Keep TypeScript enabled where already used (`<script setup lang="ts">`).

## Laravel (Backend)
- Keep controllers thin; move business logic to Service classes.
- Use Form Request classes for validation.
- Prefer Eloquent/query builder over raw SQL; use raw queries only when necessary.
- Follow RESTful naming conventions for routes, controllers, and actions.
- Reuse existing app patterns for policies, middleware, and authorization.

## Vue (Frontend)
- Use Composition API only.
- Keep components small, focused, and reusable.
- Do not place business/domain logic inside view components.
- Use typed props, emits, and composables.
- Use this top-to-bottom SFC block order:
  1. `<script setup lang="ts">`
  2. `<template>`
  3. `<style scoped>` (only when needed)
- Prefer this order in `<script setup>`:
  1. imports
  2. component metadata/options (`defineOptions`) if used
  3. custom types/interfaces
  4. props, emits, model declarations
  5. constants/refs/reactive state
  6. form setup (`useForm`) and validation bindings
  7. custom methods (arrow functions)
  8. computed properties
  9. watchers (`watch`, `watchEffect`)
  10. lifecycle hooks
  11. expose/public API (`defineExpose`) if needed
- In `<template>`, keep structure top-to-bottom as:
  1. page/container layout
  2. header/title/actions
  3. filters/search/toolbar
  4. main content (cards/table/list/form body)
  5. pagination/footer actions
  6. dialogs/modals/drawers/teleports at the end

## Styling
- Prefer existing design system and utility conventions in this repository.
- Use shared utility classes/components over one-off styling.
- Keep dark mode compatibility where already supported.
- No inline styles unless explicitly required.

## Buttons and Form Actions
- Keep disabled states explicit (`disabled:opacity-50`, `disabled:cursor-not-allowed`, or `disabled:pointer-events-none`).
- Every form submit action must show a loading state while processing.
- Reuse shared `Spinner` component when available for loading indicators.

## Comments (Important)
- Keep comments short, factual, and domain-focused.
- Do not add obvious/noisy comments.
- For non-trivial Vue components, include a short component header block:
  - Component name
  - Purpose
  - Key props (if any)
- Use multiline section comments in long `script setup` blocks when they improve readability.

## Restrictions
- No `console.log` in final code.
- No unused imports, variables, or dead code.
- Avoid magic strings; extract constants for repeated domain literals.
- Do not introduce new dependencies unless necessary and justified.
- Do not change environment/configuration behavior without explicit instruction.
