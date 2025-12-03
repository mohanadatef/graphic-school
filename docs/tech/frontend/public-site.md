# Frontend Public Site

## Overview

The public website is CMS-driven and supports multi-language content. All public pages use `PublicLayout` and are accessible without authentication.

## Public Pages

### Homepage (`HomePage.vue`)

- CMS-driven content
- Hero section
- Features section
- Call-to-action
- Dynamic blocks

### Courses Page (`CoursesPage.vue`)

- List all published courses
- Filter by category
- Search courses
- Course cards
- Pagination

### Course Details (`CourseDetailsPage.vue`)

- Course information
- Instructor details
- Course schedule
- Enrollment button
- Course reviews

### Trainers Page (`TrainersPage.vue`)

- List all instructors
- Instructor profiles
- Filter by course
- Search instructors

### Instructor Details (`InstructorDetailsPage.vue`)

- Instructor profile
- Bio and experience
- Courses taught
- Contact information

### About Page (`AboutPage.vue`)

- CMS-driven content
- About section
- Mission/vision
- Team information

### Contact Page (`ContactPage.vue`)

- Contact form
- Contact information
- Map (optional)
- Social links

### FAQ Page (`FAQPage.vue`)

- CMS-driven FAQ
- Searchable questions
- Category filters
- Expandable answers

### Login Page (`LoginPage.vue`)

- Login form
- Email/password
- Remember me
- Forgot password link
- Register link

### Register Page (`RegisterPage.vue`)

- Registration form
- User information
- Password requirements
- Terms acceptance
- Login link

### Setup Wizard (`SetupWizard.vue`)

- Multi-step wizard
- General information
- Branding configuration
- Pages setup
- Contact information
- Review and launch

### Public Enrollment (`PublicEnrollmentForm.vue`)

- Public enrollment form
- Course selection
- Student information
- Payment information
- Submit enrollment

### Certificate Verification (`CertificateVerification.vue`)

- Certificate lookup
- Verification by code
- Certificate display
- Verification status

### 404 Page (`NotFound.vue`)

- 404 error page
- Helpful message
- Navigation links
- Search functionality

## CMS Integration

### Page Rendering

`CMSPageRenderer.vue` renders CMS pages:
- Fetches page by slug
- Renders page content
- Displays blocks
- Multi-language support

### Block Components

Block components render CMS blocks:
- `HeroBlock.vue` - Hero sections
- `FeaturesBlock.vue` - Feature listings
- `TestimonialsBlock.vue` - Testimonials
- `CTABlock.vue` - Call-to-action
- `ContentBlock.vue` - General content

### Multi-Language Content

- Content per language
- Language switcher
- Fallback to default
- RTL support

## Public Layout

### Header

- Logo
- Navigation menu
- Language switcher
- Theme toggle
- Login/Register buttons

### Navigation

- Home
- Courses
- Trainers
- About
- Contact
- FAQ

### Footer

- Quick links
- Contact information
- Social media
- Copyright
- Language switcher

## Features

### Course Discovery

- Browse courses
- Filter by category
- Search functionality
- Course details
- Enrollment

### Instructor Profiles

- View instructors
- Instructor details
- Courses taught
- Contact information

### Contact Forms

- Contact form
- Enrollment form
- Form validation
- Success messages

### Multi-Language

- Language switcher
- Translated content
- RTL support
- Language persistence

### Responsive Design

- Mobile-friendly
- Tablet optimized
- Desktop layouts
- Touch interactions

## SEO

### Meta Tags

- Page titles
- Meta descriptions
- Open Graph tags
- Twitter cards

### Structured Data

- Course schema
- Organization schema
- Breadcrumb schema

### URL Structure

- Clean URLs
- SEO-friendly slugs
- Language prefixes (optional)

## Performance

### Optimization

- Lazy loading
- Image optimization
- Code splitting
- Caching

### Loading States

- Skeleton screens
- Loading indicators
- Progressive loading
- Error states

## Accessibility

### Standards

- WCAG 2.1 AA compliance
- Semantic HTML
- ARIA labels
- Keyboard navigation

### Features

- Screen reader support
- High contrast mode
- Focus indicators
- Skip links

## Conclusion

The public site provides:
- CMS-driven content
- Multi-language support
- Course discovery
- Instructor profiles
- Contact forms
- SEO optimization
- Accessibility support

All public pages are optimized for performance, SEO, and accessibility.

