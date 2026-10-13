ALTER TABLE users
ADD COLUMN remember_token_hash VARCHAR(64),
ADD COLUMN remember_token_expires TIMESTAMP;