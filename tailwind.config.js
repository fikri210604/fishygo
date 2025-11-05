const defaultTheme = require('tailwindcss/defaultTheme');
const forms = require('@tailwindcss/forms');

module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
    },
  },
  plugins: [
    forms,
    require("daisyui"), 
  ],
  daisyui: {
    themes: ["light", "dark", "corporate", "dracula","cupcake"], // optional
    darkTheme: "dark",
    lightTheme: "light",
    base: true,
    styled: true,
    utils: true,
    rtl: false,
    prefix: "",
    logs: true
  },
};
