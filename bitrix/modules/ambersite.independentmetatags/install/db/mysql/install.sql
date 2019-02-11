create table if not exists b_ambersite_independentmetatags
(
	ID int(11) not null auto_increment,
	SITEID varchar(255),
	URL varchar(255),
	TITLE varchar(255),
	DESCRIPTION varchar(255),
	KEYWORDS varchar(255),
	DATE datetime,
	OGIMAGEID int(11),
	OGTITLE varchar(255),
	OGDESCRIPTION varchar(255),
	STRINGS longtext,
	AIMT_TEXT_1 longtext,
	AIMT_ZAG_1 varchar(255),
	primary key (ID)
);