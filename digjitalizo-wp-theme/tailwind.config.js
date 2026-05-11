/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './**/*.php',
    './src/**/*.js',
    './assets/js/*.js',
  ],
  theme: {
    extend: {
      colors: {
        primary:    'var(--color-primary)',
        secondary:  'var(--color-secondary)',
        accent:     'var(--color-accent)',
        'btn-bg':   'var(--color-btn-bg)',
        'btn-text': 'var(--color-btn-text)',
        'brand-dark':  '#004c7a',
        'brand':       '#165783',
        'brand-light': '#1face3',
        'brand-pale':  '#cee4f3',
        'sale':        '#d32f2f',
        'sale-bright': '#ff2427',
        'star':        '#eab308',
      },
      fontFamily: {
        heading: 'var(--font-heading)',
        body:    'var(--font-body)',
      },
      maxWidth: {
        site: '1280px',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}
