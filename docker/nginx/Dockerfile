FROM node:20-alpine AS frontend-builder

WORKDIR /app

COPY frontend/package*.json ./
RUN npm install --no-audit --no-fund

COPY frontend/ ./
RUN npm run build

FROM nginx:alpine

COPY --from=frontend-builder /app/dist /var/www/frontend

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

EXPOSE 80
