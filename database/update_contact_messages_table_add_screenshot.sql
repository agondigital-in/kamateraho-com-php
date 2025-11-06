USE kamateraho;
ALTER TABLE contact_messages ADD COLUMN screenshot VARCHAR(255) NULL AFTER message;