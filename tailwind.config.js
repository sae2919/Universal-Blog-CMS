import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['DM Sans', ...defaultTheme.fontFamily.sans],
                heading: ['Outfit', 'sans-serif'],
            },
            colors: {
                indigo: {
                    50: '#f0f5ff',
                    100: '#e0ebff',
                    200: '#c2d7ff',
                    300: '#94b8ff',
                    400: '#5c90ff',
                    500: '#2b66ff',
                    600: '#1F5FFF', // Primary Blue
                    650: '#1F5FFF',
                    700: '#045CB4', // Hover Blue
                    800: '#002f9c',
                    900: '#000D44', // Accent Navy
                    950: '#000726',
                },
                red: {
                    650: '#04CE78', // Primary Green
                    700: '#029E5A', // Hover Green
                },
            },
        },
    },

    plugins: [forms],
};
