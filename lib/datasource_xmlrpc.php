<?php
#############################################################################
## datasource.php - xDRE data source abstraction layer                     ##
## author: Krzysztof Kowalczykiewicz                                       ##
#############################################################################

require_once("config.php");
require_once("lib/xmlrpc.php");
require_once("lib/session.php");
require_once("lib/error.php");

define("EMPTY_XML", "<empty/>");

// Class that implements XMLRPC connectivity with Requirements Server
class DataSource
{
	var $opened;
	var $cookie;
	var $cache;
	var $loggedin;

	// XMLRPC connection initialization
	function DataSource()
	{
		$this->opened = false;

		$sock = fsockopen(RM_XMLRPC_HOST, RM_XMLRPC_PORT, $errno, $errstr);
		if ($sock)
		{
			fclose($sock);
			$this->opened = true;
		}
		else
			show_error("DataSource: Cannot establish XMLRPC connection ($errno:$errstr)");
		$this->loggedin = get_user_id() && get_project_id();
	}

	// RM.getFolder
	function get_folder($folder, $cache=true)
	{
		if (!$this->opened || !$this->loggedin) return EMPTY_XML;
		$xml = xmlrpc_call(RM_XMLRPC_HOST, RM_XMLRPC_PORT, "RM.getFolder", array($this->cookie,$folder));
		return $this->$this->check_error_xml($xml,"getting folder");
	}

	// RM.listFolders
	function list_folders($folder, $cache=true)
	{
		if (!$this->opened || !$this->loggedin) return EMPTY_XML;
		$xml = xmlrpc_call(RM_XMLRPC_HOST, RM_XMLRPC_PORT, "RM.listFolders", array($this->cookie,$folder));
		return $this->check_error_xml($xml,"listing folders");
	}

	// RM.listAllFolders
	function list_all_folders($cache=true)
	{
		if (!$this->opened || !$this->loggedin) return EMPTY_XML;
		$xml = xmlrpc_call(RM_XMLRPC_HOST, RM_XMLRPC_PORT, "RM.listAllFolders", array($this->cookie));
		return $this->check_error_xml($xml,"getting folders tree");
	}

	// RM.listRequirements
	function list_requirements($folder, $cache=true)
	{
		if (!$this->opened || !$this->loggedin) return EMPTY_XML;
		$xml = xmlrpc_call(RM_XMLRPC_HOST, RM_XMLRPC_PORT, "RM.listRequirements", array($this->cookie,$folder));
		return $this->check_error_xml($xml,"getting requirements list");
	}

	// RM.listAll
	function list_all($cache=true)
	{
		if (!$this->opened || !$this->loggedin) return EMPTY_XML;
		$xml = xmlrpc_call(RM_XMLRPC_HOST, RM_XMLRPC_PORT, "RM.listAll", array($this->cookie));
		return $this->check_error_xml($xml,"listing all elements");
	}

	// RM.listRevisions
	function list_revisions($req_id, $cache=true)
	{
		if (!$this->opened || !$this->loggedin) return EMPTY_XML;
		$xml = xmlrpc_call(RM_XMLRPC_HOST, RM_XMLRPC_PORT, "RM.listRevisions", array($this->cookie,$req_id));
		return $this->check_error_xml($xml,"listing revisions");
	}

	// RM.getRequirement
	function get_requirement($req_id, $cache=true)
	{
		if (!$this->opened || !$this->loggedin) return EMPTY_XML;
		$xml = xmlrpc_call(RM_XMLRPC_HOST, RM_XMLRPC_PORT, "RM.getRequirement", array($this->cookie,$req_id));
		return $this->check_error_xml($xml,"getting requirement");
	}

	// RM.editRequirement
	function edit_requirement($req_id)
	{
		if (!$this->opened || !$this->loggedin) return EMPTY_XML;
		$result = xmlrpc_call(RM_XMLRPC_HOST, RM_XMLRPC_PORT, "RM.editRequirement", array($this->cookie,$req_id));
		return $result;
	}

	// RM.uneditRequirement
	function unedit_requirement($req_id)
	{
		if (!$this->opened || !$this->loggedin) return EMPTY_XML;
		$result = xmlrpc_call(RM_XMLRPC_HOST, RM_XMLRPC_PORT, "RM.uneditRequirement", array($this->cookie,$req_id));
		return $result;
	}

	// RM.commitRequirement
	function commit_requirement($req_id, $requirement)
	{
		if (!$this->opened || !$this->loggedin) return EMPTY_XML;
		$result = xmlrpc_call(RM_XMLRPC_HOST, RM_XMLRPC_PORT, "RM.commitRequirement", array($this->cookie, $req_id, $requirement));
		return $result;
	}

	// RM.createRequirement
	function create_requirement($path, $requirement)
	{
		if (!$this->opened || !$this->loggedin) return false;
		$result = xmlrpc_call(RM_XMLRPC_HOST, RM_XMLRPC_PORT, "RM.createRequirement", array($this->cookie, $path, $requirement));
		return $result;
	}

	// RM.createFolder
	function create_folder($parent, $name, $prefix)
	{
		if (!$this->opened || !$this->loggedin) return false;
		$result = xmlrpc_call(RM_XMLRPC_HOST, RM_XMLRPC_PORT, "RM.createFolder", array($this->cookie, $parent, $name, $prefix));
		return $result;
	}

	// RM.renameFolder
	function rename_folder($path, $name, $prefix)
	{
		//$result = xmlrpc_call(RM_XMLRPC_HOST, RM_XMLRPC_PORT, "RM.renameFolder", array($this->cookie, $path, $name, $prefix));
		//return $result;
		show_error("Feature disabled");
		return false;
	}

	// RM.moveRequirement
	function move_requirement($req_id, $target)
	{
		show_error("Feature disabled");
		return false;
	}

	// RM.deleteFolder
	function delete_folder($path)
	{
		show_error("Feature disabled");
		return false;
	}

	// RM.deleteRequirement
	function delete_requirement($req_id)
	{
		show_error("Feature disabled");
		return false;
	}
	
	function check_error_xml($result, $context)
	{
		if (is_array($result))
		{
			show_error("Error $context: ".$xml[0]["faultString"]);
			return EMPTY_XML;
		}
		else
			return $result;
	}

	function check_error($result, $context)
	{
		if (is_array($result))
		{
			show_error("Error $context: ".$xml[0]["faultString"]);
			return "";
		}
		else
			return $result;
	}

	function list_users()
	{
		if (!$this->opened) return false;
		$list = xmlrpc_call(RM_XMLRPC_HOST, RM_XMLRPC_PORT, "RM.listUsers", array());
		$list = $this->check_error_xml($list, "listing users");
		foreach (explode(";", $list) as $user)
			if ($user)
			{
				$user = explode(":", $user);
				$result[$user[0]] = $user[1];
			}
		return $result;
	}

	function list_projects()
	{
		if (!$this->opened) return false;
		$list = xmlrpc_call(RM_XMLRPC_HOST, RM_XMLRPC_PORT, "RM.listProjects", array());
		$list = $this->check_error($list, "listing projects");
		foreach (explode(";", $list) as $user)
			if ($user)
			{
				$user = explode(":", $user);
				$result[$user[0]] = $user[1];
			}
		return $result;
	}

	function log_in($username, $password, $project)
	{
		if (!$this->opened) return false;
		$result = xmlrpc_call(RM_XMLRPC_HOST, RM_XMLRPC_PORT, "RM.logIn", array($username, $password, $project));
		$result = $this->check_error($result, "logging in user");
		$this->loggedin = $result;
		return $result;
	}

	function get_user_name($login = "")
	{
		if (!$this->opened || !$this->loggedin) return false;
		if ($login)
			return $login;
		else
			return get_user_id();
	}

	function get_project_name($id = "")
	{
		if (!$this->opened || !$this->loggedin) return false;
		if ($id) 
			return $id;
		else
			return get_project_id();
	}
}


?>