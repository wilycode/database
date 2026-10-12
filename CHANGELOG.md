# Changelog

All notable changes to WilyCode Database Driver will be documented in this file.

## [0.1.0] - 2026-10-12

### Added

- Initial public release of the database-agnostic `DatabaseInterface`.
- Shared connection, query, and transaction exception hierarchy.
- `QueryException::fromThrowable()` with parameter-key-only context.
- Contract and exception regression tests with PHP 8.4 GitHub Actions.
- Installation, usage, transaction, and error-handling documentation.
