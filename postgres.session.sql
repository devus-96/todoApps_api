
ALTER TABLE calendar ADD CONSTRAINT users_fkey foreign key (user_id) references users(id) on delete cascade;