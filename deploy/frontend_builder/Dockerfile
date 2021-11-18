# Образ контейнера для сборки фронта

FROM ubuntu:latest

RUN apt update && apt install -y curl dirmngr apt-transport-https lsb-release ca-certificates gnupg2
RUN curl -sL https://deb.nodesource.com/setup_12.x | bash - \
    && apt update \
    && apt -y install nodejs

RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - \
    && echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list \
    && apt update \
    && apt install -y yarn

# Образ выгружен на DockerHub, nwtn_devops/frontend_builder:latest (https://hub.docker.com/repository/docker/nwtndevops/frontend_builder)
# Для сборки клиента и админки из директории investor-testing/frontend/ запустите:
# $ docker run -t --name frontend_builder -v `pwd`:/frontend -w /frontend nwtndevops/frontend_builder:latest yarn install && yarn build-client && yarn build-admin
