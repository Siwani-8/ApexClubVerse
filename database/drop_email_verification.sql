-- Optional upgrade for existing databases that still have email-verification columns.
-- Safe to run once after deploying the no-verification signup flow.

ALTER TABLE `users` DROP COLUMN IF EXISTS `email_verified`;
ALTER TABLE `users` DROP COLUMN IF EXISTS `verification_token`;
