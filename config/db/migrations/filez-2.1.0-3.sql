SET NAMES 'utf8';

ALTER TABLE fz_user ADD quota VARCHAR(60) NOT NULL SET DEFAULT '2G' AFTER email;
