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
        },
    },
    safelist: [
        'bg-red-100',
        'hover:bg-red-200',
        'text-red-700',
        'bg-indigo-100',
        'hover:bg-indigo-200',
        'text-indigo-700',
        'bg-green-500',
        'bg-red-500',
        'text-white',
        'rounded',
        'shadow-sm',
        'px-2',
        'py-1',
        'ml-1',
        'inline-block',
        'text-xs',
        'font-semibold',
        'bg-yellow-100'
    ],


    plugins: [forms],
};
