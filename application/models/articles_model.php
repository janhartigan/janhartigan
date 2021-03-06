<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Articles_Model extends CI_Model {
	
	/**
	 * Adds an article to the database
	 * 
	 * @param array		data ==> array( 
	 * 									[str]: title, description, image, published, date, content, marked-up-content 
	 * 								  )
	 * @return array('success' ? 'item' : 'error')
	 */
	function addItem($data)
	{
		//prepare values
		$published = $data['published'] == "yes" ? true : false;
		$split_article = explode('<div class="article_division"></div>', $data['marked_up_content']);
		$marked_up_content_short = $split_article[0];
		$title_url = $data['title_url'] ? url_title($data['title_url'], 'dash', TRUE) : url_title($data['title'], 'dash', TRUE);
		
		$date = strtotime($data['date']); 
		$date = date('Y-m-d', $date);
		
		$qStr = "INSERT INTO news (title, description, image, published, date, content, marked_up_content, marked_up_content_short, title_url)
				VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$q = $this->db->query($qStr, array($data['title'], $data['description'], $data['image'], $published, $date, $data['content'],
											$data['marked_up_content'], $marked_up_content_short, $title_url));
		
		if ($q)
			return $this->getItem($this->db->insert_id());
		else
			return array('success'=>false, 'error'=>"There was an error adding this item");
	}

	/**
	 * Saves an article
	 * 
	 * @param array		data ==> array( [int]: id [int], 
	 * 									[str]: title, description, image, published, date, content, marked-up-content
	 * 								  )
	 * @return array('success' ? null : 'error')
	 */
	function saveItem($data)
	{
		//check if item exists
		$item = $this->getItem($data['id']);
		if (!$item['success'])
			return $item;
		
		//prepare values
		$published = ( $data['published'] == "yes" );
		$split_article = explode('<div class="article_division"></div>', $data['marked_up_content']);
		$marked_up_content_short = $split_article[0];
		$title_url = $data['title_url'] ? url_title($data['title_url'], 'dash', TRUE) : url_title($data['title'], 'dash', TRUE);
		
		$date = strtotime($data['date']); 
		$date = date('Y-m-d', $date);
		
		$qStr = "UPDATE news 
					SET title=?, description=?, image=?, published=?, date=?, content=?, marked_up_content=?, marked_up_content_short=?, title_url=?
				WHERE id=?";
		$q = $this->db->query($qStr, array($data['title'], $data['description'], $data['image'], $published, $date, $data['content'], 
											$data['marked_up_content'], $marked_up_content_short, $title_url, intval($data['id'])));
		
		if ($q)
			return $this->getItem($data['id']);
		else
			return array('success'=>false, 'error'=>"There was an error saving this item");
	}
	
	/**
	 * Deletes an article
	 * 
	 * @param int		id
	 * 
	 * @return array('success' ? null : 'error')
	 */
	function deleteItem($id)
	{
		$qStr = "DELETE FROM news WHERE id=?";
		$q = $this->db->query($qStr, array($id));
		
		if ($q)
			return array('success'=>true);
		else
			return array('success'=>false, 'error'=>"There was an error deleting this item from the database");
	}
	
	/**
	 * Gets an article by id
	 * 
	 * @param int		id
	 * 
	 * @return array('success' ? 'item'=>array of item data : 'error')
	 */
	function getItem($id)
	{
		$qStr = "SELECT * FROM news
				WHERE id=?";
		$q = $this->db->query($qStr, array(intval($id)));
		
		if ($q->num_rows() > 0)
		{
			return array('success'=>true, 'item'=>$q->row_array());
		}
		else
		{
			return array('success'=>false, 'error'=>"There is no item with that id");
		}
	}
	
	/**
	 * Gets articles from the database
	 * 
	 * @return array('success' ? 'items'=>array of arrays of item data : 'error')
	 */
	function getItems($rows = null, $page = 1, $published_only=true)
	{
		$qStr = "SELECT * FROM news ".
				($published_only ? ' WHERE published=1 ': '').
				"ORDER BY date DESC";
		
		$page = $page - 1;
		
		if ($rows)
			$qStr .= " LIMIT ".intval($page).", ".intval($rows);
		
		$q = $this->db->query($qStr);
		
		if ($q->num_rows() > 0)
			return array('success'=>true, 'items'=>$q->result_array());
		else
			return array('success'=>false, 'error'=>"No news items found");
	}
	
	/**
	 * Gets an article by title url
	 * 
	 * @param string	title_url
	 * 
	 * @return array('success' ? 'item'=>array of item data : 'error')
	 */
	function getItemByTitleUrl($title_url)
	{
		$qStr = "SELECT * FROM news
				WHERE title_url=?";
		$q = $this->db->query($qStr, array($title_url));
		
		if ($q->num_rows() > 0)
		{
			return array('success'=>true, 'item'=>$q->row_array());
		}
		else
		{
			return array('success'=>false, 'error'=>"There is no item with that id");
		}
	}
}