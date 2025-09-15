# Usamos Node.js LTS
FROM node:18

# Carpeta de trabajo dentro del contenedor
WORKDIR /app

# Copiamos package.json y package-lock.json primero
COPY package*.json ./

# Instalamos dependencias
RUN npm install

# Copiamos el resto del código
COPY . .

# Exponemos el puerto que usará tu API
EXPOSE 3000

# Comando para arrancar la app
CMD ["npm", "start"]
