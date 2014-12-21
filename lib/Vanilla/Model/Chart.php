<?php
/**
 * @name Vanilla_Model_Chart
 * @author Niall St John <niall.stjohn@living-group.com>
 * @package	Vanilla
 * @subpackage Model
 * 
 */
class Vanilla_Model_Chart extends Vanilla_Model_Row
{
	protected $table = 'charts';
	
	/**
	 * Name of the corresponding Data Source Class
	 * @var string
	 */
	public $db_class_name = "Chart";
	
	public $data = array();
	
	/**
	 * List of required fields that will be checked during validation
	 * @var array
	 */
	public $required_fields = array ();

    public static $types = array (
        'Pie Chart' => 'pie',
        'Bar Chart' => 'bar',
        'Line Chart' => 'line',
        'Venn Diagram' => 'venn'
    );
    public static $themes = array (
        'Green' => 'green',
        'Blue' => 'blue',
        'Pink' => 'pink',
        'Orange' => 'orange'
    );
    public static $positions = array (
        'Left' => 'left',
        'Right' => 'right',
        'Middle' => 'middle'
    );
    
    /*
     * Sets the property defaults
     */
    public function initialise() {
		$this->id = 0;
		$this->name = '';
		$this->type = 'line';
		$this->theme = 'green';
		$this->width = 900;
		$this->height = 400;
		$this->position = 'left';
		$this->interactive = 0;
		$this->status = 0;
		
		return $this;
    }


    /*
     * Gets type
     */
    public function getType() {
		switch ($this->type) {
            case 'pie':
                return 'Pie Chart';
                break;
            case 'bar':
                return 'Bar Chart';
                break;
            case 'line':
                return 'Line Chart';
                break;
            case 'venn':
                return 'Venn Diagram';
                break;
        }
    }


    public function getJS() {

        $output = '
            var chartId = ' . $this->id . ';
            if ($("#interactiveChart_"+chartId).length) {
        ';

            $sets = $this->getSets();
            fb($sets);
            $legends = $this->getLegends();
 			fb($legends);
 			$values = $this->cleanData();
 			fb($values);
 			
            $output .= '
                var data = new google.visualization.DataTable();
            ';
            switch ($this->type) {
                case 'pie':
                    $output .= '
                        data.addRows(' . count($legends) . ');
                        data.addColumn("string", "' . $sets[0]->name . '");
                        data.addColumn("number", "Value");
                    ';
                    foreach ($legends as $key => $legend) {
                        $output .= '
                            data.setValue(' . $key . ', 0, "' . $legend->name . '");
                            data.setValue(' . $key . ', 1, ' . $this->getValue($sets[0]->id, $legend->id) . ');
                        ';
                    }
                    break;
                case 'bar':
                case 'line':
                    $output .= '
                        data.addRows(' . count($sets) . ');
                        data.addColumn("string", "");
                    ';
                    foreach ($legends as $j => $legend) {
                        $output .= '
                            data.addColumn("number", "' . $legend . '");
                        ';
                    }
                    foreach ($sets as $key => $set) {
                        $output .= '
                            data.setValue(' . $key . ', 0, "' . $set  . '");
                        ';
                    }
                    foreach ($values as $j => $row) {
                        foreach ($row as $i => $column) {
                            $output .= '
                                data.setValue(' . $j . ', ' . $i . ', ' . (($column == '')? '0': $column). ');
                            ';
                        }
                    }

                    break;
                default:
                    $googleChart = 'Pie';
            }

            $coloursStr = '[';
            foreach ($this->getColourHex() as $key=>$colour) {
                $coloursStr .= $key != 0 ? ',' : '' ;
                $coloursStr .= "'#$colour'";
            }
            $coloursStr .= ']';
            $output .= '
                    var chart = new google.visualization.' . $this->getJSType() . 'Chart(document.getElementById("interactiveChart_"+chartId));
                    chart.draw(data, {width: ' . $this->width .', height: ' . $this->height . ', title: "", legend: "bottom", colors: ' . $coloursStr . '});

                } // end if ($("#interactiveChart_"+chartId").length)
            ';

        return $output;
    }
    
    /*
     * Get hex colour
     */
    public function getColourHex() {
        switch ($this->theme) {
            case 'green':
                return array('548105','77a02f','89b144','94be4a','b9e075','d6f59f');
                break;
            case 'blue':
                return array('0d40a9','3161c2','618ade','92b1f1','c6d8fd','dce4f6');
                break;
            case 'pink':
                return array('cc1266','e33684','ed63a2','ef7baf','f497c1','f9bad6');
                break;
            case 'orange':
                return array('ff9900','fdad36','febc59','ffca7b','ffd89d','ffe7c4');
                break;
        }
    }
    
    /*
     * Get type for google charts
     */
    public function getJSType() {
        switch ($this->type) {
            case 'pie':
                return 'Pie';
                break;
            case 'bar':
                return 'Column';
                break;
            case 'line':
                return 'Line';
                break;
        }
    }
    
    public function getSets() {
    	foreach($this->data as &$row){
    		$col_data[] = $row[0];
    	}
    	return array_splice($col_data,1);
    }
    
    public function getLegends() {
    	$tempdata = $this->data;
    	return array_splice($tempdata[0],1);
    }
    
	public function cleanData() {
    	//array_shift($this->data); 
    	$tempdata = $this->data;
		$tempdata = array_splice($tempdata,1);
		foreach($tempdata as &$row){
    		unset($row[0]);
    	}
		return $tempdata;
    }
}