# Stage 1: Build the Vue.js app
FROM node:16.3.0-alpine3.13 as build-stage
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm install -g @vue/cli
RUN npm install -D vite  
RUN chmod +x /app/node_modules/.bin/vite 
RUN npm run build

# Stage 2: Serve the built app with Nginx
FROM nginx:stable-alpine as production-stage
COPY --from=build-stage /app/dist /usr/share/nginx/html
COPY ./nginx/default.conf /etc/nginx/conf.d/default.conf
COPY ./ssl/certs/webte_fei_stuba_sk.pem /etc/ssl/certs/webte_fei_stuba_sk.pem
COPY ./ssl/private/webte.fei.stuba.sk.key /etc/ssl/private/webte.fei.stuba.sk.key
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
