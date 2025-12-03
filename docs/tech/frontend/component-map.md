# Frontend Component Map

## Overview

Components are organized by purpose and reusability. This document maps all components and their usage.

## Component Categories

### Common Components (`src/components/common/`)

Reusable UI components used throughout the application:

#### AccessibleButton.vue
- Accessible button with ARIA support
- Used for: All button interactions

#### ErrorBoundary.vue
- Error boundary wrapper
- Used for: Error handling in components

#### FilterDropdown.vue
- Filter dropdown component
- Used for: List filtering

#### Icon.vue
- Icon component
- Used for: Displaying icons

#### LanguagePicker.vue
- Language selection dropdown
- Used for: Language switching

#### LanguageSwitcher.vue
- Language switcher component
- Used for: Language switching in header

#### LoadingSkeleton.vue
- Loading skeleton placeholder
- Used for: Loading states

#### MultiLanguageInput.vue
- Multi-language input field
- Used for: Forms with multiple languages

#### MultiLanguageTextarea.vue
- Multi-language textarea
- Used for: Multi-language text content

#### NotificationCenter.vue
- Notification center component
- Used for: In-app notifications

#### NotificationDropdown.vue
- Notification dropdown
- Used for: Notification display

#### PaginationControls.vue
- Pagination component
- Used for: List pagination

#### ThemeToggle.vue
- Dark/light theme toggle
- Used for: Theme switching

#### ToastContainer.vue
- Toast notification container
- Used for: Success/error messages

### Layout Components (`src/components/layouts/`)

#### DashboardLayout.vue
- Dashboard layout with sidebar
- Used for: All dashboard pages
- Features:
  - Sidebar navigation
  - Header with user menu
  - Role-based navigation
  - Language switcher
  - Theme toggle

#### PublicLayout.vue
- Public website layout
- Used for: All public pages
- Features:
  - Header navigation
  - Footer
  - Language switcher
  - Theme toggle

### Admin Components (`src/components/admin/`)

#### CountryFormModal.vue
- Country form modal
- Used for: Creating/editing countries

#### CurrencyFormModal.vue
- Currency form modal
- Used for: Creating/editing currencies

#### LanguageFormModal.vue
- Language form modal
- Used for: Creating/editing languages

#### WebsiteStatusPanel.vue
- Website status panel
- Used for: Displaying website activation status

### Public Components (`src/components/public/`)

#### CMSPageRenderer.vue
- CMS page renderer
- Used for: Rendering CMS pages
- Features:
  - Multi-language content
  - Block rendering
  - Dynamic content

#### Block Components (`src/components/public/blocks/`)

##### HeroBlock.vue
- Hero section block
- Used for: Homepage hero sections

##### FeaturesBlock.vue
- Features section block
- Used for: Feature listings

##### TestimonialsBlock.vue
- Testimonials section block
- Used for: Testimonial displays

##### CTABlock.vue
- Call-to-action block
- Used for: CTA sections

##### ContentBlock.vue
- Generic content block
- Used for: General content sections

### Setup Components (`src/components/setup/`)

#### WizardGeneral.vue
- General information step
- Used for: Setup wizard step 1

#### WizardBranding.vue
- Branding configuration step
- Used for: Setup wizard step 2

#### WizardPages.vue
- Pages configuration step
- Used for: Setup wizard step 3

#### WizardContact.vue
- Contact information step
- Used for: Setup wizard step 4

#### WizardEmail.vue
- Email configuration step
- Used for: Setup wizard email setup

#### WizardPayment.vue
- Payment configuration step
- Used for: Setup wizard payment setup

#### WizardReview.vue
- Review step
- Used for: Setup wizard review

#### WizardLaunch.vue
- Launch step
- Used for: Setup wizard completion

## Component Patterns

### Composition API
All components use Composition API:
```vue
<script setup>
import { ref, computed, onMounted } from 'vue';
</script>
```

### Props
Components accept props:
```vue
<script setup>
const props = defineProps({
  item: { type: Object, required: true },
  loading: { type: Boolean, default: false },
});
</script>
```

### Emits
Components emit events:
```vue
<script setup>
const emit = defineEmits(['update', 'delete']);
</script>
```

### Slots
Components use slots for flexibility:
```vue
<template>
  <div class="card">
    <slot name="header" />
    <slot />
    <slot name="footer" />
  </div>
</template>
```

## Component Reusability

### Shared Components
- Common components in `common/`
- Used across multiple features
- Generic and configurable

### Feature Components
- Feature-specific components
- Located in feature folders
- Specific to feature needs

## Component Communication

### Props Down
- Parent passes data to child
- One-way data flow
- Type-checked props

### Events Up
- Child emits events to parent
- Parent handles events
- Loose coupling

### Provide/Inject
- Used for deep component trees
- Avoids prop drilling
- Context sharing

## Component State

### Local State
- `ref()` for reactive primitives
- `reactive()` for objects
- Component-scoped

### Global State
- Pinia stores for shared state
- Accessible from any component
- Persistent state

## Component Styling

### Tailwind CSS
- Utility-first CSS
- Responsive design
- Dark mode support

### Scoped Styles
- Component-scoped styles when needed
- Avoids style conflicts
- Maintains encapsulation

## Component Testing

Components should be tested with:
- Unit tests for logic
- Component tests for rendering
- Integration tests for interactions

## Best Practices

1. **Single Responsibility**
   - Each component has one purpose
   - Keep components focused

2. **Reusability**
   - Extract common patterns
   - Create reusable components

3. **Composition**
   - Compose complex components
   - Use slots for flexibility

4. **Performance**
   - Lazy load heavy components
   - Use `v-memo` for expensive renders
   - Optimize re-renders

5. **Accessibility**
   - Semantic HTML
   - ARIA labels
   - Keyboard navigation
   - Screen reader support

## Conclusion

Components are the building blocks of the frontend. They:
- Provide reusable UI elements
- Encapsulate functionality
- Maintain consistency
- Enable composition
- Support accessibility

