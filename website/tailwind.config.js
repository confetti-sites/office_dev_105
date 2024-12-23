const colors = require('tailwindcss/colors')

module.exports = {
    content: [
        './**/*.blade.php',
        './**/*.mjs',
        './**/*.html',
    ],
    theme: {
        fontFamily: {
            'headings': ['pluto'],
            'body': ['sans-serif'],
          },
        extend: {
            colors: {
                teal: colors.teal,
                orange: colors.orange,
                gray: colors.gray,
                'color-red': '#f06060',
                'color-yellow': '#d69051',
                'color-green': '#2ecc71',
                primary: {
                    DEFAULT: '#d69051',
                    light: "#d69051",
                    dark: "#d69051",
                },
                secondary: {
                    DEFAULT: '#3dc2ff',
                    dark: "#36abe0",
                    light: "#50c8ff",
                },
            },
        },
    },
    plugins: [
        require('@tailwindcss/typography'),
    ],
}
