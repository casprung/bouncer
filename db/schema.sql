#
# Table structure for table 'radacct'
#

CREATE TABLE radacct (
  radacctid bigint(21) NOT NULL auto_increment,
  acctsessionid varchar(64) NOT NULL default '',
  acctuniqueid varchar(32) NOT NULL default '',
  username varchar(64) NOT NULL default '',
  groupname varchar(64) NOT NULL default '',
  realm varchar(64) default '',
  nasipaddress varchar(15) NOT NULL default '',
  nasportid varchar(15) default NULL,
  nasporttype varchar(32) default NULL,
  acctstarttime datetime NULL default NULL,
  acctstoptime datetime NULL default NULL,
  acctsessiontime int(12) unsigned default NULL,
  acctauthentic varchar(32) default NULL,
  connectinfo_start varchar(50) default NULL,
  connectinfo_stop varchar(50) default NULL,
  acctinputoctets bigint(20) default NULL,
  acctoutputoctets bigint(20) default NULL,
  calledstationid varchar(50) NOT NULL default '',
  callingstationid varchar(50) NOT NULL default '',
  acctterminatecause varchar(32) NOT NULL default '',
  servicetype varchar(32) default NULL,
  framedprotocol varchar(32) default NULL,
  framedipaddress varchar(15) NOT NULL default '',
  acctstartdelay int(12) unsigned default NULL,
  acctstopdelay int(12) unsigned default NULL,
  xascendsessionsvrkey varchar(10) default NULL,
  PRIMARY KEY  (radacctid),
  UNIQUE KEY acctuniqueid (acctuniqueid),
  KEY username (username),
  KEY framedipaddress (framedipaddress),
  KEY acctsessionid (acctsessionid),
  KEY acctsessiontime (acctsessiontime),
  KEY acctstarttime (acctstarttime),
  KEY acctstoptime (acctstoptime),
  KEY nasipaddress (nasipaddress)
) ENGINE = INNODB;

#
# Table structure for table 'radcheck'
#

CREATE TABLE radcheck (
  id int(11) unsigned NOT NULL auto_increment,
  username varchar(64) NOT NULL default '',
  attribute varchar(64)  NOT NULL default '',
  op char(2) NOT NULL DEFAULT '==',
  value varchar(253) NOT NULL default '',
  PRIMARY KEY  (id),
  KEY username (username(32))
) ;

#
# Table structure for table 'radgroupcheck'
#

CREATE TABLE radgroupcheck (
  id int(11) unsigned NOT NULL auto_increment,
  groupname varchar(64) NOT NULL default '',
  attribute varchar(64)  NOT NULL default '',
  op char(2) NOT NULL DEFAULT '==',
  value varchar(253)  NOT NULL default '',
  PRIMARY KEY  (id),
  KEY groupname (groupname(32))
) ;

#
# Table structure for table 'radgroupreply'
#

CREATE TABLE radgroupreply (
  id int(11) unsigned NOT NULL auto_increment,
  groupname varchar(64) NOT NULL default '',
  attribute varchar(64)  NOT NULL default '',
  op char(2) NOT NULL DEFAULT '=',
  value varchar(253)  NOT NULL default '',
  PRIMARY KEY  (id),
  KEY groupname (groupname(32))
) ;

#
# Table structure for table 'radreply'
#

CREATE TABLE radreply (
  id int(11) unsigned NOT NULL auto_increment,
  username varchar(64) NOT NULL default '',
  attribute varchar(64) NOT NULL default '',
  op char(2) NOT NULL DEFAULT '=',
  value varchar(253) NOT NULL default '',
  PRIMARY KEY  (id),
  KEY username (username(32))
) ;


#
# Table structure for table 'radusergroup'
#

CREATE TABLE radusergroup (
  username varchar(64) NOT NULL default '',
  groupname varchar(64) NOT NULL default '',
  priority int(11) NOT NULL default '1',
  KEY username (username(32))
) ;

#
# Table structure for table 'radpostauth'
#

CREATE TABLE radpostauth (
  id int(11) NOT NULL auto_increment,
  username varchar(64) NOT NULL default '',
  pass varchar(64) NOT NULL default '',
  reply varchar(32) NOT NULL default '',
  authdate timestamp NOT NULL,
  PRIMARY KEY  (id)
) ENGINE = INNODB;

CREATE TABLE endpoints (
  id int(11) unsigned NOT NULL auto_increment,
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  username varchar(64) NOT NULL default '',
  ssid varchar(32) NOT NULL default '',
  mac varchar(12) NOT NULL default '',
  PRIMARY KEY (id),
  UNIQUE KEY mac (mac)
) ;

CREATE TABLE pending_guests (
  id int(11) unsigned NOT NULL auto_increment,
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  name varchar(255) NOT NULL default '',
  email varchar(255) NOT NULL default '',
  mobile varchar(32) default NULL,
  password varchar(32) NOT NULL default '',
  sponsor varchar(255) NOT NULL default '',
  username varchar(40) NOT NULL default '',
  hashedpwd varchar(40) NOT NULL default '',
  salt varchar(40) NOT NULL default '',
  PRIMARY KEY (id),
  UNIQUE (name),
  UNIQUE (email),
  UNIQUE (mobile)
);

CREATE TABLE active_guests (
  id int(11) unsigned NOT NULL auto_increment,
  created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  expiry timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  username varchar(40) NOT NULL default '',
  hashedpwd varchar(40) NOT NULL default '',
  salt varchar(40) NOT NULL default '',
  PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS `ci_sessions` (
        `id` varchar(128) NOT NULL,
        `ip_address` varchar(45) NOT NULL,
        `timestamp` int(10) unsigned DEFAULT 0 NOT NULL,
        `data` blob NOT NULL,
        KEY `ci_sessions_timestamp` (`timestamp`)
);

DELIMITER $$

CREATE EVENT AutoDeleteOldEntries
ON SCHEDULE EVERY 1 HOUR
STARTS DATE_FORMAT(CURRENT_TIMESTAMP + INTERVAL 1 HOUR, "%Y-%m-%d %H:00:00")
DO BEGIN
  DELETE LOW_PRIORITY endpoints, active_guests, radcheck FROM active_guests LEFT JOIN endpoints ON endpoints.username = active_guests.username LEFT JOIN radcheck ON radcheck.username = active_guests.username WHERE active_guests.expiry <= NOW();
  DELETE LOW_PRIORITY FROM pending_guests WHERE created < DATE_SUB(NOW(), INTERVAL 24 HOUR);
  DELETE LOW_PRIORITY FROM endpoints WHERE created < DATE_SUB(NOW(), INTERVAL 12 HOUR);
END $$

DELIMITER ;

