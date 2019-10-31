<?php
#############################################################################
## datasource.php - xDRE data source abstraction layer                     ##
## author: Krzysztof Kowalczykiewicz                                       ##
#############################################################################

require_once("config.php");
require_once("lib/error.php");
require_once("lib/session.php");

define("EMPTY_XML", "<empty/>");

define("REQ_NONE", 0);
define("REQ_HEADER", 1);
define("REQ_FULL", 2);

// Class that implements MySQL connectivity
class DataSource
{
	var $opened;
	var $loggedin;
	var $link;

	// MYSQL initializatin
	function DataSource()
	{
		$this->link = @mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD);
		@mysql_select_db(MYSQL_DATABASE, $this->link);
		$this->opened = false;
		if (!mysql_errno())
			$this->opened = trye;
		else
			show_error("DataSource: Cannot establish MySQL connection");
		$this->loggedin = get_user_id() && get_project_id();
	}

	function serializeFolder($row, $empty=true)
	{
		return "<folder id='$row[folder_id]' name='$row[folder_name]' prefix='$row[folder_prefix]'".($empty?"/>":">");
	}

	function serializeRequirement($row)
	{
		$req = "<requirement id='$row[req_id_root]' identifier='$row[req_identifier]' name='$row[req_name]' priority='$row[req_priority]' status='$row[req_status]'>";
			$req .= $this->serializeRevision($row);
			if ($row["req_description"]) $req .= "<description>$row[req_description]</description>";
			if ($row["req_rationale"]) $req .= "<rationale>$row[req_rationale]</rationale>";
			if ($row["req_source"]) $req .= "<source>$row[req_source]</source>";
			if ($row["req_viewpoint"]) $req .= "<viewpoint>$row[req_viewpoint]</viewpoint>";
			$req .= "<estimates importance='$row[req_estimate_importance]' cost='$row[req_estimate_cost]' stability='$row[req_estimate_stability]' risk='$row[req_estimate_risk]' verifiability='$row[req_estimate_verifiability]'>";
				$result = $this->query("SELECT * FROM estimates WHERE estimate_req_id=" . $row["req_id"]);
				if ($result)
					while($row2 = mysql_fetch_array($result))
						$req .= "<estimate name='$row2[estimate_name]' value='$row2[estimate_value]'/>";
			$req .= "</estimates>";
			$req .= "<definition>";
				if ($row["req_definition_input"]) $req .= "<input>$row[req_definition_input]</input>";
				if ($row["req_definition_condition"]) $req .= "<condition>$row[req_definition_condition]</condition>";
				if ($row["req_definition_processing"]) $req .= "<processing>$row[req_definition_processing]</processing>";
				if ($row["req_definition_output"]) $req .= "<output>$row[req_definition_output]</output>";
				$req .= "<samples>";
					$result = $this->query("SELECT * FROM samples WHERE sample_req_id=" . $row["req_id"]);
					if ($result)
						while ($row2 = mysql_fetch_array($result))
							$req .= "<sample name='$row2[sample_name]'>$row2[sample_content]</sample>";
				$req .= "</samples>";
			$req .= "</definition>";
			$req .= "<keywords>";
				$result = $this->query("SELECT * FROM keywords WHERE keyword_req_id=" . $row["req_id"]);
				if ($result)
					while ($row2 = mysql_fetch_array($result))
						$req .= "<keyword>$row2[keyword_content]</keyword>";
			$req .= "</keywords>";
			$req .= "<scenarios>";
				$result = $this->query("SELECT * FROM scenarios WHERE scenario_req_id=" . $row["req_id"]);
				if ($result)
					while ($row2 = mysql_fetch_array($result))
						$req .= "<scenario name='$row2[scenario_name]'>$row2[scenario_content]</scenario>";
			$req .= "</scenarios>";
			$req .= "<test-cases>";
				$result = $this->query("SELECT * FROM testcases WHERE case_req_id=" . $row["req_id"]);
				if ($result)
					while ($row2 = mysql_fetch_array($result))
						$req .= "<test-case name='$row2[case_name]'>$row2[case_content]</test-case>";
			$req .= "</test-cases>";
			$req .= "<custom-attributes>";
				$result = $this->query("SELECT * FROM attributes WHERE attr_req_id=" . $row["req_id"]);
				if ($result)
					while ($row2 = mysql_fetch_array($result))
						$req .= "<custom-attribute name='$row2[attr_name]' value='$row2[attr_value]'/>";
			$req .= "</custom-attributes>";
		$req .= "</requirement>";
		return $req;
	}

	function serializeReqHeader($row)
	{
		return "<requirement id='$row[req_id]' identifier='$row[req_identifier]' name='$row[req_name]' version='$row[req_revision_version]' date='$row[req_revision_date]'/>";
	}

	function serializeRevision($row)
	{
		return "<revision id='$row[req_id]' version='$row[req_revision_version]' label='$row[req_revision_label]' date='$row[req_revision_date]'><author id='$row[req_revision_author_id]'>$row[req_revision_author]</author><comment>$row[req_revision_comment]</comment></revision>";
	}
	
	function serializeReportHeader($row)
	{
		return "<report id='$row[report_id]' name='$row[report_name]' date='$row[report_date]'/>";
	}

	function serializeReport($row)
	{
		$xml = "<report id='$row[report_id]' name='$row[report_name]' date='$row[report_date]'>";
			$xml .= "<folder id='$row[report_filter_folder_id]' recursive='".($row[report_filter_recursive]?"on":"")."'/>";
			if ($row["report_filter_priority"])
				$xml .= "<priority value='$row[report_filter_priority]'/>";
			if ($row["report_filter_status"])
				$xml .= "<status value='$row[report_filter_status]'/>";
			if ($row["report_filter_keywords"])
				$xml .= "<keywords>$row[report_filter_keywords]</keywords>";
			if ($row["report_filter_text"])
				$xml .= "<text>$row[report_filter_text]</text>";
			if ($row["report_filter_versions"])
				$xml .= "<versions scope='$row[report_filter_versions]'/>";
		$xml .= "</report>";
		return $xml;
	}

	function deserializeReport($report_id, $xml)
	{
		$doc = xmldoc($xml);
		$root = $doc->document_element();

		$row["report_name"] = $root->get_attribute("name");
		$row["report_date"] = date("Y-m-d H:i:s");

		$node = get_element($root, "folder");
		$row["report_filter_folder_id"] = $node->get_attribute("id");
		$row["report_filter_recursive"] = $node->get_attribute("recursive") ? "1" : "0";

		$node = get_element($root, "priority");
		if ($node)
			$row["report_filter_priority"] = $node->get_attribute("value");
		else
			$row["report_filter_priority"] = "";

		$node = get_element($root, "status");
		if ($node)
			$row["report_filter_status"] = $node->get_attribute("value");
		else
			$row["report_filter_status"] = "";

		$node = get_element($root, "keywords");
		if ($node)
			$row["report_filter_keywords"] = get_content($node);
		else
			$row["report_filter_keywords"] = "";

		$node = get_element($root, "text");
		if ($node)
			$row["report_filter_text"] = get_content($node);
		else
			$row["report_filter_text"] = "";

		$node = get_element($root, "versions");
		if ($node)
			$row["report_filter_versions"] = $node->get_attribute("scope");
		else
			$row["report_filter_versions"] = "";

		$row["report_user_id"] = get_session_id();
		$row["report_project_id"] = get_project_id();

		$assignments = $this->buildAssignments($row);

		if ($report_id)
		{
			$sql = "UPDATE reports SET $assignments WHERE report_id=$report_id";
			//echo $sql;
			$this->query($sql);
		}
		else
		{
			$sql = "INSERT INTO reports SET $assignments";
			//echo $sql;
			$this->query($sql);
			$report_id = mysql_insert_id();
		}

		return $report_id;
	}

	function buildInsertSql($table, $vars)
	{
		$sql = "";
		foreach ($vars as $var=>$val)
			$sql .= ($sql?",":"") . $var . "='" . addslashes($val) . "'";
		return "INSERT INTO $table SET $sql";
	}

	function buildAssignments($vars)
	{
		$sql = "";
		foreach ($vars as $var=>$val)
			$sql .= ($sql?",":"") . $var . "='" . addslashes($val) . "'";
		return $sql;
	}

	function deserializeRequirement($req, $path, $xml, $reqroot="")
	{
		if ($req)
		{
			$result = $this->query("SELECT req_id_root FROM requirements WHERE req_id='$req'");
			if ($result)
			{
				list($req_root) = mysql_fetch_row($result);
				mysql_free_result($result);
			}
		}

		$doc = xmldoc($xml);
		$root = $doc->document_element();

		$row["req_identifier"] = $root->get_attribute("identifier");
		$row["req_name"] = $root->get_attribute("name");
		$row["req_priority"] = $root->get_attribute("priority");
		$row["req_status"] = $root->get_attribute("status");
		$row["req_description"] = get_content(get_element($root, "description"));
		$revision = get_element($root, "revision");
		if ($revision)
		{
			$row["req_revision_date"] = $revision->get_attribute("date");
			$author = get_element($revision, "author");
			if ($author)
			{
				$row["req_revision_author"] = get_content($author);
				$row["req_revision_author_id"] = $author->get_attribute("id");
			}
			$row["req_revision_label"] = $revision->get_attribute("label");
			$row["req_revision_comment"] = get_content(get_element($revision, "comment"));
			// version?
		}

		if ($reqroot)
		{
			$row["req_revision_version"] = $this->dlookup("max(req_revision_version)", "requirements", "req_id_root='$reqroot'");
			if (!$row["req_revision_version"])
				$row["req_revision_version"] = "1";
			else
				$row["req_revision_version"]++;
		}
		else
		{
			$row["req_revision_version"] = "1";
		}

		$row["req_rationale"] = get_content(get_element($root, "rationale"));
		$row["req_source"] = get_content(get_element($root, "source"));
		$row["req_viewpoint"] = get_content(get_element($root, "viewpoint"));
		$estimates = get_element($root, "estimates");
		if ($estimates)
		{
			$row["req_estimate_importance"] = $estimates->get_attribute("importance");
			$row["req_estimate_cost"] = $estimates->get_attribute("cost");
			$row["req_estimate_stability"] = $estimates->get_attribute("stability");
			$row["req_estimate_risk"] = $estimates->get_attribute("risk");
			$row["req_estimate_verifiability"] = $estimates->get_attribute("verifiability");
		}
		$definition = get_element($root, "definition");
		if ($definition)
		{
			$row["req_definition_input"] = get_content(get_element($definition, "input"));
			$row["req_definition_condition"] = get_content(get_element($definition, "condition"));
			$row["req_definition_processing"] = get_content(get_element($definition, "processing"));
			$row["req_definition_output"] = get_content(get_element($definition, "output"));
		}
		$row["req_folder_id"] = $path;
		$row["req_project_id"] = get_project_id();

		$sql = $this->buildInsertSql("requirements", $row);
		$this->query($sql);
		//echo "<li>$sql<br>";
		$req_id = mysql_insert_id($this->link);
		//$req_id = 0;

		// setting newly added required to root
		$sql = "UPDATE requirements SET req_id_root='$req_id' WHERE req_id=$req_id"; 
		$this->query($sql);
		//echo "<li>$sql<br>";

		if ($reqroot)
		{
			$sql = "UPDATE requirements SET req_id_root='$req_id' WHERE req_id_root=$reqroot"; 
			$this->query($sql);
			//echo "<li>$sql<br>";
		}

		$keywords = get_element($root, "keywords");
		if ($keywords)
		{
			$keywords = $keywords->get_elements_by_tagname("keyword");
			foreach($keywords as $keyword)
			{
				$sql = "INSERT INTO keywords SET keyword_req_id=$req_id,keyword_content='".addslashes(get_content($keyword))."'";
				$this->query($sql);
				//echo "<li>$sql<br>";
			}
		}

		if ($definition)
		{
			$samples = get_element($definition, "samples");
			if ($samples)
			{
				$samples = $samples->get_elements_by_tagname("sample");
				foreach($samples as $sample)
				{
					$sql = "INSERT INTO samples SET sample_req_id=$req_id,sample_name='".addslashes($sample->get_attribute("name"))."',sample_content='".addslashes(get_content($sample))."'";
					$this->query($sql);
					//echo "<li>$sql<br>";
				}
			}
		}

		$scenarios = get_element($root, "scenarios");
		if ($scenarios)
		{
			$scenarios = $scenarios->get_elements_by_tagname("scenario");
			foreach($scenarios as $scenario)
			{
				$sql = "INSERT INTO scenarios SET scenario_req_id=$req_id,scenario_name='".addslashes($scenario->get_attribute("name"))."',scenario_content='".addslashes(get_content($scenario))."'";
				$this->query($sql);
				//echo "<li>$sql<br>";
			}
		}

		if ($estimates)
		{
			$estimates = $estimates->get_elements_by_tagname("estimate");
			foreach($estimates as $estimate)
			{
				$sql = "INSERT INTO estimates SET estimate_req_id=$req_id,estimate_name='".addslashes($estimate->get_attribute("name"))."',estimate_value='".addslashes($estimate->get_attribute("value"))."'";
				$this->query($sql);
				//echo "<li>$sql<br>";
			}
		}

		$cases = get_element($root, "test-cases");
		if ($cases)
		{
			$cases = $cases->get_elements_by_tagname("test-case");
			foreach($cases as $case)
			{
				$sql = "INSERT INTO testcases SET case_req_id=$req_id,case_name='".addslashes($case->get_attribute("name"))."',case_content='".addslashes(get_content($case))."'";
				$this->query($sql);
				//echo "<li>$sql<br>";
			}
		}

		$attributes = get_element($root, "custom-attributes");
		if ($attributes)
		{
			$attributes = $attributes->get_elements_by_tagname("custom-attribute");
			foreach($attributes as $attribute)
			{
				$sql = "INSERT INTO attributes SET attr_req_id=$req_id,attr_name='".addslashes($attribute->get_attribute("name"))."',attr_value='".addslashes($attribute->get_attribute("value"))."'";
				$this->query($sql);
				//echo "<li>$sql<br>";
			}
		}

		return $req_id;
	}

	function query($sql)
	{
		$result = mysql_query($sql, $this->link);
		if (mysql_errno())
		{
			show_error($sql."<br>".mysql_error());
			return false;
		}
		else
			return $result;
	}

	function dlookup($field, $table, $where="")
	{
		$value = "";
		$result = mysql_query("SELECT $field FROM $table".($where?" WHERE $where":""), $this->link);
		if ($result)
		{
			list($value) = mysql_fetch_row($result);
			mysql_free_result($result);
		}
		return $value;
	}

	function get_folder($folder, $cache=true)
	{
		if (!$this->opened || !$this->loggedin) return EMPTY_XML;
		$xml = EMPTY_XML;
		$result = $this->query("SELECT * FROM folders WHERE folder_id='$folder' AND folder_project_id=".get_project_id());
		if ($result)
		{
			$row = mysql_fetch_array($result);
			mysql_free_result($result);
			$xml = $this->serializeFolder($row);
		}
		return $xml;
	}

	function dump_folder($folder, $cache=true)
	{
		if (!$this->opened || !$this->loggedin) return EMPTY_XML;
		$xml = EMPTY_XML;
		$result = $this->query("SELECT * FROM folders WHERE folder_id = '$folder' AND folder_project_id=".get_project_id());
		if ($result)
		{
			$row = mysql_fetch_array($result);
			$xml = $this->serializeFolder($row, false);
			$xml .= $this->_list_subfolders($row["folder_id"], REQ_FULL);
			$xml .= "</folder>";
		}

		return $xml;
	}

	function list_folders($folder, $cache=true)
	{
		if (!$this->opened || !$this->loggedin) return EMPTY_XML;
		$xml = EMPTY_XML;
		$result = $this->query("SELECT * FROM folders WHERE folder_id='$folder' AND folder_project_id=".get_project_id());
		if ($result)
		{
			$row = mysql_fetch_array($result);
			mysql_free_result($result);
			$xml = $this->serializeFolder($row, false);
			$result = $this->query("SELECT * FROM folders WHERE folder_id_parent='$folder'");
			if ($result)
			{
				while ($row = mysql_fetch_array($result))
					$xml .= $this->serializeFolder($row);
				mysql_free_result($result);
			}
			$xml .= "</folder>";
		}
		return $xml;
	}

	function list_all_folders($cache=true)
	{
		if (!$this->opened || !$this->loggedin) return EMPTY_XML;
		$xml = EMPTY_XML;
		$result = $this->query("SELECT * FROM folders WHERE folder_id_parent IS NULL AND folder_project_id=".get_project_id());
		if ($result)
		{
			$row = mysql_fetch_array($result);
			$xml = $this->serializeFolder($row, false);
			$xml .= $this->_list_subfolders($row["folder_id"]);
			$xml .= "</folder>";
		}
		return $xml;
	}
	
	function _list_subfolders($folder, $requirements=REQ_NONE)
	{
		$xml = "";
		$result = $this->query("SELECT * FROM folders WHERE folder_id_parent='$folder' AND folder_project_id=".get_project_id());
		if ($result)
		{
			while ($row = mysql_fetch_array($result))
			{
				$xml .= $this->serializeFolder($row, false);
				$xml .= $this->_list_subfolders($row["folder_id"], $requirements);
				$xml .= "</folder>";
			}
			mysql_free_result($result);
		}
		if($requirements > REQ_NONE)
		{
			$result = $this->query("SELECT * FROM requirements WHERE req_folder_id='$folder' AND req_id=req_id_root AND req_project_id=".get_project_id());
			if ($result)
			{
				while ($row = mysql_fetch_array($result))
					if ($requirements == REQ_HEADER)
						$xml .= $this->serializeReqHeader($row);
					else
						$xml .= $this->serializeRequirement($row);
				mysql_free_result($result);
			}
		}
		return $xml;
	}

	function list_requirements($folder, $cache=true)
	{
		if (!$this->opened || !$this->loggedin) return EMPTY_XML;
		$xml = EMPTY_XML;
		$result = $this->query("SELECT * FROM folders WHERE folder_id='$folder'");
		if ($result)
		{
			$row = mysql_fetch_array($result);
			mysql_free_result($result);
			$xml = $this->serializeFolder($row, false);
			$result = $this->query("SELECT * FROM requirements WHERE req_folder_id='$folder' AND req_id=req_id_root");
			if ($result)
			{
				while ($row = mysql_fetch_array($result))
					$xml .= $this->serializeReqHeader($row);
				mysql_free_result($result);
			}
			$xml .= "</folder>";
		}
		return $xml;
	}

	function list_all($cache=true)
	{
		if (!$this->opened || !$this->loggedin) return EMPTY_XML;
		$xml = EMPTY_XML;
		$result = $this->query("SELECT * FROM folders WHERE folder_id_parent IS NULL AND folder_project_id=".get_project_id());
		if ($result)
		{
			$row = mysql_fetch_array($result);
			$xml = $this->serializeFolder($row, false);
			$xml .= $this->_list_subfolders($row["folder_id"], REQ_HEADER);
			$xml .= "</folder>";
		}

		return $xml;
	}

	function list_revisions($req_id, $cache=true)
	{
		if (!$this->opened || !$this->loggedin) return EMPTY_XML;
		$xml = EMPTY_XML;
		$result = $this->query("SELECT req_id_root FROM requirements WHERE req_id=$req_id AND req_project_id=".get_project_id());
		if ($result)
		{
			list($req_root) = mysql_fetch_row($result);
			mysql_free_result($result);
			$xml = "<revisions>";
			$result = $this->query("SELECT * FROM requirements WHERE req_id_root=$req_root AND req_project_id=".get_project_id());
			if ($result)
			{
				while ($row = mysql_fetch_array($result))
					$xml .= $this->serializeRevision($row);
				mysql_free_result($result);
			}
			$xml .= "</revisions>";
		}
		return $xml;
	}

	function get_requirement($req_id, $cache=true)
	{
		if (!$this->opened || !$this->loggedin) return EMPTY_XML;
		$xml = EMPTY_XML;
		$result = $this->query("SELECT * FROM requirements WHERE req_id=$req_id AND req_project_id=".get_project_id());
		if ($result)
		{
			$row = mysql_fetch_array($result);
			$xml = $this->serializeRequirement($row);
			mysql_free_result($result);
		}
		return $xml;
	}

	function edit_requirement($req_id)
	{
		if (!$this->opened || !$this->loggedin) return false;
		// TODO
		return true;
	}

	function unedit_requirement($req_id)
	{
		if (!$this->opened || !$this->loggedin) return false;
		// TODO
		return true;
	}

	function commit_requirement($req_id, $requirement)
	{
		if (!$this->opened || !$this->loggedin) return false;
		$root = $this->dlookup("req_id_root", "requirements", "req_id=$req_id");
		$path = $this->dlookup("req_folder_id", "requirements", "req_id=$req_id");
		return $this->deserializeRequirement($req_id, $path, $requirement, $root);
	}

	function create_requirement($path, $requirement)
	{
		if (!$this->opened || !$this->loggedin) return false;
		return $this->deserializeRequirement("", $path, $requirement);
	}

	function create_folder($parent, $name, $prefix)
	{
		if (!$this->opened || !$this->loggedin) return false;
		$this->query("INSERT INTO folders SET folder_id='$parent$name/', folder_project_id=".get_project_id().", folder_name='$name', folder_id_parent='$parent', folder_prefix='$prefix'");
		if (mysql_errno() == 0)
			return "$parent$name/";
		else
			return false;
	}

	function rename_folder($path, $name, $prefix)
	{
		if (!$this->opened || !$this->loggedin) return false;
		// TODO
		show_error("Folder renaming not supported yet");
		return false;
	}

	function move_requirement($req_id, $target)
	{
		if (!$this->opened || !$this->loggedin) return false;
		// TODO
		show_error("Requirement moving not supported yet");
		return false;
	}

	function delete_folder($path)
	{
		if (!$this->opened || !$this->loggedin) return false;
		// TODO
		show_error("Folder deleting not supported yet");
		return false;
	}

	function delete_requirement($req_id)
	{
		if (!$this->opened || !$this->loggedin) return false;
		// TODO
		show_error("Requirement deleting not supported yet");
		return false;
	}

	function search_criteria($criteria, $header = true)
	{
		foreach ($criteria as $cname => $cvalue)
			$criteria[$cname] = addslashes($cvalue);

		if ($criteria["priority"])
			$where = "(req_priority = '$criteria[priority]')";
		if ($criteria["status"])
			$where .= ($where?" AND ":"")."(req_status = '$criteria[status]')";
		if ($criteria["versions"] != "all")
			$where .= ($where?" AND ":"")."(req_id = req_id_root)";
		if ($criteria["keywords"])
		{
			$where .= ($where?" AND ":"");
			$keywords = split(",", $criteria["keywords"]);
			foreach ($keywords as $keyword)
				$kwhere .= ($kwhere?" OR ":"")."keyword_content LIKE '%$keyword%'";
			$where .= "($kwhere)";
		}
		if ($criteria["text"])
			$where .= ($where?" AND ":"")."(req_name LIKE '%$criteria[text]%' OR req_description LIKE '%$criteria[text]%')";

		if (!$where) $where = "1";

//		print $where;

		$xml = $this->_search_folder($criteria["folder"], $where, $header, $criteria["recursive"]);
		return $xml;
	}

	function filter_query($query)
	{
		$doc = xmldoc($query);
		$root = $doc->document_element();

		$node = get_element($root, "folder");
		$criteria["folder"] = $node->get_attribute("id");
		$criteria["recursive"] = $node->get_attribute("recursive");

		$node = get_element($root, "priority");
		if ($node) $criteria["priority"] = $node->get_attribute("value");

		$node = get_element($root, "status");
		if ($node) $criteria["status"] = $node->get_attribute("value");

		$node = get_element($root, "keywords");
		if ($node) $criteria["keywords"] = get_content($node);

		$node = get_element($root, "text");
		if ($node) $criteria["text"] = get_content($node);

		$node = get_element($root, "versions");
		if ($node) $criteria["versions"] = $node->get_attribute("scope");

		return $this->search_criteria($criteria, false);
	}

	function _search_folder($folder, $where, $header = true, $recursive = false)
	{
		$result = $this->query("SELECT * FROM folders WHERE folder_id = '$folder'");
		if ($result)
		{
			$row = mysql_fetch_array($result);
			$xml = $this->serializeFolder($row, false);

			$result = $this->query("SELECT DISTINCT req_id, requirements.* FROM requirements LEFT JOIN keywords on req_id = keyword_req_id WHERE $where AND req_folder_id = '$folder'");
			while ($row = mysql_fetch_array($result))
				if ($header)
					$xml .= $this->serializeReqHeader($row);
				else
					$xml .= $this->serializeRequirement($row);

			if ($recursive)
			{
				$result = $this->query("SELECT * FROM folders WHERE folder_id_parent = '$folder'");
				while ($row = mysql_fetch_array($result))
					$xml .= $this->_search_folder($row["folder_id"], $where, $recursive);
			}

			$xml .= "</folder>";
		}
		return $xml;
	}

	function list_reports($cache=true)
	{
		if (!$this->opened || !$this->loggedin) return EMPTY_XML;
		$xml = EMPTY_XML;
		$result = $this->query("SELECT * FROM reports WHERE report_user_id=".get_session_id()." AND report_project_id=".get_project_id());
		if ($result)
		{
			$xml = "<reports>";

			while ($row = mysql_fetch_array($result))
				$xml .= $this->serializeReportHeader($row);

			mysql_free_result($result);
			$xml .= "</reports>";
		}
		return $xml;
	}

	function get_report($report_id, $cache=true)
	{
		if (!$this->opened || !$this->loggedin) return EMPTY_XML;
		$xml = EMPTY_XML;
		$result = $this->query("SELECT * FROM reports WHERE report_id=$report_id AND report_user_id=".get_session_id()." AND report_project_id=".get_project_id());
		if ($result)
		{
			$row = mysql_fetch_array($result);
			$xml = $this->serializeReport($row);
			mysql_free_result($result);
		}
		return $xml;
	}

	function save_report($report_id, $data)
	{
		if (!$this->opened || !$this->loggedin) return false;

		return $this->deserializeReport($report_id, $data);
	}

	function list_users()
	{
		if (!$this->opened) return false;
		$result = $this->query("SELECT user_login, user_name FROM users ORDER BY user_name");
		if ($result)
		{
			unset($users_list);

			while (list($ulogin, $uname) = mysql_fetch_row($result))
				$users_list[$ulogin] = $uname;
			mysql_free_result($result);

			return $users_list;
		}
		else
			return false;
	}

	function list_projects()
	{
		if (!$this->opened) return false;
		$result = $this->query("SELECT project_id, project_name FROM projects ORDER BY project_name");
		if ($result)
		{
			while (list($pid, $pname) = mysql_fetch_row($result))
				$projects_list[$pid] = $pname;
			mysql_free_result($result);

			return $projects_list;
		}
		else
			return false;
	}

	function log_in($username, $password, $project)
	{
		if (!$this->opened) return false;
		$username = addslashes($username);
		$password = addslashes($password);
		$result = $this->query("SELECT u.user_id FROM users u INNER JOIN project_users pu ON u.user_id=pu.user_id WHERE user_login='$username' AND user_password='$password' AND (project_id='$project' or u.user_level=10)");
		if ($result)
		{
			if (list($user_id) = mysql_fetch_row($result))
			{
				$this->loggedin = true;
				return $user_id;
			}
			else
				return false;
		}
		else
			return false;
	}

	function get_user_name($login = "")
	{
		static $cache;
		if (!$this->opened || !$this->loggedin) return false;
		if ($cache) return $cache;
		if (!$login) $login = get_user_id();
		$result = $this->query("SELECT user_name FROM users WHERE user_login='".$login."'");
		if ($result)
		{
			if (list($uname) = mysql_fetch_row($result))
			{
				$cache = $uname;
				return $uname;
			}
			else
				return false;
		}
		else
			return false;
	}

	function get_project_name($id = "")
	{
		static $cache;
		if (!$this->opened || !$this->loggedin) return false;
		if ($cache) return $cache;
		$result = $this->query("SELECT project_name FROM projects WHERE project_id='".get_project_id()."'");
		if ($result)
		{
			if (list($pname) = mysql_fetch_row($result))
			{
				$cache = $uname;
				return $pname;
			}
			else
				return false;
		}
		else
			return false;
	}
}
?>