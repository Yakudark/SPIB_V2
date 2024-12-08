const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

// Créer le dossier public/css s'il n'existe pas
const cssDir = path.join(__dirname, 'public', 'css');
if (!fs.existsSync(cssDir)) {
    fs.mkdirSync(cssDir, { recursive: true });
}

// Compiler le CSS avec Tailwind
console.log('Compilation des styles CSS...');
try {
    execSync('npx tailwindcss -i ./src/css/input.css -o ./public/css/style.css --minify');
    console.log('Styles CSS compilés avec succès !');
} catch (error) {
    console.error('Erreur lors de la compilation CSS:', error);
    process.exit(1);
}
