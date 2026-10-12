# Contributing to WilyCode Database Driver

Thank you for your interest in improving this library.

## Getting started

1. Fork this repository and create a branch for your change.
2. Install development dependencies with `composer install`.
3. Run `composer validate --strict` and `composer test`.
4. Open a pull request with a concise explanation and relevant tests.

## Guidelines

- Maintain compatibility with PHP 8.4+.
- Keep this package **database-agnostic**. Driver-specific code belongs in adapter repositories.
- Preserve the `DatabaseInterface` contract unless the change is explicitly discussed as a breaking change.
- Include tests for behavior and exception handling, and update `README.md` when behavior changes.
- Never include credentials or real database connection strings in examples or tests.

Bugs and feature requests can be reported through [GitHub Issues](https://github.com/wilycode/database-driver/issues).
