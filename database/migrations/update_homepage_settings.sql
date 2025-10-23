-- Remove the image fields
ALTER TABLE `homepage_settings`
DROP COLUMN `spotlight_image`,
DROP COLUMN `spotlight_image_alt`;

-- Add the video URL field
ALTER TABLE `homepage_settings`
ADD COLUMN `spotlight_video_url` VARCHAR(255) AFTER `spotlight_blurb`;

ALTER TABLE `homepage_settings`
DROP COLUMN `programs_undergrad_blurb`,
DROP COLUMN `programs_dual_blurb`,
DROP COLUMN `programs_grad_blurb`;
DROP COLUMN `show_pinned_announcements`;
