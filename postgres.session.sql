
ALTER TABLE roles DROP COLUMN role;
ALTER TABLE roles ADD COLUMN role VARCHAR(30) CHECK (role IN ('admistrator', 'author', 'menber'))