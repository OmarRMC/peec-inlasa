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
        'text-red-800',
        'bg-indigo-50',
        'bg-indigo-100',
        'hover:bg-indigo-50',
        'hover:bg-indigo-100',
        'hover:bg-indigo-200',
        'text-indigo-600',
        'text-indigo-700',
        'text-indigo-800',
        'border-indigo-600',
        'border-transparent',
        'border-l-2',
        'bg-green-500',
        'bg-green-100',
        'text-green-800',
        'bg-red-500',
        'text-white',
        'rounded',
        'shadow-sm',
        'px-2',
        'py-1',
        'ml-1',
        'inline-block',
        'text-xs',
        'font-medium',
        'font-semibold',
        'font-bold',
        'bg-yellow-100',
        'text-yellow-800'
    ],


    plugins: [forms],
};
