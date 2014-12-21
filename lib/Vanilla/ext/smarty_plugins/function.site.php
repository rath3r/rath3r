<?php
function smarty_function_site($params, &$smarty)
{
    if(empty($params['item']))
    {
        return null;
    }
    $id       = $params['item'];
    $site = Vanilla_Model_Site::factory()->getById($id);
    if(isset($_GET['language']))
    {
        $languages = new Languages_Model_Languages();
        $params    = array('iso' => $_GET['language'], 'status' => Vanilla_Model_Row::STATUS_LIVE);
        $languages->getAll(null, null, $params);
        $language  = current($languages->rowset);
        $site->translate($language);
    }
    
    return $site->text;
}