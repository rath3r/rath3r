<?php

/**
 * @name Vanilla_Model_Relationship
 * @author Kasia Gogolek <kasia.gogolek@living-group.com>
 * @package	Vanilla
 * @subpackage Model
 * 
 */

class Vanilla_Model_Relationship extends Vanilla_Model_Row
{
	/**
	 * Child entity type
	 * @var string
	 */
	public $child_entity_type;
	
	/**
	 * Name of data source table that will provide data
	 * @var string
	 */
	public $_table = 'relationships';
	
	/**
	 * Name of the Data Source Class name
	 * @var string
	 */
	public $db_class_name = "Relationship";
	
	/**
	 * 
	 * Getting All Entity Types That are available
	 * @return array
	 */
	public function getEntityTypes()
	{
		$db_data = $this->getDataSourceClassObject();
		return $db_data->getAllEntityTypes();
	}	
	
	/**
	 * (non-PHPdoc)
	 * @see Vanilla_Model_Row::create()
	 * @param $data
	 * @return Vanilla_Model_Relationship
	 */
	public function create($data)
	{
		$db_data  = $this->getDataSourceClassObject();
		$this->id = $db_data->create($data, false, true);
		return $this;
	}	

	
	public function deleteAllForChild($child_id, $child_type_id, $parent_type_id)
	{
		$db_data  = $this->getDataSourceClassObject()->deleteAllForChild($child_id, $child_type_id, $parent_type_id);
		return $this;
	}
	
	public function reorder($new_order)
	{
		$old_order = $this->order;
		
		if($new_order == $old_order)
		{
			return $this;
		}
		if($new_order > $old_order)
		{
			$this->_orderGoDown($new_order, $old_order);
		}
		else 
		{
			$this->_orderGoUp($new_order, $old_order);
		}
		$this->update(array('order' => $new_order));
	}
	
	/**
	 * Order other entities for going down
	 * @param int $new_order
	 * @param int $old_order
	 * @return void
	 */
	protected function _orderGoUp($new_order, $old_order)
	{
		$order_array['value'] = range($new_order, $old_order - 1);
		$params = array('parent_id' => $this->parent_id, 'parent_entity_type_id' => $this->parent_entity_type_id, 'child_entity_type_id' => $this->child_entity_type_id, 'order' => $order_array);
		$relationships = Vanilla_Model_Relationships::factory()->getAll(null, null, $params);
		foreach($relationships->rowset as $relationship)
		{
			$order = $relationship->order + 1;
			$relationship->update(array('order' => $order));
		}
	}

	/**
	 * Order other entities for going down
	 * @param int $new_order
	 * @param int $old_order
	 * @return void
	 */
	protected function _orderGoDown($new_order, $old_order)
	{
		$order_array['value'] = range($old_order + 1, $new_order);
		$params = array('parent_id' => $this->parent_id, 'parent_entity_type_id' => $this->parent_entity_type_id, 'child_entity_type_id' => $this->child_entity_type_id, 'order' => $order_array);
		$relationships = Vanilla_Model_Relationships::factory()->getAll(null, null, $params);
		foreach($relationships->rowset as $relationship)
		{
			$order = $relationship->order - 1;
			$relationship->update(array('order' => $order));
		}
	}
	
	
}
