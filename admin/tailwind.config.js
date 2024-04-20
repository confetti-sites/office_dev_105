const defaultTheme = require('tailwindcss/defaultTheme')
const colors = require('tailwindcss/colors')

module.exports = {
    content: [
        './**/*.blade.php',
        './**/*.mjs',
    ],
    darkMode: 'class',
    theme: {
        customForms: (theme) => ({
            default: {
                'input, textarea': {
                    '&::placeholder': {
                        color: theme('colors.gray.400'),
                    },
                },
            },
        }),
        extend: {
            colors: {
                // primary: colors.emerald,
            },
            maxHeight: {
                xl: '36rem',
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                xs: '0 0 0 1px rgba(0, 0, 0, 0.05)',
                // outline: '0 0 0 3px rgba(66, 153, 225, 0.5)',
            },
        },
    },
    plugins: [
        require('@tailwindcss/typography'),
        require('@tailwindcss/forms')({
            strategy: 'class',
        }),
    ],
}
