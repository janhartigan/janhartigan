<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Portfolio_Model extends CI_Model {
	
	/**
	 * Adds a portfolio item to the database
	 * 
	 * @param array		data
	 * 
	 * @return array('success' ? 'item' : 'error')
	 */
	function addItem($data)
	{
		$qStr = "INSERT INTO portfolio (name, description, marked_up_description, short_description, uri, live_url, image, image_small, time)
				VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
		$q = $this->db->query($qStr, array($data['name'], $data['description'], $data['marked_up_description'], $data['short_description'], 
											$data['uri'], $data['live_url'], $data['image'], $data['image_small'], $data['time']));
		
		if ($q)
			return $this->getCreation($this->db->insert_id());
		else
			return array('success'=>false, 'error'=>"There was an error adding this item");
	}

	/**
	 * Saves a portfolio item
	 * 
	 * @param array		data
	 * 
	 * @return array('success' ? null : 'error')
	 */
	function saveItem($data)
	{
		//check if item exists
		$item = $this->getItem($data['id']);
		if (!$item['success'])
			return $item;
		
		//prepare values
		$id = intval($data['id']);
		
		$qStr = "UPDATE portfolio
					SET name=?, description=?, marked_up_description=?, short_description=?, uri=?, live_url=?, image=?, image_small=?, time=?
				WHERE id=?";
		$q = $this->db->query($qStr, array($data['name'], $data['description'], $data['marked_up_description'], $data['short_description'], $data['uri'], 
											$data['live_url'], $data['image'], $data['image_small'], $data['time'], $id));
		
		if ($q)
			return $this->getItem($id);
		else
			return array('success'=>false, 'error'=>"There was an error saving this item");
	}
	
	/**
	 * Deletes a portfolio item
	 * 
	 * @param int		id
	 * 
	 * @return array('success' ? null : 'error')
	 */
	function deleteItem($id)
	{
		$qStr = "DELETE FROM portfolio WHERE id=?";
		$q = $this->db->query($qStr, array($id));
		
		if ($q)
			return array('success'=>true);
		else
			return array('success'=>false, 'error'=>"There was an error deleting this item from the database");
	}
	
	/**
	 * Gets portfolio item by id
	 * 
	 * @param int		id
	 * 
	 * @return array('success' ? 'item'=>array of portfolio item data : 'error')
	 */
	function getItem($id)
	{
		$qStr = "SELECT * FROM portfolio
				WHERE id=?";
		$q = $this->db->query($qStr, array(intval($id)));
		
		if ($q->num_rows() > 0)
			return array('success'=>true, 'creation'=>$q->row_array());
		else
			return array('success'=>false, 'error'=>"There is no portfolio item with that id");
	}
	
	/**
	 * Gets a portfolio item by uri
	 * 
	 * @param int		id
	 * 
	 * @return array('success' ? 'creation'=>array of portfolio item data : 'error')
	 */
	function getItemByUri($uri)
	{
		$qStr = "SELECT * FROM portfolio
				WHERE uri=?";
		$q = $this->db->query($qStr, array($uri));
		
		if ($q->num_rows() > 0)
			return array('success'=>true, 'item'=>$q->row_array());
		else
			return array('success'=>false, 'error'=>"There is no portfolio item with that id");
	}
	
	/**
	 * Gets portfolio from the database
	 * 
	 * @param int		rows
	 * @param int		page
	 * @param bool		limit_uris ==> if set to true, it only returns those items with the URI field filled in
	 * @param string	sort ==> can be 'date'
	 * 
	 * @return array('success' ? 'items'=>array of arrays of creation data : 'error')
	 */
	function getPortfolio($rows = null, $page = 0, $limit_uris = false, $sort='date')
	{
		$qStr = "SELECT p.*, GROUP_CONCAT(t.name SEPARATOR ', ') AS tools
				FROM portfolio p
					LEFT JOIN portfolio_tools pt ON c.id=pt.portfolio_id
					LEFT JOIN tools t ON pt.tool_id=t.id";
		
		if ($limit_uris)
			$qStr .= " WHERE p.uri!='' OR p.uri IS NOT NULL";
		
		$qStr .= " GROUP BY p.id";
		$qStr .= " ORDER BY p.time DESC";
		
		if (!is_null($rows)) {
			$page = intval($page);
			$page = $page <= 1 ? 0 : $page - 1;
			
			$qStr .= " LIMIT ".intval($page).", ".intval($rows);
		}
		
		$q = $this->db->query($qStr);
		
		if ($q->num_rows() > 0)
			return array('success'=>true, 'portfolio'=>$q->result_array());
		else
			return array('success'=>false, 'error'=>"No items found");
	}
	
	/**
	 * Gets a portfolio item's tools
	 * 
	 * @param int		id
	 * 
	 * @return array('success' ? 'tools'=>array of portfolio item tools : 'error')
	 */
	function getItemTools($id)
	{
		$qStr = "SELECT pt.* FROM portfolio_tools pt
					LEFT JOIN tools t ON pt.tool_id = t.id
				WHERE pt.creation_id=?";
		$q = $this->db->query($qStr, array(intval($id)));
		
		if ($q->num_rows() > 0)
			return array('success'=>true, 'tools'=>$q->result_array());
		else
			return array('success'=>false, 'error'=>"This item has no tools");
	}
}