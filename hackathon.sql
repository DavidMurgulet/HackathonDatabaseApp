drop table Attend;

drop table Hacker_U19;

drop table J_Volunteers;

drop table M_Volunteers;

drop table Organize;

drop table Supports;

drop table Rank;

drop table Prize_Win;

drop table Provide;

drop table Participates;

drop table Sponsors;

drop table Student_Hacker;

drop table Hacker_Parent;

drop table Hacker_DateOfBirth;

drop table Belong_to;

drop table Hacker;

drop table Judge;

drop table Mentor;

drop table Sponsor;

drop table Workshop;

drop table Workshop_topic;

drop table Team_Submits_project;

drop table Hackathon;

drop table Hackathon_loc;

CREATE TABLE Hackathon_loc (location CHAR (50) PRIMARY KEY, city CHAR(30));

grant
select
	on Hackathon_loc to public;

CREATE TABLE Sponsor (
	organization CHAR(30) PRIMARY KEY,
	field CHAR(30)
);

grant
select
	on Sponsor to public;

CREATE TABLE Hackathon (
	location CHAR(50) NOT NULL,
	name CHAR(30),
	year INT,
	proj_deadline TIMESTAMP NOT NULL,
	start_time TIMESTAMP,
	end_time TIMESTAMP,
	CONSTRAINT pk_hackathon PRIMARY KEY (name, year),
	CONSTRAINT fk_loc 
		FOREIGN KEY (location) 
		REFERENCES Hackathon_loc(location) 
		ON DELETE CASCADE,
	UNIQUE (name, proj_deadline),
	UNIQUE (name, start_time),
	UNIQUE (name, end_time)
);

grant
select
	on Hackathon to public;

CREATE TABLE Hacker(
	HID INTEGER PRIMARY KEY,
	name CHAR(30),
	email CHAR(30) NOT NULL UNIQUE,
	skill_level CHAR(30)
);

grant
select
	on Hacker to public;

CREATE TABLE Student_Hacker(
	HID INTEGER PRIMARY KEY NOT NULL, 
	school CHAR(30), 
	stu_id INTEGER,
	CONSTRAINT fk_hid_stu
		FOREIGN KEY (HID)
		REFERENCES Hacker(HID)
		ON DELETE CASCADE
);

grant
select
	on Student_Hacker to public;

CREATE TABLE Hacker_Parent (
	parent_phone INTEGER PRIMARY KEY,
	parent_name CHAR(30)
);

grant
select
	on Hacker_Parent to public;

CREATE TABLE Hacker_DateOfBirth (DateOfBirth DATE PRIMARY KEY, age INTEGER);

grant
select
	on Hacker_DateOfBirth to public;

CREATE TABLE Hacker_U19 (
	HID INTEGER PRIMARY KEY,
	parent_phone INT NOT NULL, 
	DateOfBirth DATE NOT NULL, 
	CONSTRAINT fk_hid_u19
		FOREIGN KEY (HID) 
		REFERENCES Hacker(HID)
		ON DELETE CASCADE,
	CONSTRAINT fk_phone
		FOREIGN KEY (parent_phone) 
		REFERENCES Hacker_Parent(parent_phone)
		ON DELETE CASCADE,
	CONSTRAINT fk_dob
		FOREIGN KEY (DateOfBirth)
		REFERENCES Hacker_DateOfBirth(DateOfBirth)
		ON DELETE CASCADE
);

grant
select
	on Hacker_U19 to public;

CREATE TABLE Judge (
	VID INT PRIMARY KEY,
	name CHAR(30),
	occupation CHAR(30)
);

grant
select
	on Judge to public;

CREATE TABLE Mentor (
	VID INT PRIMARY KEY,
	name CHAR(30),
	expertise CHAR(30)
);

grant
select
	on Mentor to public;

CREATE TABLE J_Volunteers (
	VID INT,
	hackathon_name CHAR(30),
	Hackathon_yr INT,
	CONSTRAINT pk_jv
		PRIMARY KEY (VID, hackathon_name, hackathon_yr),
	CONSTRAINT fk_jvid_vol
		FOREIGN KEY (VID) 
		REFERENCES Judge(VID)
		ON DELETE CASCADE,
	CONSTRAINT fk_hackathon_jv
		FOREIGN KEY (hackathon_name, hackathon_yr)
		REFERENCES Hackathon(name, year)
		ON DELETE CASCADE
);

grant
select
	on J_Volunteers to public;

CREATE TABLE M_Volunteers (
	VID INT,
	hackathon_name CHAR(30),
	Hackathon_yr INT,
	CONSTRAINT pk_mv 
		PRIMARY KEY (VID, hackathon_name, hackathon_yr),
	CONSTRAINT fk_mvid_vol
		FOREIGN KEY (VID)
		REFERENCES Mentor(VID)
		ON DELETE CASCADE,
	CONSTRAINT fk_hackathon_mv
		FOREIGN KEY (hackathon_name, hackathon_yr)
		REFERENCES Hackathon(name, year)
		ON DELETE CASCADE
);

grant
select
	on M_Volunteers to public;

CREATE TABLE Sponsors (
	sponsor_org CHAR (30),
	hackathon_name CHAR(30),
	hackathon_yr INT,
	CONSTRAINT pk_sponsors 
		PRIMARY KEY (sponsor_org, hackathon_name, hackathon_yr),
	CONSTRAINT fk_sponsor_org_sponsors
		FOREIGN KEY (sponsor_org)
		REFERENCES Sponsor(organization)
		ON DELETE CASCADE,
	CONSTRAINT fk_hackathon_sp
		FOREIGN KEY (hackathon_name, hackathon_yr)
		REFERENCES Hackathon(name, year)
		ON DELETE CASCADE
);

grant
select
	on Sponsors to public;

CREATE TABLE Participates (
	HID INT,
	hackathon_name CHAR(30),
	hackathon_yr INT,
	CONSTRAINT pk_participates 
		PRIMARY KEY (HID, hackathon_name, hackathon_yr),
	CONSTRAINT fk_hid_par	
		FOREIGN KEY (HID)
		REFERENCES Hacker(HID)
		ON DELETE CASCADE,
	CONSTRAINT fk_hackathon_par
		FOREIGN KEY (hackathon_name, hackathon_yr)
		REFERENCES Hackathon(name, year)
		ON DELETE CASCADE
);

grant
select
	on Participates to public;

CREATE TABLE Team_Submits_project (
	tnum INT PRIMARY KEY,
	hackathon_name CHAR(30) NOT NULL,
	hackathon_yr INT NOT NULL,
	submission_time TIMESTAMP NOT NULL,
	project_name CHAR(30) NOT NULL,
	CONSTRAINT fk_hackathon_team
		FOREIGN KEY (hackathon_name, hackathon_yr)
		REFERENCES Hackathon(name, year)
		ON DELETE CASCADE
);

grant
select
	on Team_Submits_project to public;

CREATE TABLE Rank (
	VID INT,
	tnum INT,
	score INT NOT NULL,
	CONSTRAINT pk_rank 
		PRIMARY KEY (VID, tnum),
	CONSTRAINT fk_jvid_rank
		FOREIGN KEY (VID)
		REFERENCES Judge(VID)
		ON DELETE CASCADE,
	CONSTRAINT fk_tnum_rank
		FOREIGN KEY (tnum)
		REFERENCES Team_Submits_project(tnum)
		ON DELETE CASCADE
);

grant
select
	on Rank to public;

CREATE TABLE Supports (
	VID INTEGER,
	HID INTEGER,
	CONSTRAINT pk_supports 
		PRIMARY KEY (VID, HID),
	CONSTRAINT fk_mvid_sup
		FOREIGN KEY (VID) 
		REFERENCES Mentor(VID)
		ON DELETE CASCADE,
	CONSTRAINT fk_hid_sup	
		FOREIGN KEY (HID)
		REFERENCES Hacker(HID)
		ON DELETE CASCADE
);

grant
select
	on Supports to public;
	
CREATE TABLE Belong_to (
	HID INTEGER,
	tnum INTEGER,
	CONSTRAINT pk_belong_to 
		PRIMARY KEY (HID, tnum),
	CONSTRAINT fk_hid_b
		FOREIGN KEY (HID)
		REFERENCES Hacker(HID)
		ON DELETE CASCADE,
	CONSTRAINT fk_tnum_b
		FOREIGN KEY (tnum)
		REFERENCES Team_Submits_project(tnum)
		ON DELETE CASCADE
);

grant
select
	on Belong_to to public;

CREATE TABLE Workshop_topic (name CHAR(30) PRIMARY KEY, topic CHAR(30));

grant
select
	on WorkShop_topic to public;

CREATE TABLE Workshop (
	name CHAR(30),
	location CHAR(30),
	start_time TIMESTAMP,
	end_time TIMESTAMP,
	CONSTRAINT pk_workshop 
		PRIMARY KEY (name, location),
	CONSTRAINT fk_workshop_topic
		FOREIGN KEY (name) 
		REFERENCES Workshop_topic(name)
		ON DELETE CASCADE
);
grant
select
	on Workshop to public;

CREATE TABLE Organize (
	VID INT,
	name CHAR(30),
	location CHAR(30), 
	CONSTRAINT pk_organize 
		PRIMARY KEY (VID, name, location),
	CONSTRAINT fk_mvid_org
		FOREIGN KEY (VID) 
		REFERENCES Mentor(VID)
		ON DELETE CASCADE,
	CONSTRAINT fk_workshop_org
		FOREIGN KEY (name, location)
		REFERENCES Workshop(name, location)
		ON DELETE CASCADE
);

grant
select
	on Organize to public;

CREATE TABLE Attend (
	HID INT,
	name CHAR(30),
	location CHAR(30),
	CONSTRAINT pk_attend 
		PRIMARY KEY (HID, name, location),
	CONSTRAINT fk_hid_att
		FOREIGN KEY (HID)
		REFERENCES Hacker(HID)
		ON DELETE CASCADE,
	CONSTRAINT fk_workshop_att
		FOREIGN KEY (name, location)
		REFERENCES Workshop(name, location)
		ON DELETE CASCADE
);

grant
select
	on Attend to public;

CREATE TABLE Provide (
	prize_name CHAR(30) PRIMARY KEY,
	sponsor_org CHAR(30) NOT NULL,
	CONSTRAINT fk_sponsor_org_provide
		FOREIGN KEY (sponsor_org)
		REFERENCES Sponsor(organization)
		ON DELETE CASCADE
);
grant
select
	on Provide to public;

CREATE TABLE Prize_Win (
    p_name CHAR(30),
    amount INT NOT NULL,
    tnum INT NOT NULL,
	CONSTRAINT pk_prize_win
		PRIMARY KEY (p_name, tnum),
	CONSTRAINT fk_tnum_prize
		FOREIGN KEY (tnum)
		REFERENCES Team_Submits_project(tnum)
		ON DELETE CASCADE,
	CONSTRaint fk_prize
		FOREIGN KEY (p_name)
		REFERENCES Provide(prize_name)
		ON DELETE CASCADE
);	

grant
select
	on Prize_Win to public;



/* Hackathon_loc inserts*/
insert into
	Hackathon_loc
values
	('UBC Life Science Institute', 'Vancouver');

insert into
	Hackathon_loc
values
	('UBC Robert H. Lee Alumni Centre', 'Vancouver');

insert into
	Hackathon_loc
values
	('SFU', 'Burnaby');

insert into
	Hackathon_loc
values
	('Zoom', NULL);

insert into
	Hackathon_loc
values
	('UBCO', 'Kelowna');

insert into
	Hackathon_loc
values
	('University of Waterloo', 'Waterloo');


/*Sponsor Inserts*/
insert into
	Sponsor
values
	('Steves Poke Bar', 'Food');

insert into
	Sponsor
values
	('Microsoft', 'Tech');

insert into
	Sponsor
values
	('Amazon', 'Tech');

insert into
	Sponsor
values
	('Red Bull', 'Food');

insert into
	Sponsor
values
	('MLH', 'Tech');

insert into
	Sponsor
values
	('SAP', 'Tech');

insert into
	Sponsor
values
	('livepeer', 'Tech');

insert into
	Sponsor
values
	('Deloitte', 'Consulting');

/* Hackathon inserts */
INSERT INTO
	Hackathon
VALUES
	(
		'UBC Life Science Institute',
		'nwHacks',
		2023,
		(TO_TIMESTAMP('2023-01-22 12:00:00', 'YYYY-MM-DD HH24:MI:SS')),
		(TO_TIMESTAMP('2023-01-21 09:00:00', 'YYYY-MM-DD HH24:MI:SS')),
		(TO_TIMESTAMP('2023-01-22 18:00:00', 'YYYY-MM-DD HH24:MI:SS'))
	);

insert into
	Hackathon
values
	(
		'UBC Robert H. Lee Alumni Centre',
		'cmd-f',
		2023,
		(TO_TIMESTAMP('2023-03-12 12:00:00', 'YYYY-MM-DD HH24:MI:SS')),
		(TO_TIMESTAMP('2023-03-11 08:00:00', 'YYYY-MM-DD HH24:MI:SS')),
		(TO_TIMESTAMP('2023-03-12 19:00:00', 'YYYY-MM-DD HH24:MI:SS'))
	);

insert into Hackathon values (
	'UBC Robert H. Lee Alumni Centre',
	'cmd-f',
	2022,
	(TO_TIMESTAMP('2022-03-12 12:00:00', 'YYYY-MM-DD HH24:MI:SS')),
	(TO_TIMESTAMP('2022-03-11 08:00:00', 'YYYY-MM-DD HH24:MI:SS')),
	(TO_TIMESTAMP('2022-03-12 19:00:00', 'YYYY-MM-DD HH24:MI:SS'))
);

insert into Hackathon values (
	'UBC Robert H. Lee Alumni Centre',
	'cmd-f',
	2021,
	(TO_TIMESTAMP('2021-03-12 12:00:00', 'YYYY-MM-DD HH24:MI:SS')),
	(TO_TIMESTAMP('2021-03-11 08:00:00', 'YYYY-MM-DD HH24:MI:SS')),
	(TO_TIMESTAMP('2021-03-12 19:00:00', 'YYYY-MM-DD HH24:MI:SS'))
);

insert into Hackathon values (
	'UBC Robert H. Lee Alumni Centre',
	'cmd-f',
	2020,
	(TO_TIMESTAMP('2020-03-12 12:00:00', 'YYYY-MM-DD HH24:MI:SS')),
	(TO_TIMESTAMP('2020-03-11 08:00:00', 'YYYY-MM-DD HH24:MI:SS')),
	(TO_TIMESTAMP('2020-03-12 19:00:00', 'YYYY-MM-DD HH24:MI:SS'))
);

insert into
	Hackathon
values
	(
		'SFU',
		'StormHacks',
		2023,
		(TO_TIMESTAMP('2023-05-21 13:00:00', 'YYYY-MM-DD HH24:MI:SS')),
		(TO_TIMESTAMP('2023-05-20 08:30:00', 'YYYY-MM-DD HH24:MI:SS')),
		(TO_TIMESTAMP('2023-05-21 18:30:00', 'YYYY-MM-DD HH24:MI:SS'))
	);

insert into
	Hackathon
values
	(
		'Zoom',
		'nwHacks',
		2021,
		(TO_TIMESTAMP('2021-01-11 12:00:00', 'YYYY-MM-DD HH24:MI:SS')),
		(TO_TIMESTAMP('2021-01-10 08:30:00', 'YYYY-MM-DD HH24:MI:SS')),
		(TO_TIMESTAMP('2021-01-11 19:00:00', 'YYYY-MM-DD HH24:MI:SS'))
	);

insert into Hackathon values 
	(		
		'UBC Life Science Institute',
		'nwHacks',
		2020,
		(TO_TIMESTAMP('2020-01-22 12:00:00', 'YYYY-MM-DD HH24:MI:SS')),
		(TO_TIMESTAMP('2020-01-21 09:00:00', 'YYYY-MM-DD HH24:MI:SS')),
		(TO_TIMESTAMP('2020-01-22 18:00:00', 'YYYY-MM-DD HH24:MI:SS'))
	);

insert into Hackathon values 
	(		
		'UBC Life Science Institute',
		'nwHacks',
		2019,
		(TO_TIMESTAMP('2019-01-22 12:00:00', 'YYYY-MM-DD HH24:MI:SS')),
		(TO_TIMESTAMP('2019-01-21 09:00:00', 'YYYY-MM-DD HH24:MI:SS')),
		(TO_TIMESTAMP('2019-01-22 18:00:00', 'YYYY-MM-DD HH24:MI:SS'))
	);
	

insert into
	Hackathon
values
	(
		'UBCO',
		'BC Hacks 2.0',
		2021,
		(TO_TIMESTAMP('2021-02-21 13:30:00', 'YYYY-MM-DD HH24:MI:SS')),
		(TO_TIMESTAMP('2021-02-20 08:30:00', 'YYYY-MM-DD HH24:MI:SS')),
		(TO_TIMESTAMP('2021-02-21 18:30:00', 'YYYY-MM-DD HH24:MI:SS'))
	);

insert into
	Hackathon
values
	(
		'University of Waterloo',
		'Hack the North',
		2022,
		(TO_TIMESTAMP('2022-09-18 12:00:00', 'YYYY-MM-DD HH24:MI:SS')),
		(TO_TIMESTAMP('2022-09-16 09:00:00', 'YYYY-MM-DD HH24:MI:SS')),
		(TO_TIMESTAMP('2022-09-18 19:00:00', 'YYYY-MM-DD HH24:MI:SS'))
	);

/*Hacker Inserts*/
insert into
	Hacker
values
	(
		000,
		'Michael Robinson',
		'mrobinson06@gmail.com',
		'beginner'
	);

insert into
	Hacker
values
	(
		001,
		'Erica Wade',
		'ericawade@outlook.com',
		'intermediate'
	);

insert into
	Hacker
values
	(
		002,
		'Brianna Gregory',
		'brigreg00@gmail.com',
		'beginner'
	);

insert into
	Hacker
values
	(
		003,
		'Karl Melton',
		'karlmelton87@aol.com',
		'professional'
	);

insert into
	Hacker
values
	(
		004,
		'Mary Robles',
		'mrobles@gmail.com',
		'beginner'
	);

insert into
	Hacker
values
	(
		005,
		'Don Madden',
		'dmadden@gmail.com',
		'intermediate'
	);

insert into
	Hacker
values
	(
		006,
		'Kent Newman',
		'knewman@yahoo.com',
		'beginner'
	);

insert into
	Hacker
values
	(
		007,
		'Danielle Thompson',
		'dthompson08@gmail.com',
		'beginner'
	);

insert into
	Hacker
values
	(
		008,
		'Thomas Morgan',
		'thomorgan07@gmail.com',
		'beginner'
	);

insert into
	Hacker
values
	(
		009,
		'Steven Bonila',
		'stevebonila05@outlook.com',
		'intermediate'
	);

insert into
	Hacker
values
	(
		010,
		'Brandon Robinson',
		'brobinson06@gmail.com',
		'beginner'
	);

insert into Hacker values 
	(
		011,
		'Mr. All Hackathons',
		'iminallthehackathons@aol.com',
		'professional'
	);

insert into Hacker values
(
	012,
	'nwHacks Fan',
	'iminallnwHacks@gmail.com',
	'intermediate'
);

insert into Hacker values
(
	013,
	'Script Kiddie',
	'cmdffan@outlook.com',
	'beginner'
);

/*Student_Hacker inserts*/
insert into
	Student_Hacker
values
	(000, 'SFU', 37516290);

insert into
	Student_Hacker
values
	(001, 'UBC', 37516290);

insert into
	Student_Hacker
values
	(002, 'UBC', 95301764);

insert into
	Student_Hacker
values
	(004, 'UVIC', 70469513);

insert into
	Student_Hacker
values
	(006, 'UBC', 34097651);

/*Hacker_parent inserts*/
insert into
	Hacker_Parent
values
	(2484345508, 'Deborah Robinson');

insert into
	Hacker_Parent
values
	(8453549912, 'Kurt Thompson');

insert into
	Hacker_Parent
values
	(7192662837, 'Marisa Morgan');

insert into
	Hacker_Parent
values
	(2018675309, 'Jenny Bonila');

insert into
	Hacker_Parent
values
	(5058425662, 'Saul Robinson');

/*Hacker_DOB inserts (idk if we really needed this one)*/
insert into
	Hacker_DateOfBirth
values
	((TO_DATE('2006-03-01', 'YYYY-MM-DD')), 17);

insert into
	Hacker_DateOfBirth
values
	((TO_DATE('2008-01-17', 'YYYY-MM-DD')), 15);

insert into
	Hacker_DateOfBirth
values
	((TO_DATE('2007-01-01', 'YYYY-MM-DD')), 16);

insert into
	Hacker_DateOfBirth
values
	((TO_DATE('2005-02-01', 'YYYY-MM-DD')), 18);

insert into
	Hacker_DateOfBirth
values
	((TO_DATE('2006-04-02', 'YYYY-MM-DD')), 16);

/*Hacker_u19 inserts*/
insert into
	Hacker_U19
values
	(000, 2484345508, (TO_DATE('2006-03-01', 'YYYY-MM-DD')));

insert into
	Hacker_U19
values
	(007, 8453549912, (TO_DATE('2008-01-17', 'YYYY-MM-DD')));

insert into
	Hacker_U19
values
	(008, 7192662837, (TO_DATE('2007-01-01', 'YYYY-MM-DD')));

insert into
	Hacker_U19
values
	(009, 2018675309, (TO_DATE('2005-02-01', 'YYYY-MM-DD')));

insert into
	Hacker_U19
values
	(010, 5058425662, (TO_DATE('2006-04-02', 'YYYY-MM-DD')));

/*Judge Inserts*/
insert into
	Judge
values
	(00000, 'Gregor Kiczales', 'Software Processor');

insert into
	Judge
values
	(
		11111,
		'Andrew Biddell',
		'Investor, Inovia Capital'
	);

insert into
	Judge
values
	(22222, 'Danielle Rose', 'CEO, Ceragen');

insert into
	Judge
values
	(
		33333,
		'Shannon Wells',
		'Head of Ecosystem Growth'
	);

insert into
	Judge
values
	(
		44444,
		'Myra Arshad',
		'Co-founder and CEO, ALT TEX'
	);

insert into Judge values 
	(
		55555,
		'Paul Atreides',
		'Muadib'
	);

insert into Judge values 
	(
		66666,
		'Phoenix Wright',
		'Attorney at Law'
	);

insert into Judge values 
	(
		77777,
		'Conan Obrien',
		'Talk show host'
	);

insert into Judge values 
	(
		88888,
		'Woody Goss',
		'Bird watcher'
	);

insert into Judge values
	(
		99999,
		'Cole Cassidy',
		'Bounty Hunter'
	);

/*Mentor inserts*/
insert into
	Mentor
values
	(10000, 'Lori Larson', 'Java');

insert into
	Mentor
values
	(20000, 'Trevor Johns', 'SQL');
insert into
	Mentor
values
	(30000, 'Joseph Pittman', 'Databases');

insert into
	Mentor
values
	(40000, 'Sandra Adams', 'OOP');

insert into
	Mentor
values
	(50000, 'Bruce Garcia', 'Python');

insert into Mentor values 
	(
		60000,
		'Carl Sagan', 
		'science guy'
	);

insert into Mentor values 
	(
		70000,
		'Bill Nye',
		'THE science guy'
	);

insert into Mentor values 
	(
		80000, 
		'Colin Mcrae',
		'Rally Driver'
	);

insert into Mentor values 
	(
		90000,
		'Daniel Ricciardo',
		'Professional Australian'
	);


/*J-Volunteers inserts*/
insert into
	J_Volunteers
values
	(00000, 'nwHacks', 2023);

insert into
	J_Volunteers
values
	(66666, 'nwHacks', 2023);

insert into
	J_Volunteers
values
	(77777, 'nwHacks', 2023);

insert into
	J_Volunteers
values
	(00000, 'cmd-f', 2023);

insert into
	J_Volunteers
values
	(11111, 'cmd-f', 2023);

insert into
	J_Volunteers
values
	(55555, 'cmd-f', 2023);

insert into
	J_Volunteers
values
	(11111, 'Hack the North', 2022);

insert into
	J_Volunteers
values
	(22222, 'Hack the North', 2022);

insert into
	J_Volunteers
values
	(33333, 'Hack the North', 2022);

insert into
	J_Volunteers
values
	(33333, 'StormHacks', 2023);

insert into
	J_Volunteers
values
	(44444, 'StormHacks', 2023);

insert into
	J_Volunteers
values
	(66666, 'StormHacks', 2023);

insert into
	J_Volunteers
values
	(22222, 'StormHacks', 2023);

insert into
	J_Volunteers
values
	(44444, 'nwHacks', 2021);

insert into
	J_Volunteers
values
	(55555, 'nwHacks', 2021);

insert into
	J_Volunteers
values
	(99999, 'nwHacks', 2021);

insert into
	J_Volunteers
values
	(00000, 'cmd-f', 2022);

insert into
	J_Volunteers
values
	(88888, 'cmd-f', 2022);

insert into
	J_Volunteers
values
	(99999, 'cmd-f', 2022);

insert into
	J_Volunteers
values
	(11111, 'cmd-f', 2022);

insert into
	J_Volunteers
values
	(22222, 'cmd-f', 2021);

insert into
	J_Volunteers
values
	(33333, 'cmd-f', 2021);

insert into
	J_Volunteers
values
	(99999, 'cmd-f', 2021);

insert into
	J_Volunteers
values
	(00000, 'cmd-f', 2020);

insert into
	J_Volunteers
values
	(66666, 'cmd-f', 2020);

insert into
	J_Volunteers
values
	(77777, 'cmd-f', 2020);

insert into
	J_Volunteers
values
	(00000, 'nwHacks', 2020);

insert into
	J_Volunteers
values
	(88888, 'nwHacks', 2020);

insert into
	J_Volunteers
values
	(99999, 'nwHacks', 2020);

insert into
	J_Volunteers
values
	(99999, 'nwHacks', 2019);

insert into
	J_Volunteers
values
	(33333, 'nwHacks', 2019);

insert into
	J_Volunteers
values
	(66666, 'nwHacks', 2019);

insert into
	J_Volunteers
values
	(77777, 'BC Hacks 2.0', 2021);

insert into
	J_Volunteers
values
	(88888, 'BC Hacks 2.0', 2021);

insert into
	J_Volunteers
values
	(44444, 'BC Hacks 2.0', 2021);

insert into
	J_Volunteers
values
	(11111, 'BC Hacks 2.0', 2021);


/*M_Volunteers inserts*/
insert into
	M_Volunteers
values
	(10000, 'nwHacks', 2023);

insert into
	M_Volunteers
values
	(10000, 'cmd-f', 2023);

insert into
	M_Volunteers
values
	(20000, 'nwHacks', 2023);

insert into
	M_Volunteers
values
	(20000, 'nwHacks', 2021);

insert into
	M_Volunteers
values
	(30000, 'BC Hacks 2.0', 2021);

insert into
	M_Volunteers
values
	(30000, 'StormHacks', 2023);

insert into
	M_Volunteers
values
	(40000, 'BC Hacks 2.0', 2021);

insert into
	M_Volunteers
values
	(50000, 'StormHacks', 2023);

insert into
	M_Volunteers
values
	(60000, 'StormHacks', 2023);

insert into
	M_Volunteers
values
	(70000, 'StormHacks', 2023);

insert into
	M_Volunteers
values
	(50000, 'BC Hacks 2.0', 2021);

insert into
	M_Volunteers
values
	(60000, 'BC Hacks 2.0', 2021);

insert into
	M_Volunteers
values
	(60000, 'nwHacks', 2023);

insert into
	M_Volunteers
values
	(60000, 'nwHacks', 2021);

insert into
	M_Volunteers
values
	(70000, 'nwHacks', 2023);

insert into
	M_Volunteers
values
	(70000, 'nwHacks', 2021);

insert into
	M_Volunteers
values
	(20000, 'cmd-f', 2023);

insert into
	M_Volunteers
values
	(30000, 'cmd-f', 2023);

insert into
	M_Volunteers
values
	(10000, 'cmd-f', 2022);

insert into
	M_Volunteers
values
	(20000, 'cmd-f', 2022);

insert into
	M_Volunteers
values
	(30000, 'cmd-f', 2022);

insert into
	M_Volunteers
values
	(40000, 'cmd-f', 2021);

insert into
	M_Volunteers
values
	(50000, 'cmd-f', 2021);

insert into
	M_Volunteers
values
	(60000, 'cmd-f', 2021);

insert into
	M_Volunteers
values
	(70000, 'cmd-f', 2020);

insert into
	M_Volunteers
values
	(80000, 'cmd-f', 2020);

insert into
	M_Volunteers
values
	(90000, 'cmd-f', 2020);

insert into
	M_Volunteers
values
	(70000, 'nwHacks', 2020);

insert into
	M_Volunteers
values
	(70000, 'nwHacks', 2019);

insert into
	M_Volunteers
values
	(80000, 'nwHacks', 2020);

insert into
	M_Volunteers
values
	(80000, 'nwHacks', 2019);

insert into
	M_Volunteers
values
	(90000, 'nwHacks', 2020);

insert into
	M_Volunteers
values
	(90000, 'nwHacks', 2019);

/*Sponsors Inserts*/
insert into
	Sponsors
values
	('MLH', 'nwHacks', 2023);

insert into
	Sponsors
values
	('MLH', 'nwHacks', 2020);

insert into
	Sponsors
values
	('MLH', 'nwHacks', 2019);

insert into
	Sponsors
values
	('MLH', 'cmd-f', 2023);

insert into
	Sponsors
values
	('MLH', 'cmd-f', 2022);

insert into
	Sponsors
values
	('MLH', 'cmd-f', 2021);

insert into
	Sponsors
values
	('MLH', 'cmd-f', 2020);

insert into
	Sponsors
values
	('SAP', 'cmd-f', 2023);

insert into
	Sponsors
values
	('SAP', 'cmd-f', 2022);

insert into
	Sponsors
values
	('SAP', 'cmd-f', 2021);

insert into
	Sponsors
values
	('SAP', 'cmd-f', 2020);

insert into
	Sponsors
values
	('SAP', 'nwHacks', 2023);

insert into
	Sponsors
values
	('SAP', 'nwHacks', 2020);

insert into
	Sponsors
values
	('SAP', 'nwHacks', 2019);

insert into
	Sponsors
values
	('livepeer', 'nwHacks', 2023);

insert into
	Sponsors
values
	('MLH', 'nwHacks', 2021);

insert into
	Sponsors
values
	('Red Bull', 'StormHacks', 2023);

insert into
	Sponsors
values
	('Red Bull', 'nwHacks', 2023);

insert into
	Sponsors
values
	('Red Bull', 'nwHacks', 2021);

insert into
	Sponsors
values
	('Red Bull', 'nwHacks', 2020);

insert into
	Sponsors
values
	('MLH', 'BC Hacks 2.0', 2021);

insert into
	Sponsors
values
	('MLH', 'StormHacks', 2023);

insert into
	Sponsors
values
	('MLH', 'Hack the North', 2022);

insert into
	Sponsors
values
	('Deloitte', 'Hack the North', 2022);

/*Participates Inserts*/
insert into
	Participates
values
	(000, 'nwHacks', 2023);

insert into
	Participates
values
	(000, 'nwHacks', 2021);

insert into
	Participates
values
	(000, 'StormHacks', 2023);

insert into
	Participates
values
	(000, 'Hack the North', 2022);

insert into
	Participates
values
	(001, 'nwHacks', 2023);

insert into
	Participates
values
	(001, 'nwHacks', 2021);

insert into
	Participates
values
	(001, 'cmd-f', 2023);

insert into
	Participates
values
	(002, 'nwHacks', 2023);

insert into
	Participates
values
	(002, 'nwHacks', 2021);

insert into
	Participates
values
	(002, 'cmd-f', 2023);

insert into
	Participates
values
	(003, 'StormHacks', 2023);

insert into
	Participates
values
	(003, 'cmd-f', 2023);

insert into
	Participates
values
	(003, 'BC Hacks 2.0', 2021);

insert into
	Participates
values
	(004, 'StormHacks', 2023);

insert into
	Participates
values
	(004, 'Hack the North', 2022);

insert into
	Participates
values
	(004, 'BC Hacks 2.0', 2021);

insert into
	Participates
values
	(005, 'Hack the North', 2022);

insert into
	Participates
values
	(005, 'BC Hacks 2.0', 2021);


insert into Participates values
	(
		011, 'nwHacks', 2023
	);

insert into Participates values 
	(
		011, 'cmd-f', 2023
	);

insert into Participates values 
	(
		011, 'cmd-f', 2022
	);

insert into Participates values 
	(
		011, 'cmd-f', 2021
	);

insert into Participates values 
	(
		011, 'cmd-f', 2020
	);

insert into Participates values 
	(
		011, 'StormHacks', 2023
	);

insert into Participates values
	(
		011, 'nwHacks', 2021
	);

insert into Participates values 
	(
		011, 'nwHacks', 2020
	);

insert into Participates values 
	(
		011, 'nwHacks', 2019
	);

insert into Participates values 
	(
		011, 'BC Hacks 2.0', 2021
	);

insert into Participates values 
	(
		011, 'Hack the North', 2022
	);

insert into Participates values 
	(
		012, 'nwHacks', 2023
	);

insert into Participates values 
	(
		012, 'nwHacks', 2021
	);

insert into Participates values 
	(
		012, 'nwHacks', 2020
	);

insert into Participates values 
	(
		012, 'nwHacks', 2019
	);	

insert into Participates values 
	(
		013, 'cmd-f', 2023
	);

insert into Participates values 
	(
		013, 'cmd-f', 2022
	);

insert into Participates values 
	(
		013, 'cmd-f', 2021
	);

insert into Participates values 
	(
		013, 'cmd-f', 2020
	);
/*team_submits_project inserts*/
insert into
	Team_Submits_project
values
	(
		100,
		'nwHacks',
		2023,
		(TO_TIMESTAMP('2023-01-22 12:00:00', 'YYYY-MM-DD HH24:MI:SS')),
		'Maya Mental Health Assistant'
	);

insert into
	Team_Submits_project
values
	(
		106,
		'nwHacks',
		2023,
		(TO_TIMESTAMP('2023-01-22 11:59:00', 'YYYY-MM-DD HH24:MI:SS')),
		'project1'
	);

insert into
	Team_Submits_project
values
	(
		107,
		'nwHacks',
		2021,
		(TO_TIMESTAMP('2021-01-12 11:59:00', 'YYYY-MM-DD HH24:MI:SS')),
		'project2'
	);

insert into
	Team_Submits_project
values
	(
		108,
		'nwHacks',
		2020,
		(TO_TIMESTAMP('2020-01-22 12:00:00', 'YYYY-MM-DD HH24:MI:SS')),
		'project3'
	);

insert into
	Team_Submits_project
values
	(
		109,
		'nwHacks',
		2019,
		(TO_TIMESTAMP('2019-01-22 12:00:00', 'YYYY-MM-DD HH24:MI:SS')),
		'project4'
	);


insert into
	Team_Submits_project
values
	(
		110,
		'cmd-f',
		2023,
		(TO_TIMESTAMP('2023-03-12 12:00:00', 'YYYY-MM-DD HH24:MI:SS')),
		'project5'
	);

insert into
	Team_Submits_project
values
	(
		111,
		'cmd-f',
		2022,
		(TO_TIMESTAMP('2022-03-12 12:00:00', 'YYYY-MM-DD HH24:MI:SS')),
		'project6'
	);


insert into
	Team_Submits_project
values
	(
		112,
		'cmd-f',
		2021,
		(TO_TIMESTAMP('2021-03-12 12:00:00', 'YYYY-MM-DD HH24:MI:SS')),
		'project7'
	);

insert into
	Team_Submits_project
values
	(
		113,
		'cmd-f',
		2020,
		(TO_TIMESTAMP('2020-03-12 12:00:00', 'YYYY-MM-DD HH24:MI:SS')),
		'project8'
	);

insert into
	Team_Submits_project
values
	(
		101,
		'nwHacks',
		2021,
		(TO_TIMESTAMP('2021-01-11 12:00:00', 'YYYY-MM-DD HH24:MI:SS')),
		'Yudo'
	);

insert into
	Team_Submits_project
values
	(102, 'cmd-f', 2023, (TO_TIMESTAMP('2023-03-12 12:00:00', 'YYYY-MM-DD HH24:MI:SS')), 'test');

insert into
	Team_Submits_project
values
	(
		103,
		'StormHacks',
		2023,
		(TO_TIMESTAMP('2023-05-21 13:00:00', 'YYYY-MM-DD HH24:MI:SS')),
		'kizuna'
	);

insert into
	Team_Submits_project
values
	(
		104,
		'BC Hacks 2.0',
		2021,
		(TO_TIMESTAMP('2021-02-21 13:30:00', 'YYYY-MM-DD HH24:MI:SS')),
		'Project Panini'
	);

insert into
	Team_Submits_project
values
	(
		105,
		'Hack the North',
		2022,
		(TO_TIMESTAMP('2022-09-18 12:00:00', 'YYYY-MM-DD HH24:MI:SS')),
		'Recipeasy'
	);

/*Rank inserts*/
insert into
	Rank
values
	(
		00000, 100, 10
	);

insert into Rank values 
	(
		66666, 100, 9
	);

insert into Rank values 
	(
		77777, 100, 10	
	);

insert into Rank values 
	(
		00000, 106, 7
	);

insert into Rank values 
	(
		77777, 106, 5	
	);

insert into
	Rank
values
	(
		11111, 105, 6
	);

insert into Rank values 
	(
		22222, 105, 7	
	);

insert into Rank values 
	(
		33333, 105, 5	
	);

insert into
	Rank
values
	(
		77777, 104, 8
	);
	
insert into Rank values 
	(
		88888, 104, 4
	);

insert into Rank values 
	(
		44444, 104, 6	
	);

insert into
	Rank
values
	(
		33333, 103, 5
	);

insert into Rank values 
	(
		44444, 103, 9
	);

insert into Rank values 
	(
		66666, 103, 10	
	);

insert into
	Rank
values
	(
		44444, 101, 9
	);

insert into Rank values 
	(
		55555, 101, 8	
	);

insert into Rank values 
	(
		99999, 101, 7	
	);

insert into
	Rank
values
	(
		00000, 102, 10
	);

insert into Rank values 
	(
		11111, 102, 8	
	);

insert into Rank values 
	(
		55555, 102, 7	
	);

insert into 
	Rank
values
	(
		11111, 104, 7
	);

insert into
	Rank 
values	
	(
		22222, 103, 6
	);

insert into Rank values 
	(
		66666, 106, 4
	);

insert into Rank values 
	(
		44444, 107, 2
	);

insert into Rank values 
	(
		55555, 107, 1	
	);

insert into Rank values 
	(
		99999, 107, 10	
	);

insert into Rank values 
	(
		00000, 108, 6
	);


insert into Rank values 
	(
		88888, 108, 5	
	);

insert into Rank values 
	(
		99999, 108, 4	
	);

insert into Rank values 
	(
		99999, 109, 8
	);

insert into Rank values 
	(
		33333, 109, 9	
	);

insert into Rank values 
	(
		66666, 109, 10	
	);

insert into Rank values 
	(
		00000, 110, 5
	);

insert into Rank values 
	(
		11111, 110, 4	
	);

insert into Rank values 
	(
		55555, 110, 6	
	);

insert into Rank values 
	(
		00000, 111, 5	
	);

insert into Rank values 
	(
		88888, 111, 8	
	);

insert into Rank values 
	(
		99999, 111, 9	
	);

insert into Rank values 
	(
		11111, 111, 7	
	);


insert into Rank values 
	(
		22222, 112, 4	
	);

insert into Rank values 
	(
		33333, 112, 4	
	);

insert into Rank values 
	(
		99999, 112, 5	
	);

insert into Rank values 
	(
		00000, 113, 1
	);

insert into Rank values 
	(
		66666, 113, 2	
	);

insert into Rank values 
	(
		77777, 113, 3	
	);

/*Supports inserts*/
insert into
	Supports
values
	(10000, 001);

insert into
	Supports
values
	(20000, 001);

insert into
	Supports
values
	(30000, 003);

insert into
	Supports
values
	(40000, 004);

insert into
	Supports
values
	(50000, 005);

/*Belong_to inserts*/
insert into
	Belong_to
values
	(000, 100);

insert into
	Belong_to
values
	(001, 100);

insert into
	Belong_to
values
	(002, 100);

insert into
	Belong_to
values
	(000, 101);

insert into
	Belong_to
values
	(001, 101);

insert into
	Belong_to
values
	(002, 101);

insert into
	Belong_to
values
	(001, 102);

insert into
	Belong_to
values
	(002, 102);

insert into
	Belong_to
values
	(003, 102);

insert into
	Belong_to
values
	(000, 103);

insert into
	Belong_to
values
	(003, 103);

insert into
	Belong_to
values
	(004, 103);

insert into
	Belong_to
values
	(003, 104);

insert into
	Belong_to
values
	(004, 104);

insert into
	Belong_to
values
	(005, 104);

insert into
	Belong_to
values
	(000, 105);

insert into
	Belong_to
values
	(004, 105);

insert into
	Belong_to
values
	(005, 105);

insert into
	Belong_to
values
	(012, 106);

insert into
	Belong_to
values
	(001, 106);

insert into
	Belong_to
values
	(000, 106);

insert into
	Belong_to
values
	(012, 107);

insert into
	Belong_to
values
	(011, 107);

insert into
	Belong_to
values
	(000, 107);

insert into
	Belong_to
values
	(011, 108);

insert into
	Belong_to
values
	(012, 108);

insert into
	Belong_to
values
	(011, 109);

insert into
	Belong_to
values
	(012, 109);

insert into
	Belong_to
values
	(011, 110);

insert into
	Belong_to
values
	(013, 110);

insert into
	Belong_to
values
	(011, 111);


insert into
	Belong_to
values
	(013, 111);

insert into
	Belong_to
values
	(011, 112);

insert into
	Belong_to
values
	(013, 112);

insert into
	Belong_to
values
	(011, 113);

insert into
	Belong_to
values
	(013, 113);

/*Workshop_topic inserts*/
insert into
	Workshop_topic
values
	('Java Workshop', 'Java');

insert into
	Workshop_topic
values
	('Python Workshop', 'Python');

insert into
	Workshop_topic
values
	('OOP Workshop', 'OOP');

insert into
	Workshop_topic
values
	('SQL Workshop', 'SQL');

insert into
	Workshop_topic
values
	('SQL Workshop B', 'SQL');

/*Workshop Inserts*/

insert into
	Workshop
values
	(
		'Java Workshop',
		'Room 4',
		(TO_TIMESTAMP('2023-01-21 12:00:00', 'YYYY-MM-DD HH24:MI:SS')),
		(TO_TIMESTAMP('2023-01-21 13:30:00', 'YYYY-MM-DD HH24:MI:SS'))
	);

insert into
	Workshop
values
	(
		'Python Workshop',
		'Room 7',
        (TO_TIMESTAMP('2023-05-20 11:30:00', 'YYYY-MM-DD HH24:MI:SS')),
		(TO_TIMESTAMP('2023-05-20 12:45:00', 'YYYY-MM-DD HH24:MI:SS'))
	);

insert into
	Workshop
values
	(
		'SQL Workshop',
		'Room 6', 
		(TO_TIMESTAMP('2021-01-10 10:00:00', 'YYYY-MM-DD HH24:MI:SS')),
		(TO_TIMESTAMP('2021-01-10 11:00:00', 'YYYY-MM-DD HH24:MI:SS'))
	);

insert into
	Workshop
values
	(
		'Java Workshop',
		'Room 3',
		(TO_TIMESTAMP('2021-02-20 11:33:33', 'YYYY-MM-DD HH24:MI:SS')),
		(TO_TIMESTAMP('2021-02-20 11:45:00', 'YYYY-MM-DD HH24:MI:SS'))
	);

insert into
	Workshop
values
	(
		'OOP Workshop',
		'Room 1',
		(TO_TIMESTAMP('2021-02-20 14:30:00', 'YYYY-MM-DD HH24:MI:SS')),
		(TO_TIMESTAMP('2021-02-20 15:45:00', 'YYYY-MM-DD HH24:MI:SS'))
	);

/*Organize inserts*/
insert into
	Organize
values
	(10000, 'Java Workshop', 'Room 4');

insert into
	Organize
values
	(20000, 'SQL Workshop', 'Room 6');

insert into
	Organize
values
	(30000, 'Java Workshop', 'Room 3');

insert into
	Organize
values
	(40000, 'OOP Workshop', 'Room 1');

insert into
	Organize
values
	(50000, 'Python Workshop', 'Room 7');




/*Attend inserts*/
insert into
	Attend
values
	(001, 'Java Workshop', 'Room 4');

insert into
	Attend
values
	(002, 'Java Workshop', 'Room 3');

insert into
	Attend
values
	(003, 'Python Workshop', 'Room 7');

insert into
	Attend
values
	(004, 'OOP Workshop', 'Room 1');

insert into
	Attend
values
	(005, 'SQL Workshop', 'Room 6');

/*Provide inserts*/
insert into
	Provide 
values 
	('1st Place', 'Amazon');

insert into
	Provide 
values 
	('2nd Place', 'Microsoft');

insert into
	Provide 
values 
	('3rd Place', 'MLH');

insert into
	Provide 
values 
	('Best Beginner Project', 'MLH');

insert into
	Provide
values 
	('Most Creative', 'livepeer');

insert into Provide values 
	(
		'Red Bull Award',
		'Red Bull'
	);

insert into Provide values 
	(
		'SAP Award',
		'SAP'
	);

insert into Provide values 
	(
		'SAP Award 3',
		'SAP'
	);

insert into Provide values 
	(
		'Poke Award',
		'Steves Poke Bar'
	);
/*Prize-Win inserts*/
insert into
	Prize_Win
values
	('1st Place', 2000, 101);

insert into
	Prize_Win
values
	('2nd Place', 1000, 102);

insert into
	Prize_Win
values
	('3rd Place', 500, 103);

insert into
	Prize_Win
values
	('Best Beginner Project', 100, 105);

insert into
	Prize_Win
values
	('Most Creative', 100, 104);

insert into Prize_Win values (
	'1st Place', 2000, 106	
);

insert into Prize_Win values (
	'2nd Place', 1000, 106	
);

insert into Prize_Win values (
	'3rd Place', 250, 107
);

insert into Prize_Win values (
	'Red Bull Award', 100000, 108	
);

insert into Prize_Win values (
	'Poke Award', 20, 109
);

insert into Prize_Win values (
	'1st Place', 3000, 110	
);

insert into Prize_Win values (
	'SAP Award', 4000, 111	
);

insert into Prize_Win values (
	'SAP Award 3', 4500, 112	
);

insert into Prize_Win values (
	'Best Beginner Project', 2000, 113	
);

