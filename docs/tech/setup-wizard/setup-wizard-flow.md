# Setup Wizard Flow

## Overview

The setup wizard guides administrators through initial system configuration. It runs on first visit if the website is not activated.

## Setup Flow

### Step 1: General Information

**Fields:**
- Academy name
- Default language
- Default currency
- Default country
- Timezone

**Actions:**
- Save general information
- Set default language
- Set default currency
- Set default country

### Step 2: Branding

**Fields:**
- Logo upload
- Primary color
- Secondary color
- Main font
- Headings font
- Default theme (light/dark)

**Actions:**
- Upload logo
- Configure colors
- Select fonts
- Preview branding

### Step 3: Pages Configuration

**Fields:**
- Enable/disable pages
- Homepage template selection
- Page configuration

**Actions:**
- Enable required pages
- Select homepage template
- Configure page settings

### Step 4: Contact Information

**Fields:**
- Email address
- Phone number
- Address
- Social media links

**Actions:**
- Save contact information
- Configure contact settings

### Step 5: Email Configuration (Optional)

**Fields:**
- SMTP host
- SMTP port
- SMTP username
- SMTP password
- From email
- From name

**Actions:**
- Configure email settings
- Test email connection

### Step 6: Payment Configuration (Optional)

**Fields:**
- Payment gateway selection
- API keys
- Payment settings

**Actions:**
- Configure payment gateway
- Test payment connection

### Step 7: Review

**Content:**
- Review all settings
- Summary of configuration
- Edit any step

**Actions:**
- Review configuration
- Edit previous steps
- Proceed to launch

### Step 8: Launch

**Content:**
- Activation confirmation
- Success message
- Next steps

**Actions:**
- Activate website
- Create default pages
- Redirect to dashboard

## Setup Logic

### Activation Check

On every page load:
1. Check if website is activated
2. If not activated, redirect to `/setup`
3. If activated, allow normal access

### Setup Status

`GET /api/setup/status` returns:
- `is_activated` - Activation status
- `current_step` - Current setup step
- `completed_steps` - Completed steps
- `settings` - Current settings

### Step Saving

Each step can be saved independently:
- `POST /api/setup/save-step/{step}` - Save step data
- Data saved incrementally
- Can return to previous steps

### Activation

Website activation:
- `POST /api/setup/activate` - Activate website
- Creates default pages
- Sets `is_activated` to `true`
- Enables public access

## Default Activation

### Quick Activation

`POST /api/setup/activate-default`:
- Activates with default settings
- Skips setup wizard
- Creates default pages
- Uses default branding

### Default Settings

- Language: English (en)
- Currency: USD
- Country: None
- Branding: Default colors and fonts
- Pages: Default pages created

## Default Pages

On activation, default pages are created:

### Home Page
- Slug: `home`
- Title: "Home" / "الرئيسية"
- Hero block
- Features block

### About Page
- Slug: `about`
- Title: "About Us" / "من نحن"
- Content block

### Contact Page
- Slug: `contact`
- Title: "Contact Us" / "اتصل بنا"
- Contact form block

### Courses Page
- Slug: `courses`
- Title: "Our Courses" / "دوراتنا"
- Courses listing

### Trainers Page
- Slug: `trainers`
- Title: "Our Trainers" / "مدربونا"
- Instructors listing

### FAQ Page
- Slug: `faq`
- Title: "FAQ" / "الأسئلة الشائعة"
- FAQ content

## Setup Components

### WizardGeneral.vue
- General information form
- Language selection
- Currency selection
- Country selection

### WizardBranding.vue
- Logo upload
- Color picker
- Font selection
- Theme selection

### WizardPages.vue
- Page enable/disable
- Homepage template
- Page configuration

### WizardContact.vue
- Contact information form
- Social media links
- Contact settings

### WizardEmail.vue
- Email configuration form
- SMTP settings
- Test email

### WizardPayment.vue
- Payment gateway selection
- API configuration
- Payment settings

### WizardReview.vue
- Configuration summary
- Edit links
- Launch button

### WizardLaunch.vue
- Activation confirmation
- Success message
- Redirect to dashboard

## Setup State

### SetupWizard Store

`setupWizard.js` store manages:
- Current step
- Setup data
- Loading state
- Error state

### Actions

- `loadStatus()` - Load setup status
- `saveStep(step, data)` - Save step data
- `completeSetup(data)` - Complete setup
- `activateDefault()` - Activate with defaults

## Setup Middleware

### setupCheckMiddleware

Checks setup status:
- If not activated, redirect to `/setup`
- If activated, allow access
- Public routes exempt from check

## Reset Setup

### Reset to Default

`POST /api/setup/reset`:
- Resets all settings
- Deactivates website
- Clears setup data
- Returns to step 1

## Best Practices

1. **Incremental Saving**
   - Save each step independently
   - Allow returning to previous steps
   - Don't lose progress

2. **Validation**
   - Validate each step
   - Show errors clearly
   - Prevent invalid data

3. **User Experience**
   - Clear instructions
   - Progress indicator
   - Preview options
   - Help text

4. **Error Handling**
   - Handle API errors
   - Show user-friendly messages
   - Allow retry

5. **Default Values**
   - Provide sensible defaults
   - Allow quick activation
   - Don't require all steps

## Conclusion

The setup wizard provides:
- Guided configuration
- Incremental saving
- Default activation option
- Default page creation
- Smooth user experience

The wizard ensures the system is properly configured before going live.

