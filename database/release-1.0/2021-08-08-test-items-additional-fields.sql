--liquibase formatted sql

--changeset 2021-08-08-test-items-additional-fields splitStatements:false logicalFilePath:release-1.0/2021-08-08-test-items-additional-fields.sql

alter table test_questions add column answers_count_to_choose_min int not null;
alter table test_questions add column answers_count_to_choose_max int;

comment on column test_questions.answers_count_to_choose_min is 'Минимально допустимое к выбору количество ответов';
comment on column test_questions.answers_count_to_choose_max is 'Максимально допустимое к выбору количество ответов';

alter table test_answers add column correct boolean not null;

--rollback alter table test_questions drop column answers_count_to_choose_min;
--rollback alter table test_questions drop column answers_count_to_choose_max;
--rollback alter table test_answers drop column correct;
