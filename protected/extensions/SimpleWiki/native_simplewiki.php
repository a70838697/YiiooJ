<?php
/*
SimpleWiki module, version 1.0 Beta 3, January 6, 2011
copyright (c) Henrik Bechmann, 2009-2011, Toronto, Canada. All rights reserved. simplewiki.org
licence: BSD
*/
/*
Modelled after creole.py and creole2html.py 
	at http://wiki.sheep.art.pl/Wiki%20Creole%20Parser%20in%20Python 
	- author of creole.py and creole2html.py: Radomir Dopieralski
	- many of the regular expressions were based on creole.py
The notions for decorator and block declaration markup were derived in part
	from the wikistyle and directive markup developed for PmWiki (pmwiki.org)
	by its author, Patrick Michaud.
	
Two steps: 
	1. build document tree (parser)
	2. use document tree to generate html (emitter)

Markup extensions:
Arguments can be associated with most document objects 
	through decorators and declarations

Generally, elipsis (...) in the following means arguments:
	- identifier=value ("=" separator) means attribute, 
		- value can be delimited with double or single quotes
	- identifier:value (":" separator) means css style rule, 
		or command:arguments eg. zebrastripes:"white,gray"
		- value can be delimited with double or single quotes
	- value on its own means class or command (eg. zebrastripes)
		referred to as 'style class' or 'method class'
	- argument callouts for classes can be registered 
		with SimpleWiki by client software
	- selectors of callouts can vary interpretation of arguments

decorators must be left-abutted to the objects they decorate

inline decorators => %selector ...% (selector = l,i,s,c)
	- selectors = l (lower case 'L') for list, i for image, s for span, c for code.
	- if l, i or c are not immediately followed by their respective objects, 
		deocrators are returned as text
	- s creates a span
	- %% = (empty inline decorator) is optional close for span decorator
	
block decorators => |:selector ...:| (selector = h,p,ul,ol,li,table,tr,th,td,b,pre)
	- "b" is block divider and creates an empty div

block declaration => (:selector[\d]* ...:)<text>(:selector[\d]*end:)
	- block declarations, both opening and closing tags, 
		must be the first non-space characters of a line
	- opening tags can be followed by text on same line 
		to prevent generation of paragraph markup
	- can be nested based on id number ("[\d]*") 
	- native selectors:
		div, blockquote, 
		table, thead, tbody, tr, td, th, tfoot, caption,
		ul, ol, li, dl, dt, dd

macro => <<macroname ...|text>> as (generally) specified in extended creole
	- can be inline, or act as block on its own line

creole markup is used for basic markup, generally based on
	http://www.wikicreole.org/wiki/Creole1.0 
	http://www.wikicreole.org/wiki/CheatSheet
	http://www.wikicreole.org/wiki/CreoleAdditions
extensions and modifications to creole:
- raw url is not recognized separately from link for performance reasons
- url object does not accept decoration, use link instead
- table markup requires closing (trailing righmost) "|"
- link format => 
	[[url or SymbolicLink:Selector, or #anchor 
		| caption (default = url or SymbolicLink) 
		| title]]
- target anchor written as [[#anchor]]
- image format =>
	{{url or SymbolicLink:Selector
		| caption (default = url or SymbolicLink) 
		| title}}
- symbolic links can be registered by client software
- link and image captions are parsed
- heading text is parsed
- macros can be registered by client software
- no link to wikipage [[WikiPage]], use symbolic links instead
- no alternate link syntax
- no monospace, use inline span decoration instead (%s mono%...%%)
- no indented paragraphs, use blockquote or dl block delcarations instead
- no plugin extension per se, though class methods, symlinks, events, and macros 
	can be registered by client software
- superscipts, subscripts, underline, overline, and strikeout are provided with span decorator
- definition lists available with block declarations, not in basic markup

Usage:
	$wiki = new SimpleWiki($raw_text);
	echo $wiki->get_html();

For auto_quicktoc, register the prepared event before getting html:

$wiki = new SimpleWiki($markup);
$wiki->register_events(array('onemit' => array($wiki,'auto_quicktoc')));
$html = $wiki->get_html();

*/
/*
#==========================================================================
#-----------------------------[ SIMPLE WIKI ]------------------------------
#==========================================================================
// Facade and default method classes, macros, events and symlinks
/* 	
	public methods:
	$wiki = new SimpleWiki($markup)  - create object to process markup text
	->parser() - get or set parser
	->prepare($markup) - prepare for get_html(), returns $this
	->emitter() - get or set emitter
	->allow_html($bool = NULL) - get or set allow preformatted blocks to be emitted as html if so decorated
	->register_class_callbacks($callbacks) - ['nodekind']['class']=>$methodref; client responses to classes
	->register_macro_callbacks($callbacks) - ['macroname']=>$methodref; client responses to macros
	->register_events($callbacks) - ['eventname']=>$methodref; client responses to events (onemit, onafteremit)
	->register_symlinks($symlinks) - ['symlink']=>$value
	->register_symlink_handler($handler) - default handler for symlinks not registered
	->get_html() - emit html from text
	->get_metadata() - get metadata from parser
	** note that SimpleWiki registers a number of default behaviours with SimpleWikiEmitter **
*/
class Native_SimpleWiki
{
	protected $_parser;
	protected $_allow_html = TRUE;
	protected $_emitter;
	protected $_footnotes;
	protected $_footnotereferences;
	protected $_working_parser;
	
	public function __construct($text)
	{
		$this->_parser = new SimpleWiki_Parser($text);
		$this->_emitter = new SimpleWiki_Emitter();
		$this->register_class_callbacks(
			array(
				'span' => array(
					'subscript'=> array($this,'callback_span_subscript'),
					'superscript'=> array($this,'callback_span_superscript'),
					'footnote'=> array($this,'callback_span_footnote')),
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
		$this->register_macro_callbacks(
			array(
				'quicktoc' => array($this,'macro_quicktoc')
			)
		);
		$this->register_symlinks(array('Anchor'=>'','Local'=>''));
	}
	public function parser($parser = NULL)
	{
		if (!is_null($parser))
			$this->_parser = $parser;
		return $this->_parser;
	}
	public function prepare($raw)
	{
		$this->_parser->prepare($raw);
		return $this;
	}
	public function emitter($emitter = NULL)
	{
		if (!is_null($emitter))
			$this->_emitter = $parser;
		return $this->_emitter;
	}
	public function allow_html($bool = NULL)
	{
		if (!is_null($bool))
			$this->_allow_html = $bool;
		return $this->_allow_html;
	}
	public function get_html()
	{
		$dom = $this->_parser->parse();
		return $this->_emitter->emit($dom);
	}
	public function get_metadata()
	{
		return $this->_parser->metadata();
	}
	public function register_class_callbacks($callbacks) // ['nodekind']['class']=>$methodref
	{
		$emitter = $this->_emitter;
		$emitter->register_class_callouts($callbacks);
	}
	public function register_macro_callbacks($callbacks) // ['macroname']=>$methodref
	{
		$emitter = $this->_emitter;
		$emitter->register_macro_callouts($callbacks);
	}
	public function register_events($callbacks) // ['eventname']=>$methodref
	{
		$emitter = $this->_emitter;
		$emitter->register_events($callbacks);
	}
	public function register_symlinks($symlinks) // ['symlink']=>$value
	{
		$emitter = $this->_emitter;
		$emitter->register_symlinks($symlinks);
	}
	public function register_symlink_handler($handler)
	{
		$emitter = $this->_emitter;
		$emitter->register_symlink_handler($handler);
	}
	#-----------------------------[ DEFAULT CLASS CALLBACKS ]-----------------------------#
	public function callback_paragraph_nop($node)
	{
		$node->prefix = '';
		$node->prefixtail = '';
		$node->postfix = '';
		$node->decorator = new StdClass();
		return $node;
	}
	public function callback_paragraph_div($node)
	{
		$node->prefix = '<div';
		$node->prefixtail = '>';
		$node->postfix = '</div>';
		unset($node->decorator->classes[array_search('div',$node->decorator->classes)]);
		return $node;
	}
	public function callback_code_html($node)
	{
		if ($this->_allow_html)
		{
			$node->prefix = '';
			$node->prefixtail = '';
			$node->postfix = '';
			$node->escapecontent = FALSE;
			$node->decorator = new StdClass();
		}
		return $node;
	}
	public function callback_blockdef_frame($node)
	{
		if ($node->blocktag != 'div') return $node;
		$node->decorator->classes[] = 'frame';
		return $node;
	}
	public function callback_image_frame($node)
	{
		$lframeindex = array_search('lframe',$node->decorator->classes);
		$rframeindex = array_search('rframe',$node->decorator->classes);
		if ($lframeindex === FALSE) // must be rframe
		{
			unset($node->decorator->classes[$rframeindex]);
			$orientation = 'rframe';
		}
		else // must be lframe;
		{
			unset($node->decorator->classes[$lframeindex]);
			$orientation = 'lframe';
		}
		$prefix = $node->prefix;
		$prefixtail = $node->prefixtail;
		$prefix = "<div class='frame $orientation'>" . $prefix;
		$prefixtail .= "<br>{$node->caption}</div>";
		$node->prefix = $prefix;
		$node->prefixtail = $prefixtail;
		return $node;
	}
	public function callback_pre_html($node)
	{
		if ($this->_allow_html)
		{
			$node->prefix = '';
			$node->prefixtail = '';
			$node->postfix = '';
			$node->escapecontent = FALSE;
			$node->decorator = new StdClass();
		}
		return $node;
	}
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
		$footnotereference = @$footnote->decorator->attributes['footnotereference'];
		if (!empty($footnotereference))
		{
			$this->_footnotereferences[$footnotereference][] = $footnote;
		}
		// fix and return footnote link span
		$span->infix = $this->_emitter->emit_children($span);
		$span = $this->callback_span_superscript($span); // set html elements
		return $span;
	}
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
			$footnotename = @$footnote->decorator->attributes['footnotename'];
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
			$infix = $footnote->infix;
			$infix = preg_replace('/\\n/','',$infix); // to allow \\ (newline) markup in footnotes
			$markup .=
				' %c html%{{{'
				. $infix
				. "}}}\n";
		}
		// wrap footnote block
		$markup = 
			"----\n"
			. "(:div footnoteblock:)\n======Footnotes:======\n"
			. $markup
			. "(:divend:)\n";
		// allow html
		$allowhtml = $this->allow_html();
		$this->allow_html(TRUE);
		$wiki = new SimpleWiki($markup);
		$wiki->register_symlinks($this->emitter()->symlinks());
		$wiki->register_symlink_handler($this->emitter()->symlink_handler());
		$document->postfix .= $wiki->get_html();
		$this->allow_html($allowhtml);
		return $document;
	}
	public function callback_span_superscript($node)
	{
		$node->prefix = '<sup';
		$node->postfix = '</sup>';
		unset($node->decorator->classes[array_search('superscript',$node->decorator->classes)]);
		return $node;
	}
	public function callback_span_subscript($node)
	{
		$node->prefix = '<sub';
		$node->postfix = '</sub>';
		unset($node->decorator->classes[array_search('subscript',$node->decorator->classes)]);
		return $node;
	}
	public function callback_link_newwin($node)
	{
		$node->decorator->attributes['target'] = '_blank';
		unset($node->decorator->classes[array_search('newwin',$node->decorator->classes)]);
		return $node;
	}
	#-----------------------------[ DEFAULT MACROS ]----------------------------------#
	// enclosing div is given class 'quicktoc-platform'
	// to suppress heading inclusion, give it attribute toc=no
	public function macro_quicktoc($node)
	{
		$caption = $node->caption;
		if (!$caption) $caption = 'Table of contents';
		# move to root of document
		$document = $this->_parser->up_to($node,array('document'));
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
			$headingid = @$heading->decorator->attributes['id'];
			if (is_null($headingid))
			{
				$headingid = $heading->decorator->attributes['id'] = $sessionid;
			}
			$heading->decorator->attributes['contentsid'] = $sessionid;
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
					(:div1 id=quicktoc-header:)\n
						|:p div id=quicktoc-caption quicktoc-closed:|%c html%{{{" . $caption . "}}}\n
					(:div1end:)\n
					(:div1 id=quicktoc-body:)\n" 
						. $markup . "\n
					(:div1end:)\n
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
	protected function macro_quicktoc_assemble_headings($node,$contents)
	{
		static $nesting = -1;
		$nesting++;
		$isheading = false;
		if ($node->kind == 'heading')
		{
			$notoc = @$node->decorator->attributes['toc'] == 'no';
			if ($notoc)
			{
				unset($node->decorator->attributes['toc']);
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
