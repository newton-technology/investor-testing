--liquibase formatted sql

--changeset 2021-08-05-category-status splitStatements:false logicalFilePath:release-1.0/2021-08-05-category-status.sql
create type category__status as enum ('enabled', 'disabled');
comment on type category__status is 'Статус категории';

alter table categories add column status category__status default 'disabled';
comment on column categories.status is 'Статус категории (`enabled` - категория доступна; `disabled` - категория недоступна)';

--rollback alter table categories drop column status;
--rollback drop type category__status;
