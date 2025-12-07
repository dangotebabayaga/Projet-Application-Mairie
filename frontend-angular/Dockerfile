FROM node:18

WORKDIR /app

# Copier package.json et package-lock.json
COPY app/package*.json ./

# Installer les dépendances
RUN npm install

# Copier le reste du projet
COPY app/. .

# Lancer l'application
CMD ["npm", "start"]

