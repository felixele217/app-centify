import forms from '@tailwindcss/forms'
import defaultTheme from 'tailwindcss/defaultTheme'

export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            width: {
                128: '32rem',
                144: '36rem',
                176: '44rem',
                216: '54rem',
                240: '60rem',
                346: '86.5rem',
            },
            margin: {
                0.25: '0.0625rem',
                0.75: '0.1875rem',
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: '#4E77F9',
                'primary-hover': '#6b8ffa',

                'primary-600': '#4E77F9',
                'primary-500': '#4E77F9',
                'primary-400': '#6b8ffa',
            },
        },
    },

    plugins: [forms, require('@tailwindcss/forms')],
}
