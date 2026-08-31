/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        scada: {
          950: '#06090e',
          900: '#0b111a',
          850: '#101926',
          800: '#162233',
          750: '#1d2c42',
          700: '#263852',
          600: '#384f6e',
          500: '#506b91',
        },
        neon: {
          cyan: '#00f0ff',
          emerald: '#00ff9d',
          amber: '#ffb703',
          rose: '#ff0055',
          purple: '#b5179e',
          blue: '#3a86ff',
        },
      },
      fontFamily: {
        mono: ['JetBrains Mono', 'Fira Code', 'Consolas', 'monospace'],
        sans: ['Inter', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'sans-serif'],
      },
      boxShadow: {
        'neon-cyan': '0 0 20px -3px rgba(0, 240, 255, 0.45)',
        'neon-emerald': '0 0 20px -3px rgba(0, 255, 157, 0.45)',
        'neon-amber': '0 0 20px -3px rgba(255, 183, 3, 0.45)',
        'neon-rose': '0 0 20px -3px rgba(255, 0, 85, 0.45)',
        'inner-dark': 'inset 0 2px 4px 0 rgba(0, 0, 0, 0.6)',
        'metallic': '0 4px 15px -1px rgba(0, 0, 0, 0.7), inset 0 1px 1px 0 rgba(255, 255, 255, 0.1)',
      },
      animation: {
        'pulse-fast': 'pulse 1s cubic-bezier(0.4, 0, 0.6, 1) infinite',
        'glow-cyan': 'glowCyan 2s ease-in-out infinite alternate',
      },
      keyframes: {
        glowCyan: {
          '0%': { boxShadow: '0 0 5px rgba(0, 240, 255, 0.2)' },
          '100%': { boxShadow: '0 0 20px rgba(0, 240, 255, 0.6)' },
        },
      },
    },
  },
  plugins: [],
}
