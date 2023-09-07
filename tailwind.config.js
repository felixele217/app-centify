import forms from '@tailwindcss/forms'

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
                sans: [
                    'Poppins',
                    '-apple-system',
                    'BlinkMacSystemFont',
                    'Segoe UI',
                    'Roboto',
                    'Helvetica Neue',
                    'Arial',
                    'sans-serif',
                ],
            },
            colors: {
                primary: '#4E77F9',
                'primary-hover': '#6b8ffa',

                // hsl(228, 80%, 46%)
                'primary-900': '#173dd3',

                // hsl(228, 83%, 53%)
                'primary-800': '#244beb',

                // hsl(227, 85%, 55%)
                'primary-700': '#2b55ee',

                // hsl(227, 90%, 60%)
                'primary-600': '#3d65f5',

                // hsl(226, 93%, 64%)
                'primary-500': '#4E77F9',

                // hsl(225, 93%, 70%)
                'primary-400': '#6b8ffa',

                // hsl(225, 95%, 71%)
                'primary-300': '#6f92fb',

                // hsla(224, 95%, 78%)
                'primary-200': '#92aefc',

                // hsla(223, 86%, 90%, 1)
                'primary-100': '#d0dcfb',

                // hsla(221, 95%, 94%, 1)
                'primary-50': '#e1eafe',
            },
        },
    },

    plugins: [forms, require('@tailwindcss/forms')],
}
