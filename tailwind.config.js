/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
    "./templates/*.html.twig",
    "./node_modules/flowbite/**/*.js"
  ],
  theme: {
    extend: {},
    screens: {
        'sm': {'max': '639px'},
    }
  },
  plugins: [
    require('flowbite/plugin')
  ],
}
