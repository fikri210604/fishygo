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
      colors: {
        primary: {
          DEFAULT: '#3b82f6', 
          hover: '#2563eb',   
          focus: '#1d4ed8',   
        },
        secondary: {
          DEFAULT: '#8b5cf6', 
          hover: '#7c3aed',
          focus: '#6d28d9',
        },
        accent: {
          DEFAULT: '#f59e0b', 
          hover: '#d97706',
          focus: '#b45309',
        },
        neutral: {
          DEFAULT: '#3d4451',
          hover: '#2a2e37',
          focus: '#16181d',
        },
      },
    },
  },
  plugins: [
    forms,
    require("daisyui"), 
  ],
  daisyui: {
    themes: [
      {
        mytheme: {
          "primary": "#3b82f6",
          "secondary": "#8b5cf6",
          "accent": "#f59e0b",
          "neutral": "#3d4451",
          "base-100": "#ffffff",
          "info": "#0ea5e9",
          "success": "#22c55e",
          "warning": "#f59e0b",
          "error": "#ef4444",
        },
      },
      "light", 
      "dark", 
      "corporate",
    ],
    darkTheme: "dark",
    base: true,
    styled: true,
    utils: true,
    rtl: false,
    prefix: "",
    logs: true
  },
};