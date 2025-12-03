# Page Builder Rendering Flow

## Overview

This document explains how CMS pages are rendered on the frontend, from API request to final HTML output.

## Rendering Process

### 1. Route Matching

User navigates to a page:
- Route matches `/` or `/pages/{slug}`
- Router loads `CMSPageRenderer.vue` component
- Slug extracted from route

### 2. API Request

Component makes API request:
```javascript
GET /api/public/pages/{slug}?locale={locale}
```

Request includes:
- Page slug
- Current locale
- Optional preview mode

### 3. Backend Processing

Backend processes request:
1. Fetches page by slug
2. Loads enabled blocks
3. Filters by language
4. Returns page data

Response format:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "slug": "home",
    "title": {
      "en": "Home",
      "ar": "الرئيسية"
    },
    "content": {
      "en": "Welcome...",
      "ar": "مرحباً..."
    },
    "blocks": [
      {
        "id": 1,
        "type": "hero",
        "title": { "en": "...", "ar": "..." },
        "content": { "en": "...", "ar": "..." },
        "config": { ... },
        "is_enabled": true,
        "sort_order": 1
      }
    ]
  }
}
```

### 4. Frontend Processing

Component processes response:
1. Extracts page data
2. Filters enabled blocks
3. Sorts blocks by `sort_order`
4. Maps block types to components

### 5. Block Rendering

Each block is rendered:
1. Component selected by block type
2. Block data passed as props
3. Component renders content
4. Content displayed in order

### 6. Final Output

Final HTML output:
- Page title
- Page content
- Rendered blocks
- Styled with Tailwind CSS

## Component Flow

### CMSPageRenderer.vue

Main renderer component:
```vue
<template>
  <div>
    <!-- Page Title -->
    <h1>{{ pageData.title }}</h1>
    
    <!-- Page Content -->
    <div v-html="pageData.content"></div>
    
    <!-- Blocks -->
    <component
      v-for="block in enabledBlocks"
      :key="block.id"
      :is="getBlockComponent(block.type)"
      :block="block"
    />
  </div>
</template>
```

### Block Component Mapping

```javascript
function getBlockComponent(type) {
  const components = {
    hero: HeroBlock,
    features: FeaturesBlock,
    testimonials: TestimonialsBlock,
    cta: CTABlock,
    content: ContentBlock,
  };
  return components[type] || ContentBlock;
}
```

## Multi-Language Rendering

### Language Selection

1. Get current locale from i18n
2. Extract content for locale
3. Fallback to default language
4. Display appropriate content

### Content Extraction

```javascript
function getContent(block, locale) {
  const content = block.content || {};
  return content[locale] || content['en'] || content[Object.keys(content)[0]] || '';
}
```

## Block Rendering

### Hero Block

Renders hero section:
- Title from block.title
- Content from block.content
- CTA from block.config
- Background from block.config

### Features Block

Renders features:
- Title from block.title
- Features array from block.config
- Grid layout
- Feature cards

### Testimonials Block

Renders testimonials:
- Title from block.title
- Testimonials array from block.config
- Carousel or grid layout
- Testimonial cards

### CTA Block

Renders call-to-action:
- Content from block.content
- Button from block.config
- Styled section

### Content Block

Renders general content:
- HTML from block.content
- Custom CSS from block.config
- Formatted content

## Performance Optimization

### Lazy Loading

Blocks can be lazy loaded:
- Load visible blocks first
- Load remaining on scroll
- Improve initial load time

### Caching

Page content can be cached:
- Cache API responses
- Cache rendered HTML
- Invalidate on update

### Image Optimization

Images optimized:
- Lazy loading
- Responsive images
- WebP format
- CDN delivery

## Error Handling

### Missing Page

If page not found:
- Show 404 error
- Display error message
- Provide navigation links

### Missing Block Component

If block component missing:
- Fallback to ContentBlock
- Log warning
- Display block content

### API Errors

If API error:
- Show error message
- Allow retry
- Log error for debugging

## SEO Optimization

### Meta Tags

Page meta tags:
- Title from page.title
- Description from page.meta_description
- Open Graph tags
- Twitter cards

### Structured Data

Structured data:
- Page schema
- Organization schema
- Breadcrumb schema

## Responsive Rendering

### Mobile

- Stack blocks vertically
- Full-width blocks
- Touch-optimized
- Simplified layouts

### Tablet

- 2-column layouts
- Optimized spacing
- Touch interactions
- Adaptive components

### Desktop

- Multi-column layouts
- Full feature set
- Hover effects
- Rich interactions

## Accessibility

### Semantic HTML

- Proper heading hierarchy
- ARIA labels
- Alt text for images
- Descriptive links

### Keyboard Navigation

- Tab navigation
- Focus indicators
- Skip links
- Keyboard shortcuts

## Conclusion

The rendering flow provides:
- Dynamic page rendering
- Multi-language support
- Block-based architecture
- Performance optimization
- SEO optimization
- Accessibility support

The system enables flexible, dynamic page rendering while maintaining performance and accessibility.

