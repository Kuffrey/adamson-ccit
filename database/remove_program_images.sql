-- Remove image columns from programs table
-- Run this SQL script in your adamson_ccit database

-- First, let's check if the columns exist
-- SHOW COLUMNS FROM programs LIKE 'image%';

-- Remove the image and image_alt columns from programs table
ALTER TABLE programs 
DROP COLUMN IF EXISTS image,
DROP COLUMN IF EXISTS image_alt;

-- Verify the changes
-- DESCRIBE programs;