<?php
/**
 * Vanilla Navigation Node
 * Node of Vanilla Navigation
 * 
 * @package    Vanilla
 * @subpackage Navigation
 * @author     Kasia Gogolek <kasia.gogolek@living-group.com>
 * @name       Vanilla Navigation
 */

class Vanilla_Navigation_Node {
	
	public $id;
	public $title;
	public $url;
	public $parent_id;
	public $order;
	public $pages_access_type_id;
	
	public $selected = false;
	
	/**
	 * Construct new node
	 * @param int $id
	 * @param string $title
	 * @param string $url
	 * @param int $parent_id
	 * @param int $order
	 * @param int $pages_access_type_id
	 */
	
	public function __construct($id, $title, $url, $full_url, $parent_id, $order = 0, $subtitle = null)
	{
		$this->id                   = $id;
		$this->title                = $title;
		$this->parent_id            = $parent_id;
		$this->full_url             = $full_url;
		$this->order                = (int) $order;
//		$this->pages_access_type_id = (int) $pages_access_type_id;
        $this->subtitle             = $subtitle;
		$this->matchUrl($url);
		return $this;
	}
	
	/**
	 * Checking if the Url Matches. If it does, add
	 * selected = true to the class variables
	 * @param string $url
	 */
	
	public function matchUrl($url)
	{
		if($url =="/" && $this->full_url == "/")
		{
			$this->selected = true;
			return;
		}
		if(null !== $url)
		{
			$parts     = explode("/",$url);
			$parts     = array_filter($parts);
			$build_url = "";
			foreach($parts as $key => $part)
			{
				$build_url .= "/".$part;
				if(preg_match("/^".str_replace("/","\/",$build_url) ."$/", $this->full_url))
				{
					$this->selected = true;
					return;
				}
			}
		}
	}
	
	/**
	 * Turn object into array of variables
	 * @return array
	 */
	
	public function toArray()
	{
		$data = get_object_vars($this);
		unset($data['children']);
		if(count($this->children) > 0)
		{
			foreach($this->children as $child)
			{
				$data['children'][] = $child->toArray();
			}
		}
		return $data;
	}
}