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
        text:        'var(--color-text)',
        heading:     'var(--color-heading)',
        muted:       'var(--color-muted)',
        'btn-bg':   'var(--color-btn-bg)',
        'btn-text': 'var(--color-btn-text)',
        'brand-dark':  'var(--color-secondary)',
        'brand':       'var(--color-primary)',
        'brand-light': 'var(--color-accent)',
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
        site: '1460px',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}
