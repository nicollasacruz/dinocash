import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                menu: "Upheavtt",
            },
            colors: {
                'verde-claro': '#d6f8b8',
                'roxo-fundo': '#6c567b',
                'roxo-escuro': '#3b2b45',
            },
            dropShadow: {
                'xl': '10px 10px 0px 0px rgba(0, 0, 0, 0.75)',
                '4xl': [
                    '0 35px 35px rgba(0, 0, 0, 0.25)',
                    '0 45px 65px rgba(0, 0, 0, 0.15)'
                ]
            }
        },
        screens: {
            'mobile': '300px',
            // => @media (min-width: 300px) { ... }
            'tablet': '640px',
            // => @media (min-width: 640px) { ... }

            'laptop': '1024px',
            // => @media (min-width: 1024px) { ... }

            'desktop': '1280px',
            // => @media (min-width: 1280px) { ... }
        },
    },

    plugins: [forms],
};
