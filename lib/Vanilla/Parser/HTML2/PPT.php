<?

/**
 * May need to install wkhtmltopdf
 * 
 * @see http://code.google.com/p/wkhtmltopdf/
 */

/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . LIB_FRAMEWORK_DIR . "Vanilla/ext/");

require_once "PHPPowerPoint.php";

/** PHPPowerPoint_IOFactory */
require_once 'PHPPowerPoint/IOFactory.php';
 
class Vanilla_Parser_HTML2_PPT extends Vanilla_Parser_HTML2
{
        public $xml;
        public $slideCount = 1;
        
        public $xpos = 0;
        public $ypos = 0;
        
        public $xmax = 960;
        public $ymax = 720;
        
        public $style = array();
        
        public $ppt;
        
        public $save_state;
        
        public $file_extension = "ppt";
        
        public $run_instructions = true;
        
        public function __construct() {
                
                $this->style =  array(
                        "left" => 0,
                        "top" => 0,
                        "width" => $this->ymax,
                        "height" => 0,
                        "font-size" => 8,
                        "color" => "FF000000",
                        "margin-top" => 0,
                        "margin-right" => 0,
                        "margin-bottom" => 0,
                        "margin-left" => 0,
                        "text-align" => 'left',
                        "font-weight" => 'normal'
                        
                );
                
                $this->defaultstyle = $this->style;
                
        }

        public function parse($title)
        {
            $this->setTitle2File($title);
            if(!file_exists($this->tmp_file))
            {
            if(count($this->url) > 1)
                {
                    $this->concatFiles();
                }
                else 
                {
                    $this->getPPTFromUrl(end($this->url), $this->tmp_file);
                }
            }
                $this->returnFile();
                die;
        }
        
        public function getPPTFromUrl($url, $tmp_file)
        {
            $this->xml = $this->parseUrlToXML($url);
            $this->createPPT($tmp_file);
        }
        
        
        public function getFileFromUrl($url, $target_file)
        {
            $this->getPPTFromUrl($url, $target_file);
        }
        
        public function createPPT($file)
        {
                $this->objPPT = new PHPPowerPoint();
                
                $this->objPPT->getProperties()->setCreator(HTML2_CREATOR);
                $this->objPPT->getProperties()->setLastModifiedBy(HTML2_LAST_MODIFIED_BY);
                $this->objPPT->getProperties()->setTitle(HTML2_CREATOR);
                $this->objPPT->getProperties()->setSubject(HTML2_CREATOR);
                $this->objPPT->getProperties()->setDescription(HTML2_CREATOR);
                $this->objPPT->getProperties()->setKeywords("office 2007 openxml php");
                $this->objPPT->getProperties()->setCategory(HTML2_CREATOR);
                
                $this->objPPT->removeSlideByIndex(0);
                
                $array = array();
                
                $instruction_array = $this->convertXmlObjToArr($this->xml, $array);
                
                $instruction_array = $this->loopInstructions($instruction_array);
                
                $objWriter = PHPPowerPoint_IOFactory::createWriter($this->objPPT, 'PowerPoint2007');
                $objWriter->save($file);
                
        }

        private function loopInstructions($arrObjData,$depthCount=-1,$counter=0) {
                
                $depthCount++;
                
                if (is_array($arrObjData)) {
                        
                    foreach ($arrObjData as $key => $value) {
                        
                        if($this->run_instructions == true)
                        {

                            $levelStyle = $this->style;
                            
                            // save current state of ppt object
                            $this->saveState();
                           
                            $this->startMethod($value['name'],$value,$depthCount);
                            
                            if(
                                isset($value['children'])&&
                                (count($value['children'])>0)
                            )
                            {
                                $arrObjData[$key]['children'] = $this->loopInstructions($value['children'], $depthCount, $counter);
                            }
                            
                            $this->endMethod($value['name'],$value);
                            
                            if(($this->run_instructions == true))
                            {
                                unset($arrObjData[$key]);
                            }
                            
                            $this->style = $levelStyle;
                        }
                    }
                }
                
                if(($depthCount == 0)&&(count($arrObjData)>0))
                {
                    $this->run_instructions = true;
                    $arrObjData = $this->prepareInstructions($arrObjData);
                    $arrObjData = $this->loopInstructions($arrObjData);
                }

                return $arrObjData;
        }
        
        private function startMethod($key,$value,$depthCount = 0){

            if(isset($value['attributes']['style']))
            {
                $this->getStylesArray($value['attributes']['style']);
            }
            if(isset($_GET['debug']))
            {
                echo($key." add<br/>");
            }
            $func_name = $key.'_add';
            if (method_exists($this,$func_name)) {
                $this->$func_name($value);
            }
        }
        
        private function endMethod($key,$value){

            if(isset($_GET['debug']))
            {
                echo($key." end<br/>");
            }
            
            $func_name = $key.'_end';
            if (method_exists($this,$func_name)) {
                $continue = $this->$func_name($value);
            }
        }
        
        private function prepareInstructions($arrObjData)
        {
            $arrObjData[key($arrObjData)]['children'] = 
                array_merge(
                    $this->master_slide['children'],
                    $arrObjData[key($arrObjData)]['children']
                 );
                 
            foreach($arrObjData[key($arrObjData)]['children'] as &$child)
            {
                if($child['name'] == "title")
                {
                    $child['text'] = $this->title;
                }
            }
            return $arrObjData;
        }
        
        private function div_add($value = null){
            
            switch($value['attributes']['class']){
                case 'slide':
                        $this->createTemplatedSlide();
                        $this->slideCount++;
                        break;
                case 'master_content':
                       $this->master_content_x = $this->style['left'];
                       $this->master_content_y = $this->style['top'];
                       $this->master_content_width = $this->style['width'];
                       $this->master_content_height = $this->style['height'];
                       break;
                case 'master_slide':
                       $this->master_slide = $value;
                       $this->master_slide['class'] = "slide";
                       break;
            }       
        }
 
        private function p_add($value){
            if(isset($value['text']))
            {
                $this->createText(trim($value['text']));
            }
        }
        
        private function title_add($value){
            if(isset($value['text']))
            {
                if(preg_match("/continued/i", $value['text']))
                {
                    $this->title = trim($value['text']);
                }
                else
                {
                    $this->title = trim($value['text']." continued");
                }
                $this->createText(trim($value['text']));
            }
        }
        
        private function table_add($value)
        {
            if(isset($_GET['debug']))
            {
                var_dump($this->style);
            }
            if(($this->slide !== null)&&(count($value['children'])>0))
            {
                $maxColumns = 0;
                foreach($value['children'] as $child)
                { 
                    $maxColumns = (count($child['children'])>$maxColumns)? count($child['children']):$maxColumns;
                }
                $this->table = $this->slide->createTableShape($maxColumns);
                $this->table->value = $value;
                
                $this->table->height = 0;
                
                $this->styleElement($this->table);
                
                $this->trCount = 0;
                $this->tdCount = 0;
            }
        }
        
        private function table_end($value){

        }
        

        private function tr_add($value){
            
                $this->checkTableBoundaries($value);
                
                if($this->run_instructions)
                {
                    $this->row = $this->table->createRow();
                    $this->row->setHeight(30);
                    $this->row->height = 0;
                    
                    $this->row->getFill()->setFillType(PHPPowerPoint_Style_Fill::FILL_SOLID)
                                         ->setStartColor(new PHPPowerPoint_Style_Color('FFFFFFFF'))
                                         ->setEndColor(new PHPPowerPoint_Style_Color('FFFFFFFF'));
                    $this->tdCount = 0;
                    
                    return $this->checkTableBoundaries($value);
                    
                    $this->trCount++;
                }
        }
        
        private function tr_end($value){
               
               $this->table->height += $this->row->height;
               $this->table->setHeight($this->table->height);

        }
        
        private function td_add($value){
        
                // convert value styles to normal styles
                $this->convertStyles($value['attributes']);
            
                $cell = $this->row->nextCell();
                
                $cell = $this->styleBorder($cell);
                $cell = $this->styleElement($cell);
                
                $cell->getActiveParagraph()->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_LEFT );
                
                $cell_height = 0;
                if(isset($value['text']))
                {                
                    $cell->createTextRun(trim($value['text']))
                        ->getFont()->setBold($this->getFontWeight($this->style['font-weight']))
                        ->setSize($this->style['font-size'])
                        ->setName('Arial')
                        ->setColor( new PHPPowerPoint_Style_Color( $this->style['color'] ) );
                
                    $cell_height = $this->calculateTextHeight(trim($value['text']));
                }
                        
                $this->row->height = ($cell_height > $this->row->height)? $cell_height : $this->row->height ;
                
                $this->tdCount++;
        }
        
        private function img_add($value){

            if($this->slide != null)
            {
                // Add background image
                $shape = $this->slide->createDrawingShape();
                $shape->setName($this->slideCount);
                $shape->setDescription('');
                if(file_exists($value['attributes']['src']))
                {
                    $shape->setPath((string) $value['attributes']['src']);
                }
                else 
                {
                    $shape->setPath((string) BASE_DIR . $value['attributes']['src']);
                }
                $this->styleElement($shape);
            }
                        
        }
        
        private function hr_add(){
                $shape = $this->slide->createLineShape(
                        $this->style['left'], 
                        $this->style['top'], 
                        $this->style['left']+$this->style['width'], 
                        $this->style['top']+$this->style['height']
                );
                $shape->getBorder()->getColor()->setARGB( $this->style['color']);
        }
        
        private function createText($text)
        {        
            if($this->slide !== null)
            {
                $shape = $this->slide->createRichTextShape();
                $this->styleElement($shape);
            
                $shape->getActiveParagraph()->getAlignment()->setHorizontal( constant("PHPPowerPoint_Style_Alignment::".$this->PPTAlignment($this->style['text-align'])));
                
                $textRun = $shape->createTextRun((string) trim($text));

                $this->styleText($textRun->getFont());
            }
        }
        
        private function createTemplatedSlide($title = null)
        {
            // Create slide
            $this->slide = $this->objPPT->createSlide();
                
        }
        
        private function convertGraphArray($inputArray){
            foreach($inputArray as $item){
                    $array[$item[0]] =  (float) $item[1];
                    $labelCount++;
            }
            return $array;
        }
        
        private function getStylesArray($style){
            $params = explode(';',$style);
            if(is_array($params)){
                    foreach($params as $item)
                    {
                            if (strpos($item, ':') === false)
                                    continue;
                            
                            $item = explode(':',$item);
                            $find =         array('px','#');
                            $replace =      array('','FF');
                            $this->style[$item[0]] = (string) str_replace($find,$replace,$item[1]);
                    }
            }
        }
        
        
        private function styleBorder($element){
                
            $this->convertBorderStyles();
            
            $element->getBorders()->getTop()->setLineWidth($this->style['border-top-width']);
            $element->getBorders()->getTop()->setLineStyle(PHPPowerPoint_Style_Border::LINE_THICKTHIN);
            $element->getBorders()->getTop()->setDashStyle(PHPPowerPoint_Style_Border::$this->style['border-top-style']);
            $element->getBorders()->getTop()->getColor()->setARGB($this->style['border-top-color']);
            
            $element->getBorders()->getRight()->setLineWidth($this->style['border-right-width']);
            $element->getBorders()->getRight()->setLineStyle(PHPPowerPoint_Style_Border::LINE_THICKTHIN);
            $element->getBorders()->getRight()->setDashStyle(PHPPowerPoint_Style_Border::$this->style['border-right-style']);
            $element->getBorders()->getRight()->getColor()->setARGB($this->style['border-right-color']);
            
            $element->getBorders()->getLeft()->setLineWidth($this->style['border-left-width']);
            $element->getBorders()->getLeft()->setLineStyle(PHPPowerPoint_Style_Border::LINE_THICKTHIN);
            $element->getBorders()->getLeft()->setDashStyle(PHPPowerPoint_Style_Border::DASH_SOLID);
            $element->getBorders()->getLeft()->getColor()->setARGB($this->style['border-left-color']);
            
            $element->getBorders()->getBottom()->setLineWidth($this->style['border-bottom-width']);
            $element->getBorders()->getBottom()->setLineStyle(PHPPowerPoint_Style_Border::LINE_THICKTHIN);
            $element->getBorders()->getBottom()->setDashStyle(PHPPowerPoint_Style_Border::$this->style['border-bottom-style']);
            $element->getBorders()->getBottom()->getColor()->setARGB($this->style['border-bottom-color']);
            
            return $element;
                
        }
        
        private function styleElement($element)
        {
             if(method_exists($element, 'setHeight'))
             {
                 $element->setHeight((string) $this->style['height']);
             }
             
             if(method_exists($element, 'setWidth'))
             {
                 $element->setWidth((string) $this->style['width']);
             }
             
             if(method_exists($element, 'setOffsetX'))
             {
                 $element->setOffsetX((string) $this->style['left']);
             }
             
             if(method_exists($element, 'setOffsetY'))
             {
                 $element->setOffsetY((string) $this->style['top']);
             }
             
             if(method_exists($element, 'setInsetTop'))
             {
                 $element->setInsetTop((string) $this->style['margin-left']);
             }
             
             if(method_exists($element, 'setInsetBottom'))
             {
                 $element->setInsetBottom((string) $this->style['margin-right']);
             }
             
             return $element;
        }
        
        private function styleText($element)
        {
            if(method_exists($element, 'setBold'))
            {
                $element->setBold($this->getFontWeight($this->style['font-weight']));
            }
            
            if(method_exists($element, 'setSize'))
            {
                $element->setSize($this->style['font-size']);
            }
            
            if(method_exists($element, 'setName'))
            {
                $element->setName('Arial');
            }
            
            $element->setColor( new PHPPowerPoint_Style_Color( $this->style['color'] ) );
            
            return $element;
        }
        
        private function convertStyles($value)
        {
            if(isset($value['width']))
            {
                $this->style['width'];
            }
            
            if(isset($value['height']))
            {
                $this->style['height'];
            }
            
            if(isset($value['colspan']))
            {
                $this->style['colspan'];
            }
        }
        
        private function convertBorderStyles()
        {
            
            // nb: next row top overwrites this bottom
            // next column left overwrites this right
            
            if(!isset($this->style['border-top-style']))
            {
                $this->style['border-top-style'] = "solid";
            }
            if(!isset($this->style['border-top-width']))
            {
                $this->style['border-top-width'] = 1;
            }
            if(!isset($this->style['border-top-color']))
            {
                $this->style['border-top-color'] = "FFCCCCCC";
            }

            if(!isset($this->style['border-bottom-style']))
            {
                $this->style['border-bottom-style'] = "solid";
            }
            if(!isset($this->style['border-bottom-width']))
            {
                $this->style['border-bottom-width'] = 1;
            }
            if(!isset($this->style['border-bottom-color']))
            {
                $this->style['border-bottom-color'] = "FFCCCCCC";
            }
            
            if(!isset($this->style['border-right-style']))
            {
                $this->style['border-right-style'] = "solid";
            }
            if(!isset($this->style['border-right-width']))
            {
                $this->style['border-right-width'] = 1;
            }
            if(!isset($this->style['border-right-color']))
            {
                $this->style['border-right-color'] = "FFCCCCCC";
            }
            
            if(!isset($this->style['border-left-style']))
            {
                $this->style['border-left-style'] = "solid";
            }
            if(!isset($this->style['border-left-width']))
            {
                $this->style['border-left-width'] = 1;
            }
            if(!isset($this->style['border-left-color']))
            {
                $this->style['border-left-color'] = "FFCCCCCC";
            }
        }
        
        private function createColorFromHex($hex)
        {
            $color = 'FF'.str_replace('#', '', $hex);
            return $color;
        }
        
        private function PPTAlignment($alignment){
            switch($alignment){
                    
                case 'center':
                case 'centre':
                        return 'HORIZONTAL_LEFT';
                        break;
                
                case 'right':
                        return 'HORIZONTAL_RIGHT';
                        break;
                
                case 'left':
                default:
                        return 'HORIZONTAL_LEFT';
                        break;
            }
        }
        
        private function getFontWeight($weight){
                
            switch($weight){
                    
                case 'bold':
                        return true;
                        break;
                
                case 'normal':
                default:        
                        break false;
            }
        }
        
        private function saveState(){
            
            $this->save_state = clone($this->objPPT);
        }
        
        private function loadState(){
                
            $this->objPPT = $this->save_state;
        }
        
        private function checkTableBoundaries($value)
        {
               
            $absolute_x = ($this->table->getOffsetX() + $this->table->getWidth());
            $absolute_y = ($this->table->getOffsetY() + $this->table->getHeight());
            
            // cells are going outside of the page
            if($absolute_y >= $this->ymax)
            {
                $this->run_instructions = false;
                if(isset($_GET['debug']))
                {
                    echo("Out of bounds<br/>");
                }
            }
        }
        
        private function calculateTextHeight($text)
        {
            $lines = $this->calculateNumberOfLines($text);
            $average_font_height = ($this->style['font-size']*3.5);
            $height = $lines*$average_font_height;

            return $height;
        }
        
        private function calculateNumberOfLines($text)
        {
            $width = $this->calculateTextWidth($text);
            $lines = (isset($this->style['width']))? ceil($width / $this->style['width']) : 1;
            
            return $lines;
        }
        
        private function calculateTextWidth($text)
        {
            $weight_multi = ($this->style['font-weight'] == 'bold')? 1.15 : 1;
            $average_font_width = ($this->style['font-size']*0.5);
            $character_number = strlen($text);
            
            $width = $weight_multi*($average_font_width*$character_number);
            
            return $width;
        }
        
        private function deepClone($object)
        {
            return unserialize(serialize($object));
        }
        
        private function convertXmlObjToArr($obj, &$arr) 
        { 
            $children = $obj->children(); 
            foreach ($children as $elementName => $node) 
            { 
                $nextIdx = count($arr); 
                $arr[$nextIdx] = array(); 
                $arr[$nextIdx]['name'] = strtolower((string)$elementName); 
                $arr[$nextIdx]['attributes'] = array(); 
                $attributes = $node->attributes(); 
                foreach ($attributes as $attributeName => $attributeValue) 
                { 
                    $attribName = strtolower(trim((string)$attributeName)); 
                    $attribVal = trim((string)$attributeValue); 
                    $arr[$nextIdx]['attributes'][$attribName] = $attribVal; 
                } 
                $text = (string)$node; 
                $text = trim($text); 
                if (strlen($text) > 0) 
                { 
                    $arr[$nextIdx]['text'] = $text; 
                } 
                $arr[$nextIdx]['children'] = array(); 
                $this->convertXmlObjToArr($node, $arr[$nextIdx]['children']); 
            } 
            return $arr; 
        }  
}

?>