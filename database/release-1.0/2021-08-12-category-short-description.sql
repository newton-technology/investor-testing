--liquibase formatted sql

--changeset 2021-08-12-category-short-description splitStatements:false logicalFilePath:release-1.0/2021-08-12-category-short-description.sql

alter table categories add column description_short text;
comment on column categories.description_short is 'Краткое описание категории';

--rollback alter table categories drop column description_short;
