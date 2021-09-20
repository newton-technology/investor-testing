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
 && yum-config-manager --enable remi-php74 \
 && yum install msodbcsql17 -y\
 && yum install -y php74 \
                   php74-php \
                   php74-php-bcmath \
                   php74-php-cli \
                   php74-php-common \
                   php74-php-devel \
                   php74-php-fpm \
                   php74-php-json \
                   php74-php-mbstring \
                   php74-php-pdo \
                   php74-php-pdo-dblib \
                   php74-php-pear \
                   php74-php-pecl-mongodb \
                   php74-php-pecl-redis4 \
                   php74-php-pgsql \
                   php74-php-phpiredis \
                   php74-php-sqlsrv \
                   php74-php-xml \
                   php74-php-opcache \
                   php74-php-pecl-zip \
                   unzip \
 && rm -rf /etc/opt/remi/php74/php-fpm.d/www.conf \
 && chmod +x /opt/entrypoint.sh \
 && yum install -y wget \
                   cyrus-sasl \
                   cyrus-sasl-lib \
                   cyrus-sasl-devel \

 && wget -O /opt/remi/php74/root/bin/php-fpm-prometheus 'https://github.com/hipages/php-fpm_exporter/releases/download/v1.1.1/php-fpm_exporter_1.1.1_linux_amd64' \
 && chmod +x /opt/remi/php74/root/bin/php-fpm-prometheus \

 && yum clean all

RUN mkdir -p /var/www/ 

COPY investor_testing /var/www/projects/php/investor_testing
COPY common /var/www/projects/php/common

RUN chown -R $PHP_FPM_USER:$PHP_FPM_USER /var/www/ \
    && ln -s /opt/remi/php74/root/bin/php /bin/php 

ENTRYPOINT ["/opt/entrypoint.sh"]


