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
                sans: ['Geist', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                terracotta: {
                    50: '#FDF8F6',
                    100: '#F9EAE3',
                    200: '#F2D5C8',
                    300: '#E9B8A4',
                    400: '#DE947A',
                    500: '#D36A49',
                    600: '#C15335',
                    700: '#A14128',
                    800: '#833724',
                    900: '#6C2F20',
                    950: '#39150D',
                }
            }
        },
    },

    plugins: [forms],
};
