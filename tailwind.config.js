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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'baby-blue': '#BBDCE5',
                'navy': '#3674B5',
            },
        },
    },
    safelist: [
        'text-baby-blue',
        'bg-baby-blue',
        'text-navy',
        'bg-navy',
    ],
    plugins: [],
};
