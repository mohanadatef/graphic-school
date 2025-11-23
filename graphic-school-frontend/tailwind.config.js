/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './index.html',
    './src/**/*.{vue,js,ts,jsx,tsx}',
  ],
  darkMode: 'class', // Enable class-based dark mode
  theme: {
    extend: {
      colors: {
        primary: 'var(--primary, #3b82f6)',
        secondary: 'var(--secondary, #0ea5e9)',
        background: 'var(--background, #ffffff)',
        'text-color': 'var(--text-color, #111111)',
      },
      fontFamily: {
        main: 'var(--font-main, "Inter", sans-serif)',
        headings: 'var(--font-headings, "Inter", sans-serif)',
      },
      borderRadius: {
        DEFAULT: 'var(--radius, 0.5rem)',
      },
      boxShadow: {
        DEFAULT: 'var(--shadow-level, 0)',
      },
    },
  },
  plugins: [],
};

