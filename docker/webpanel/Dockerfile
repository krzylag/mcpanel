FROM nginx:1-bookworm

RUN rm -rf /usr/share/nginx
COPY docker/webpanel/html /usr/share/nginx/
RUN chown -R www-data:www-data /usr/share/nginx/html
