import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
                montserrat: ['Montserrat', 'sans-serif'],
                manrope: ['Manrope', 'sans-serif'],
            },
            colors: {
                brand: {
                    red: '#E31E24',
                    'red-dark': '#B91C1C',
                },
            },
            backgroundColor: {
                dark: {
                    base: '#0F172A',
                    surface: '#111827',
                    sidebar: '#020617',
                    border: '#1F2937',
                },
            },
        },
    },

    plugins: [forms],
};
