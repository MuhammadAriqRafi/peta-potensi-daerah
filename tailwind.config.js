/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    // "./app/Views/index.html",
    "./app/Views/**/*.php",
    "./app/Views/**/**/*.php",
  ],
  theme: {
    extend: {},
  },
  plugins: [require("daisyui")]
}