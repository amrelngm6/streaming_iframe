/** @type {import('tailwindcss').Config} */
const colors = require("tailwindcss/colors");

module.exports = {
  content: [
    "./src/*.{vue,js,ts,jsx,tsx,twig}",
    "./src/**/*.{vue,js,ts,jsx,tsx,twig}",
    "./src/**/**/*.{vue,js,ts,jsx,tsx,twig}",
    "./app/views/front/*.{html,twig}",
    "./app/views/front/*/*.{html,twig}",
    "./app/views/front/*/*/*.{html,twig}",
    "./node_modules/vue-tailwind-datepicker/**/*.js",
  ],
  safelist: [
    {
      pattern: /./,
      variants: ['xs', 'sm', 'md', 'lg', 'xl'], // you can add your variants here
    },
  ],
  theme: {
    extend: {
      colors: {
        "vtd-primary": colors.purple, // Light mode Datepicker color
        "vtd-secondary": colors.gray, // Dark mode Datepicker color
      },},
  },
  plugins: [
  ]
}
