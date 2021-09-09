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

alter table user_roles owner to ${database_admin_group};
grant select, insert, update, delete on table user_roles to ${terminal_user};
grant usage, select on user_roles_id_seq to ${terminal_user};

--rollback drop table user_roles;

alter table users add column password text;
comment on column users.password is 'Хэш пароля пользователя';

--rollback alter table users drop column password;
