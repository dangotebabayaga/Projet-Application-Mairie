FROM node:18

WORKDIR /app

# Copier package.json et package-lock.json
COPY myapp/package*.json ./

# Installer les dépendances
RUN npm install

# Copier le reste du projet
COPY myapp/. .

# Lancer l'application
CMD ["npm", "start"]

