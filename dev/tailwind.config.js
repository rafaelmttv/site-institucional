/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    '../index.php',
    '../views/**/*.php',
    '../admin/**/*.php',
    '../content/**/*.json',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
