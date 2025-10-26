# Use a imagem oficial PHP com Apache
FROM php:8.2-apache

# Habilitar extensões necessárias (PDO, mysqli, etc)
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Copiar arquivos do projeto para o diretório do Apache
COPY . /var/www/html/

# Definir permissões (opcional, dependendo do projeto)
RUN chown -R www-data:www-data /var/www/html

# Expor a porta que o Render vai usar
EXPOSE 10000

# Comando padrão para rodar Apache em foreground
CMD ["apache2-foreground"]
