--liquibase formatted sql

create function update_column_created_at()
    returns trigger language 'plpgsql' as $$
begin
    NEW.created_at = now();
    return NEW;
end;
$$;

create function update_column_updated_at()
    returns trigger language 'plpgsql' as $$
begin
    NEW.updated_at = now();
    return NEW;
end;
$$;

--rollback drop function update_column_created_at;
--rollback drop function update_column_updated_at;

create table users
(
    id bigserial not null constraint users_pk primary key,
    email varchar(320) not null,
    created_at timestamp without time zone not null,
    updated_at timestamp without time zone
);

comment on table users is 'Пользователи';

comment on column users.id is 'Идентификатор пользователя';
comment on column users.email is 'Адрес электронной почты';
comment on column users.created_at is 'Время создания записи';
comment on column users.updated_at is 'Время изменения записи';

create unique index users__email on users (email);

alter table users owner to admins;
grant select, insert, update on table users to terminal;
grant usage, select on users_id_seq to terminal;

create trigger users__created_at
    before insert on users
    for each row
execute procedure update_column_created_at();

create trigger users__updated_at
    before update on users
    for each row
execute procedure update_column_updated_at();

--rollback drop table users;

create table categories
(
    id bigserial not null constraint categories_pk primary key,
    code varchar(255),
    name text not null,
    description text,
    created_at timestamp without time zone not null,
    updated_at timestamp without time zone
);

comment on table categories is 'Категории тестирования';

comment on column categories.id is 'Идентификатор категории';
comment on column categories.code is 'Код категории';
comment on column categories.name is 'Имя категории';
comment on column categories.description is 'Описание категории';
comment on column categories.created_at is 'Время создания записи';
comment on column categories.updated_at is 'Время изменения записи';

create unique index categories__code on categories (code);

alter table categories owner to admins;
grant select, insert, update on table categories to terminal;
grant usage, select on categories_id_seq to terminal;

create trigger categories__created_at
    before insert on categories
    for each row
execute procedure update_column_created_at();

create trigger categories__updated_at
    before update on categories
    for each row
execute procedure update_column_updated_at();

--rollback drop table categories;


--changeset questions logicalFilePath:release-1.0/2021-08-02-initialization.sql
create type questions__status as enum ('enabled', 'disabled');
comment on type questions__status is 'Статус вопроса';

create type questions__group_code as enum ('evaluation', 'custom', 'knowledge');
comment on type questions__group_code is 'Код блока вопросов';

create table questions
(
    id bigserial not null constraint questions_pk primary key,
    group_code questions__group_code not null,
    category_id bigint not null,
    text text not null,
    answers_count_min int not null default 4,
    answers_count_max int not null default 4,
    answers_correct_count_min int not null default 1,
    answers_correct_count_max int default 1,
    weight int not null default 0,
    status questions__status not null default 'enabled',
    created_at timestamp without time zone not null,
    updated_at timestamp without time zone
);

comment on table questions is 'Вопросы тестирования';

comment on column questions.id is 'Идентификатор вопроса';
comment on column questions.group_code is 'Код блока вопросов';
comment on column questions.category_id is 'Идентификатор категории';
comment on column questions.text is 'Текст вопроса';
comment on column questions.answers_count_min is 'Минимальное количество предлагаемых вариантов ответа';
comment on column questions.answers_count_max is 'Максимальное количество предлагаемых вариантов ответа';
comment on column questions.answers_correct_count_min is 'Минимально допустимое количество ответов';
comment on column questions.answers_correct_count_max is 'Максимально допустимое количество ответов';
comment on column questions.weight is 'Вес вопроса';
comment on column questions.status is 'Статус вопроса (`enabled` - может быть предложен; `disabled` - не может быть предложен)';
comment on column questions.created_at is 'Время создания записи';
comment on column questions.updated_at is 'Время изменения записи';

alter table questions owner to admins;
grant select, insert, update on table questions to terminal;
grant usage, select on questions_id_seq to terminal;

create trigger questions__created_at
    before insert on questions
    for each row
execute procedure update_column_created_at();

create trigger questions__updated_at
    before update on questions
    for each row
execute procedure update_column_updated_at();

--rollback drop table questions;
--rollback drop type questions__status;
--rollback drop type questions__group_code;

--changeset table-answers logicalFilePath:release-1.0/2021-08-02-initialization.sql
create type answers__status as enum ('enabled', 'required', 'disabled');
comment on type answers__status is 'Статус ответа';

create table answers
(
    id bigserial not null constraint answers_pk primary key,
    question_id bigint not null,
    text text not null,
    correct boolean not null,
    sort int not null default 0,
    status answers__status not null default 'enabled',
    created_at timestamp without time zone not null,
    updated_at timestamp without time zone
);

comment on table answers is 'Варианты ответов';

comment on column answers.id is 'Идентификатор ответа';
comment on column answers.question_id is 'Идентификатор вопроса';
comment on column answers.text is 'Текст ответа';
comment on column answers.correct is 'Признак правильности ответа';
comment on column answers.sort is 'Вес при сортировке предлагаемых вариантов ответа (от меньшего к большему)';
comment on column answers.status is 'Статус ответа (`enabled` - может быть предложен; `required` - должен быть предложен; `disabled` - не может быть предложен)';
comment on column answers.created_at is 'Время создания записи';
comment on column answers.updated_at is 'Время изменения записи';

alter table answers owner to admins;
grant select, insert, update on table answers to terminal;
grant usage, select on answers_id_seq to terminal;

create trigger answers__created_at
    before insert on answers
    for each row
execute procedure update_column_created_at();

create trigger answers__updated_at
    before update on answers
    for each row
execute procedure update_column_updated_at();

--rollback drop table answers;
--rollback drop type answers__status;

--changeset table-tests logicalFilePath:release-1.0/2021-08-02-initialization.sql
create type tests__status as enum ('draft', 'processing', 'failed', 'passed', 'canceled');
comment on type tests__status is 'Статус прохождения теста';

create table tests
(
    id bigserial not null constraint tests_pk primary key,
    user_id bigint not null,
    category_id bigint not null,
    status tests__status not null default 'draft',
    created_at timestamp without time zone not null,
    updated_at timestamp without time zone
);

comment on table tests is 'Тесты пользователей';

comment on column tests.id is 'Идентификатор теста';
comment on column tests.user_id is 'Идентификатор пользователя';
comment on column tests.category_id is 'Идентификатор категории';
comment on column tests.status is 'Статус теста';
comment on column tests.created_at is 'Время создания записи';
comment on column tests.updated_at is 'Время изменения записи';

create index tests__user_id on tests (user_id);
create index tests__user_id__category_id on tests (user_id, category_id);
create unique index tests__passed on tests (user_id, category_id) where status='passed';

alter table tests owner to admins;
grant select, insert, update on table tests to terminal;
grant usage, select on tests_id_seq to terminal;

create trigger tests__created_at
    before insert on tests
    for each row
execute procedure update_column_created_at();

create trigger tests__updated_at
    before update on tests
    for each row
execute procedure update_column_updated_at();

--rollback drop table tests;
--rollback drop type tests__status;


--changeset table-test_questions logicalFilePath:release-1.0/2021-08-02-initialization.sql
create table test_questions
(
    id bigserial not null constraint test_questions_pk primary key,
    test_id bigint not null,
    question_id bigint not null,
    question_text text not null,
    question_weight int not null,
    created_at timestamp without time zone not null,
    updated_at timestamp without time zone
);

comment on table test_questions is 'Вопросы тестов';

comment on column test_questions.id is 'Идентификатор вопроса';
comment on column test_questions.test_id is 'Идентификатор теста';
comment on column test_questions.question_id is 'Идентификатор вопроса';
comment on column test_questions.question_text is 'Текст вопроса';
comment on column test_questions.question_weight is 'Вес вопроса';
comment on column test_questions.created_at is 'Время создания записи';
comment on column test_questions.updated_at is 'Время изменения записи';

create index test_questions__test_id on test_questions (test_id);
create unique index test_questions__test_id__question_id on test_questions (test_id, question_id);

alter table test_questions owner to admins;
grant select, insert, update on table test_questions to terminal;
grant usage, select on test_questions_id_seq to terminal;

create trigger test_questions__created_at
    before insert on test_questions
    for each row
execute procedure update_column_created_at();

create trigger test_questions__updated_at
    before update on test_questions
    for each row
execute procedure update_column_updated_at();

--rollback drop table test_questions;


--changeset table-test_answers logicalFilePath:release-1.0/2021-08-02-initialization.sql
create table test_answers
(
    id bigserial not null constraint test_answers_pk primary key,
    test_question_id bigint not null,
    answer_id bigint not null,
    answer_text text not null,
    selected boolean not null default false,
    created_at timestamp without time zone not null,
    updated_at timestamp without time zone
);

comment on table test_answers is 'Ответы на вопросы тестов';

comment on column test_answers.id is 'Идентификатор записи';
comment on column test_answers.test_question_id is 'Идентификатор вопроса в тесте';
comment on column test_answers.answer_id is 'Идентификатор ответа';
comment on column test_answers.answer_text is 'Текст ответа';
comment on column test_answers.selected is 'Признак выбора ответа';
comment on column test_answers.created_at is 'Время создания записи';
comment on column test_answers.updated_at is 'Время изменения записи';

create index test_answers__test_question_id on test_answers (test_question_id);
create unique index test_answers__test_question_id__answer_id on test_answers (test_question_id, answer_id);

alter table test_answers owner to admins;
grant select, insert, update on table test_answers to terminal;
grant usage, select on test_answers_id_seq to terminal;

create trigger test_answers__created_at
    before insert on test_answers
    for each row
execute procedure update_column_created_at();

create trigger test_answers__updated_at
    before update on test_answers
    for each row
execute procedure update_column_updated_at();

--rollback drop table test_answers;
--liquibase formatted sql

--changeset 2021-08-05-category-status splitStatements:false logicalFilePath:release-1.0/2021-08-05-category-status.sql
create type category__status as enum ('enabled', 'disabled');
comment on type category__status is 'Статус категории';

alter table categories add column status category__status default 'disabled';
comment on column categories.status is 'Статус категории (`enabled` - категория доступна; `disabled` - категория недоступна)';

--rollback alter table categories drop column status;
--rollback drop type category__status;
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
--liquibase formatted sql

--changeset 2021-08-11-not-an-important-answer splitStatements:false logicalFilePath:release-1.0/2021-08-11-not-an-important-answer.sql

alter table answers alter column correct drop not null;
alter table test_answers alter column correct drop not null;

--rollback alter table answers alter column correct set not null;
--rollback alter table test_answers alter column correct set not null;
--liquibase formatted sql

--changeset 2021-08-12-category-short-description splitStatements:false logicalFilePath:release-1.0/2021-08-12-category-short-description.sql

alter table categories add column description_short text;
comment on column categories.description_short is 'Краткое описание категории';

--rollback alter table categories drop column description_short;
--liquibase formatted sql

--changeset 2021-08-26-PD-15-grant-password splitStatements:false logicalFilePath:release-1.1/2021-08-26-PD-15-grant-password.sql

create table user_roles
(
    id bigserial not null constraint user_roles_pk primary key,
    user_id bigint not null,
    role varchar(255) not null,
    created_at timestamp without time zone not null,
    updated_at timestamp without time zone
);

create index user_roles__user_id on user_roles (user_id);
create index user_roles__role on user_roles (role);
create unique index user_roles__user_id_role on user_roles (user_id, role);

comment on column user_roles.id is 'Идентификатор записи';
comment on column user_roles.user_id is 'Идентификатор пользователя';
comment on column user_roles.role is 'Имя роли';
comment on column user_roles.created_at is 'Время создания записи';
comment on column user_roles.updated_at is 'Время изменения записи';

create trigger user_roles__created_at
    before insert on user_roles
    for each row
execute procedure update_column_created_at();

create trigger user_roles__updated_at
    before update on user_roles
    for each row
execute procedure update_column_updated_at();

alter table user_roles owner to admins;
grant select, insert, update, delete on table user_roles to terminal;
grant usage, select on user_roles_id_seq to terminal;

--rollback drop table user_roles;

alter table users add column password text;
comment on column users.password is 'Хэш пароля пользователя';

--rollback alter table users drop column password;
