<?php
/**
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<!--
 Nested Set Admin GUI
 Main View File  index.php

 @author Spiros Kabasakalis <kabasakalis@gmail.com>,myspace.com/spiroskabasakalis
 @copyright Copyright &copy; 2011 Spiros Kabasakalis
 @since 1.0
 @license The MIT License-->

<h1> <?php echo $this->modelClass; ?></h1><br>
<h2>Administration</h2><br>
<ul>
     <li>If tree is empty,start by creating one or more root nodes.</li>
    <li>Right Click on a node to see available operations.</li>
    <li>Move nodes with Drag And Drop.You can move a non-root node to root position and vice versa.</li>
     <li>Root nodes cannot be reordered.Their order is fixed  by id.</li>
</ul>
<div style="margin-bottom: 70px;" >
<div style="float:left">
  <input id="reload"  type="button" style="display:block; clear: both;" value="Refresh"class="client-val-form button">
</div>
<div style="float:left">
  <input id="add_root" type="button" style="display:block; clear: both;" value="Create Root" class="client-val-form button">
</div>
</div>


<!--The tree will be rendered in this div-->

<div id="<?php echo '<?php echo '; ?><?php echo $this->modelClass; ?>::ADMIN_TREE_CONTAINER_ID<?php echo ';?>'; ?>" >

</div>

<script  type="text/javascript">
$(function () {
$("#<?php echo '<?php echo '; ?><?php echo $this->modelClass; ?>::ADMIN_TREE_CONTAINER_ID<?php echo ';?>'; ?>")
		.jstree({
                           "html_data" : {
	            "ajax" : {
                                 "type":"POST",
 	                          "url" : "<?php echo '<?php echo '; ?>$baseUrl;<?php echo '?>'; ?>/<?php echo $this->class2var($this->modelClass);?>/fetchTree",
	                         "data" : function (n) {
	                          return {
                                                  id : n.attr ? n.attr("id") : 0,
                                                  "YII_CSRF_TOKEN":"<?php echo '<?php echo '; ?>Yii::app()->request->csrfToken;<?php echo '?>'; ?>"
                                                   };
	                }
  	            }
	        },

"contextmenu":  { 'items': {

"rename" : {
	            "label" : "Rename",
                    "action" : function (obj) { this.rename(obj); }
                  },
"update" : {
	              "label"	: "Update",
	              "action"	: function (obj) {
                                                id=obj.attr("id").replace("node_","");
                     $.ajax({
                                 type: "POST",
                                 url: "<?php echo '<?php echo '; ?>$baseUrl;<?php echo '?>'; ?>/<?php echo $this->class2var($this->modelClass);?>/returnForm",
                                data:{
                                          'update_id':  id,
                                           "YII_CSRF_TOKEN":"<?php echo '<?php echo '; ?>Yii::app()->request->csrfToken;<?php echo '?>'; ?>"
                                              },
			       'beforeSend' : function(){
                                           $("#<?php echo '<?php echo '; ?><?php echo $this->modelClass; ?>::ADMIN_TREE_CONTAINER_ID<?php echo ';?>'; ?>").addClass("ajax-sending");
                                                             },
                               'complete' : function(){
                                           $("#<?php echo '<?php echo '; ?><?php echo $this->modelClass; ?>::ADMIN_TREE_CONTAINER_ID<?php echo ';?>'; ?>").removeClass("ajax-sending");
                                                   },
                    success: function(data){

                        $.fancybox(data,
                        {    "transitionIn"	:	"elastic",
                            "transitionOut"    :      "elastic",
                             "speedIn"		:	600,
                            "speedOut"		:	200,
                            "overlayShow"	:	false,
                            "hideOnContentClick": false,
                             "onClosed":    function(){
                                                                       } //onclosed function
                        })//fancybox

                    } //success
                });//ajax

                                                  }//action function

},//update

    "properties" : {
	"label"	: "Properties",
	"action" : function (obj) {
                                   id=obj.attr("id").replace("node_","")
                             $.ajax({
                                   type:"POST",
			           url:"<?php echo '<?php echo '; ?>$baseUrl;<?php echo '?>'; ?>/<?php echo $this->class2var($this->modelClass);?>/returnView",
		                   data:   {
                                             "id" :id,
                                            "YII_CSRF_TOKEN":"<?php echo '<?php echo '; ?>Yii::app()->request->csrfToken;<?php echo '?>'; ?>"
                                              },
			         beforeSend : function(){
                                               $("#<?php echo '<?php echo '; ?><?php echo $this->modelClass; ?>::ADMIN_TREE_CONTAINER_ID<?php echo ';?>'; ?>").addClass("ajax-sending");
                                                               },
                                complete : function(){
                                              $("#<?php echo '<?php echo '; ?><?php echo $this->modelClass; ?>::ADMIN_TREE_CONTAINER_ID<?php echo ';?>'; ?>").removeClass("ajax-sending");
                                                             },
                               success :  function(data){
                        $.fancybox(data,
                        {    "transitionIn"	:	"elastic",
                            "transitionOut"    :      "elastic",
                             "speedIn"		:	600,
                            "speedOut"		:	200,
                            "overlayShow"	:	false,
                            "hideOnContentClick": false,
                             "onClosed":    function(){
                                                                       } //onclosed function
                        })//fancybox

                    } //function



		});//ajax

                                                },
	"_class"			: "class",	// class is applied to the item LI node
	"separator_before"	: false,	// Insert a separator before the item
	"separator_after"	: true	// Insert a separator after the item

	},//properties

"remove" : {
	               "label"	: "Delete",
	              "action" : function (obj) {
		       $('<div title="Delete Confirmation">\n\
                     <span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>\n\
                    <?php echo $this->modelClass; ?> <span style="color:#FF73B4;font-weight:bold;">'+(obj).attr('rel')+'</span> and all it\'s subcategories will be deleted.Are you sure?</div>')
                       .dialog({
			resizable: false,
			height:170,
			modal: true,
			buttons: {
				       "Delete": function() {
                                        jQuery("#<?php echo '<?php echo '; ?><?php echo $this->modelClass; ?>::ADMIN_TREE_CONTAINER_ID<?php echo ';?>'; ?>").jstree("remove",obj);
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});

                                                                                     }
},//remove
"create" : {
	"label"	: "Create",
	"action" : function (obj) { this.create(obj); },
        "separator_after": false
	}

//The next two context menu items,add_product and list_products are commented out because they are meaningful only if you have
// a related Product Model (Nested Model HAS MANY Product).See Demo included in extension.


//"add_product" : {
//	"label"	: "Add Product",
//	"action" : function (obj) {
//                                   id=obj.attr("id").replace("node_","")
//                             $.ajax({
//                                    type:"POST",
//			            url:"<?php //echo '<?php echo '; ?>$baseUrl;<?php// echo '?>'; ?>/product/returnProductForm",
//			           data:  {
//				         "id" :id,
//                                         "YII_CSRF_TOKEN":"<?php //echo '<?php echo '; ?>Yii::app()->request->csrfToken;<?php //echo '?>'; ?>"
//			            },
//                                       beforeSend : function(){
//                                               $("#<?php // echo '<?php echo '; ?><?php // echo $this->modelClass; ?>::ADMIN_TREE_CONTAINER_ID<?php // echo ';?>'; ?>").addClass("ajax-sending");
//                                                               },
//                                        complete : function(){
//                                              $("#<?php // echo '<?php echo '; ?><?php //echo $this->modelClass; ?>::ADMIN_TREE_CONTAINER_ID<?php // echo ';?>'; ?>").removeClass("ajax-sending");
//                                                             },
//
//                      success: function(data){
//
//                        $.fancybox(data,
//                        {    "transitionIn"	:	"elastic",
//                            "transitionOut"    :      "elastic",
//                             "speedIn"		:	600,
//                            "speedOut"		:	200,
//                            "overlayShow"	:	false,
//                            "hideOnContentClick": false,
//                             "onClosed":    function(){
//                                                                       } //onclosed function
//                        })//fancybox
//
//                    } //function
//
//		});//ajax
//
//
//
//                                                },
//	"separator_before"	: false,	// Insert a separator before the item
//	"separator_after"	: false	// Insert a separator after the item
//	},//add product

//   "list_products" : {
//	"label"	: "List Products",
//	"action" : function (obj) {
//                                   id=obj.attr("id").replace("node_","")
//                             $.ajax({
//                                         type:"POST",
//			                 url:"<?php //echo '<?php echo '; ?>$baseUrl;<?php //echo '?>'; ?>/product/productList",
//			                 data:{
//				                   "id" :id,
//			                           "YII_CSRF_TOKEN":"<?php //echo '<?php echo '; ?>Yii::app()->request->csrfToken;<?php //echo '?>'; ?>"
//                                              },
//			                beforeSend : function(){
//                                               $("#<?php //echo '<?php echo '; ?><?php// echo $this->modelClass; ?>::ADMIN_TREE_CONTAINER_ID<?php //echo ';?>'; ?>").addClass("ajax-sending");
//                                                               },
//                                        complete : function(){
//                                              $("#<?php //echo '<?php echo '; ?><?php // echo $this->modelClass; ?>::ADMIN_TREE_CONTAINER_ID<?php// echo ';?>'; ?>").removeClass("ajax-sending");
//                                                             },
//                                       success: function(data){
//                                        $.fancybox(data,
//                            {  "transitionIn"	:	"elastic",
//                            "transitionOut"    :      "elastic",
//                             "speedIn"		:	600,
//                            "speedOut"		:	200,
//                            "overlayShow"	:	false,
//                            "hideOnContentClick": false,
//                             "onClosed":    function(){
//                                                                       } //onclosed function
//                        })//fancybox
//
//                    } //function
//
//		});//post
//
//                                                },
//	"separator_before"	: false,	// Insert a separator before the item
//	"separator_after"	: true	// Insert a separator after the item
//	}//list products

                  }//items
                  },//context menu

			// the `plugins` array allows you to configure the active plugins on this instance
			"plugins" : ["themes","html_data","contextmenu","crrm","dnd"],
			// each plugin you have included can have its own config object
			"core" : { "initially_open" : [ <?php echo '<?php echo '; ?>$open_nodes<?php echo '?>'; ?> ],'open_parents':true}
			// it makes sense to configure a plugin only if overriding the defaults

		})

                ///EVENTS
               .bind("rename.jstree", function (e, data) {
		$.ajax({
                           type:"POST",
			   url:"<?php echo '<?php echo '; ?>$baseUrl;<?php echo '?>'; ?>/<?php echo $this->class2var($this->modelClass);?>/rename",
			   data:  {
				        "id" : data.rslt.obj.attr("id").replace("node_",""),
                                         "new_name" : data.rslt.new_name,
			                 "YII_CSRF_TOKEN":"<?php echo '<?php echo '; ?>Yii::app()->request->csrfToken;<?php echo '?>'; ?>"
                                       },
                         beforeSend : function(){
                                                     $("#<?php echo '<?php echo '; ?><?php echo $this->modelClass; ?>::ADMIN_TREE_CONTAINER_ID<?php echo ';?>'; ?>").addClass("ajax-sending");
                                                             },
                         complete : function(){
                                                       $("#<?php echo '<?php echo '; ?><?php echo $this->modelClass; ?>::ADMIN_TREE_CONTAINER_ID<?php echo ';?>'; ?>").removeClass("ajax-sending");
                                                             },
			success:function (r) {  response= $.parseJSON(r);
				           if(!response.success) {
					                                   $.jstree.rollback(data.rlbk);
				                                            }else{
                                                                               data.rslt.obj.attr("rel",data.rslt.new_name);
                                                                            };
			                   }
		});
	})

         .bind("remove.jstree", function (e, data) {
		$.ajax({
                           type:"POST",
			    url:"<?php echo '<?php echo '; ?>$baseUrl;<?php echo '?>'; ?>/<?php echo $this->class2var($this->modelClass);?>/remove",
			    data:{
				        "id" : data.rslt.obj.attr("id").replace("node_",""),
			                "YII_CSRF_TOKEN":"<?php echo '<?php echo '; ?>Yii::app()->request->csrfToken;<?php echo '?>'; ?>"
                                        },
                           beforeSend : function(){
                                                     $("#<?php echo '<?php echo '; ?><?php echo $this->modelClass; ?>::ADMIN_TREE_CONTAINER_ID<?php echo ';?>'; ?>").addClass("ajax-sending");
                                                             },
                          complete: function(){
                                                       $("#<?php echo '<?php echo '; ?><?php echo $this->modelClass; ?>::ADMIN_TREE_CONTAINER_ID<?php echo ';?>'; ?>").removeClass("ajax-sending");
                                                             },
			  success:function (r) {  response= $.parseJSON(r);
				           if(!response.success) {
					                                   $.jstree.rollback(data.rlbk);
				                                            };
			                   }
		});
	})

        .bind("create.jstree", function (e, data) {
                           newname=data.rslt.name;
                           parent_id=data.rslt.parent.attr("id").replace("node_","");
            $.ajax({
                    type: "POST",
                    url: "<?php echo '<?php echo '; ?>$baseUrl;<?php echo '?>'; ?>/<?php echo $this->class2var($this->modelClass);?>/returnForm",
                      data:{   'name': newname,
                                 'parent_id':   parent_id,
                                 "YII_CSRF_TOKEN":"<?php echo '<?php echo '; ?>Yii::app()->request->csrfToken;<?php echo '?>'; ?>"
                                                          },
                           beforeSend : function(){
                                                     $("#<?php echo '<?php echo '; ?><?php echo $this->modelClass; ?>::ADMIN_TREE_CONTAINER_ID<?php echo ';?>'; ?>").addClass("ajax-sending");
                                                             },
                           complete : function(){
                                                       $("#<?php echo '<?php echo '; ?><?php echo $this->modelClass; ?>::ADMIN_TREE_CONTAINER_ID<?php echo ';?>'; ?>").removeClass("ajax-sending");
                                                             },
                          success: function(data){

                        $.fancybox(data,
                        {    "transitionIn"	:	"elastic",
                            "transitionOut"    :      "elastic",
                             "speedIn"		:	600,
                            "speedOut"		:	200,
                            "overlayShow"	:	false,
                            "hideOnContentClick": false,
                             "onClosed":    function(){
                                                                       } //onclosed function
                        })//fancybox

                    } //success
                });//ajax

	})
.bind("move_node.jstree", function (e, data) {
		data.rslt.o.each(function (i) {

                //jstree provides a whole  bunch of properties for the move_node event
                //not all are needed for this view,but they are there if you need them.
                //Commented out logs  are for debugging and exploration of jstree.

                 next= jQuery.jstree._reference('#<?php echo '<?php echo '; ?><?php echo $this->modelClass; ?>::ADMIN_TREE_CONTAINER_ID<?php echo ';?>'; ?>')._get_next (this, true);
                 previous= jQuery.jstree._reference('#<?php echo '<?php echo '; ?><?php echo $this->modelClass; ?>::ADMIN_TREE_CONTAINER_ID<?php echo ';?>'; ?>')._get_prev(this,true);

                    pos=data.rslt.cp;
                    moved_node=$(this).attr('id').replace("node_","");
                    next_node=next!=false?$(next).attr('id').replace("node_",""):false;
                    previous_node= previous!=false?$(previous).attr('id').replace("node_",""):false;
                    new_parent=$(data.rslt.np).attr('id').replace("node_","");
                    old_parent=$(data.rslt.op).attr('id').replace("node_","");
                    ref_node=$(data.rslt.r).attr('id').replace("node_","");
                    ot=data.rslt.ot;
                    rt=data.rslt.rt;
                    copy= typeof data.rslt.cy!='undefined'?data.rslt.cy:false;
                   copied_node= (typeof $(data.rslt.oc).attr('id') !='undefined')? $(data.rslt.oc).attr('id').replace("node_",""):'UNDEFINED';
                   new_parent_root=data.rslt.cr!=-1?$(data.rslt.cr).attr('id').replace("node_",""):'root';
                   replaced_node= (typeof $(data.rslt.or).attr('id') !='undefined')? $(data.rslt.or).attr('id').replace("node_",""):'UNDEFINED';


//                      console.log(data.rslt);
//                      console.log(pos,'POS');
//                      console.log(previous_node,'PREVIOUS NODE');
//                      console.log(moved_node,'MOVED_NODE');
//                      console.log(next_node,'NEXT_NODE');
//                      console.log(new_parent,'NEW PARENT');
//                      console.log(old_parent,'OLD PARENT');
//                      console.log(ref_node,'REFERENCE NODE');
//                      console.log(ot,'ORIGINAL TREE');
//                      console.log(rt,'REFERENCE TREE');
//                      console.log(copy,'IS IT A COPY');
//                      console.log( copied_node,'COPIED NODE');
//                      console.log( new_parent_root,'NEW PARENT INCLUDING ROOT');
//                      console.log(replaced_node,'REPLACED NODE');


			$.ajax({
				async : false,
				type: 'POST',
				url: "<?php echo '<?php echo '; ?>$baseUrl;<?php echo '?>'; ?>/<?php echo $this->class2var($this->modelClass);?>/moveCopy",

				data : {
					"moved_node" : moved_node,
                                        "new_parent":new_parent,
                                        "new_parent_root":new_parent_root,
                                         "old_parent":old_parent,
                                         "pos" : pos,
                                         "previous_node":previous_node,
                                          "next_node":next_node,
                                          "copy" : copy,
                                          "copied_node":copied_node,
                                          "replaced_node":replaced_node,
				         "YII_CSRF_TOKEN":"<?php echo '<?php echo '; ?>Yii::app()->request->csrfToken;<?php echo '?>'; ?>"
                                                          },
                           beforeSend : function(){
                                                     $("#<?php echo '<?php echo '; ?><?php echo $this->modelClass; ?>::ADMIN_TREE_CONTAINER_ID<?php echo ';?>'; ?>").addClass("ajax-sending");
                                                             },
                          complete : function(){
                                                       $("#<?php echo '<?php echo '; ?><?php echo $this->modelClass; ?>::ADMIN_TREE_CONTAINER_ID<?php echo ';?>'; ?>").removeClass("ajax-sending");
                                                             },
				success : function (r) {
                                    response=$.parseJSON(r);
					if(!response.success) {
						$.jstree.rollback(data.rlbk);
                                                 alert(response.message);
					}
					else {
                                          //if it's a copy
                                          if  (data.rslt.cy){
						$(data.rslt.oc).attr("id", "node_" + response.id);                         
						if(data.rslt.cy && $(data.rslt.oc).children("UL").length) {
							data.inst.refresh(data.inst._get_parent(data.rslt.oc));
						}
                                          }
                                                                             //  console.log('OK');
					}

				}
			}); //ajax



		});//each function
	});   //bind move event

                ;//JSTREE FINALLY ENDS (PHEW!)

//BINDING EVENTS FOR THE ADD ROOT AND REFRESH BUTTONS.
   $("#add_root").click(function () {
	$.ajax({
                      type: 'POST',
	              url:"<?php echo '<?php echo '; ?>$baseUrl;<?php echo '?>'; ?>/<?php echo $this->class2var($this->modelClass);?>/returnForm",
		     data:	{
				    "create_root" : true,
			             "YII_CSRF_TOKEN":"<?php echo '<?php echo '; ?>Yii::app()->request->csrfToken;<?php echo '?>'; ?>"
                                                          },
                                     beforeSend : function(){
                                                     $("#<?php echo '<?php echo '; ?><?php echo $this->modelClass; ?>::ADMIN_TREE_CONTAINER_ID<?php echo ';?>'; ?>").addClass("ajax-sending");
                                                             },
                                     complete : function(){
                                                       $("#<?php echo '<?php echo '; ?><?php echo $this->modelClass; ?>::ADMIN_TREE_CONTAINER_ID<?php echo ';?>'; ?>").removeClass("ajax-sending");
                                                             },
                                     success:    function(data){

                        $.fancybox(data,
                        {    "transitionIn"	:	"elastic",
                            "transitionOut"    :      "elastic",
                             "speedIn"		:	600,
                            "speedOut"		:	200,
                            "overlayShow"	:	false,
                            "hideOnContentClick": false,
                             "onClosed":    function(){
                                                                       } //onclosed function
                        })//fancybox

                    } //function

		});//post
	});//click function

              $("#reload").click(function () {
		jQuery("#<?php echo '<?php echo '; ?><?php echo $this->modelClass; ?>::ADMIN_TREE_CONTAINER_ID<?php echo ';?>'; ?>").jstree("refresh");
	});
});

</script>

