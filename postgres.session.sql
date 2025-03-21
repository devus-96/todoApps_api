

ALTER TABLE usercompanies ADD COLUMN role VARCHAR(30) CHECK (role IN ('ownner', 'manager', 'employer', 'freelancer'))