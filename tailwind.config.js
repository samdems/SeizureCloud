/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
    "./storage/framework/views/*.php",
    "./resources/views/**/*.blade.php",
    "./resources/js/**/*.js",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Instrument Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
      },
      colors: {
        primary: {
          50: '#f0f9ff',
          100: '#e0f2fe',
          200: '#bae6fd',
          300: '#7dd3fc',
          400: '#38bdf8',
          500: '#0ea5e9',
          600: '#0284c7',
          700: '#0369a1',
          800: '#075985',
          900: '#0c4a6e',
          950: '#082f49',
        }
      },
      animation: {
        'fade-in': 'fadeIn 0.5s ease-in-out',
        'slide-up': 'slideUp 0.5s ease-in-out',
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        slideUp: {
          '0%': { transform: 'translateY(10px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        }
      }
    },
  },
  plugins: [
    require('daisyui'),
  ],
  daisyui: {
    themes: [
      {
        light: {
          "primary": "#0ea5e9",
          "primary-focus": "#0284c7",
          "primary-content": "#ffffff",
          "secondary": "#f59e0b",
          "secondary-focus": "#d97706",
          "secondary-content": "#ffffff",
          "accent": "#10b981",
          "accent-focus": "#059669",
          "accent-content": "#ffffff",
          "neutral": "#3d4451",
          "neutral-focus": "#2a2e37",
          "neutral-content": "#ffffff",
          "base-100": "#ffffff",
          "base-200": "#f9fafb",
          "base-300": "#f3f4f6",
          "base-content": "#1f2937",
          "info": "#3abff8",
          "success": "#36d399",
          "warning": "#fbbd23",
          "error": "#f87272",
        },
        dark: {
          "primary": "#38bdf8",
          "primary-focus": "#0ea5e9",
          "primary-content": "#0c1821",
          "secondary": "#fbbf24",
          "secondary-focus": "#f59e0b",
          "secondary-content": "#1a1a1a",
          "accent": "#34d399",
          "accent-focus": "#10b981",
          "accent-content": "#0d1f17",
          "neutral": "#2a303c",
          "neutral-focus": "#1f242e",
          "neutral-content": "#d1d5db",
          "base-100": "#1f2937",
          "base-200": "#374151",
          "base-300": "#4b5563",
          "base-content": "#f9fafb",
          "info": "#0ca5e9",
          "success": "#2dd4bf",
          "warning": "#f4bf50",
          "error": "#fb7085",
        },
      },
    ],
    darkTheme: "dark",
    base: true,
    styled: true,
    utils: true,
    rtl: false,
    prefix: "",
    logs: true,
  },
  darkMode: ['class', '[data-theme="dark"]'],
}
