# Website Activation Logic

## Overview

Website activation is the process of enabling public access to the system. The system checks activation status on every request and redirects to setup if not activated.

## Activation Status

### Check Activation

`WebsiteActivationService::isActivated()`:
1. Checks if `website_settings` table exists
2. Gets default website settings
3. Returns `is_activated` value

### Setup Check

`WebsiteActivationService::shouldRunSetup()`:
1. Checks if table exists
2. Checks if already activated
3. Checks if required settings exist
4. Returns `true` if setup needed

## Activation Process

### Manual Activation

Via setup wizard:
1. Complete all setup steps
2. Review configuration
3. Click "Launch"
4. System activates website

### Default Activation

Via API:
1. Call `POST /api/setup/activate-default`
2. System uses default settings
3. Creates default pages
4. Activates website

## Activation Service

### WebsiteActivationService

**Methods:**

#### `isActivated(): bool`
- Checks activation status
- Returns boolean

#### `shouldRunSetup(): bool`
- Checks if setup needed
- Returns boolean

#### `activateDefaultWebsite(): WebsiteSetting`
- Activates with defaults
- Creates default pages
- Returns settings

#### `completeSetup(array $data): WebsiteSetting`
- Completes setup wizard
- Saves all settings
- Creates default pages
- Activates website

#### `saveStep(int $step, array $data): WebsiteSetting`
- Saves setup step
- Updates settings incrementally
- Returns settings

#### `createDefaultPages(): void`
- Creates default CMS pages
- Creates default blocks
- Sets up homepage

## Default Pages Creation

### Pages Created

1. **Home Page**
   - Slug: `home`
   - Hero block
   - Features block
   - Multi-language content

2. **About Page**
   - Slug: `about`
   - Content block
   - Multi-language content

3. **Contact Page**
   - Slug: `contact`
   - Contact form
   - Multi-language content

4. **Courses Page**
   - Slug: `courses`
   - Courses listing
   - Multi-language content

5. **Trainers Page**
   - Slug: `trainers`
   - Instructors listing
   - Multi-language content

6. **FAQ Page**
   - Slug: `faq`
   - FAQ content
   - Multi-language content

### Block Creation

Home page includes:
- Hero block with CTA
- Features block with 3 features
- All blocks enabled by default

## Activation Settings

### Required Settings

- `general_info` - General information
- `branding` - Branding settings
- `default_language` - Default language
- `default_currency` - Default currency
- `enabled_pages` - Enabled pages

### Default Values

If not set, defaults are:
- Language: `en`
- Currency: `USD`
- Branding: Default colors and fonts
- Pages: All enabled

## Middleware Integration

### setupCheckMiddleware

Runs on every request:
1. Checks activation status
2. If not activated and not on `/setup`, redirects
3. If activated, allows access
4. Public routes exempt from check

### Route Protection

Public routes:
- `/setup` - Always accessible
- `/login` - Always accessible
- `/register` - Always accessible
- Other routes - Check activation

## Activation Flow

### First Visit

1. User visits website
2. System checks activation
3. If not activated, redirects to `/setup`
4. User completes setup
5. System activates
6. User redirected to dashboard

### Subsequent Visits

1. User visits website
2. System checks activation
3. If activated, normal access
4. If not activated, redirects to `/setup`

## Database Schema

### website_settings Table

Key fields:
- `is_activated` - Boolean activation flag
- `activated_at` - Activation timestamp
- `branding` - JSON branding settings
- `default_language` - Default language code
- `default_currency` - Default currency code
- `enabled_pages` - JSON enabled pages

## API Endpoints

### Setup Status

`GET /api/setup/status`:
- Returns activation status
- Returns current step
- Returns settings

### Save Step

`POST /api/setup/save-step/{step}`:
- Saves step data
- Updates settings
- Returns settings

### Activate Default

`POST /api/setup/activate-default`:
- Activates with defaults
- Creates default pages
- Returns settings

### Complete Setup

`POST /api/setup/complete`:
- Completes setup
- Activates website
- Returns settings

### Reset

`POST /api/setup/reset`:
- Resets settings
- Deactivates website
- Clears setup data

## Error Handling

### Activation Errors

- Database errors
- Page creation errors
- Settings validation errors

### Error Recovery

- Log errors
- Show user-friendly messages
- Allow retry
- Don't leave system in broken state

## Best Practices

1. **Atomic Operations**
   - Use transactions
   - Rollback on error
   - Ensure consistency

2. **Default Values**
   - Provide sensible defaults
   - Don't require all settings
   - Allow quick activation

3. **Error Handling**
   - Handle all errors
   - Log for debugging
   - Show user-friendly messages

4. **Validation**
   - Validate settings
   - Check required fields
   - Prevent invalid activation

5. **Testing**
   - Test activation flow
   - Test default activation
   - Test error scenarios

## Conclusion

Website activation:
- Controls public access
- Creates default content
- Configures system settings
- Provides smooth onboarding

The activation system ensures the website is properly configured before going live.

