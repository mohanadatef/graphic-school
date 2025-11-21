/**
 * SEO Utilities
 * Provides functions for managing meta tags and SEO
 */

export function setMetaTags(meta) {
  const {
    title,
    description,
    keywords,
    image,
    url,
    type = 'website',
    siteName = 'Graphic School',
  } = meta;

  // Title
  if (title) {
    document.title = `${title} | ${siteName}`;
    updateMetaTag('og:title', title);
    updateMetaTag('twitter:title', title);
  }

  // Description
  if (description) {
    updateMetaTag('description', description);
    updateMetaTag('og:description', description);
    updateMetaTag('twitter:description', description);
  }

  // Keywords
  if (keywords) {
    updateMetaTag('keywords', Array.isArray(keywords) ? keywords.join(', ') : keywords);
  }

  // Image
  if (image) {
    updateMetaTag('og:image', image);
    updateMetaTag('twitter:image', image);
  }

  // URL
  if (url) {
    updateMetaTag('og:url', url);
  }

  // Type
  updateMetaTag('og:type', type);

  // Site Name
  updateMetaTag('og:site_name', siteName);
}

function updateMetaTag(property, content) {
  // Handle both property and name attributes
  let element = document.querySelector(`meta[property="${property}"]`) || 
                document.querySelector(`meta[name="${property}"]`);

  if (!element) {
    element = document.createElement('meta');
    if (property.startsWith('og:') || property.startsWith('twitter:')) {
      element.setAttribute('property', property);
    } else {
      element.setAttribute('name', property);
    }
    document.head.appendChild(element);
  }

  element.setAttribute('content', content);
}

/**
 * Generate structured data (JSON-LD)
 */
export function setStructuredData(data) {
  // Remove existing structured data
  const existing = document.querySelector('script[type="application/ld+json"]');
  if (existing) {
    existing.remove();
  }

  // Add new structured data
  const script = document.createElement('script');
  script.type = 'application/ld+json';
  script.textContent = JSON.stringify(data);
  document.head.appendChild(script);
}

/**
 * Course structured data
 */
export function setCourseStructuredData(course) {
  setStructuredData({
    '@context': 'https://schema.org',
    '@type': 'Course',
    name: course.title,
    description: course.description,
    provider: {
      '@type': 'Organization',
      name: 'Graphic School',
    },
    ...(course.price && {
      offers: {
        '@type': 'Offer',
        price: course.price,
        priceCurrency: 'EGP',
      },
    }),
  });
}

