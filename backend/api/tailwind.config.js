module.exports = {
  content: [
    './resources/**/*.blade.php',
    './resources/js/**/*.vue',
    './resources/js/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        primary: {
          DEFAULT: '#6D28D9',
          50: '#F6F0FF',
          100: '#EDE5FF',
        },
        secondary: {
          DEFAULT: '#2563EB',
          50: '#EFF6FF',
        },
      },
    },
  },
  plugins: [],
};
