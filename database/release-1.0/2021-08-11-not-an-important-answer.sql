--liquibase formatted sql

--changeset 2021-08-11-not-an-important-answer splitStatements:false logicalFilePath:release-1.0/2021-08-11-not-an-important-answer.sql

alter table answers alter column correct drop not null;
alter table test_answers alter column correct drop not null;

--rollback alter table answers alter column correct set not null;
--rollback alter table test_answers alter column correct set not null;
