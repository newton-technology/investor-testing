--liquibase formatted sql
--changeset rename-columns-for-questions logicalFilePath:release-1.0/2021-08-05-rename-columns-for-questions.sql

alter table questions rename column answers_correct_count_min to answers_count_to_choose_min;
alter table questions rename column answers_correct_count_max to answers_count_to_choose_max;

comment on column questions.answers_count_to_choose_min is 'Минимально допустимое к выбору количество ответов';
comment on column questions.answers_count_to_choose_max is 'Максимально допустимое к выбору количество ответов';

--rollback alter table questions rename column answers_count_to_choose_min to answers_correct_count_min;
--rollback alter table questions rename column answers_count_to_choose_max to answers_correct_count_max;
--rollback comment on column questions.answers_correct_count_min is 'Минимально допустимое количество ответов';
--rollback comment on column questions.answers_correct_count_max is 'Максимально допустимое количество ответов';
