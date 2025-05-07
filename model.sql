CREATE TABLE teams (
    id SERIAL PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    creation TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE roles (
    user_id integer not null,
    team_id integer not null,
    PRIMARY KEY(user_id, team_id),
    foreign key (team_id) references teams(id) on delete cascade,
    foreign key (user_id) references users(id)
);

CREATE TABLE projects (
    id SERIAL PRIMARY KEY,
    team_id integer not null,
    creation TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    start_time DATE,
    deadline DATE,
    status VARCHAR(30) CHECK (status IN ('cancel', 'completed', 'in progress', 'done', 'plan')),
    foreign key (team_id) references teams(id) on delete cascade
);

CREATE TABLE calendar (
    id SERIAL PRIMARY KEY,
    creation TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    start_date DATE,
    deadline DATE,
    repeat JSON,
    status VARCHAR(30) CHECK (status IN ('cancel', 'completed', 'in progress', 'done', 'plan'))
);

CREATE TABLE meetings (
    id SERIAL PRIMARY KEY,
    team_id integer not null,
    creation TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    start DATE,
    status VARCHAR(30) CHECK (status IN ('cancel', 'in progress', 'done', 'plan')),
    foreign key (team_id) references teams(id)
);

CREATE TABLE plan (
    calendar_id integer not null,
    meeting_id integer not null,
    PRIMARY KEY(calendar_id, meeting_id),
    foreign key (calendar_id) references calendar(id) on delete cascade,
    foreign key (meeting_id) references meetings(id)
);

CREATE TABLE inviation (
    id SERIAL PRIMARY KEY,
    status VARCHAR(30) CHECK (status IN ('accepted', 'refused', 'pending')),
    email VARCHAR(150),
    creation TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    creator VARCHAR(150),
    team_id integer not null,
    foreign key (team_id) references teams(id) on delete cascade
);

CREATE TABLE tasks (
    id SERIAL PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    creation TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    start_date DATE NOT NULL,
    deadline DATE,
    start_time VARCHAR(10) NOT NULL,
    end_time VARCHAR(10) NOT NULL,
    repeat JSON,
    creator VARCHAR(150),
    assign TEXT[],
    tags TEXT[] NOT NULL,
    user_id integer,
    project_id integer,
    status VARCHAR(10) NOT NULL CHECK (status IN ('cancel', 'completed', 'in progress', 'done', 'plan')),
    priority VARCHAR(10) CHECK (status IN ('high', 'low', 'medium')),
    foreign key (user_id) references users(id) on delete cascade,
    foreign key (project_id) references projects(id) on delete cascade
);

CREATE TABLE schedules (
    calendar_id integer not null,
    task_id integer not null,
    PRIMARY KEY(calendar_id, task_id),
    foreign key (calendar_id) references calendar(id) on delete cascade,
    foreign key (task_id) references tasks(id)
);

CREATE TABLE notes (
    id SERIAL PRIMARY KEY,
    user_id integer not null,
    name VARCHAR(150) NOT NULL,
    contenu TEXT,
    foreign key (user_id) references users(id) on delete cascade
);

DROP TABLE schedules;
DROP TABLE calendar;
ALTER TABLE tasks ADD COLUMN user_id integer;
ALTER TABLE tasks ADD CONSTRAINT user_id_fey  foreign key (user_id) references users(id) on delete cascade;
ALTER TABLE tasks ADD COLUMN team_id integer;
ALTER TABLE tasks ADD CONSTRAINT team_id_fey  foreign key (team_id) references teams(id) on delete cascade;
ALTER TABLE projects ADD COLUMN user_id integer;
ALTER TABLE projects ADD CONSTRAINT user_id_fey  foreign key (user_id) references users(id) on delete cascade;

ALTER TABLE projects DROP COLUMN state;
ALTER TABLE projects ADD COLUMN state VARCHAR(30) CHECK (state IN ('not started',
    'paused',
    'in progress',
    'done',
    'canceled'))
ALTER TABLE tasks DROP COLUMN priority;
ALTER TABLE tasks ADD COLUMN priority VARCHAR(30) CHECK (priority IN ('low',
    'high',
    'medium'))
ALTER TABLE tasks DROP COLUMN start_time;
ALTER TABLE tasks DROP COLUMN end_time;
ALTER TABLE tasks ADD COLUMN start_time VARCHAR(10);
ALTER TABLE tasks ADD COLUMN end_time VARCHAR(10)