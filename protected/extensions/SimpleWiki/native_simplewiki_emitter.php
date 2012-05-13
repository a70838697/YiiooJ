<?php
/*
SimpleWiki module, version 1.0 Beta 3, January 6, 2011
copyright (c) Henrik Bechmann, 2009-2011, Toronto, Canada. All rights reserved. simplewiki.org
licence: BSD
*/
#==========================================================================#
#-----------------------------[ EMITTER ]----------------------------------#
#==========================================================================#
/*
public methods:
$emitter = new SimpleWikiEmitter() - create new instance of emitter
->emit($dom) - generate html from the passed document object model
->emit_children($node) - useful for registered method classes, macros and events
->emit_node_text($node) // text only, no html or other markup, helpful for registrants
->symlinks() - returns registered symlinks
->symlink_handler() - returns registered symlink handler
->blocktags($blocktaglist=$NULL) - get or set collection of block declarations recognized by emitter
->register_events($callbacks)
->register_symlinks($symlinks)
->register_symlink_handler($handler) - default handler for symlinks not registered
->register_class_callouts($callouts) - typically called by SimpleWiki (as facade)
->register_macro_callouts($callouts) - typically called by SimpleWiki (as facade)
*/
// generates the HTML; other emitters could be substituted
class Native_SimpleWiki_Emitter
{
	protected $_dom;
	protected $_rules;
	protected $_class_callouts = array();
	protected $_macro_callouts = array();
	protected $_symlinks = array();
	protected $_symlink_handler;
	protected $_events = array();
	protected $_blocktags = // could be replaced or changed by client
		array
		(
			'div', 'blockquote', # division, blockquote
			'table', 'thead', 'tbody', 'tr', 'td', 'th', 'tfoot', 'caption', # table
			'ul', 'ol', 'li', 'dl', 'dt', 'dd' # lists
		);
	protected $addr_re;
	
	public function __construct()
	{
		$this->set_rules();
		$this->set_re($this->_rules);
	}
	public function emit($dom) // main method
	{
		$this->_dom = $dom;
		return $this->emit_node($dom);
	}
	public function symlinks()
	{
		return $this->_symlinks;
	}
	public function symlink_handler()
	{
		return $this->_symlink_handler;
	}
	public function blocktags($blocktaglist = NULL)
	{
		if (!is_null($blocktaglist))
			$this->_blocktags = $blocktaglist;
		return $this->_blocktags;
	}
	#========================[ callout handling ]=======================================#
	// clients register callouts for classes, macros, symlinks, and start/finish events
	#---------------------------[register callouts]-------------------------------------#
	# all callbacks are passed nodes
	// events are published - all registered callbacks are notified, including multiple callbacks per event.
	// ['event']['methodref']
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
	public function register_symlinks($symlinks)
	{
		$symlinklist = $this->_symlinks;
		foreach ($symlinks as $symlink => $value)
		{
			$symlinklist[$symlink] = $value;
		}
		$this->_symlinks = $symlinklist;
	}
	public function register_symlink_handler($handler)
	{
		$this->_symlink_handler = $handler;
	}
	// one callback per kind class
	public function register_class_callouts($callouts) 
	{
		// ['nodekind']['class']=>$methodref
		$calloutslist = $this->_class_callouts;
		foreach ($callouts as $nodekind => $classcallouts)
		{
			if (empty($calloutlist[$nodekind]))
			{
				$calloutlist[$nodekind] = $callouts[$nodekind];
			}
			else
			{
				$classcallouts = $callouts[$nodekind];
				foreach($classcallouts as $class =>$methodref)
				{
					$calloutlist[$nodekind][$class] = $methodref;
				}
			}
		}
		$this->_class_callouts = $calloutlist;
	}
	// one callback per macro
	public function register_macro_callouts($callouts)
	{
		// ['macroname'][$methodref]
		$calloutslist = $this->_macro_callouts;
		foreach ($callouts as $macroname => $methodref)
		{
			$calloutlist[$macroname] = $methodref;
		}
		$this->_macro_callouts = $calloutlist;
	}
	#---------------------------[trigger callouts]-------------------------------------#
	// triggered from prepare_link_node
	protected function expand_symlink($node)
	{
		$symlinks = $this->_symlinks;
		if (isset($symlinks[$node->symlink]))
			$node->path = $symlinks[$node->symlink];
		elseif (isset($this->_symlink_handler))
			$node = call_user_func($this->_symlink_handler,$node);
		else
			$node->path = $node->symlink . ':'; // stub
		return $node;
	}
	// triggered from prepare_macro
	protected function call_macro($node)
	{
		$node->processed = FALSE;
		$callbacks = $this->_macro_callouts;
		if (isset($callbacks[$node->macroname]))
		{
			$node = call_user_func($callbacks[$node->macroname],$node);
			$node->processed = TRUE;
		}
		return $node;
	}
	// triggered from prepare_node
	protected function call_classes($node)
	{
		$callbacks = @$this->_class_callouts[$node->kind];
		if (!empty($callbacks)) 
		{
			$classes = @$node->decorator->classes;
			if (!empty($classes))
			{
				foreach ($classes as $class)
				{
					$callback = @$callbacks[$class];
					if ($callback)
					{
						$node = call_user_func($callback,$node);
					}
				}
			}
		}
		return $node;
	}
	protected function call_event($node,$event)
	{
		$events = @$this->_events[$event];
		if (!empty($events))
		{
			foreach ($events as $callback)
			{
				$node = call_user_func($callback,$node);
			}
		}
		return $node;
	}
	#----------------------------------[ init ]-----------------------------------------#
	protected function set_rules()
	{
		$rules = new StdClass();
		$proto = 'http|https|ftp|nntp|news|mailto|telnet|file|irc';
		$rules->extern = "(?P<external_address>(?P<external_proto>$proto)
			(?P<external_selector>:.*))";
		$rules->symlink = '
            (?P<internal_address> (?P<symlink>[A-Z][a-zA-Z-]+) :
            (?P<internal_selector> .* ))
        ';
		$rules->anchor = '
			(?P<anchor>\\#[a-zA-Z][\\w-]*)
		';
		$this->_rules = $rules;
	}
	protected function set_re($rules)
	{
		$this->link_re = '/' . implode('|',array($rules->extern,$rules->symlink,$rules->anchor)) . '/x';
		$this->addr_re =  '/' . implode('|',array($rules->extern,$rules->symlink)) . '/x';
	}
	#--------------------------------[ utilities ]---------------------------------------#
	protected function get_value($value,$default)
	{
		return isset($value)? $value: $default;
	}
	protected function emit_node($node) // controller
	{
		$emit = $node->kind . '_emit';
		return $this->$emit($node);
	}
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
	// text only, no html or other markup
	public function emit_node_text($node)
	{
		if ($node->kind == 'text')
			return $node->content;
		else
			return $this->emit_children_text($node);
	}
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
	// interpret address, inlcuding symlink; prepare src, alt, title attributes
	protected function prepare_image_node($node)
	{
		if (@$node->symlink)
		{
			$node = $this->expand_symlink($node);
			$node->decorator->attributes['src'] = 
				$node->path . $node->internalselector;
		}
		else
		{
			$node->decorator->attributes['src'] = 
				@$node->externaladdress;
		}
		if ($node->caption) 
			$node->decorator->attributes['alt'] = htmlspecialchars($node->caption);
		elseif ($node->content) 
			$node->decorator->attributes['alt'] = htmlspecialchars($node->content);
			
		if (@$node->title) $node->decorator->attributes['title'] = $node->title;
		$node = $this->prepare_node($node);
		return $node;
	}
	// identify anchor for special handling
	// prepare attributes - name, href, title
	protected function prepare_link_node($node)
	{
		$attributename = 'href';
		if (@$node->anchor)
		{
			if (!@$node->caption) 
			{
				$attributename = 'name';
				$node->anchor = substr($node->anchor,1);
				$node->decorator->attributes[$attributename] = $node->anchor;
			}
			else
			{
				$node->symlink = 'Anchor';
				$node = $this->expand_symlink($node);
				$node->decorator->attributes[$attributename] = $node->path . $node->anchor;
			}
		}
		elseif (@$node->symlink)
		{
			$node = $this->expand_symlink($node);
			$node->decorator->attributes[$attributename] = 
				$node->path . @$node->internalselector;
		}
		else
		{
			$node->decorator->attributes[$attributename] = 
				@$node->externaladdress;
		}
		if (@$node->title) $node->decorator->attributes['title'] = $node->title;
		$node = $this->prepare_node($node);
		return $node;
	}
	// trigger callouts; prepare output property
	protected function prepare_macro($node)
	{
		$node->output = '';
		$this->call_macro($node);
		if (($node->output == '') and ($node->caption != ''))
		{
			$node->output = $node->caption;
		}
		return $node;
	}
	// trigger class callouts; prepare attributes, classes, and styles for HTML
	//	by combining into single attribute array
	protected function prepare_node($node)
	{
		# trigger callouts
		$node = $this->call_classes($node);
		# convert input decorator attributes, values, and properties into html attributes
		$attributes = array();
		// attributes
		$attr = $this->get_value(@$node->decorator->attributes,array());
		foreach ($attr as $key => $value)
		{
			$attributes[] = $key . '="' . preg_replace('/"/','\\"',$value) . '"';
		}
		// classes
		$values = array();
		$values = $this->get_value(@$node->decorator->classes,array());
		$classes = preg_replace('/"/','\\"',implode(' ',$values)); // escape embedded double quotes
		if (!empty($classes)) $attributes[]='class="' . $classes . '"';
		// styles
		$properties = array();
		$properties = $this->get_value(@$node->decorator->properties,array());
		$styles = array();
		foreach ($properties as $key => $value)
		{
			$styles[] = $key . ':' . $value;
		}
		if (!empty($styles))
		{
			$style = implode(';',preg_replace('/"/','\\"',$styles)); // escape embedded double quotes
			$attributes[] = 'style="' . $style . '"';
		}
		if (!empty($attributes)) $node->prefix .= ' ';
		$node->attributes = $attributes;
		return $node;
	}
	#------------------------------------------------------------------------------#
	#-----------------------------[ node emitters ]--------------------------------#
	#------------------------------------------------------------------------------#
	#==============================[ document ]====================================#
	protected function document_emit($node)
	{
		// anticipate event calls
		$node->prefix = '';
		$node->postfix = '';
		$node = $this->call_event($node,'onemit');
		$node->infix = $this->emit_children($node);
		$node = $this->call_event($node,'onafteremit');
		return $node->prefix . $node->infix . $node->postfix;
	}
	#=========================[ basic processing ]=================================#
	protected function paragraph_emit($node) // b decorator "p"
	{
		$node->prefix = "\n<p";
		$node->prefixtail = ">";
		$node->infix = $this->emit_children($node);
		$node->postfix = "</p>";
		$node = $this->prepare_node($node);
		return $node->prefix . implode(' ',$node->attributes) . $node->prefixtail 
			. $node->infix . $node->postfix;
	}
	protected function text_emit($node)
	{
		return htmlspecialchars($node->content);
	}
	#================================[ core markup ]===============================#
	#--------------------------------[ basic markup ]------------------------------#
	protected function heading_emit($node) // b decorator "h"
	{
		$node->prefix = "\n<h" . $node->level;
		$node->prefixtail = ">";
		$node->infix = $this->emit_children($node);
		$node->postfix = "</h". $node->level . ">";
		$node = $this->prepare_node($node);
		return $node->prefix . implode(' ',$node->attributes) . $node->prefixtail 
			. $node->infix . $node->postfix;
	}
	protected function emphasis_emit($node)
	{
		return "<em>" . $this->emit_children($node) . "</em>";
	}
	protected function strong_emit($node)
	{
		return "<strong>" . $this->emit_children($node) . "</strong>";
	}
	protected function break_emit($node)
	{
		return "<br />\n";
	}
	protected function separator_emit($node)
	{
		return "\n<hr />";
	}
	#--------------------------------[ links ]-------------------------------------#
	// raw url not used for performance reasons
/*	protected function url_emit($node) # not used for performance reasons
	{
		$node->caption = $this->emit_children($node);
		// also available: $node->title
		$address = $node->content;
		$matches = array();
		if (preg_match($this->addr_re,$address,$matches))
		{
			@$node->internaladdress = $matches['internal_address'];
			@$node->symlink = $matches['symlink'];
			@$node->internalselector = $matches['internal_selector'];
			
			@$node->externaladdress = $matches['external_address'];
			@$node->externalprotocol = $matches['external_proto'];
			@$node->externalselector = $matches['external_selector'];
		}
		$node->prefix = "<a";
		$node->prefixtail = ">";
		$node->infix = $node->caption;
		$node->postfix = "</a>";
		$node = $this->prepare_link_node($node); 
		return $node->prefix . implode(' ',$node->attributes) . $node->prefixtail 
			. $node->infix . $node->postfix;
	}
*/
	protected function link_emit($node) // i decorator "a"
	{
		$node->caption = $this->emit_children($node);
		// also available: $node->title
		$address = $node->content;
		$matches = array();
		if (preg_match($this->link_re,$address,$matches))
		{
			@$node->anchor = $matches['anchor'];
			
			@$node->internaladdress = $matches['internal_address'];
			@$node->symlink = $matches['symlink'];
			@$node->internalselector = $matches['internal_selector'];
			
			@$node->externaladdress = $matches['external_address'];
			@$node->externalprotocol = $matches['external_proto'];
			@$node->externalselector = $matches['external_selector'];
		}
		if (($node->caption == '') and (empty($node->anchor))) $node->caption = $node->content;

		$node->prefix = "<a";
		$node->prefixtail = ">";
		$node->infix = $node->caption;
		$node->postfix = "</a>";
		$node = $this->prepare_link_node($node); 
		return $node->prefix . implode(' ',$node->attributes) . $node->prefixtail 
			. $node->infix . $node->postfix;
	}
	#--------------------------------[ images ]------------------------------------#
	protected function image_emit($node) // i decorator "i"
	{
		$node->caption = $this->emit_children($node);
		// also available: $node->title
		$address = $node->content;
		$matches = array();
		if (preg_match($this->addr_re,$address,$matches))
		{
			@$node->internaladdress = $matches['internal_address'];
			@$node->symlink = $matches['symlink'];
			@$node->internalselector = $matches['internal_selector'];
			
			@$node->externaladdress = $matches['external_address'];
			@$node->externalprotocol = $matches['external_proto'];
			@$node->externalselector = $matches['external_selector'];
		}
		
		$node->prefix = "<img";
		$node->prefixtail = "/>";
		$node = $this->prepare_image_node($node); 
		return $node->prefix . implode(' ',$node->attributes) . $node->prefixtail;
	}
	#--------------------------------[ lists ]-------------------------------------#
	protected function number_list_emit($node) // b decorator "ol"
	{
		$node->prefix = "\n<ol";
		$node->prefixtail = ">";
		$node->infix = $this->emit_children($node);
		$node->postfix = "\n</ol>";
		$node = $this->prepare_node($node);
		return $node->prefix . implode(' ',$node->attributes) . $node->prefixtail 
			. $node->infix . $node->postfix;
	}
	protected function bullet_list_emit($node) // b decorator "ul"
	{
		$node->prefix = "\n<ul";
		$node->prefixtail = ">";
		$node->infix = $this->emit_children($node);
		$node->postfix = "\n</ul>";
		$node = $this->prepare_node($node);
		return $node->prefix . implode(' ',$node->attributes) . $node->prefixtail 
			. $node->infix . $node->postfix;
	}
	protected function list_item_emit($node) // decorator "li"
	{
		$node->prefix = "\n<li";
		$node->prefixtail = ">";
		$node->infix = $this->emit_children($node);
		$node->postfix = "</li>";
		$node = $this->prepare_node($node);
		return $node->prefix . implode(' ',$node->attributes) . $node->prefixtail 
			. $node->infix . $node->postfix;
	}
	#--------------------------------[ tables ]------------------------------------#
	protected function table_emit($node) // b decorator "table"
	{
		$node->prefix = "\n<table";
		$node->prefixtail = ">";
		$node->infix = $this->emit_children($node);
		$node->postfix = "\n</table>";
		$node = $this->prepare_node($node);
		return $node->prefix . implode(' ',$node->attributes) . $node->prefixtail 
			. $node->infix . $node->postfix;
	}
	protected function table_headrow_emit($node) // b decorator "tr"
	{
		$node->prefix = "\n<tr";
		$node->prefixtail = ">";
		$node->infix = $this->emit_children($node);
		$node->postfix = "\n</tr>";
		$node = $this->prepare_node($node);
		return $node->prefix . implode(' ',$node->attributes) . $node->prefixtail 
			. $node->infix . $node->postfix;
	}
	protected function table_row_emit($node) // b decorator "tr"
	{
		$node->prefix = "\n<tr";
		$node->prefixtail = ">";
		$node->infix = $this->emit_children($node);
		$node->postfix = "\n</tr>";
		$node = $this->prepare_node($node);
		return $node->prefix . implode(' ',$node->attributes) . $node->prefixtail 
			. $node->infix . $node->postfix;
	}
	protected function table_headcell_emit($node) // b decorator "th"
	{
		$node->prefix = "\n<th";
		$node->prefixtail = ">\n";
		$node->infix = $this->emit_children($node);
		$node->postfix = "</th>";
		$node = $this->prepare_node($node);
		return $node->prefix . implode(' ',$node->attributes) . $node->prefixtail 
			. $node->infix . $node->postfix;
	}
	protected function table_cell_emit($node) // b decorator "td"
	{
		$node->prefix = "\n<td";
		$node->prefixtail = ">\n";
		$node->infix = $this->emit_children($node);
		$node->postfix = "</td>";
		$node = $this->prepare_node($node);
		return $node->prefix . implode(' ',$node->attributes) . $node->prefixtail 
			. $node->infix . $node->postfix;
	}
	#=========================[ special decorators ]===============================#
	#---------------------------[ span decoration ]--------------------------------#
	protected function span_emit($node) // i decorator "s"
	{
		$node->prefix = "<span";
		$node->prefixtail = ">";
		$node->infix = $this->emit_children($node);
		$node->postfix = "</span>";
		$node = $this->prepare_node($node);
		//$node->postfix = "</span>";
		$node->prefixtail = ">";
		return $node->prefix . implode(' ',$node->attributes) . $node->prefixtail 
			. $node->infix . $node->postfix;
	}
	#----------------------------[ block dividers ]--------------------------------#
	protected function blockdivider_emit($node) // b decorator "b"
	{
		$node->prefix = "\n<div";
		$node->prefixtail = ">";
		$node->infix = '';
		$node->postfix = "\n</div>";
		$node = $this->prepare_node($node);
		return $node->prefix . implode(' ',$node->attributes) . $node->prefixtail 
			. $node->infix . $node->postfix;
	}
	#============================[ preformatted text ]=============================#
	protected function code_emit($node) // i decorator "c"
	{
		$node->prefix = "<code";
		$node->prefixtail = ">";
		$node->infix = $node->content;
		$node->escapecontent = TRUE;
		$node->postfix = "</code>";
		$node = $this->prepare_node($node);
		if ($node->escapecontent) $node->infix = htmlspecialchars($node->infix);
		return $node->prefix . implode(' ',$node->attributes) . $node->prefixtail 
			. $node->infix . $node->postfix;
	}
	protected function preformatted_emit($node) // b decorator "pre"
	{
		$node->prefix = "\n<pre";
		$node->prefixtail = ">\n";
		$node->infix = $node->content;
		$node->escapecontent = TRUE;
		$node->postfix = "</pre>";
		$node = $this->prepare_node($node);
		if ($node->escapecontent) $node->infix = htmlspecialchars($node->infix);
		return $node->prefix . implode(' ',$node->attributes) . $node->prefixtail 
			. $node->infix . $node->postfix;
	}
	#==============================[ advanced markup ]=============================#
	#------------------------------[ block declarations ]----------------------------#
	protected function blockdef_emit($node) // declaration decorator (various)
	{
		$blocktag = $node->blocktag;
		$knowntag = TRUE;
		if (!in_array($blocktag,$this->_blocktags)) 
		{
//			$blocktag = $node->blocktag = 'div'; // default
			$blocktag .= $node->blockid;
			$knowntag = FALSE;
			$node->prefix = "\n(:$blocktag " . $node->argumentstring;
			$node->prefixtail = ":)";
			$node->postfix = "\n(:{$blocktag}end:)";
		}
		else
		{
			$node->prefix = "\n<$blocktag";
			$node->prefixtail = ">";
			$node->postfix = "\n</$blocktag>";
		}
		$node->infix = $this->emit_children($node);
		if ($knowntag)
		{
			$node = $this->prepare_node($node);
			return $node->prefix . implode(' ',$node->attributes) . $node->prefixtail 
				. $node->infix . $node->postfix;
		}
		else
		{
			return $node->prefix . $node->prefixtail 
				. $node->infix . $node->postfix;
		}
	}
	#--------------------------------[ macros ]--------------------------------#
	protected function macro_emit($node) // macro decorator
	{
		$node->caption = $this->emit_children($node);
		$node = $this->prepare_macro($node);
		if ($node->processed)
		{
			return $node->output;
		}
		else
		{
			$prefix = '<<' . $node->macroname;
			$arguments = $node->argumentstring;
			if ($node->arguments != '') $node->arguments = ' ' . $node_arguments;
			if ($node->text != '') 
				$text = '|' . $node->text;
			else
				$text = '';
			$postfix = '>>';
			return htmlspecialchars($prefix . $arguments .  $text . $postfix);
		}
	}
}
