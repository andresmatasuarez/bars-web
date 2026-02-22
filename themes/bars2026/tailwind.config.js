/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./php/**/*.php', './ts/**/*.{ts,tsx}'],
  theme: {
    extend: {
      colors: {
        'bars-primary': '#8B0000',
        'bars-primary-light': 'rgba(139, 0, 0, 0.27)',
        'bars-primary-muted': 'rgba(139, 0, 0, 0.08)',
        'bars-primary-subtle': 'rgba(139, 0, 0, 0.2)',
        'bars-bg-dark': '#0A0A0A',
        'bars-bg-medium': '#0F0F0F',
        'bars-bg-card': '#1A1A1A',
        'bars-bg-elevated': '#050505',
        'bars-header': 'rgba(10, 10, 10, 0.8)',
        'bars-footer': '#050505',
        'bars-text-primary': '#FFFFFF',
        'bars-text-secondary': 'rgba(255, 255, 255, 0.7)',
        'bars-text-muted': 'rgba(255, 255, 255, 0.6)',
        'bars-text-subtle': 'rgba(255, 255, 255, 0.4)',
        'bars-text-faint': 'rgba(255, 255, 255, 0.27)',
        'bars-text-disabled': 'rgba(255, 255, 255, 0.2)',
        'bars-icon-empty': 'var(--color-bars-icon-empty)',
        'bars-border-light': 'rgba(255, 255, 255, 0.2)',
        'bars-border-subtle': 'rgba(255, 255, 255, 0.08)',
        'bars-divider': 'rgba(255, 255, 255, 0.08)',
      },
      fontFamily: {
        display: ['"Bebas Neue"', 'sans-serif'],
        heading: ['"Cormorant Garamond"', 'serif'],
        body: ['Inter', 'sans-serif'],
      },
      borderRadius: {
        'bars-sm': '4px',
        'bars-md': '8px',
        'bars-lg': '16px',
        'bars-pill': '20px',
      },
      screens: {
        sm: '640px',
        md: '768px',
        lg: '1024px',
        xl: '1280px',
        '2xl': '1440px',
      },
      keyframes: {
        'fade-in': {
          from: { opacity: '0' },
          to: { opacity: '1' },
        },
      },
      animation: {
        'fade-in': 'fade-in 0.15s ease-out',
      },
    },
  },
  plugins: [],
};
