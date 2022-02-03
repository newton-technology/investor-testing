FROM centos:7

USER root

ENV ACCEPT_EULA=Y
ENV PHP_FPM_USER=apache
ENV LANG en_US.UTF-8
ENV LANGUAGE en_US:en
ENV LC_ALL en_US.UTF-8

COPY entrypoint.sh /opt/entrypoint.sh

RUN yum update -y && yum install epel-release -y \
 && yum install -y openssl \
                yum-utils \
                curl \
                http://rpms.remirepo.net/enterprise/remi-release-7.rpm \
                gettext \
 && curl https://packages.microsoft.com/config/rhel/7/prod.repo > /etc/yum.repos.d/mssql-release.repo \
 && yum-config-manager --enable remi-php80 \
 && yum install msodbcsql17 -y\
 && yum install -y php80 \
                   php80-php \
                   php80-php-bcmath \
                   php80-php-cli \
                   php80-php-common \
                   php80-php-devel \
                   php80-php-fpm \
                   php80-php-json \
                   php80-php-mbstring \
                   php80-php-pdo \
                   php80-php-pdo-dblib \
                   php80-php-pear \
                   php80-php-pecl-mongodb \
                   php80-php-pecl-redis4 \
                   php80-php-pgsql \
                   php80-php-phpiredis \
                   php80-php-sqlsrv \
                   php80-php-xml \
                   php80-php-opcache \
                   php80-php-pecl-zip \
                   unzip \
 && rm -rf /etc/opt/remi/php80/php-fpm.d/www.conf \
 && chmod +x /opt/entrypoint.sh \
 && yum install -y wget \
                   cyrus-sasl \
                   cyrus-sasl-lib \
                   cyrus-sasl-devel \

 && wget -O /opt/remi/php80/root/bin/php-fpm-prometheus 'https://github.com/hipages/php-fpm_exporter/releases/download/v1.1.1/php-fpm_exporter_1.1.1_linux_amd64' \
 && chmod +x /opt/remi/php80/root/bin/php-fpm-prometheus \

 && yum clean all

RUN mkdir -p /var/www/ 

COPY investor_testing /var/www/projects/php/investor_testing
COPY common /var/www/projects/php/common

RUN chown -R $PHP_FPM_USER:$PHP_FPM_USER /var/www/ \
    && ln -s /opt/remi/php80/root/bin/php /bin/php 

ENTRYPOINT ["/opt/entrypoint.sh"]


