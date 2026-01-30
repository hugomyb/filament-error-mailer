# Changelog

All notable changes to `filament-error-mailer` will be documented in this file.

## 1.2.0 - 2026-01-30

### Added
- **Smart Application File Detection**: Automatically identifies the first line of code from your application (excluding vendor files) in the stack trace
  - New `appFile` and `appLine` fields in error details
  - Helps developers quickly locate errors in their own code
- **Beautiful Error Details Page** with modern UI:
  - Dark/Light mode toggle with persistent preference (localStorage)
  - Copy error details as Markdown format
  - Copy error details as JSON format
  - Share functionality using Web Share API with fallback
  - Responsive design with smooth transitions
- **Enhanced Error Information Display**:
  - Shows both application file (your code) and origin file (where exception was thrown)
  - Improved visual hierarchy with color-coded sections
  - Better mobile experience

### Changed
- Error details page now uses simple HTML view instead of Filament page for better performance
- Improved authentication flow with proper redirect after login
- Updated README with comprehensive documentation and examples

### Fixed
- Share button now correctly displays feedback on the Share button (not Copy button)
- Copy functionality now works on all browsers using `document.execCommand` fallback
- Dropdown menu closes properly after selection

## 1.1.0 - 2026-01-30

### Added
- Type hints for all methods and properties
- Filament authentication for error details route
- Error filtering by level and type
- Discord webhook integration
- Comprehensive test suite (30 tests)

### Fixed
- Spelling errors in configuration and notifications
- Replaced `env()` calls with `config()` in configuration files
- Added proper error handling for file operations

### Changed
- Refactored NotifyAdminOfError listener into separate services:
  - ErrorStorage
  - ErrorFilter
  - ErrorDetailsBuilder
  - DiscordWebhookBuilder
  - WebhookNotifier
- Improved code quality and maintainability

## 1.0.0 - 2024-01-01

- Initial release
