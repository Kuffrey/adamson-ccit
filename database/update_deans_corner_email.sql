-- Add 'email' column to deans_corner table
ALTER TABLE deans_corner ADD COLUMN email VARCHAR(128) DEFAULT 'dean@adamson.edu.ph' AFTER photo;

-- Example: Update dean's email
UPDATE deans_corner SET email = 'dean@adamson.edu.ph' WHERE id = 1;