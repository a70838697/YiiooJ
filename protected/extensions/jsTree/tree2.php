<?php
/**
 * No you can use data property to dynamically get data with async json.
 * Refer to www.jstree.com for format of json data etc.
 * 
 */
$this->Widget('application.extensions.jsTree.CjsTree', array(
    'id'=>'firstTree',
    
    'data' => array(
        'type' => 'json',
        'async'=> true,
        'opts' => array(
            'method'=>'GET',
            'async'=>true,
            'url' => 'http://www.jstree.com/demos/async_json_data.json', //here you can create url like: $this->createUrl('render')
           
        ),

    ),
    'ui'=>array('theme_name'=>'default'),
    'rules'=>array(
        'droppable' => "tree-drop",
        'multiple' => true,
        'deletable' => "all",
        'draggable' => "all"
        
    ),
    'plugins'=>array(
      'contextmenu'=>array(),
    ),
    'callback'=>array(
        'beforedata'=>'js: function(NODE, TREE_OBJ) { return { id : $(NODE).attr("id") || 0 }; }', //this method will be used when 
    ),


));


?>
