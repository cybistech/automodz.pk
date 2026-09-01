import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Rajdhani', 'Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50: '#fffbeb',
                    100: '#fef3c7',
                    200: '#fde68a',
                    300: '#fcd34d',
                    400: '#fbbf24',
                    500: '#f59e0b',
                    600: '#d97706',
                    700: '#b45309',
                    800: '#92400e',
                    900: '#78350f',
                },
            },
            backgroundImage: {
                'brand-gradient': 'linear-gradient(135deg, #fbbf24 0%, #f97316 50%, #ef4444 100%)',
                'hero-glow': 'radial-gradient(ellipse 80% 60% at 50% -10%, rgba(249,115,22,0.25), transparent)',
            },
            boxShadow: {
                brand: '0 10px 40px -10px rgba(249,115,22,0.45)',
            },
        },
    },

    plugins: [forms],
};
