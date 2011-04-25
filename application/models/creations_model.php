<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Creations_Model extends CI_Model {
	
	/**
	 * Adds a creation to the database
	 * 
	 * @param array		data
	 * 
	 * @return array('success' ? 'item' : 'error')
	 */
	function addCreation($data)
	{
		$qStr = "INSERT INTO creations (name, description, short_description, uri, github_url, layout, image, image_small)
				VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
		$q = $this->db->query($qStr, array($data['name'], $data['description'], $data['short_description'], $data['uri'], $data['github_url'], 
											$data['layout'], $data['image'], $data['image_small']));
		
		if ($q)
			return $this->getCreation($this->db->insert_id());
		else
			return array('success'=>false, 'error'=>"There was an error adding this item");
	}

	/**
	 * Saves a creation
	 * 
	 * @param array		data
	 * 
	 * @return array('success' ? null : 'error')
	 */
	function saveCreation($data)
	{
		//check if creation exists
		$creation = $this->getCreation($data['id']);
		if (!$creation['success'])
			return $creation;
		
		//prepare values
		$id = intval($data['id']);
		
		$qStr = "UPDATE creations 
					SET name=?, description=?, short_description=?, uri=?, github_url=?, layout=?, image=?, image_small=?, documentation=?,
							marked_up_documentation=?
				WHERE id=?";
		$q = $this->db->query($qStr, array($data['name'], $data['description'], $data['short_description'], $data['uri'], $data['github_url'],
											$data['layout'], $data['image'], $data['image_small'], $data['documentation'], 
											$data['marked_up_documentation'], $id));
		
		if ($q)
			return $this->getCreation($id);
		else
			return array('success'=>false, 'error'=>"There was an error saving this creation");
	}
	
	/**
	 * Deletes a creation
	 * 
	 * @param int		id
	 * 
	 * @return array('success' ? null : 'error')
	 */
	function deleteCreation($id)
	{
		$qStr = "DELETE FROM creations WHERE id=?";
		$q = $this->db->query($qStr, array($id));
		
		if ($q)
			return array('success'=>true);
		else
			return array('success'=>false, 'error'=>"There was an error deleting this creation from the database");
	}
	
	/**
	 * Gets creation by id
	 * 
	 * @param int		id
	 * 
	 * @return array('success' ? 'item'=>array of creation data : 'error')
	 */
	function getCreation($id)
	{
		$qStr = "SELECT * FROM creations
				WHERE id=?";
		$q = $this->db->query($qStr, array(intval($id)));
		
		if ($q->num_rows() > 0)
			return array('success'=>true, 'creation'=>$q->row_array());
		else
			return array('success'=>false, 'error'=>"There is no creation with that id");
	}
	
	/**
	 * Gets creation by uri
	 * 
	 * @param int		id
	 * 
	 * @return array('success' ? 'creation'=>array of creation data : 'error')
	 */
	function getCreationByUri($uri)
	{
		$qStr = "SELECT * FROM creations
				WHERE uri=?";
		$q = $this->db->query($qStr, array($uri));
		
		if ($q->num_rows() > 0)
			return array('success'=>true, 'creation'=>$q->row_array());
		else
			return array('success'=>false, 'error'=>"There is no creation with that id");
	}
	
	/**
	 * Gets creations from the database
	 * 
	 * @param int		rows
	 * @param int		page
	 * @param bool		limit_uris ==> if set to true, it only returns those creations with the URI field filled in
	 * @param string	sort ==> can be 'date'
	 * 
	 * @return array('success' ? 'items'=>array of arrays of creation data : 'error')
	 */
	function getCreations($rows = 10, $page = 0, $limit_uris = false, $sort='date')
	{
		$qStr = "SELECT c.*, GROUP_CONCAT(t.name SEPARATOR ', ') AS tools
				FROM creations c
					LEFT JOIN creations_tools ct ON c.id=ct.creation_id
					LEFT JOIN tools t ON ct.tool_id=t.id";
		
		if ($limit_uris)
			$qStr .= " WHERE c.uri!=''";
		
		$qStr .= " GROUP BY c.id";
		$qStr .= " ORDER BY c.time DESC";
		
		if (!is_null($rows)) {
			$page = intval($page);
			$page = $page <= 1 ? 0 : $page - 1;
			
			$qStr .= " LIMIT ".intval($page).", ".intval($rows);
		}
		
		$q = $this->db->query($qStr);
		
		if ($q->num_rows() > 0)
			return array('success'=>true, 'creations'=>$q->result_array());
		else
			return array('success'=>false, 'error'=>"No creations found");
	}
	
	/**
	 * Gets a creation's tools
	 * 
	 * @param int		id
	 * 
	 * @return array('success' ? 'tools'=>array of creation tools : 'error')
	 */
	function getCreationTools($id)
	{
		$qStr = "SELECT * FROM creations_tools
					LEFT JOIN tools ON creations_tools.tool_id = tools.id
				WHERE creation_id=?";
		$q = $this->db->query($qStr, array(intval($id)));
		
		if ($q->num_rows() > 0)
			return array('success'=>true, 'tools'=>$q->result_array());
		else
			return array('success'=>false, 'error'=>"This creation has no tools");
	}
	
	/**
	 * Updates the "update_time" field for each creation based on the latest github repo activity
	 * 
	 * @return array('success' ? void : 'error')
	 */
	function syncWithGitHub()
	{
		$error = '';
		
		//first get the creations
		$creations = $this->getCreations();
		
		if (!$creations['success'])
			return true;
		
		//cycle over each creation
		foreach($creations['creations'] as $c) {
			
			//if there is no github_url, continue to the next item
			if (!$c['github_url'])
				continue;
			
			//extract the uri from the github_url
			$git_uri = str_replace('https://github.com/', '', $c['github_url']);
			
			//get the json data from the github api
			$json = file_get_contents('https://github.com/api/v2/json/repos/show/'.$git_uri);
			
			//decode the json data into an associative array
			$data = json_decode($json, true);
			
			//if the proper data is there, run the setUpdateTime function in order to sync the times
			if (isset($data['repository']) && isset($data['repository']['pushed_at']))
				$this->setUpdateTime($c['id'], $data['repository']['pushed_at']);
			else {
				$error = 'Failed to grab data for '.$c['name'];
				break;
			}
		}
		
		return ( empty($error) ? array('success'=>true) : array('success'=>false, 'error'=>$error) );
	}
	
	/**
	 * Sets a particular creation's update_time given the creation id and the update time
	 * 
	 * @param int		id
	 * @param string	time
	 * 
	 * @param bool
	 */
	function setUpdateTime($id, $time)
	{
		$qStr = "UPDATE creations 
					SET update_time = ?
				WHERE id = ?";
		
		return $this->db->query($qStr, array(strval($time), intval($id)));
	}
}