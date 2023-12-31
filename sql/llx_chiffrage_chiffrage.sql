-- Copyright (C) ---Put here your own copyright and developer email---
--
-- This program is free software: you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation, either version 3 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program.  If not, see https://www.gnu.org/licenses/.


CREATE TABLE llx_chiffrage_chiffrage(
	-- BEGIN MODULEBUILDER FIELDS
	rowid integer AUTO_INCREMENT PRIMARY KEY NOT NULL, 
	ref varchar(40) DEFAULT '(PROV)' NOT NULL,
	entity integer DEFAULT 1,
	label varchar(160),
    group_title varchar(160),
	amount double DEFAULT NULL,
	qty real, 
	fk_soc integer, 
	fk_project integer, 
	description text, 
	note_public text, 
	note_private text, 
	date_creation datetime NOT NULL, 
	tms timestamp, 
	fk_user_creat integer NOT NULL, 
	fk_user_modif integer, 
	last_main_doc varchar(255), 
	import_key varchar(14), 
	model_pdf varchar(255), 
	status smallint DEFAULT 0,
	commercial_text text,
    detailed_feature_specification text,
	tech_detail text,
	dev_estimate integer,
	po_estimate integer NOT NULL,
	module_name integer,
	keywords varchar(128),
	estimate_date date,
    fk_product integer

	-- END MODULEBUILDER FIELDS
) ENGINE=innodb;
