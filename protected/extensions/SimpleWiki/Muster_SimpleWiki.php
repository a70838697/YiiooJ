<?php
// Muster Software Copyright (c) Henrik Bechmann, Toronto, Canada 2009-2012. All rights reserved.
// See "musterlicence.txt" for licencing information.
// mustersoftware.net
/**
@mainpage Simplewiki  Markup Parser and HTML generator. 
@htmlonly <b><a target="_blank" href="http://simplewiki.org">SimpleWiki</a></b> @endhtmlonly is a wiki markup parser and HTML emitter.
 Go to @htmlonly <b><a target="_blank" href="http://simplewiki.org">website</a></b>@endhtmlonly.

@version RC1.05
@date July 9, 2011

@par Software roots:
Modelled after creole.py and creole2html.py 
	at http://wiki.sheep.art.pl/Wiki%20Creole%20Parser%20in%20Python 
	- author of creole.py and creole2html.py: Radomir Dopieralski
	- many of the regular expressions were based on creole.py
	
@par
The notions for decorator and block declaration markup were derived in part
	from the wikistyle and directive markup developed for PmWiki (pmwiki.org)
	by its author, Patrick Michaud.
	
@par Two steps: 
	-# build document tree (parser)
	-# use document tree to generate html (emitter)

@par Creole markup:
creole markup is used for basic markup, generally based on\n
	http://www.wikicreole.org/wiki/Creole1.0 \n
	http://www.wikicreole.org/wiki/CheatSheet \n
	http://www.wikicreole.org/wiki/CreoleAdditions \n
@par	
extensions and modifications to creole:
- raw url is minimally recognized separately from link for performance reasons
- table markup requires closing (trailing righmost) "|"
- link format => 
	[[url or SymbolicLink:Selector, or \#anchor\n
		| caption (default = url or SymbolicLink)\n 
		| title]]\n
- target anchor written as [[\#anchor]]
- image format =>\n
	{{url or SymbolicLink:Selector\n
		| caption (default = url or SymbolicLink) \n
		| title}}\n
- symbolic links can be registered by client software
- link and image captions are parsed
- heading text is parsed
- macros can be registered by client software
- no link to wikipage [[WikiPage]], use symbolic links instead, or register rawlink handler
- no alternate link syntax
- no monospace, use inline span decoration instead (\%s mono%...%%)
- for indented paragraphs, use dl markup (:), or blockquote or dl block declarations or instead
- no plugin extension per se, though class methods, symlinks, events, and macros 
	can be registered by client software
- superscipts, subscripts, underline, overline, and strikeout are provided with span decorator

@par Markup extensions:
Arguments can be associated with most document objects 
	through decorators and declarations
@par
	- identifier=value ("=" separator) means attribute, 
		- value can be delimited with double or single quotes
	- identifier:value (":" separator) means css style rule
		- value can be delimited with double or single quotes
	- value on its own means class or command (eg. zebrastripes)
		referred to as 'class method'
	- callouts for classes can be registered 
		with SimpleWiki by client software
	- element selectors of callouts can vary interpretation of arguments
@par
decorators must be left-abutted to the objects they decorate

@par
Generally, elipsis (...) in the following means arguments:

@par
inline decorators => \%selector ...% (selector = l,i,s,c)
	- selectors = l (lower case 'L') for list, i for image, s for span, c for code.
	- if l, i or c are not immediately followed by their respective objects, 
		deocrators are returned as text
	- s creates a span
	- %% = (empty inline decorator) is optional close for span decorator

@par	
block decorators => |:selector ...:| (selector = h,p,ul,ol,li,table,tr,th,td,b,pre)
	- "b" is block divider and creates an empty div
	
@par
block declaration => (:selector[\\d]* ...:)\<text\>(:selector[\\d]*end:)
	- block declarations, both opening and closing tags, 
		must be the first non-space characters of a line
	- opening tags can be followed by text on same line 
		to prevent generation of paragraph markup
	- can be nested based on id number [\\d]*
	- native selectors:\n
		div, blockquote, # blocks\n
		table, thead, tbody, tr, td, th, tfoot, caption, \#tables\n
		ul, ol, li, dl, dt, dd, \#lists\n
		dlmodule, dlobject, dlsettings \#dlml (see dlml.org)
@par
macro => <<macroname ...|text>> as (generally) specified in extended creole
	- can be inline, or act as block on its own line

@par Usage:
@verbatim
$wiki = new SimpleWiki($raw_text);
$html = $wiki->get_html();
@endverbatim
@par 
Or for iterative usage:
@verbatim
$wiki = new SimpleWiki(); //once
$wiki->prepare($raw_text);
$html = $wiki->get_html();
@endverbatim
or:
@verbatim
$wiki = new SimpleWiki();
$html = $wiki->get_html($raw_text);
@endverbatim
@par
For auto_quicktoc, register the prepared event before getting html:

@par
@verbatim
$wiki = new SimpleWiki($markup);
$wiki->register_events(array('onemit' => array($wiki,'auto_quicktoc')));
$html = $wiki->get_html();
@endverbatim

*/
#==========================================================================
#-----------------------------[ SIMPLE WIKI ]------------------------------
#==========================================================================
/**
	Facade, and default method classes, macros, events and symlinks
	public methods.
	
	$wiki = new SimpleWiki($markup)  - create object to process markup text
	
	note that SimpleWiki registers a number of default behaviours with SimpleWikiEmitter
*/
class Muster_SimpleWiki
{
	protected $_parser;
	protected $_allow_html = TRUE;
	protected $_emitter;
	protected $_footnotes;
	protected $_footnotereferences;
	protected $_working_parser;
	
/**@{ @name constructor */
/** The constructor for Simplewiki. The constructor does the following:
	- invokes internal instances of the parser and emitter
	- registers standard callbacks, macros, and symlinks
	
	@param string $text the markup text to be processed.
	@return void
	
	@sa prepare\n
		register_class_callbacks\n
		register_property_callbacks\n
		register_macro_callbacks\n
		register_symlinks
	
	@par Specific operations:
*/
	public function __construct($text = NULL)
	{
		/// instantiate and save new parser
		$this->_parser = new SimpleWiki_Parser($text);
		/// instantiate and save new emitter
		$this->_emitter = new SimpleWiki_Emitter();
		/// register standard class callbacks 
		$this->register_class_callbacks(
			array(
				'span' => array(
					'subscript'=> array($this,'callback_span_subscript'),
					'superscript'=> array($this,'callback_span_superscript'),
					'footnote'=> array($this,'callback_span_footnote'),
					'comment'=> array($this,'callback_span_comment')),
				'link' => array(
					'newwin' => array($this,'callback_link_newwin')),
				'image' => array(
					'lframe'=> array($this,'callback_image_frame'),
					'rframe'=> array($this,'callback_image_frame')),
				'paragraph' => array(
					'nop'=> array($this,'callback_paragraph_nop'),
					'div'=> array($this,'callback_paragraph_div')),
				'blockdef' => array(
					'lframe'=> array($this,'callback_blockdef_frame'),
					'rframe'=> array($this,'callback_blockdef_frame')),
				'preformatted' => array(
					'html'=> array($this,'callback_pre_html')),
				'code' => array(
					'html'=> array($this,'callback_code_html'))
			)
		);
		/// register standard macro callbacks 
		$this->register_macro_callbacks(
			array(
				'quicktoc' => array($this,'macro_quicktoc')
			)
		);
		/// register standard symlinks 
		$this->register_symlinks(array('Anchor'=>'','Local'=>''));
	}
	public function __clone()
	{
		$this->_parser = clone $this->_parser;
		$this->_emitter = clone $this->_emitter;
		$this->_working_parser = NULL;
	}
/**@}*/
/**@{ @name settings methods*/
/** Get or set permission to allow html in markup. ...from within "<pre>" elements. 
	@param boolean $bool true allows html (default), false disallows
	@return boolean

	@todo trace and document how allow_html is enforced
*/
	public function allow_html($bool = NULL)
	{
		if (!is_null($bool))
			$this->_allow_html = $bool;
		return $this->_allow_html;
	}
/** Set callbacks for class methods.
	@param array $callbacks [$nodetype][$classname]=>$methodref\n
	$methodref is typically <i> array(objectref,'methodname') </i>
	@return void

	$classname can be any valid class string
	
	Callback methods are passed a SimpleWiki_DocNode object, and are expected to manipulate
		and return that object
		
	This method is a facade for the emitter object (SimpleWiki_Emitter), to which the callbacks are passed directly.

	@sa Muster_SimpleWiki_DocNode::$type for $nodetype options
	@sa Muster_SimpleWiki_Emitter::register_class_callouts
*/
	public function register_class_callbacks($callbacks)
	{
		$emitter = $this->_emitter;
		$emitter->register_class_callouts($callbacks);
	}
/** Set callbacks for property methods.
	@param array $callbacks [$nodetype][$propertyname]=>$methodref\n
	$methodref is typically <i> array(objectref,'methodname') </i>
	@return void

	$propertyname can be any valid class string
	
	Callback methods are passed a SimpleWiki_DocNode object, and are expected to manipulate
		and return that object
		
	This method is a facade for the emitter object (SimpleWiki_Emitter), to which the callbacks are passed directly.

	@sa Muster_SimpleWiki_DocNode::$type for $nodetype options
	@sa Muster_SimpleWiki_Emitter::register_property_callouts
*/
	public function register_property_callbacks($callbacks)
	{
		$emitter = $this->_emitter;
		$emitter->register_property_callouts($callbacks);
	}
/** Set specific symlinks for link and image markup.
	@param array $symlinks  [$symlink]=>$value\n
	$value should be an absolute or relative html link (href/src)
	@return void
	
	$symlink can be any string of letters with a leading uppercase letter.
	
	This method is a facade for the emitter object (SimpleWiki_Emitter), to which the callbacks are passed directly.
	
	@sa register_symlink_handler
	@sa Muster_SimpleWiki_Emitter::register_symlinks
*/
	public function register_symlinks($symlinks)
	{
		$emitter = $this->_emitter;
		$emitter->register_symlinks($symlinks);
	}
/** Set symlink handler, to handle any number of symlinks.
	@param method $handler typically in the form <i> array(objectref,'methodname') </i>
	@return void

	$handler is handed a SimpleWiki_DocNode object, and is expected to manipulate
		and return that object.
	
	This method is a facade for the emitter object (SimpleWiki_Emitter), to which the callbacks are passed directly.
	
	@sa register_symlinks
	@sa Muster_SimpleWiki_Emitter::register_symlink_handler
*/
	public function register_symlink_handler($handler)
	{
		$emitter = $this->_emitter;
		$emitter->register_symlink_handler($handler);
	}
	/**
	Set rawlink handler. The rawlink handler is called for any links that are not anchors, external links (with recognized prototcol), or symlinks.
	@param method $handler typically in the form <i> array(objectref,'methodname') </i>
	@return void
	*/
	public function register_rawlink_handler($handler)
	{
		$emitter = $this->_emitter;
		$emitter->register_rawlink_handler($handler);
	}
	/**
	Set charfilter handler. The charfilter handler is called by the emitter where normally htmlspecialchars
	would be applied (the default), allowing clients to substitute alternate or custom character filters.
	@param method $handler typically in the form <i> array(objectref,'methodname') </i>
	@return void
	*/
	public function register_charfilter_handler($handler)
	{
		$emitter = $this->_emitter;
		$emitter->register_charfilter_handler($handler);
	}
	/**
	Set blockdef handler. The blockdef handler is called for any known blockdef, with a param of the blockdef node.
	@param method $handler typically in the form <i> array(objectref,'methodname') </i>
	@return void
	*/
	public function register_blockdef_handler($handler)
	{
		$emitter = $this->_emitter;
		$emitter->register_blockdef_handler($handler);
	}
/** Registers macro callbacks.
	@param array $callbacks in the form <i>[$macroname]=>$methodref</i>\n
	$methodref is typically <i> array(objectref,'methodname') </i>
	@return void
	
	$macro callbacks are handed a SimpleWiki_DocNode object, and are expected to manipulate
		and return that object.
		
	This method is a facade for the emitter object (SimpleWiki_Emitter), to which the callbacks are passed directly.
	@sa Muster_SimpleWiki_Emitter::register_macro_callouts
*/
	public function register_macro_callbacks($callbacks) 
	{
		$emitter = $this->_emitter;
		$emitter->register_macro_callouts($callbacks);
	}
/** Registers events.
	@param array $callbacks in the form <i>[$eventname]=>$methodref</i>\n
	$methodref is typically <i> array(objectref,'methodname') </i>
	@return void
	
	$eventname can be 'onemit' or 'onafteremit'
	
	$event callbacks are handed a SimpleWiki_DocNode object, and are expected to manipulate
		and return that object.
		
	This method is a facade for the emitter object (SimpleWiki_Emitter), to which the callbacks are passed directly.
	@sa Muster_SimpleWiki_Emitter::register_events
*/
	public function register_events($callbacks)
	{
		$emitter = $this->_emitter;
		$emitter->register_events($callbacks);
	}

/**@}*/
/**@{ @name operational methods*/
/** Prepare the markup for processing.
	@param string $raw (markup text)
	@return object $this (so the method can be chained)

	This method is a facade for the parser object (SimpleWiki_Parser), to which the markup is passed directly.
	@sa Muster_SimpleWiki_Parser::prepare
*/
	public function prepare($raw)
	{
		$this->_parser->prepare($raw);
		return $this;
	}
/**	Generate html from the markup text, as passed to the constructor, or to prepare.
	@sa __construct, prepare
*/
	public function get_html($markup = NULL)
	{
		if ($markup) $this->prepare($markup);
		global $profiles;
		$profile = array();
		$profile['parse']['start'] = microtime(true);
		$dom = $this->_parser->parse();
		$profile['parse']['end'] = microtime(true);
		$profiles[] = $profile;
		$profile = array();
		$profile['emit']['start'] = microtime(true);
		$retval = $this->_emitter->emit($dom);
		$profile['emit']['end'] = microtime(true);
		$profiles[] = $profile;
		return $retval;
	}
/**@}*/

/**@{ @name accessor methods */

/** Get or set the instance of the parser used by Simplewiki.
	The parser method allows the caller to set a different instance of the parser, 
	or to obtain the current instance of the parser.
	@param object $parser class SimpleWiki_Parser
	@return object, class SimpleWiki_Parser
	@sa Muster_SimpleWiki_Parser
*/
	public function parser($parser = NULL)
	{
		if (!is_null($parser))
			$this->_parser = $parser;
		return $this->_parser;
	}
/** Get or set the instance of the emitter used by Simplewiki.
	The emitter method allows the caller to set a different instance of the emitter, 
	or to obtain the current instance of the emitter.
	@param $emitter object, optional, class SimpleWiki_Emitter
	@sa Muster_SimpleWiki_Emitter
*/
	public function emitter($emitter = NULL )
	{
		if (!is_null($emitter))
			$this->_emitter = $parser;
		return $this->_emitter;
	}
/** Get metadata from prepared markup.
	Gets the metadata from the metadata section of the parsed markup.
	@return object
	@sa Muster_SimpleWiki_Parser::metadata
*/
	public function get_metadata()
	{
		return $this->_parser->metadata();
	}
/** Get marker data from prepared markup.
	@return object
	@sa Muster_SimpleWiki_Parser::markerdata
*/
	public function get_markerdata()
	{
		return $this->_parser->markerdata();
	}
/** Get markup from prepared markup.
	@return string
	@sa Muster_SimpleWiki_Parser::preprocessed_markup
*/
	public function get_preprocessed_markup()
	{
		return $this->_parser->preprocessed_markup();
	}
/**@}*/

	#-----------------------------[ STANDARD CLASS CALLBACKS ]-----------------------------#
	
/**@{ @name standard class callbacks

Standard class callbacks are registered in the constructor (__construct) with register_class_callbacks

*/
/** Standard callback |:p nop:| (no paragraph).
	@param object $node class SimpleWiki_DocNode
	@return object SimpleWiki_DocNode
*/
	public function callback_paragraph_nop($node)
	{
		$node->opentag_head = '';
		$node->opentag_tail = '';
		$node->closetag = '';
		$node->decoration = new StdClass();
		return $node;
	}
/** Standard callback |:p div:| (paragraph to div).
	@param object $node class SimpleWiki_DocNode
	@return object SimpleWiki_DocNode
*/
	public function callback_paragraph_div($node)
	{
		$node->opentag_head = '<div';
		$node->opentag_tail = '>';
		$node->closetag = '</div>';
		unset($node->decoration->classes[array_search('div',$node->decoration->classes)]);
		return $node;
	}
/** Standard callback %c html% code to html.
	@param object $node class SimpleWiki_DocNode
	@return object SimpleWiki_DocNode
*/
	public function callback_code_html($node)
	{
		if ($this->_allow_html)
		{
			$node->opentag_head = '';
			$node->opentag_tail = '';
			$node->closetag = '';
			$node->escapecontent = FALSE;
			$node->decoration = new StdClass();
		}
		return $node;
	}
/** Standard callback (:div frame:) frame.
	@param object $node class SimpleWiki_DocNode
	@return object SimpleWiki_DocNode
*/
	public function callback_blockdef_frame($node)
	{
		if ($node->blocktag != 'div') return $node;
		$node->decoration->classes[] = 'frame';
		return $node;
	}
/** Standard callback left or right frame. \%i lframe%{{... or  \%i lframe%{{...
	@param object $node class SimpleWiki_DocNode
	@return object SimpleWiki_DocNode
*/
	public function callback_image_frame($node)
	{
		$lframeindex = array_search('lframe',$node->decoration->classes);
		$rframeindex = array_search('rframe',$node->decoration->classes);
		if ($lframeindex === FALSE) // must be rframe
		{
			unset($node->decoration->classes[$rframeindex]);
			$orientation = 'rframe';
		}
		else // must be lframe;
		{
			unset($node->decoration->classes[$lframeindex]);
			$orientation = 'lframe';
		}
		$opentag_head = $node->opentag_head;
		$opentag_tail = $node->opentag_tail;
		$opentag_head = "<div class='frame $orientation'>" . $opentag_head;
		$opentag_tail .= "<br>{$node->caption}</div>";
		$node->opentag_head = $opentag_head;
		$node->opentag_tail = $opentag_tail;
		return $node;
	}
/** Standard callback block html. |:pre html:|{{{... pre to html.
	@param object $node class SimpleWiki_DocNode
	@return object SimpleWiki_DocNode
*/
	public function callback_pre_html($node)
	{
		if ($this->_allow_html)
		{
			$node->opentag_head = '';
			$node->opentag_tail = '';
			$node->closetag = '';
			$node->escapecontent = FALSE;
			$node->decoration = new StdClass();
		}
		return $node;
	}
/** Standard callback \%s comment%...%%.
	@param object $node class SimpleWiki_DocNode
	@return object SimpleWiki_DocNode
*/
	public function callback_span_comment($node)
	{
		$node->opentag_head = '';
		$node->opentag_tail = '';
		$node->closetag = '';
		$node->decoration = new StdClass();
		$node->elementcontent = '';
		return $node;
	}
/** Standard callback \%s footnote%...%%.
	@param object $node class SimpleWiki_DocNode
	@return object SimpleWiki_DocNode
*/
	public function callback_span_footnote($node)
	{
		$footnotes = $this->_footnotes;
		if (empty($footnotes)) // inititalize footnote system
		{
			$footnotes = new StdClass;
			$footnotes->count = 0;
			$footnotes->list = array();
			if (empty($this->_working_parser))
				$this->_working_parser = new SimpleWiki_Parser('');
			$this->register_events(
				array('onafteremit' =>
					array($this,'render_footnotes')));
		}
		// set aside footnote
		$footnote = $footnotes->list[] = $node;
		$count = $footnote->id = ++$footnotes->count;
		// generate markup for link
		$parser = $this->_working_parser;
		$markup = 
			'%s superscript%[[#footnotemarker'
			. $count
			. ']][[#footnote'
			. $count
			. '|['
			. $count.']]]%%';
		$dom = $parser->prepare($markup)->parse();
		// replace footnote body with reference in body of text
		$span = $dom->children[0]->children[0]; // document/paragraph
		$span->parent = $footnote->parent;
		$footnote->parent = NULL;
		$this->_footnotes = $footnotes;
		// create lookups for referenced footnotes
		$footnote->rendered = FALSE;
		$footnotereference = @$footnote->decoration->attributes['footnotereference'];
		if (!empty($footnotereference))
		{
			$this->_footnotereferences[$footnotereference][] = $footnote;
		}
		// fix and return footnote link span
		$span->elementcontent = $this->_emitter->emit_children($span);
		$span = $this->callback_span_superscript($span); // set html elements
		return $span;
	}
/** Standard callback \%s superscript%.
	@param object $node class SimpleWiki_DocNode
	@return object SimpleWiki_DocNode
*/
	public function callback_span_superscript($node)
	{
		$node->opentag_head = '<sup';
		$node->opentag_tail = '>';
		$node->closetag = '</sup>';
		unset($node->decoration->classes[array_search('superscript',$node->decoration->classes)]);
		return $node;
	}
/** Standard callback \%s subscript%.
	@param object $node class SimpleWiki_DocNode
	@return object SimpleWiki_DocNode
*/
	public function callback_span_subscript($node)
	{
		$node->opentag_head = '<sub';
		$node->opentag_tail = '>';
		$node->closetag = '</sub>';
		unset($node->decoration->classes[array_search('subscript',$node->decoration->classes)]);
		return $node;
	}
/** Standard callback \%l newwin%[[...
	@param object $node class SimpleWiki_DocNode
	@return object SimpleWiki_DocNode
*/
	public function callback_link_newwin($node)
	{
		$node->decoration->attributes['target'] = '_blank';
		$node->decoration->attributedelimiters['target'] = '"';
		unset($node->decoration->classes[array_search('newwin',$node->decoration->classes)]);
		return $node;
	}
/**@}*/
	
	#-----------------------------[ DEFAULT MACROS ]----------------------------------#
/**@{@name standard macros */
/** quicktoc macro.
	enclosing div is given class 'quicktoc-platform'\n
	to suppress heading inclusion, give header element the attribute toc=no
*/
	public function macro_quicktoc($node)
	{
		$caption = $node->caption;
		if (!$caption) $caption = 'Table of Contents';
		# move to root of document
		$document = $this->_parser->get_selected_ancestor($node,array('document'));
		# collect all headings
		$contents = array();
		$contents = $this->macro_quicktoc_assemble_headings($document,$contents);
		# set data for content line items
		$contentheadings = array();
		$count = 0;
		foreach ($contents as $heading)
		{
			// assign id
			$count++;
			$sessionid = 'heading' . $count;
			$headingid = @$heading->decoration->attributes['id'];
			if (is_null($headingid))
			{
				$headingid = $heading->decoration->attributes['id'] = $sessionid;
				$heading->decoration->attributedelimiters['id'] = '"';
			}
			$heading->decoration->attributes['contentsid'] = $sessionid;
			$heading->decoration->attributedelimiters['contentsid'] = '"';
			$contentheading = new StdClass();
			$contentheading->id = $headingid;
			// assign text
			$contentheading->text = $this->_emitter->emit_node_text($heading);
			// assign level
			$contentheading->level = $heading->level;
			$contentheading->nesting = $heading->nesting;
			$contentheadings[] = $contentheading;
		}
		# generate markup for table of contents
		if (!empty($contentheadings))
		{
			$markup = '';
			// calculate relative depth, beginning with 1
			$contentdepth = 1;
			$previouscontentdepth = $contentdepth;
			$contentdepthstack = array();
			$flooroffset = 0; // lowest depth, controlled by nesting
			$flooroffsetstack = array();
			// make sure there is no change for first item 
			//	- markup requires starting depth of 1
			$previouslevel = $contentheadings[0]->level;
			$previouslevelstack = array();
			$previousnestinglevel = $contentheadings[0]->nesting; 
			// process collected elements
			foreach ($contentheadings as $contentheading)
			{
				// calculate depth
				$level = $contentheading->level;
				$nestinglevel = $contentheading->nesting;
				if ($nestinglevel > $previousnestinglevel)
				{ // save state
					array_push($flooroffsetstack,$flooroffset);
					array_push($contentdepthstack,$contentdepth);
					array_push($previouslevelstack,$previouslevel);
					// set floor
					$flooroffset = $contentdepth;
				}
				elseif ($nestinglevel < $previousnestinglevel)
				{ // restore state
					if (!empty($flooroffsetstack))
					{
						$flooroffset = array_pop($flooroffsetstack);
						$contentdepth = array_pop($contentdepthstack);
						$previouslevel = array_pop($previouslevelstack);
					}
				}
				if ($level > $previouslevel) 
					$contentdepth++;
				elseif ($level < $previouslevel) 
					$contentdepth--;
				$contentdepth = min($level,$contentdepth);
				$contentdepth = max($contentdepth,1);
				$previouslevel = $level;
				$previousnestinglevel = $nestinglevel;
				$previouscontentdepth = $contentdepth;
				// generate markup
				$markup .= 
					str_repeat('*',$contentdepth + $flooroffset) 
					. '[[#'
					. $contentheading->id 
					. '|' 
					. $contentheading->text
					. "]]\n";
			}
			// enclose markup
			$caption = preg_replace('/\\n/','',$caption); // to allow \\ (newline) markup in caption
			$markup = 
"(:div id=quicktoc-platform:)\n
	(:div id=quicktoc-header:)\n
		|:p div id=quicktoc-caption quicktoc-closed:|%c html%{{{" . $caption . "}}}\n
	(:divend:)\n
	(:div id=quicktoc-body:)\n" 
		. $markup . "\n
	(:divend:)\n
(:divend:)\n";
			# generate html for table of contents
			// allow html
			$allowhtml = $this->allow_html();
			$this->allow_html(TRUE);
			$wiki = new SimpleWiki($markup);
			$wiki->register_symlinks($this->emitter()->symlinks());
			$wiki->register_symlink_handler($this->emitter()->symlink_handler());
			$node->output = $wiki->get_html();
			$this->allow_html($allowhtml);
		}
		return $node;
	}
/**@}*/
	protected function macro_quicktoc_assemble_headings($node,$contents)
	{
		static $nesting = -1;
		$nesting++;
		$isheading = false;
		if ($node->type == SimpleWiki_DocNode::HEADING)
		{
			$notoc = @$node->decoration->attributes['toc'] == 'no';
			if ($notoc)
			{
				unset($node->decoration->attributes['toc']);
			}
			else
			{
				$isheading = TRUE;
				$node->nesting = $nesting;
				$contents[] = $node;
			}
		}
		if (!$isheading)
		{
			$children = $node->children;
			if (!empty($children))
			{
				foreach ($children as $child)
				{
					$contents = $this->macro_quicktoc_assemble_headings($child,$contents);
				}
			}
		}
		$nesting--;
		return $contents;
	}
	#----------------------[ NATIVE EVENT CALLBACKS ]--------------------------#
/**@{ @name standard event methods */	
	# triggered at onafteremit event...
	public function render_footnotes($document)
	{
		$footnotes = $this->_footnotes->list;
		$markup = '';
		foreach ($footnotes as $footnote)
		{
			if ($footnote->rendered) continue; // has been rendered as reference to other
			// render base footnote
			$id = $footnote->id;
			$markup .=
				'* [[#footnotemarker' . $id . '|^]][' . $id .'][[#footnote' . $id . ']]';
			$footnotename = @$footnote->decoration->attributes['footnotename'];
			if (!empty($footnotename)) // possibly referenced by others
			{
				$references = @$this->_footnotereferences[$footnotename];
				if (!empty($references)) // add references
				{
					foreach ($references as $reference)
					{
						$ref = $reference->id;
						$markup .= 
							' [[#footnotemarker' 
							. $ref 
							. '|^]][' 
							. $ref 
							.'][[#footnote' 
							. $ref 
							. ']]';
						$reference->rendered=true;
					}
				}
			}
			$elementcontent = $footnote->elementcontent;
			$elementcontent = preg_replace('/\\n/','',$elementcontent); // to allow \\ (newline) markup in footnotes
			$markup .=
				' %c html%{{{'
				. $elementcontent
				. "}}}\n";
		}
		// wrap footnote block
		$markup = 
			"\n|:b divider:|\n----\n"
			. "(:div footnoteblock:)\n======Footnotes:======\n"
			. $markup
			. "(:divend:)\n";
		// allow html
		$allowhtml = $this->allow_html();
		$this->allow_html(TRUE);
		$wiki = new SimpleWiki($markup);
		$wiki->register_symlinks($this->emitter()->symlinks());
		$wiki->register_symlink_handler($this->emitter()->symlink_handler());
		$document->closetag .= $wiki->get_html();
		$this->allow_html($allowhtml);
		return $document;
	}
	// $wiki->register_events(array('onemit' =>array($wiki,'auto_quicktoc')));
	public function auto_quicktoc($document)
	{
		$markup = '<<quicktoc>>';
		if (empty($this->_working_parser))
			$this->_working_parser = new SimpleWiki_Parser('');
		$parser = $this->_working_parser;
		$dom = $parser->prepare($markup)->parse();
		$tocnode = $dom->children[0];
		$tocnode->parent = $document;
		array_unshift($document->children,$tocnode);
		return $document;
	}
}
