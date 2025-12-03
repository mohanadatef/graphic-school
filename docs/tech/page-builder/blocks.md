# Page Builder Blocks

## Overview

The page builder uses a block-based system where pages consist of multiple content blocks. Each block can be enabled/disabled and configured independently.

## Block System

### Block Structure

Each block has:
- `type` - Block type identifier
- `title` - Block title (multi-language JSON)
- `content` - Block content (multi-language JSON)
- `config` - Block configuration (JSON)
- `is_enabled` - Enabled status
- `sort_order` - Display order

### Block Types

#### Hero Block
- **Type:** `hero`
- **Purpose:** Hero section with headline and CTA
- **Config:**
  - `cta_text` - CTA button text (multi-language)
  - `cta_link` - CTA button link
  - `background_image` - Background image URL
  - `overlay` - Overlay settings

#### Features Block
- **Type:** `features`
- **Purpose:** Feature listing section
- **Config:**
  - `features` - Array of features
    - `title` - Feature title (multi-language)
    - `description` - Feature description (multi-language)
    - `icon` - Feature icon
  - `columns` - Number of columns
  - `layout` - Layout style

#### Testimonials Block
- **Type:** `testimonials`
- **Purpose:** Testimonials section
- **Config:**
  - `testimonials` - Array of testimonials
    - `name` - Testimonial author
    - `text` - Testimonial text (multi-language)
    - `image` - Author image
    - `rating` - Rating (1-5)
  - `layout` - Layout style

#### CTA Block
- **Type:** `cta`
- **Purpose:** Call-to-action section
- **Config:**
  - `button_text` - Button text (multi-language)
  - `button_link` - Button link
  - `background_color` - Background color
  - `text_color` - Text color

#### Content Block
- **Type:** `content`
- **Purpose:** General content section
- **Config:**
  - `html` - HTML content (multi-language)
  - `css` - Custom CSS
  - `layout` - Layout style

#### FAQ Block
- **Type:** `faq`
- **Purpose:** FAQ section
- **Config:**
  - `faqs` - Array of FAQs
    - `question` - Question (multi-language)
    - `answer` - Answer (multi-language)
  - `layout` - Layout style

#### Gallery Block
- **Type:** `gallery`
- **Purpose:** Image gallery
- **Config:**
  - `images` - Array of image URLs
  - `columns` - Number of columns
  - `lightbox` - Enable lightbox

#### Video Block
- **Type:** `video`
- **Purpose:** Video embed
- **Config:**
  - `video_url` - Video URL
  - `video_type` - Video type (youtube, vimeo, direct)
  - `autoplay` - Autoplay flag
  - `controls` - Show controls

## Block Components

### Frontend Components

Block components are in `src/components/public/blocks/`:

- `HeroBlock.vue` - Renders hero blocks
- `FeaturesBlock.vue` - Renders features blocks
- `TestimonialsBlock.vue` - Renders testimonials blocks
- `CTABlock.vue` - Renders CTA blocks
- `ContentBlock.vue` - Renders content blocks

### Component Mapping

`CMSPageRenderer.vue` maps block types to components:

```javascript
const components = {
  hero: HeroBlock,
  features: FeaturesBlock,
  testimonials: TestimonialsBlock,
  cta: CTABlock,
  content: ContentBlock,
};
```

## Block Management

### Creating Blocks

Blocks are created via API:
- `POST /api/admin/pages/{id}/blocks` - Create block
- Block added to page
- Sort order assigned

### Updating Blocks

Blocks are updated via API:
- `PUT /api/admin/pages/{id}/blocks/{blockId}` - Update block
- Config updated
- Content updated

### Enabling/Disabling Blocks

Blocks can be enabled/disabled:
- `is_enabled` flag controls visibility
- Disabled blocks not rendered
- Can be re-enabled later

### Reordering Blocks

Blocks can be reordered:
- `sort_order` determines display order
- Update sort order to reorder
- Blocks rendered in order

## Multi-Language Support

### Block Content

Block content is multi-language:
- `title` - JSON object with language keys
- `content` - JSON object with language keys
- `config` - Can contain multi-language values

### Rendering

Blocks render content for current locale:
- Get content for current language
- Fallback to default language
- Display appropriate content

## Block Configuration

### Config Structure

Config is stored as JSON:
```json
{
  "cta_text": {
    "en": "Get Started",
    "ar": "ابدأ الآن"
  },
  "cta_link": "/courses",
  "background_image": "/images/hero.jpg"
}
```

### Config Validation

Config is validated:
- Required fields checked
- Type validation
- Format validation

## Block Rendering

### Rendering Flow

1. Page loaded
2. Blocks fetched from API
3. Blocks filtered (enabled only)
4. Blocks sorted by `sort_order`
5. Each block rendered with component
6. Content displayed

### Component Props

Blocks receive props:
- `block` - Block data object
- Includes: type, title, content, config

### Dynamic Rendering

Blocks render dynamically:
- Component selected by type
- Props passed to component
- Component renders content

## Best Practices

1. **Block Design**
   - Keep blocks focused
   - Reusable configurations
   - Clear purpose

2. **Content Management**
   - Multi-language support
   - Rich content options
   - Flexible configuration

3. **Performance**
   - Lazy load blocks
   - Optimize images
   - Cache content

4. **Accessibility**
   - Semantic HTML
   - ARIA labels
   - Keyboard navigation

5. **Responsive Design**
   - Mobile-friendly
   - Tablet optimized
   - Desktop layouts

## Conclusion

The block system provides:
- Flexible page building
- Reusable components
- Multi-language support
- Easy content management
- Dynamic rendering

Blocks enable non-technical users to build rich, dynamic pages without coding.

