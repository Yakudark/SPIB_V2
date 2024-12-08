module.exports = {
  content: [
    "./public/**/*.{html,js,php}",
    "./src/**/*.{html,js,php}"
  ],
  theme: {
    extend: {
      colors: {
        primary: '#1D4ED8',    // Bleu principal
        secondary: '#9333EA',  // Violet secondaire
        danger: '#DC2626',     // Rouge pour alertes
        success: '#16A34A',    // Vert validation
        warning: '#F59E0B',    // Orange pour avertissements
      },
      spacing: {
        '72': '18rem',
        '84': '21rem',
        '96': '24rem',
      },
      maxHeight: {
        '0': '0',
        '1/4': '25%',
        '1/2': '50%',
        '3/4': '75%',
        'full': '100%',
      },
      minHeight: {
        '0': '0',
        '1/4': '25vh',
        '1/2': '50vh',
        '3/4': '75vh',
        'full': '100vh',
      }
    }
  },
  plugins: [],
  darkMode: 'class' // Activer le mode sombre si nécessaire
}
