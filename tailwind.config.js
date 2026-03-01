import defaultTheme from "tailwindcss/defaultTheme";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ["Inter", ...defaultTheme.fontFamily.sans],
                stencil: ['"Share Tech Mono"', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                feldgrau: {
                    DEFAULT: '#4d5d53',
                },
                sage: {
                    DEFAULT: '#697367',
                    50:  '#f0f2f0',
                    100: '#d9ddd9',
                    200: '#b3bab2',
                    300: '#8d978c',
                    400: '#697367',
                    500: '#565e55',
                    600: '#4f5750',
                    650: '#636c65',
                    700: '#454d45',
                    800: '#3a423a',
                    900: '#343933',
                    950: '#2c3335',
                },
                khaki: {
                    DEFAULT: '#c2b280',
                },
                maroon: {
                    DEFAULT: '#6c2114',
                },
            },
        },
    },
    plugins: [],
};
