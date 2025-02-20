CREATE TABLE Users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(150) NOT NULL,
    password VARCHAR(150) NOT NULL,
    birth_date DATE,
    phone_number BIGINT,
    role VARCHAR(150)
);

CREATE TABLE user_project (
    user_id integer not null,
    project_id integer not null,
    foreign key (user_id) references users(id) on delete cascade
    foreign key (project_id) references project 
);

ALTER TABLE task ADD COLUMN name VARCHAR(150) 
ALTER TABLE task ADD COLUMN priority VARCHAR(150) 
ALTER TABLE task ALTER COLUMN name SET NOT NULL;
CREATE TABLE Task (
    id SERIAL PRIMARY KEY,
    creation_date DATE NOT NULL,
    start_time TIME,
    end_time TIME,
    start_date DATE NOT NULL,
    deadline DATE NOT NULL,
    status VARCHAR(150) NOT NULL,
    next_time JSON,
    user_id integer not null,
    project_id integer not null,
    calendar_id integer not null,
    foreign key (user_id) references users(id) on delete cascade,
    foreign key (project_id) references project(id) on delete cascade,
    foreign key (calendar_id) references calendar(id) on delete cascade
);

CREATE TABLE Project (
    id SERIAL PRIMARY KEY,
    creation_date DATE NOT NULL,
    start_time TIME,
    end_time TIME,
    start_date DATE NOT NULL,
    deadline DATE NOT NULL,
    participants JSON NOT NULL,
    status VARCHAR(150) NOT NULL,
);

ALTER TABLE calendar DROP type 
ALTER TABLE calendar ADD COLUMN tasks JSON
ALTER TABLE calendar RENAME start_date TO date
ALTER TABLE calendar DROP end_date 

CREATE TABLE calendar (
    id serial primary key,
    start_date date not null,
    end_date date not null 
    user_id integer not nul,
    foreign key (user_id) references users(id) on delete cascade
);

DELETE FROM users;

ALTER TABLE users
ADD CONSTRAINT unique_number UNIQUE (phone_number);

ALTER TABLE users
RENAME COLUMN username TO firstname;
ADD COLUMN lastname varchar(60);
ALTER COLUMN firstname TYPE varchar(60);
