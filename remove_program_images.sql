-- Remove image-related columns from programs table
-- This script removes the image and image_alt columns that are no longer needed

ALTER TABLE programs DROP COLUMN image;
ALTER TABLE programs DROP COLUMN image_alt;

-- Verify the table structure after removing columns
-- Uncomment the line below to see the updated table structure
-- DESCRIBE programs;