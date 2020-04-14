FROM driftphp/base

WORKDIR /var/www

#
# Apisearch installation
#

COPY . .
RUN composer global require hirak/prestissimo && \
    cd /var/www && composer install -n --prefer-dist --no-dev --no-suggest && \
    composer dump-autoload -n --no-dev --optimize

COPY docker/* /

EXPOSE 8080
CMD ["sh", "/server-entrypoint.sh"]
