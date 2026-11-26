/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: ['class', '[data-bs-theme="dark"]'],
  content: [
    './src/pages/**/*.{js,ts,jsx,tsx,mdx}',
    './src/components/**/*.{js,ts,jsx,tsx,mdx}',
    './src/app/**/*.{js,ts,jsx,tsx,mdx}',
  ],
  theme: {
    extend: {
      colors: {
        'neon-green': '#00ff66',
        'neon-green-dim': '#00cc52',
        'neon-cyan': '#00f0ff',
        'tactical-olive': '#213608',
        'tactical-olive-dark': '#121e05',
        'cyber-dark': '#050804',
        'cyber-dark-card': '#091206',
        'cyber-dark-elevated': '#0f1c09',
      },
      fontFamily: {
        main: ['Geist', 'sans-serif'],
        cyber: ['Geist', 'sans-serif'],
        mono: ['Geist Mono', 'monospace'],
      },
    },
  },
  plugins: [],
};
