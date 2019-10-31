==============================================================================
=  xDRE - Distributed Requirements Engineering                               =
=                                                                            =
=  2001,2002 Krzysztof Kowalczykiewicz, Poznan University of Technology      =
=            http://ophelia.cs.put.poznan.pl/xdre                            =
==============================================================================


 SUMMARY
------------------------------------------------------------------------------
xDRE is a web based requirements management system. It is the front-end that
makes use of either Orpheus Requirements Server through XMLRPC connectivity,
or MySQL database through native PHP support.

Software is offered for free, open-source.

The project has been developed as a part of Ophelia project effort.
For more details visit Ophelia web page:
  http://www.opheliadev.org


 REQUIREMENTS
------------------------------------------------------------------------------
Server:
  - PHP version >= 4.0.5 with the following extensions:
    - XSLT (Sablotron >= 0.95)
 	- DOMXML (libxml >= 2.4.x)
 	- XML (Expat >= 1.95.1)
	- XMLRPC or MySQL depending on datasource used  
  
Web browser:
  - Internet Explorer version 5.0 or above
  - Mozilla version 1.0 or above


 INSTALLATION
------------------------------------------------------------------------------
1. Extract the files into target directory.
2. If using MySQL connection:
  a) Setup a database for xDRE requirements. Use mysql or mysqladmin binary 
     to create database and user account to access it.
  b) Import scheme dump into the database. The dump is provided in xdre.sql
     file. Redirect file to mysql binary input to do so.
3. Make cache subdirectory writable for web scripts (web server user account).
4. Copy config.php.dist into config.php
5. Perform configuration steps below.

 CONFIGURATION
------------------------------------------------------------------------------
config.php file contains several configuration variables:

// application version
define("VERSION", "0.3");

// host to connect to RM XMLRPC service
define("RM_XMLRPC_HOST", "localhost");

// port to connect to RM XMLRPC service
define("RM_XMLRPC_PORT", "8989");

// temporary directory to save uncommited data
define("TEMP_DIR", "/tmp");

// mysql database connection settings
define("MYSQL_HOST", "localhost");
define("MYSQL_USER", "username");
define("MYSQL_PASSWORD", "PassworD");
define("MYSQL_DATABASE", "xdre");

// datasource type: mysql or xmlrpc
define("DATASOURCE", "mysql");

 BUGS
------------------------------------------------------------------------------
Please send all bug reports and feature requests directly to author:

  krzysztof.kowalczykiewicz@cs.put.poznan.pl

or use this web bug-tracking system:

  http://ophelia.cs.put.poznan.pl/bug


 USER DOCUMENTATION
------------------------------------------------------------------------------
User documentation is not yet available. It will be provided with the next
release.
