const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
    content: [
        './**/*.blade.php',
        './**/*.mjs',
        './**/*.html',
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
            maxHeight: {
                xl: '36rem',
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                xs: '0 0 0 1px rgba(0, 0, 0, 0.05)',
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
