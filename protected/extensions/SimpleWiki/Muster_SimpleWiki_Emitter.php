<?php
/**
// Muster Software Copyright (c) Henrik Bechmann, Toronto, Canada 2009-2012. All rights reserved.
// See "musterlicence.txt" for licencing information.
// mustersoftware.net
@version RC1.05
@date July 9, 2011
*/
#==========================================================================#
#-----------------------------[ EMITTER ]----------------------------------#
#==========================================================================#
/**
$emitter = new SimpleWiki_Emitter() - create new instance of emitter\n
$html = $emitter->emit($dom); see Muster_SimpleWiki_DocNode for docnode type documentation.
@sa Muster_SimpleWiki_DocNode (constants) for documentation on properties of the individual docnode types.
*/
// generates the HTML; other emitters could be substituted
class Muster_SimpleWiki_Emitter
{
	/**@{ @name Data properties */
	/// document object model, through the root node
	protected $_dom;
	/// regex rules combined into applied regular expressions
	protected $_rules;
	/// link regular expressions, external link, symlink, and anchor
	protected $_link_re;
	/// image link regular expressions, external link and symlink
	protected $_image_re;
	/// supported block tags
	protected $_blocktags = // could be replaced or changed by client
		array
		(
			'div', 'blockquote', # division, blockquote
			'table', 'thead', 'tbody', 'tr', 'td', 'th', 'tfoot', 'caption', # table
			'ul', 'ol', 'li', 'dl', 'dt', 'dd', # lists
			'dlmodule','dlwidget','dlsettings','dlmarker' #dlml
		);
	/**@}*/
	/**@{ @name Registered data */
	/// [$nodetype][$classname]=>$methodref
	protected $_class_callouts = array();
	/// [$nodetype][$propertyname]=>$methodref
	protected $_property_callouts = array();
	/// [$macroname]=>$methodref
	protected $_macro_callouts = array();
	/// [$symlink]=>$value
	protected $_symlinks = array();
	/// array($objectref,'methodname')
	protected $_symlink_handler;
	/// array($objectref,'methodname')
	protected $_rawlink_handler;
	/// array($objectref,'methodname')
	protected $_charfilter_handler;
	/// ['event']=>'methodref'
	protected $_events = array();
	/// array($objectref,'methodname')
	protected $_blockdef_handler;
	/**@}*/
/**@{ @name Creation */	
	/** The constructor.
		Sets regex rules, and combines them into applied regex.
		No parameters.
		@return void
	*/
	public function __construct()
	{
		$this->set_rules();
		$this->set_re($this->_rules);
	}
	#----------------------------------[ init ]-----------------------------------------#
	/** Sets rules used in the class.
		lists prototypes, sets regex for external url's, symlinks, and anchors
	*/
	protected function set_rules()
	{
		$rules = new StdClass();
		$proto = 'http[s]?|ftp|nntp|news|telnet|file|irc';
		$rules->extern = "(?P<external_address>(?P<external_proto>$proto)
			:\/\/(?P<external_selector>\S+\w))";
		$rules->mail = "(?P<external_mailaddress>(?P<external_mailproto>mailto)
			:(?P<external_mailselector>\S+\w))";
		$rules->symlink = '
            (?P<internal_address> (?P<symlink>[A-Z][a-zA-Z-]+) :
            (?P<internal_selector> [^`\s]+)(?P<internal_version>`\S+)?)
        ';
		$rules->anchor = '
			(?P<anchor>\#[a-zA-Z][\w-]*)
		';
		$rules->rawlink = '
			(?P<rawlink>\S*)
		';
		$this->_rules = $rules;
	}
	/** Sets the regelar expressions applied.
	_link_re for link addresses; _image_re for image addresses (excludes anchors)
	*/
	protected function set_re($rules)
	{
		$this->_link_re = '/' . implode('|',array($rules->extern,$rules->mail,$rules->symlink,$rules->anchor,$rules->rawlink)) . '/x';
		$this->_image_re =  '/' . implode('|',array($rules->extern,$rules->symlink,$rules->rawlink)) . '/x';
	}
/**@}*/
/**@{@name Control methods */
/** generate html from the passed document object model.
	@param object $dom the root node of the document object model for which html is being emitted.
	@return string html
*/
	public function emit($dom) // main method
	{
		$this->_dom = $dom;
		return $this->emit_node($dom);
	}
/** controller directs flow to one of the node emit methods.
	@param object $node 
	@return html
*/
	protected function emit_node($node) // controller
	{
		$emit = '_' . $node->type . '_emit';
		return $this->$emit($node);
	}
/** collects and returns html for chilren. Can be useful for registered method classes, macros and events, so it is public.
	@param object $node
	@return html
*/
	public function emit_children($node)
	{
		if (empty($node->children)) return '';
		$children = $node->children;
		$childoutput = array();
		foreach ($children as $child)
		{
			$childoutput[] = $this->emit_node($child);
		}
		return implode('',$childoutput);
	}
/** text only, no html or other markup. Can be helpful for registrants, so it is public.
*/
	public function emit_node_text($node)
	{
		if ($node->type == SimpleWiki_DocNode::TEXT)
			return $node->textcontent;
		else
			return $this->emit_children_text($node);
	}
/** supports emit_node_text. text only, no html or other markup, helpful for registrants.
	@sa emit_node_text
*/
	protected function emit_children_text($node)
	{
		if (empty($node->children)) return '';
		$children = $node->children;
		$childoutput = array();
		foreach ($children as $child)
		{
			$childoutput[] = $this->emit_node_text($child);
		}
		return implode(' ',$childoutput);
	}
/**@}*/
/**@{ @name Property accessors */
/// returns registered symlinks
	public function symlinks()
	{
		return $this->_symlinks;
	}
/// returns registered symlink handler
	public function symlink_handler()
	{
		return $this->_symlink_handler;
	}
/// get or set collection of block declarations recognized by emitter
	public function blocktags($blocktaglist = NULL)
	{
		if (!is_null($blocktaglist))
			$this->_blocktags = $blocktaglist;
		return $this->_blocktags;
	}
/**@}*/
	#========================[ callout handling ]=======================================#
/**@{ @name Register callouts
	Clients register callouts for classes, macros, symlinks, and events.
	All callbacks are passed nodes
*/
/**	 ['event']=>'methodref'.
*/
	public function register_events($callbacks)
	{
		$events = $this->_events;
		foreach ($callbacks as $eventname => $callback)
		{
			if (!isset($events[$eventname]))
			{
				$events[$eventname] = array();
			}
			$events[$eventname][] = $callback;
		}
		$this->_events = $events;
	}
/** [$symlink]=>$value.
*/
	public function register_symlinks($symlinks)
	{
		$symlinklist = $this->_symlinks;
		foreach ($symlinks as $symlink => $value) {
			$symlinklist[$symlink] = $value;
		}
		$this->_symlinks = $symlinklist;
	}
/** Default handler for symlinks not registered:
	array($objectref,'methodname').
*/
	public function register_symlink_handler($handler)
	{
		$this->_symlink_handler = $handler;
	}
/** Handler for rawlinks:
	array($objectref,'methodname').
*/
	public function register_charfilter_handler($handler)
	{
		$this->_charfilter_handler = $handler;
	}
/** Handler for rawlinks:
	array($objectref,'methodname').
*/
	public function register_rawlink_handler($handler)
	{
		$this->_rawlink_handler = $handler;
	}
/** [$nodetype][$classname]=>$methodref. Typically called by SimpleWiki (as facade).
 One callback per type class. 
*/
	public function register_class_callouts($callouts) 
	{
		// ['nodetype']['class']=>$methodref
		$calloutslist = $this->_class_callouts;
		foreach ($callouts as $nodetype => $classcallouts)
		{
			if (empty($calloutlist[$nodetype]))
			{
				$calloutlist[$nodetype] = $callouts[$nodetype];
			}
			else
			{
				$classcallouts = $callouts[$nodetype];
				foreach($classcallouts as $class =>$methodref)
				{
					$calloutlist[$nodetype][$class] = $methodref;
				}
			}
		}
		$this->_class_callouts = $calloutlist;
	}
/** [$nodetype][$propertyname]=>$methodref. Typically called by SimpleWiki (as facade).
 One callback per type class. 
*/
	public function register_property_callouts($callouts) 
	{
		// ['nodetype']['property']=>$methodref
		$calloutslist = $this->_property_callouts;
		foreach ($callouts as $nodetype => $propertycallouts)
		{
			if (empty($calloutlist[$nodetype]))
			{
				$calloutlist[$nodetype] = $callouts[$nodetype];
			}
			else
			{
				$propertycallouts = $callouts[$nodetype];
				foreach($propertycallouts as $class =>$methodref)
				{
					$calloutlist[$nodetype][$class] = $methodref;
				}
			}
		}
		$this->_property_callouts = $calloutlist;
	}
/** [$macroname]=>$methodref. Typically called by SimpleWiki (as facade). 
*/
	public function register_macro_callouts($callouts)
	{
		// ['macroname']=>$methodref
		$calloutlist = $this->_macro_callouts;
		foreach ($callouts as $macroname => $methodref)
		{
			$calloutlist[$macroname] = $methodref;
		}
		$this->_macro_callouts = $calloutlist;
	}
/** Handler for block definitions:
	array($objectref,'methodname').
	$param methodref $handler normally array($object,'methodname')
*/
	public function register_blockdef_handler($handler)
	{
		$this->_blockdef_handler = $handler;
	}
/**@}*/
	#---------------------------[trigger callouts]-------------------------------------#
/**@{@name Invoke callouts */
	/** triggered from prepare_link_node.
	@param object $node the current document node
	@return object modified document node
		@sa prepare_link_node
	*/
	protected function expand_symlink($node)
	{
		$symlinks = $this->_symlinks;
		if (isset($symlinks[$node->linkparts->symlink]))
			$node->linkparts->symlinkpath = $symlinks[$node->linkparts->symlink];
		elseif (isset($this->_symlink_handler))
			$node = call_user_func($this->_symlink_handler,$node);
		else {
			$node->unknown = TRUE;
			$node->linkparts->symlinkpath = $node->linkparts->symlink . ':'; // stub
		}
		return $node;
	}
	/** triggered from prepare_link_node.
	@param object $node the current document node
	@return object modified document node
		@sa prepare_link_node
	*/
	protected function expand_rawlink($node)
	{
		if (isset($this->_rawlink_handler))
			$node = call_user_func($this->_rawlink_handler,$node);
		else {
			$node->unknown = TRUE;
			$node->linkparts->rawlinkaddress = $node->linkparts->rawlink; // stub
		}
		return $node;
	}
	/** triggered from prepare_macro.
	@param object $node the current document node
	@return object modified document node
		@sa prepare_macro
	*/
	protected function call_macro($node)
	{
		$callbacks = $this->_macro_callouts;
		if (isset($callbacks[$node->macroname]))
		{
			$node = call_user_func($callbacks[$node->macroname],$node);
			$node->processed = TRUE;
		}
		return $node;
	}
	/** triggered from prepare_node.
		@return object modified document node
		@sa prepare_node
	*/
	protected function call_classes($node)
	{
		$callbacks = isset($this->_class_callouts[$node->type])?$this->_class_callouts[$node->type]:NULL;
		if (!empty($callbacks)) 
		{
			$classes = !empty($node->decoration->classes)?$node->decoration->classes:NULL;
			if (!empty($classes))
			{
				foreach ($classes as $class)
				{
					$callback = isset($callbacks[$class])?$callbacks[$class]:NULL;
					if ($callback)
					{
						$node = call_user_func($callback,$node);
					}
				}
			}
		}
		return $node;
	}
	/** triggered from prepare_node.
	@param object $node the current document node
	@return object modified document node
		@sa prepare_node
	*/
	protected function call_properties($node)
	{
		$callbacks = isset($this->_property_callouts[$node->type])?$this->_property_callouts[$node->type]:NULL;
		if (!empty($callbacks)) 
		{
			$properties = !empty($node->decoration->properties)?$node->decoration->properties:NULL;
			if (!empty($properties))
			{
				foreach ($properties as $propertyindex => $property)
				{
					$callback = isset($callbacks[$propertyindex])?$callbacks[$propertyindex]:NULL;
					if ($callback)
					{
						$node = call_user_func($callback,$node);
					}
				}
			}
		}
		return $node;
	}
	/** triggered from _document_emit
	@param string $event the event being triggered
	@param object $node the current document node
	@return object modified document node
	@sa _document_edit
	*/
	protected function call_event($event,$node)
	{
		$events = isset($this->_events[$event])?$this->_events[$event]:NULL;
		if (!empty($events)) {
			foreach ($events as $callback) {
				$node = call_user_func($callback,$node);
			}
		}
		return $node;
	}
/**@}*/
	#--------------------------------[ utilities ]---------------------------------------#
/**@{ @name Support methods */
/*	protected function get_value($value,$default)
	{
		return isset($value)? $value: $default;
	}
*/
	/** interpret address, inlcuding symlink; prepare src, alt, title attributes.
	Then call standard prepare_node
	@param object $node the current document node
	@return object modified document node
	@sa prepare_node
	*/
	protected function prepare_image_node($node)
	{
		# symlink for src
		if (!empty($node->linkparts->symlink)) {
			$node = $this->expand_symlink($node);
			$node->decoration->attributes['src'] = 
				$node->linkparts->symlinkpath . $node->linkparts->internalselector;
			$node->decoration->attributedelimiters['src'] = '"';
		}
		# external address for src
		else {
			$node->decoration->attributes['src'] = 
				!empty($node->linkparts->externaladdress)?$node->linkparts->externaladdress:NULL;
			$node->decoration->attributedelimiters['src'] = '"';
		}
		# alt attribute
		// caption, or...
		if ($node->caption) {
			$node->decoration->attributes['alt'] = $this->char_filter($node->caption,$node);
			$node->decoration->attributedelimiters['alt'] = '"';
		}
		// ... target.
		elseif ($node->target) 
		{
			$node->decoration->attributes['alt'] = $this->char_filter($node->target,$node);
			$node->decoration->attributedelimiters['alt'] = '"';
		}
		# title attribute
		if (!empty($node->title)) {
			$node->decoration->attributes['title'] = $node->title;
			$node->decoration->attributedelimiters['title'] = '"';
		}
		# standard prepare node...
		$node = $this->prepare_node($node);
		return $node;
	}
	/** identify anchor for special handling; 
		prepare attributes - name, href, title.
		Then call standard prepare_node
		@param object $node
		@return object modified document node
		@sa prepare_node
	*/
	protected function prepare_link_node($node)
	{
		$attributename = 'href';
		if (!empty($node->linkparts->anchor)) {
			if (empty($node->caption)) {
				$attributename = 'name';
				$node->linkparts->anchor = substr($node->linkparts->anchor,1);
				$node->decoration->attributes[$attributename] = $node->linkparts->anchor;
				$node->decoration->attributedelimiters[$attributename] = '"';
			} else {
				$node->linkparts->symlink = 'Anchor';
				$node = $this->expand_symlink($node);
				$node->decoration->attributes[$attributename] = $node->linkparts->symlinkpath . $node->linkparts->anchor;
				$node->decoration->attributedelimiters[$attributename] = '"';
			}
		}
		elseif (!empty($node->linkparts->symlink)) {
			$node = $this->expand_symlink($node);
			$node->decoration->attributes[$attributename] = 
				$node->linkparts->symlinkpath . (!empty($node->linkparts->internalselector)?$node->linkparts->internalselector:'');
			$node->decoration->attributedelimiters[$attributename] = '"';
		}
		elseif (!empty($node->linkparts->externaladdress)) {
			$node->decoration->attributes[$attributename] = 
				!empty($node->linkparts->externaladdress)?$node->linkparts->externaladdress:NULL;
			$node->decoration->attributedelimiters[$attributename] = '"';
		}
		else {
			$node = $this->expand_rawlink($node);
			$node->decoration->attributes[$attributename] = 
				!empty($node->linkparts->rawlinkaddress)?$node->linkparts->rawlinkaddress:NULL;
			$node->decoration->attributedelimiters[$attributename] = '"';
		}
		if (!empty($node->title)) {
			$node->decoration->attributes['title'] = $node->title;
			$node->decoration->attributedelimiters['title'] = '"';
		}
		$node = $this->prepare_node($node);
		return $node;
	}
	/** trigger callouts; prepare output property
		@param object $node document node
		@return object modified document node
	*/
	protected function prepare_macro_node($node)
	{
		$node->output = '';
		$this->call_macro($node);
		if (empty($node->output) and ($node->caption)) {
			$node->output = $node->caption;
		}
		return $node;
	}
	/** Standard data preparation for emitting html. 
		trigger class and property callouts; 
		prepare attributes, classes, and styles for HTML
		by combining into single attribute array
		@param object $node document node
		@return object modified document node
	*/
	protected function prepare_node($node)
	{
		# trigger callouts
		$node = $this->call_classes($node);
		$node = $this->call_properties($node);
		# convert input decoration attributes, values, and properties into html attributes
		$attributes = array();
		// attributes
		$attr = !empty($node->decoration->attributes)?$node->decoration->attributes:array();
		$attrdelimiters = !empty($node->decoration->attributedelimiters)?$node->decoration->attributedelimiters:array();
		foreach ($attr as $key => $value) {
			$attributes[] = $key . '=' . $attrdelimiters[$key] . $value . $attrdelimiters[$key];
		}
		// classes
		$values = array();
		$values = !empty($node->decoration->classes)?$node->decoration->classes:array();
		$classes = preg_replace('/"/','\\"',implode(' ',$values)); // escape embedded double quotes
		if (!empty($classes)) $attributes[]='class="' . $classes . '"';
		// styles
		$properties = array();
		$properties = !empty($node->decoration->properties)?$node->decoration->properties:array();
		$styles = array();
		foreach ($properties as $key => $value) {
			$styles[] = $key . ':' . $value;
		}
		if (!empty($styles)) {
			$style = implode(';',preg_replace('/"/','\\"',$styles)); // escape embedded double quotes
			$attributes[] = 'style="' . $style . '"';
		}
		if (!empty($attributes)) $node->opentag_head .= ' ';
		$node->attributes = $attributes;
		return $node;
	}
	/**
	Assembles and returns standard node html components.
		opentag_head, node attributes, opentag_tail, elementcontent, and closetag.
		@param object $node document node
		@return object modified document node
	*/
	protected function standard_assembly($node)
	{
		return $node->opentag_head . implode(' ',$node->attributes) . $node->opentag_tail 
			. $node->elementcontent . $node->closetag;
	}
	/**
		Filters text with htmlspecialchars($text) by default. But allows substitution of alternate or custom character filters.
	*/
	protected function char_filter($text, $node = NULL)
	{
		if (isset($this->_charfilter_handler)) {
			return call_user_func($this->_charfilter_handler,$text,$node);
		} else {
			return htmlspecialchars($text);
		}
	}
/**@}*/
	#------------------------------------------------------------------------------#
	#-----------------------------[ node emitters ]--------------------------------#
	#------------------------------------------------------------------------------#
	#==============================[ document ]====================================#
/**@{ @name Node emitters */
/** emit html for document.
	@param object $node document node
	@return object modified document node
*/
	protected function _document_emit($node)
	{
		// anticipate event calls
		$node->opentag_head = '';
		$node->closetag = '';
		$node = $this->call_event('onemit',$node);
		$node->elementcontent = $this->emit_children($node);
		$node = $this->call_event('onafteremit',$node);
		return $node->opentag_head . $node->elementcontent . $node->closetag;
	}
	#=========================[ basic processing ]=================================#
/**	emit html for paragraph.
	@param object $node document node
	@return object modified document node
*/
	protected function _paragraph_emit($node) // b decorator "p"
	{
		$node->opentag_head = "\n<p";
		$node->opentag_tail = ">";
		$node->elementcontent = $this->emit_children($node);
		$node->closetag = "</p>";
		$node = $this->prepare_node($node);
		return $this->standard_assembly($node);
	}
/**	emit html for text node.
	@param object $node document node
	@return object modified document node
*/
	protected function _text_emit($node)
	{
		return $this->char_filter($node->textcontent,$node);
	}
	#================================[ core markup ]===============================#
	#--------------------------------[ basic markup ]------------------------------#
/**	emit html for heading.
	@param object $node document node
	@return object modified document node
*/
	protected function _heading_emit($node) // b decorator "h"
	{
		$node->opentag_head = "\n<h" . $node->level;
		$node->opentag_tail = ">";
		$node->elementcontent = $this->emit_children($node);
		$node->closetag = "</h". $node->level . ">";
		$node = $this->prepare_node($node);
		return $this->standard_assembly($node);
	}
/**	emit html for emphasis.
	@param object $node document node
	@return object modified document node
*/
	protected function _emphasis_emit($node)
	{
		return "<em>" . $this->emit_children($node) . "</em>";
	}
/**	emit html for strong.
	@param object $node document node
	@return object modified document node
*/
	protected function _strong_emit($node)
	{
		return "<strong>" . $this->emit_children($node) . "</strong>";
	}
/**	emit html for linebreak.
	@param object $node document node
	@return object modified document node
*/
	protected function _linebreak_emit($node)
	{
		return "<br />\n";
	}
/**	emit html for horizontalrule.
	@param object $node document node
	@return object modified document node
*/
	protected function _horizontalrule_emit($node)
	{
		return "\n<hr />";
	}
	#--------------------------------[ links ]-------------------------------------#
/**	emit html for link.
	@param object $node document node
	@return object modified document node
*/
	protected function _link_emit($node) // i decorator "a"
	{
		$node->caption = $this->emit_children($node);
		// also available: $node->title
		$address = $node->target;
		$matches = array();
		if (preg_match($this->_link_re,$address,$matches))
		{
			isset($matches['anchor']) or ($matches['anchor'] = '');
			isset($matches['internal_address']) or ($matches['internal_address'] = '');
			isset($matches['symlink']) or ($matches['symlink'] = '');
			isset($matches['internal_selector']) or ($matches['internal_selector'] = '');
			isset($matches['internal_version']) or ($matches['internal_version'] = '');
			isset($matches['external_address']) or ($matches['external_address'] = '');
			isset($matches['external_proto']) or ($matches['external_proto'] = '');
			isset($matches['external_selector']) or ($matches['external_selector'] = '');
			isset($matches['external_mailaddress']) or ($matches['external_mailaddress'] = '');
			isset($matches['external_mailproto']) or ($matches['external_mailproto'] = '');
			isset($matches['external_mailselector']) or ($matches['external_mailselector'] = '');
			isset($matches['rawlink']) or ($matches['rawlink'] = '');
			
			$node->linkparts = new StdClass();
			$node->linkparts->anchor = $matches['anchor'];
			
			$node->linkparts->internaladdress = $matches['internal_address'];
			$node->linkparts->symlink = $matches['symlink'];
			$node->linkparts->internalselector = $matches['internal_selector'];
			$node->linkparts->internalversion = $matches['internal_version'];
			
			$node->linkparts->externaladdress = $matches['external_address'];
			$node->linkparts->externalprotocol = $matches['external_proto'];
			$node->linkparts->externalselector = $matches['external_selector'];
			
			if ($matches['external_mailaddress']) {
				$node->linkparts->externaladdress = $matches['external_mailaddress'];
				$node->linkparts->externalprotocol = $matches['external_mailproto'];
				$node->linkparts->externalselector = $matches['external_mailselector'];
			}
			
			$node->linkparts->rawlink = $matches['rawlink'];
		}
		if (empty($node->caption) and empty($node->linkparts->anchor)) {
			if ($matches['external_mailaddress']) {
				$node->caption = $node->linkparts->externalselector;
			} else {
				$node->caption = $node->target;
			}
		}

		$node->opentag_head = "<a";
		$node->opentag_tail = ">";
		$node->elementcontent = $node->caption;
		$node->closetag = "</a>";
		$node->unknown = FALSE;
		$node = $this->prepare_link_node($node); // might set $node->unknown = TRUE;
		if ($node->unknown) {
			$node->opentag_head .= 'rel="nofollow" ';
			$node->opentag_tail .= '<span style="border-bottom:1px dashed gray">';
			$node->closetag = '</span><sup>?</sup>' . $node->closetag;
		}
		return $this->standard_assembly($node);
	}
	#--------------------------------[ images ]------------------------------------#
/**	emit html for image.
	@param object $node document node
	@return object modified document node
*/
	protected function _image_emit($node) // i decorator "i"
	{
		$node->caption = $this->emit_children($node);
		// also available: $node->title
		$address = $node->target;
		$matches = array();
		if (preg_match($this->_image_re,$address,$matches))
		{
			isset($matches['internal_address']) or ($matches['internal_address'] = '');
			isset($matches['symlink']) or ($matches['symlink'] = '');
			isset($matches['internal_selector']) or ($matches['internal_selector'] = '');
			isset($matches['internal_version']) or ($matches['internal_version'] = '');
			isset($matches['external_address']) or ($matches['external_address'] = '');
			isset($matches['external_proto']) or ($matches['external_proto'] = '');
			isset($matches['external_selector']) or ($matches['external_selector'] = '');
			isset($matches['rawlink']) or ($matches['rawlink'] = '');
			
			$node->linkparts = new StdClass();
			$node->linkparts->internaladdress = $matches['internal_address'];
			$node->linkparts->symlink = $matches['symlink'];
			$node->linkparts->internalselector = $matches['internal_selector'];
			$node->linkparts->internalversion = $matches['internal_version'];
			
			$node->linkparts->externaladdress = $matches['external_address'];
			$node->linkparts->externalprotocol = $matches['external_proto'];
			$node->linkparts->externalselector = $matches['external_selector'];
			
			$node->linkparts->rawlink = $matches['rawlink'];
		}
		$node->opentag_head = "<img";
		$node->opentag_tail = "/>";
		$node = $this->prepare_image_node($node); 
		return $node->opentag_head . implode(' ',$node->attributes) . $node->opentag_tail;
	}
	#--------------------------------[ lists ]-------------------------------------#
/**	emit html for definition list.
	@param object $node document node
	@return object modified document node
*/
	protected function _def_list_emit($node) // b decorator "dl"
	{
		$node->opentag_head = "\n<dl";
		$node->opentag_tail = ">";
		$node->elementcontent = $this->emit_children($node);
		$node->closetag = "\n</dl>";
		$node = $this->prepare_node($node);
		return $this->standard_assembly($node);
	}
/**	emit html for definition term.
	@param object $node document node
	@return object modified document node
*/
	protected function _def_term_emit($node) // decorator "dt"
	{
		$node->opentag_head = "\n<dt";
		$node->opentag_tail = ">";
		$node->elementcontent = $this->emit_children($node);
		$node->closetag = "</dt>";
		$node = $this->prepare_node($node);
		return $this->standard_assembly($node);
	}
/**	emit html for definition description.
	@param object $node document node
	@return object modified document node
*/
	protected function _def_desc_emit($node) // decorator "dd"
	{
		$node->opentag_head = "\n<dd";
		$node->opentag_tail = ">";
		$node->elementcontent = $this->emit_children($node);
		$node->closetag = "</dd>";
		$node = $this->prepare_node($node);
		return $this->standard_assembly($node);
	}
/**	emit html for ordered list.
	@param object $node document node
	@return object modified document node
*/
	protected function _ordered_list_emit($node) // b decorator "ol"
	{
		$node->opentag_head = "\n<ol";
		$node->opentag_tail = ">";
		$node->elementcontent = $this->emit_children($node);
		$node->closetag = "\n</ol>";
		$node = $this->prepare_node($node);
		return $this->standard_assembly($node);
//		return $node->opentag_head . implode(' ',$node->attributes) . $node->opentag_tail 
//			. $node->elementcontent . $node->closetag;
	}
/**	emit html for unordered list.
	@param object $node document node
	@return object modified document node
*/
	protected function _unordered_list_emit($node) // b decorator "ul"
	{
		$node->opentag_head = "\n<ul";
		$node->opentag_tail = ">";
		$node->elementcontent = $this->emit_children($node);
		$node->closetag = "\n</ul>";
		$node = $this->prepare_node($node);
		return $this->standard_assembly($node);
	}
/**	emit html for list item.
	@param object $node document node
	@return object modified document node
*/
	protected function _list_item_emit($node) // decorator "li"
	{
		$node->opentag_head = "\n<li";
		$node->opentag_tail = ">";
		$node->elementcontent = $this->emit_children($node);
		$node->closetag = "</li>";
		$node = $this->prepare_node($node);
		return $this->standard_assembly($node);
	}
	#--------------------------------[ tables ]------------------------------------#
/**	emit html for table.
	@param object $node document node
	@return object modified document node
*/
	protected function _table_emit($node) // b decorator "table"
	{
		$node->opentag_head = "\n<table";
		$node->opentag_tail = ">";
		$node->elementcontent = $this->emit_children($node);
		$node->closetag = "\n</table>";
		$node = $this->prepare_node($node);
		return $this->standard_assembly($node);
	}
/**	emit html for table row.
	@param object $node document node
	@return object modified document node
*/
	protected function _table_row_emit($node) // b decorator "tr"
	{
		$node->opentag_head = "\n<tr";
		$node->opentag_tail = ">";
		$node->elementcontent = $this->emit_children($node);
		$node->closetag = "\n</tr>";
		$node = $this->prepare_node($node);
		return $this->standard_assembly($node);
	}
/**	emit html for table head cell.
	@param object $node document node
	@return object modified document node
*/
	protected function _table_headcell_emit($node) // b decorator "th"
	{
		$node->opentag_head = "\n<th";
		$node->opentag_tail = ">\n";
		$node->elementcontent = $this->emit_children($node);
		$node->closetag = "</th>";
		$node = $this->prepare_node($node);
		return $this->standard_assembly($node);
	}
/**	emit html for table data cell.
	@param object $node document node
	@return object modified document node
*/
	protected function _table_cell_emit($node) // b decorator "td"
	{
		$node->opentag_head = "\n<td";
		$node->opentag_tail = ">\n";
		$node->elementcontent = $this->emit_children($node);
		$node->closetag = "</td>";
		$node = $this->prepare_node($node);
		return $this->standard_assembly($node);
	}
	#=========================[ special decorators ]===============================#
	#---------------------------[ span decoration ]--------------------------------#
/**	emit html for span.
	@param object $node document node
	@return object modified document node
*/
	protected function _span_emit($node) // i decorator "s"
	{
		$node->opentag_head = "<span";
		$node->opentag_tail = ">";
		$node->elementcontent = $this->emit_children($node);
		$node->closetag = "</span>";
		$node = $node = $this->prepare_node($node);
		return $this->standard_assembly($node);
	}
	#----------------------------[ block dividers ]--------------------------------#
/**	emit html for block divider.
	@param object $node document node
	@return object modified document node
*/
	protected function _blockdivider_emit($node) // b decorator "b"
	{
		$node->opentag_head = "\n<div";
		$node->opentag_tail = ">";
		$node->elementcontent = '';
		$node->closetag = "\n</div>";
		$node = $this->prepare_node($node);
		return $this->standard_assembly($node);
	}
	#============================[ preformatted text ]=============================#
/**	emit html for code.
	@param object $node document node
	@return object modified document node
*/
	protected function _code_emit($node) // i decorator "c"
	{
		$node->opentag_head = "<code";
		$node->opentag_tail = ">";
		$node->elementcontent = $node->textcontent;
		$node->escapecontent = TRUE;
		$node->closetag = "</code>";
		$node = $this->prepare_node($node);
		if ($node->escapecontent) $node->elementcontent = $this->char_filter($node->elementcontent,$node);
		return $this->standard_assembly($node);
	}
/**	emit html for preformatted.
	@param object $node document node
	@return object modified document node
*/
	protected function _preformatted_emit($node) // b decorator "pre"
	{
		$node->opentag_head = "\n<pre";
		$node->opentag_tail = ">\n";
		$node->elementcontent = $node->textcontent;
		$node->escapecontent = TRUE;
		$node->closetag = "</pre>";
		$node = $this->prepare_node($node);
		if ($node->escapecontent) $node->elementcontent = $this->char_filter($node->elementcontent,$node);
		$retval = $this->standard_assembly($node);
		return $retval;
	}
	#==============================[ advanced markup ]=============================#
	#------------------------------[ block declarations ]----------------------------#
/**	emit html for blockdef.
	@param object $node document node
	@return object modified document node
*/
	protected function _blockdef_emit($node) // declaration decorator (various)
	{
		$blocktag = $node->blocktag;
		$node->knowntag = in_array($blocktag,$this->_blocktags);
		if (!$node->knowntag)
		{
			$node->opentag_head = "\n(:$blocktag " . $node->decoration->markup;
			$node->opentag_tail = ":)";
			$node->closetag = "\n(:{$blocktag}end:)";
		}
		elseif (substr($blocktag,0,2) == 'dl')
		{
			$dlmltag = substr($blocktag,2);
			$node->opentag_head = "\n<dl:" . $dlmltag;
			$node->opentag_tail = ">";
			$node->closetag = "\n</dl:" . $dlmltag. '>';
		}
		else
		{
			$node->opentag_head = "\n<$blocktag";
			$node->opentag_tail = ">";
			$node->closetag = "\n</$blocktag>";
		}
		$node->elementcontent = '';
		if (isset($this->_blockdef_handler))
			$node = call_user_func($this->_blockdef_handler,$node);
		if (!$node->elementcontent) $node->elementcontent = $this->emit_children($node);
		if ($node->knowntag)
		{	
			$node = $this->prepare_node($node);
			return $this->standard_assembly($node);
		}
		else
		{
			return $node->opentag_head . $node->opentag_tail 
				. $node->elementcontent . $node->closetag;
		}
	}
	#--------------------------------[ macros ]--------------------------------#
/**	emit html for macro.
	@param object $node document node
	@return object modified document node
*/
	protected function _macro_emit($node) // macro decorator
	{
		$node->caption = $this->emit_children($node);
		$node->processed = FALSE; // set to TRUE by prepare_macro_node if macro found.
		$node = $this->prepare_macro_node($node);
		if ($node->processed)
		{
			return $node->output;
		} else { // return re-assembled source markup
			$opentag_head = '<<' . $node->macroname;
			$arguments = property_exists($node,"decoration")? $node->decoration->markup: '';
			if ($arguments != '') $arguments = ' ' . $arguments;
			$caption = $node->caption;
			if ($caption) 
				$caption = '|' . $caption;
			$closetag = '>>';
			return $this->char_filter($opentag_head . $arguments .  $caption . $closetag,$node);
		}
	}
/**@}*/
}
