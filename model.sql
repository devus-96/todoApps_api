CREATE TABLE user (
    id SERIAL PRIMARY KEY,
    firstname VARCHAR(150) NOT NULL,
    lastname VARCHAR(150) NOT NULL
    role VARCHAR(30) CHECK (role IN ('administrator', 'menber')),
    password VARCHAR(150),
    provider VARCHAR(30) CHECK (role IN ('google', 'github')),
    creation TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP;
);

CREATE TABLE notes (
    id SERIAL PRIMARY KEY,
    user_id integer not null,
    name VARCHAR(150) NOT NULL,
    contenu TEXT,
    foreign key (user_id) references users(id) on delete cascade
);

CREATE TABLE inviation (
    user_id integer not null,
    team_id integer not null,
    PRIMARY KEY(team_id, user_id),
    foreign key (user_id) references users(id),
    foreign key (team_id) references team(id) on delete cascade
);


CREATE TABLE team (
    id NUMBER PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    creation TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP;
);

CREATE TABLE meeting (
    id SERIAL PRIMARY KEY,
    team_id integer not null,
    creation TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    start DATE,
    status VARCHAR(30) CHECK (status IN ('schedule', 'cancel', 'in progress', 'done')),
    foreign key (team_id) references team(id)
);

CREATE TABLE project (
    id SERIAL PRIMARY KEY,
    team_id integer not null,
    creation TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    start_time DATE,
    deadline DATE,
    status VARCHAR(30) CHECK (status IN ('schedule', 'cancel', 'in progress', 'done')),
    foreign key (team_id) references team(id) on delete cascade
);

CREATE TABLE task (
    id SERIAL PRIMARY KEY,
    creation TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    start_date DATE,
    deadline DATE,
    start_time VARCHAR(10),
    end_time VARCHAR(10),
    status VARCHAR(30) CHECK (status IN ('schedule', 'cancel', 'in progress', 'done')),
    repeat JSON
);
CREATE TABLE schedule (
    day_id integer not null,
    task_id integer not null,
    PRIMARY KEY(day_id, task_id),
    foreign key (day_id) references day(id) on delete cascade,
    foreign key (task_id) references task(id)
);
CREATE TABLE plan (
    day_id integer not null,
    meeting_id integer not null,
    PRIMARY KEY(day_id, meeting_id),
    foreign key (day_id) references day(id) on delete cascade,
    foreign key (meeting_id) references meeting(id)
);

CREATE TABLE day (
    id SERIAL PRIMARY KEY,
    creation TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    start_date DATE,
    deadline DATE,
    repeat JSON,
    status VARCHAR(30) CHECK (status IN ('schedule', 'cancel', 'in progress', 'done'))
);

ALTER TABLE task ADD CONSTRAINT project_fkey FOREIGN KEY (project_id) REFERENCES project (id);
CREATE INDEX idx_email ON users(email);
ALTER TABLE users ADD CONSTRAINT email_key UNIQUE (email)
ALTER TABLE calendar ADD CONSTRAINT start_date_key UNIQUE (start_date)