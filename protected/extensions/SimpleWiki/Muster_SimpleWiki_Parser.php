<?php
/**
// Muster Software Copyright (c) Henrik Bechmann, Toronto, Canada 2009-2012. All rights reserved.
// See "musterlicence.txt" for licencing information.
// mustersoftware.net
@version 1.0
@date February 9, 2012
*/
#==========================================================================
#-----------------------------[ PARSER ]----------------------------------
#==========================================================================
/** Parses wiki markup into tree of document nodes.
	public methods:
	$parser = new SimpleWikiParser($text) - create instance of parser for text
	- parse() - main method, parses text, returns document object model (tree)
	- prepare($markup) - reset with new markup
	- metadata() - set with ```## arguments on first line
	- argchars($argchars = NULL) - get or set characters allowed for arguments
	- get_selected_ancestor($node, $types)
	- parse_arguments($decorator)
	- for debugging:
		- display_regex()
		- display_dom($dom)
	- see _create_node($preg_groups) below, which is the controller for the parsing process.
*/
class Muster_SimpleWiki_Parser
{
/**@{ @name Regular expression properties 

$_rules holds the core rules combined into the applied regular expressions. 
The rest of these properties hold the applied regular expressions used by the dom creation (*_node) methods)
	@sa _set_rules
	@sa _set_re
*/
/** Object that holds basic rules to be combined into applied regular expressions.
	@sa _set_rules
*/
	protected $link_inline_re; ///< link inline parse
	protected $image_inline_re; ///< image inline parse
	protected $item_inline_re; ///< list inline item parse
	protected $defitem_inline_re; ///< definition inline item parse
	protected $cell_re; ///< quick table cell parse
	protected $decorator_re; ///< parse anything's decorator arguments
	protected $tablerow_setaside_re; ///< setaside for table row
	protected $pre_escape_re; ///< remove escape from pre
	protected $_rules; ///< object containing rules which combine into applied regular expressions
	protected $block_re; ///< block parse
	protected $inline_re; ///< inline parse
/**@}*/

/**@{ @name DOM control properties */
	/// the current node being processed
	protected $_curnode;
	/// the leaftextnode, which holds presentation text (no markup), and is alwasy of type SimpleWiki_DocNode::TEXT
	protected $_leaftextnode;
	/// the root document node
	protected $_root;
/**@}*/
/**@{ @name Data properties */
	/// the original souce text to be processed
	protected $_raw;
	/// the original souce text after preprocessing, but before parsing
	protected $_preprocessed_raw;
	/// character set allowed for decorators (class names, properties and their values, attribuets and their values
	protected $_argchars = '^()'; // no parenthesis
//	protected $_argchars = '\\w\\s:="\'%\\\#.-'; // for decorators, notably parentheses omitted for security
	/// metadata object possibly generated from first line of markup ("```## ...")
	protected $_metadata; // from first line ```## arguments
	/// from preprocessing, an object with textlength, markercount, markers
	protected $_markerdata; // from preprocessing
	/// Transient: from preprocessing, an array with marker objects, later folded into _markerdata.
	/// Each marker object contains offset, name, decoration.
	/// Marker objects are indexed through integer in order, as well as by name index
	protected $_markers;
/**@}*/
/**@{ @name Create Parser */
/**
	Sets rules, sets regular expressions, and prepares any passed text
	@param string $text markup text to be parsed (can be NULL)
	@return void
*/
	public function __construct($text)
	{
		$this->_set_rules();
		$this->_set_re($this->_rules);
		$this->prepare($text);
	}
/**	Set rules for parsing.
	Creates a generic object, addes rules to it, and saves it to $this->_rules.
	
	Rules are combined into applied regular expressions in _set_re. 
	
	The first subgroup name of each rule matches the first part of the node method name 
	called by _create_node to create a dom node for the markup fragment.
	
	@return void
	@par Specific rules:
*/
	protected function _set_rules()
	{
		// the first group name of each rule, if set, is used by controller (_create_node($preg_groups)) 
		// for further processing of parsed data
		$rules = new StdClass();
		$argchars = $this->_argchars;
		#================================[ basic processing ]=================================#
		# no explicit action by user (other than include blank lines between blocks)
//		$rules->char =  '(?P<char> . )'; // slower, but allows capture of raw url's
//		$rules->char =  '(?P<char> ([\\w\\s]+$|. ))'; //faster, but misses raw url's, twice as slow as markup filter
		// markup characters: =/*-\[]|#{}%:<>~
		/// \b char: characters except those that start inline markup: "/*\[{%<~" (emphasis, strong, linebreak, link, image/code, span, macro, escape), 
		/// 	char is default expression for inline string.
		$rules->char =  '(?P<char> ([^\/\*\\\[{%<~]+|.))'; // characters before next inline markup start, or markup start char
		/// \b blankline: empty line that separates blocks, especially paragraphs
		$rules->blankline = '(?P<blankline> ^ \s* $ )'; 
		/// \b paragraph: text not otherwise parsed with block parsing - handed over to inline parsing
		$rules->paragraph = '(?P<paragraph>
			^\s*(\|:p\s+(?P<paragraph_decorator>['.$argchars.']+?):\|)?(?P<text_chars>.+)
			|
			(?P<text_charstream>.+) 
		)'; 
		#================================[ core markup ]===============================#
		#--------------------------------[ basic markup ]------------------------------#
		/// \b headings: =*
		$rules->heading = '(?P<heading>
            ^\s*(\|:h\s+(?P<heading_decorator>['.$argchars.']+?):\|)? \s*
            (?P<heading_head>={1,6}) \s*
            (?P<heading_text> .*? ) \s*
            (?P<heading_tail>=*) \s*
            $
        )';
		/// \b emph: emphasis //
		/// there must be no ":" in front of the "//"
		/// or there must be whitespace after the forward slashes.
		/// This avoids italic rendering in urls with unknown protocols
		$rules->emph = '(?P<emph> (?<!~)\/\/ )'; 
		/// \b strong: "**"
		$rules->strong = '(?P<strong> \*\* )';
		/// \b linebreak: "\\"
		$rules->linebreak = '(?P<linebreak> \\\\\\\\ )';
		// \b horizontalrule: horizontal rule: "-----*"
		$rules->horizontalrule = '(?P<horizontalrule>
			^ \s* -----* \s* $ 
		)';
		#--------------------------------[ links ]-------------------------------------#
		/// \b link: marked links: '[[...]]'
		$rules->link = '(?P<link>
            (%l\s+(?P<link_decorator>['.$argchars.']+?)%)?
			\[\[
            (?P<link_target>\S+?) \s*
            (\| \s* (?P<link_text>.*?) \s* (\| \s* (?P<link_title>[^|\]}]+))? \s*)?
            \]\](?!]) # allow embedded "]"
		)';
		#--------------------------------[ images ]-------------------------------------#
		/// \b image: '{{...}}'
		$rules->image = '(?P<image>
            (%i\s+(?P<image_decorator>['.$argchars.']+?)%)?
			{{
            (?P<image_target>\S+?) \s*
            (\| \s* (?P<image_text>.*?) \s* (\| \s* (?P<image_title>[^|\]}]+))? \s*)?
            }}
		)';
		#--------------------------------[ lists ]-------------------------------------#
		/// \b deflist: definition list ':* ... :: ... '.
		/// Matches the whole list, separate items are parsed later.
		$rules->deflist = '(?P<deflist>
            ^ \s*
			(\|:d[ltd]\s+(['.$argchars.']+?):\|){0,3}
			(:(?=[^:])).* $ # only one opening list marker allowed
            (\n [\t\x20]*
			(\|:d[ltd]\s+(['.$argchars.']+?):\|){0,3}
			:+.* $ 
			)*
        )'; 
		/// \b defitem: definition list item: Matches single list item. Isolates decorators, markup, term, and definition
		$rules->defitem = '(?P<defitem>
            ^\s*
			(\|:dl\s+(?P<deflist_decorator>(['.$argchars.']+?)):\|)?
			(\|:dt\s+(?P<defterm_decorator>(['.$argchars.']+?)):\|)?
			(\|:dd\s+(?P<defdesc_decorator>(['.$argchars.']+?)):\|)?
            (?P<defitem_head> :+) \s*
            ((?P<defterm_text> .*?)(?<!~)::)?
            (?P<defdesc_text> .*?)
            \s*$
        )';
		/// \b list: ordered or unordered lists. Matches the whole list, separate items are parsed later. 
		/// The list *must* start with a single bullet.
		$rules->list = '(?P<list>
            ^\s*
			(\|:([uo]l|li) \s+(['.$argchars.']+?):\|){0,2}
			([*\\#](?=[^*\\#])).* $ # only one opening list marker allowed
            (\n [\t\x20]*
			(\|:([uo]l|li) \s+(['.$argchars.']+?):\|){0,2}
			[*\#]+.* $ 
			)*
        )'; 
		/// \b item: list item, matches single list item. Isolates decorators, markup, contents
		$rules->item = '(?P<item>
            ^\s*
			(\|:[uo]l\s+(?P<list_decorator> (['.$argchars.']+?)):\|)?
			(\|:li\s+(?P<item_decorator>(['.$argchars.']+?)):\|)?
            (?P<item_head> [\#*]+) \s*
            (?P<item_text> .*)
            \s*$
        )';
		#--------------------------------[ tables ]-------------------------------------#
		/// \b table: simple tables, one line per row. table requires closing pipe
		$rules->table = '(?P<table>
            ^\s*
			(\|:table\\s+(?P<table_decorator>(['.$argchars.']+?)):\|)?
			(\|:tr\s+(?P<row_decorator>(['.$argchars.']+?)):\|)? 
			\s*
			(?P<table_row>
            (((?<!~)\|:t[dh]\s+(['.$argchars.']+?):\|)?\|(?!:[a-z]).*?)* \s*
            \| \s*
			)
            \s*$
        )';
		/// \b cell: break table row into cells.  used for preg_match in table_node
		$rules->cell = '
            (\|:t[dh]\s+(?P<cell_decorator>['.$argchars.']+?):\|)?
			\| \s*
            (
                (?P<head> = ([^|]|(?<=~)\|)+ ) |
                (?P<cell> ([^|]|(?<=~)\|)+ )
            ) \s* 
        ';
		#================================[ escape character ]=================================#
		/// \b escape: '~'
		$rules->escape = '(?P<escape> ~ (?P<escaped_char>\S) )'; # embedded in various regex's
		#================================[ special decorators ]===============================#
		#--------------------------------[ span decoration ]----------------------------------#
		/// \b span: span decorator '% ... %'. Empty decorator (optional) connotes end of span
		$rules->span = '(?P<span> %(s\s+(?P<span_decorator>['.$argchars.']+?))?% )';
		#--------------------------------[ block dividers ]-----------------------------------#
		/// \b blockdivider: '|:b ... :|' on line by itself.
		$rules->blockdivider = '(?P<blockdivider>
			^\s* \|:b \s+(?P<blockdivider_decorator>(['.$argchars.']+?)):\| \s* $ 
		)'; # generic block
		#===============================[ preformatted text ]=================================#
		// inline
		/// \b code: '\%c ...'
		$rules->code = '(?P<code>
			(%c\s+(?P<code_decorator>['.$argchars.']+?)%)?{{{ (?P<code_text>.*?) }}} 
		)';
		/// \b pre: block '{{{ ...'
		$rules->pre = '(?P<pre>
            ^\s*(\|:pre\s+(?P<pre_decorator>['.$argchars.']+?):\|)?(?<!~){{{ \s* $
            (\n)?
            (?P<pre_markup>
                ([\#]!(?P<pre_type>\w*?)(\s+.*)?$)?
                (.|\n)+?
            )
            \n?
            ^\s*}}} \s*$
        )';
		/// \b pre_escape: allow ~ before closing included pre brace set (}}}). Simply removed.
		$rules->pre_escape = ' ^(?P<indent>\s*) ~ (?P<rest> \}\}\} \s*) $';
		#================================[ advanced markup ]===============================#
		#--------------------------------[ block declarations ]------------------------------#
/** \b blockdef: block declarations
*/
		$rules->blockdef = '
			^(?P<blockdef>
			\n?(?P<block_indent>[\t\x20]*)\(:(?P<block_selector>\w+)(\s+(?P<block_decorator>['.$argchars.']+?))? \s* :\)
			\s*?(?P<block_inline>.*) $
			(?P<block_content>(\n.*$)*?)
			\n(?P=block_indent)\(:(?P=block_selector)end\s*:\)\s*$
		)';

		#--------------------------------[ macros ]--------------------------------#
		/// \b macro: << ... >> inline
		$rules->macro = '(?P<macro>
			<<
            (?P<macro_name> \w+)
            ((?P<macro_args> ['.$argchars.']*) )? \s*
            (\| \s* (?P<macro_text> .+?) \s* )?
            >>
        )'; 
		/// \b blockmacro: block version to prevent generation of \<p> markup, allows surrounding whitespace
		$rules->blockmacro = '(?P<blockmacro>
			^ \s*
			<<
            (?P<blockmacro_name> \w+)
            ((?P<blockmacro_args> ['.$argchars.']*) )? \s*
            (\| \s* (?P<blockmacro_text> .+?) \s* )?
            >> \s*
			$
        )';
		/// \b decorator: re to pull standard decorator structure: with or without operator; with or withoug '' or "" delimiters
		$rules->decorator = '
			(?>(?P<variable>[\w-]+)(?P<operator>[:=]))?	# optional attribute or property name, and operator applied
			(
				"(?P<ddelim_value>.*?)(?<!\\\)"				# double quote delimited
			|
				\'(?P<sdelim_value>.*?)(?<!\\\)\'			# single quote delimited
			|
				(?P<ndelim_value>\S+)						# not delimited
			)
		';
		$this->_rules = $rules;
	}
	#---------------------------------------------------------------------------------------#
	#------------------------------[ set regular expressions ]------------------------------#
	#---------------------------------------------------------------------------------------#
/**	combine (set) the rules into applied regular expressions.
	The following applied regular expressions are set by combining rules. See source code for details.
	- block_re
	- inline_re
	- link_inline_re
	- image_inline_re
	- item_inline_re
	- defitem_inline_re
	- cell_re
	- tablerow_setaside_re
	- pre_escape_re
	- decorator_re
*/
	protected function _set_re($rules)
	{
		// from least to most general
		// For special case pre escaping, in creole 1.0 done with ~:
		$this->pre_escape_re = '/' . $rules->pre_escape . '/xm';
		// For sub-processing: includes image, but excludes links
		$this->link_inline_re = "/\n"
			. implode("\n|\n",
			array($rules->code, $rules->image, $rules->strong, 
				$rules->emph, $rules->span, $rules->linebreak, 
				$rules->escape, $rules->char))
			. "\n/x"; # for link captions
		// For sub-processing: includes links, but excludes images
		$this->image_inline_re = "/\n"
			. implode("\n|\n",
				array($rules->link, $rules->code, $rules->strong, 
				$rules->emph, $rules->span, $rules->linebreak, 
				$rules->escape, $rules->char))
			. "\n/x"; # for image captions
		$this->item_inline_re = '/' . $rules->item . '/xm'; // for list items
		$this->defitem_inline_re = '/' . $rules->defitem . '/xm'; // for def list items
		$this->cell_re = '/' . $rules->cell . '/x'; // for quick table cells
		// For inline elements:
		$this->inline_re = "/\n" 
			. implode("\n|\n", 
				array($rules->link, $rules->macro,
				$rules->code, $rules->image, $rules->strong, $rules->emph, 
				$rules->span, $rules->linebreak, $rules->escape, $rules->char))
			. "\n/x";
		// set aside table row contents
		$this->tablerow_setaside_re =  "/\n" 
			. implode("\n|\n", array($rules->link, $rules->macro,$rules->code,$rules->image))
			. "\n/x";
		// For block elements:
		$this->block_re = "/\n" 
			. implode("\n|\n",
				array($rules->blankline, $rules->blockdef, $rules->heading, 
				$rules->horizontalrule, $rules->blockdivider, $rules->blockmacro,
				$rules->pre, $rules->list, $rules->deflist, $rules->table, $rules->paragraph)) 
			. "\n/xm";
		$this->decorator_re = '/' . $rules->decorator . '/x';
	}
/**@}*/

/**@{ @name Property accessors
*/
/** Get metadata from parsed markup.
	Gets the metadata from the metadata section of the parsed markup.
	@return object, with three possible properties:\e array classes, \e object properties, \e object attributes
	@sa Muster_SimpleWiki::metadata
*/
	public function metadata()
	{
		return $this->_metadata;
	}
/** Get markerdata from parsed markup.
	Gets the markerdata from search of markers on the markup text.
	
	The markerdata->markers array contains an object for each marker, with the properties \e string name, \e object decorator \e integer offset
	@return object, with the properties \e integer markercount, \e integer textlength (final length of the markup text), \e array markers
	@sa Muster_SimpleWiki::markerdata
*/
	public function markerdata()
	{
		return $this->_markerdata;
	}
/** Get preprocessed markup, before parsing.
	Preprocessed markup has metadata, comments, line coninuations, and markers removed.
	@return string, the preprocessed markup
	@sa Muster_SimpleWiki::preprocessed_markup
*/
	public function preprocessed_markup()
	{
		return $this->_preprocessed_markup;
	}
/** Get or set decorator argument character set (default '^()').
	@param string $argchars content of character set expression: '[' . $argchars . ']'
	@return string
*/
	public function argchars($argchars = NULL)
	{
		if (!is_null($argchars))
		{
			$this->_argchars = $argchars;
			// recompile regex
			$this->_set_rules();
			$this->_set_re($this->_rules);
		}
		return $this->_argchars;
	}
/**@}*/
	#---------------------[ process initiation ]--------------------------#
/**@{ @name Parse control methods */
/** Prepares the text for processing. Creates node SimpleWiki_DocNode::DOCUMENT.

	@param string $markup (markup text)
	@return object $this (so the method can be chained)

	This method creates the root document node (SimpleWiki_DocNode), 
	and initializes the parser with the root node, the current node, 
	and the leaftextnode.
	
	Is called by constructor for any markup passed on creation, 
	or can be called separately for iterative parsiong.
	$todo move preprocess_markup to prepare
*/
	public function prepare($markup)
	{
		$this->_raw = $markup;
        $this->_root = new SimpleWiki_DocNode(SimpleWiki_DocNode::DOCUMENT); # 'document' is the top level node
        $this->_curnode = $this->_root;    # The most recent document node
        $this->_leaftextnode = NULL;           # The node to add inline characters to
		$raw = $this->preprocess_raw_markup($this->_raw);
		return $this;
	}
/** Parse the prepared markup.

	Parse preprocesses the raw markup, then calls the internal parse_block routine.

	@return object Document Node of the root DOM node
*/
    public function parse() // initiate parsing
	{
		# try to clean $raw of unnecessary newlines
        # parse the text given as $this->_raw...
        $this->_parse_block($this->_preprocessed_raw);
		#...and return DOM tree.
        return $this->_root;
	}
	/** Use the _block_re assembled regular expressions to parse the raw text for block structures.
	
	This method directs match processing to the _create_node controller, 
	which then directs processing to the node creation method of the found (parsed) text
	
	@param string $raw raw markup text
	@returns void
	@sa _create_node
	*/
    protected function _parse_block($raw)
	{
        # Recognize block elements.
        preg_replace_callback($this->block_re, array($this,'_create_node'), $raw);
	}
	/** Use the _inline_re assembled regular expressions to parse the raw text for inline structures.

	This method directs match processing to the _create_node controller, 
	which then directs processing to the node creation method of the found (parsed) text

	@param string $raw raw markup text
	@returns void
	@sa _create_node
	*/
    protected function _parse_inline($raw)
	{
        # Recognize inline elements inside blocks.
        preg_replace_callback($this->inline_re, array($this,'_create_node'), $raw);
	}
	#---------------------[ process control ]--------------------------#
	/** Directs parsed data from _parse_block and _parse_inline to the appropriate node creation method.
	
	Regular expressions are organized such that sucess results in assigning a string to the first subgroup name of the expression rule.
	The first subgroup name of the expression rule corresponds to the first part of the assembled name of the node creation method.
	This allows _create_node to search through the preg_groups for the first named array entry, 
	and use that name to direct the preg_groups data to the method that can use the parsed data to create a dom element.
	
	@param array $preg_groups taken from preg_replace_callback
	@return void
	*/
    protected function _create_node($preg_groups) // controller
	{
        # Invoke appropriate _*_node method. Called for every matched group.
		foreach ($preg_groups as $name => $text)
		{
			if ((!is_int($name)) and ($text != ''))
			{
				$node_method = "_{$name}_node";
				$this->$node_method($preg_groups);
				return;
			}
		}
		# special case: pick up empty line for block boundary
		$keys = array_keys($preg_groups);
		$name = 'blankline';
		if ($keys[count($keys)-2]==$name) // last name in key array indicates returned as found
		{
			$node_method = "_{$name}_node";
			$this->$node_method($preg_groups);
			return;
		}
	}
/**@}*/
/**@{ @name Setaside support */
	/// markers to replace preformatted data
	protected $_pre_markers = array();
	/// preformatted element markup, matching markers
	protected $_pre_markup = array();
	/// count to include in generated preformatted marker identifier
	protected $_pre_count = 0;
	/** mark locations of preformatted data. 
	
		Set aside preformatted data, generate unique marker in the code for the data.
		@param array $preg_groups as returned by preg_replace_callback
		@return string to be used by preg_replace_callback to replace the found text
		@todo determine if this method is still required - add_pre_and_code_markers
	*/
	protected function add_pre_and_code_markers($preg_groups)
	{
		isset($preg_groups['pre']) or ($preg_groups['pre'] = '');
		isset($preg_groups['code']) or ($preg_groups['code'] = '');
		$this->_pre_markup[] = preg_replace('/(\$|\\\\)(?=\d)/', '\\\\\1', // escape backreference markup
			$preg_groups['pre'].$preg_groups['code']); // one or the other
		$this->_pre_count++;
		$marker = '{{{' . chr(255). $this->_pre_count . '}}}';
		$this->_pre_markers[] = '/{{\\{' . chr(255) . $this->_pre_count . '\\}}}/';
		return $marker;
	}
	/// structure to set aside row contents before parsing row structure itself
	protected $_tablerow_markers = array();
	/// tablerow element markup, matching markers
	protected $_tablerow_markup = array();
	/// count to include in generated tablerow marker identifier
	protected $_tablerow_count = 0;
	/** mark locations of tablerow data. set aside tablerow data
	
		Set aside tablerow data, generate unique marker in the code for the data.
		@param array $preg_groups as returned by preg_replace_callback
		@return string to be used by preg_replace_callback to replace the found text
	*/
	protected function add_tablerow_markers($preg_groups)
	{
		isset($preg_groups['link']) or ($preg_groups['link'] = '');
		isset($preg_groups['macro']) or ($preg_groups['macro'] = '');
		isset($preg_groups['code']) or ($preg_groups['code'] = '');
		isset($preg_groups['image']) or ($preg_groups['image'] = '');
		($value = $preg_groups['link']) or ($value = $preg_groups['macro']) or 
			($value = $preg_groups['code']) or ($value = $preg_groups['image']);
		$this->_tablerow_markup[] = $value;
		$this->_tablerow_count++;
		$marker = '{{{' . chr(255). $this->_tablerow_count . '}}}';
		$this->_tablerow_markers[] = '/{{\\{' . chr(255) . $this->_tablerow_count . '\\}}}/';
		return $marker;
	}
/**@}*/
/**@{ @name Parse support methods
A handful of methods that provide support for parsing.
*/
/** pre process the markup.
	
	- capture the metadata from the first line, if present.
	- remove comments
	- remove line continuations
	@param string $raw source markup
	@return string preprocessed markup
*/
	protected function preprocess_raw_markup($raw)
	{
		# get metadata
		$raw = preg_replace_callback('/\A```##(.*$(\n``.*$)*)/m',array($this,'preprocess_metadata'),$raw);
		$raw = "\n".$raw."\n"; // in case there is comment on first line, lookahead on last
		# remove comments
		$raw = preg_replace('/\n```.*$/m','',$raw);
		# remove line continuations
		$raw = preg_replace('/\n``/','',$raw);
		# set aside preformatted blocks
		$raw = preg_replace_callback('/'.$this->_rules->pre .'|' .$this->_rules->code .'/xm',array($this,'add_pre_and_code_markers'),$raw);
		# add markup around raw url's; this allows "//" emphasis markup to operate without constraint
		$raw = preg_replace('/(^|\W)((?<!\[\[|{{|~)(http[s]?|mailto):\/\/\S+\w)/','$1[[$2]]',$raw); // add markup to raw url
		# restore preformatted blocks
		$raw = preg_replace($this->_pre_markers,$this->_pre_markup,$raw);
		# get marker data and offsets
		$markerdata = $this->_markerdata = new StdClass();
		$markerdata->offset = 0;
		$markerdata->markercount = 0;
		$this->_markers = array();
		// pull out markers {{##markername marker decoration##}}
		$re = '/(?P<text>[^{]*)|(?<!~)(?P<marker>\{\{##(?P<markername>[a-zA-Z]\w*)(\s+(?P<decorator>['.$this->_argchars.']+?))?\s*##\}\})|(?P<char>.)/';
		$raw = preg_replace_callback($re,array($this,'preprocess_markerdata'),$raw);
		$markerdata->markers = $this->_markers;
		$this->_markers = NULL;
		$markerdata->textlength = $markerdata->offset;
		unset($markerdata->offset);
		$this->_preprocessed_raw = $raw;
		return $raw;
	}
	/** preprocess marker data.
		Find {{\#\#markername markedecoration##}} markers, remove them, and collect data about them including offsets.
		@param array $matches as returned from preg_replace_callback
		@return text without the markers
		@sa markerdata
	*/
	protected function preprocess_markerdata($matches)
	{
		isset($matches['text']) or ($matches['text'] = '');
		isset($matches['char']) or ($matches['char'] = '');
		isset($matches['marker']) or ($matches['marker'] = '');
		isset($matches['markername']) or ($matches['markername'] = '');
		isset($matches['decorator']) or ($matches['decorator'] = '');
		$text = $matches['text'].$matches['char'];
		$this->_markerdata->offset += strlen($text);
		if ($marker = $matches['marker'])
		{
			$this->_markerdata->markercount++;
			$markerobject = $this->_markers[] = new StdClass();
			$markerobject->offset = $this->_markerdata->offset;
			$name = $markerobject->name = $matches['markername'];
			$this->_markers[$name] = $markerobject;
			if ($decorator = $matches['decorator']) 
				$markerobject->decoration = $this->get_decoration($decorator);
			else
				$markerobject->decoration = NULL;
		}
		return $text;
	}
	/** preprocess metadata.
		Remove line continuation characters, and store metadata structure in _metadata property.
		@param array $matches from preg_replace_callback
		@return string '' removes metadata from markup to be processed
		@sa preprocess_raw_markup
	*/
	protected function preprocess_metadata($matches)
	{
		$arguments = trim($matches[1]);
		// remove line continuations
		$arguments = preg_replace('/\n``/','',$arguments);
		// save data
		$this->_metadata = $this->get_decoration($arguments);
		return '';
	}
/**	gets common argument structure for decorators and block declarations.
	@param string $decorator_string the string found in decorators and block declarations
	@return object with attributes, properties, and classes properties, if found
*/
	public function get_decoration($decorator_string) 
	{
		$decoration = new StdClass();
		$decoration->classes = array();
		$decoration->properties = array();
		$decoration->attributes = array();
		$terms = array();
		preg_match_all($this->decorator_re, $decorator_string, $terms, PREG_SET_ORDER); // returns terms
		foreach($terms as $term) 
		{
			isset($term['variable']) or ($term['variable'] = '');
			isset($term['operator']) or ($term['operator'] = '');
			isset($term['ddelim_value']) or ($term['ddelim_value'] = '');
			isset($term['sdelim_value']) or ($term['sdelim_value'] = '');
			isset($term['ndelim_value']) or ($term['ndelim_value'] = '');
			$variable = $term['variable'];
			$operator = $term['operator'];
			if ($term['ddelim_value']) $delimiter = '"';
			elseif ($term['sdelim_value']) $delimiter = "'";
			else $delimiter = '';
			// only one of the following will not be empty
			$value = $term['ddelim_value'] . $term['sdelim_value'] . $term['ndelim_value']; 
			switch ($operator)
			{
				case '=':
					$decoration->attributes[$variable] = $value;
					if ($delimiter == '') $delimiter = '"';
					$decoration->attributedelimiters[$variable] = $delimiter;
					break;
				case ':':
					$decoration->properties[$variable] = $value;
					break;
				default:
					$decoration->classes[] = $value;
					break;
			}
		}
		return $decoration;
	}
	/** parse decorator and declaration arguments from string to structure into a node.
	@param object $node the node having the decoration
	@param string $decorator_string the pre-parsed string containing the argument data
	@return void
	*/
	protected function set_node_decoration($node,$decorator_string)
	{
		$node->decoration = $this->get_decoration($decorator_string);
		$node->decoration->markup = $decorator_string;
	}
	#------------------------------------------------------------------------------#
	#---------------------------[ utilities ]--------------------------------------#
	#------------------------------------------------------------------------------#
    /** get the ancestor node of the passed node that has a type as listed in the second argument
        Look up the tree (starting with $node) to the first occurence
        of one of the listed types of nodes or root.
		If $node is in the list then the current node is returned.
		@param object $node the node requesting the ancestor
		@param array $types the types of ancestors being requested
		@return object the requested node (could be self)
    */
    public function get_selected_ancestor($node, $types) // public as can be used by registered callbacks
	{
        while ((!empty($node->parent)) and (!in_array($node->type,$types)))
		{
            $node = $node->parent;
		}
        return $node;
	}
/**@}*/

/**@{ @name Dom creation methods

These methods are invoked by _create_node, based on the subgroup name of the first successful regular expression. 
The name is assembled with _<subgroupname>_node.
@sa _create_node
*/

	#=========================[ basic processing ]=================================#
/** Character batch added to text stream. Creates leaftextnode SimpleWiki_DocNode::TEXT if required.
	Creates a 'text' leaftextnode if none exists and adds the found characters to its content property.
	
	Sets property $leaftextnode->textcontent.
	@param array $preg_groups as returned by preg_replace_callback
	@return void which removes the found string from the raw text
*/
    protected function _char_node($preg_groups) // can create text leaf node
	{
		$char = $preg_groups['char'];
        if (is_null($this->_leaftextnode))
            $this->_leaftextnode = new SimpleWiki_DocNode(SimpleWiki_DocNode::TEXT, $this->_curnode);
        $this->_leaftextnode->textcontent .= $char;
	}
/** Escaped character added to text stream. Creates leaftextnode SimpleWiki_DocNode::TEXT if required.
	Creates a 'text' leaftextnode if none exists and adds the escaped character to its content property.
	The escape character itself is left behind.
	
	Sets property $leaftextnode->textcontent.
	@param array $preg_groups as returned by preg_replace_callback
	@return void which removes the found string from the raw text
*/
   protected function _escape_node($preg_groups)
	{
		$char = $preg_groups['escaped_char'];
        if (is_null($this->_leaftextnode))
            $this->_leaftextnode = new SimpleWiki_DocNode(SimpleWiki_DocNode::TEXT, $this->_curnode);
        $this->_leaftextnode->textcontent .= $char;
	}
/** Triggers new block.
	This is done by resetting the current block to the nearest ancestor blockdef, or the root document.
	@param array $preg_groups as returned by preg_replace_callback
	@return void which removes the found string from the raw text
*/
    protected function _blankline_node($preg_groups)
	{
		# triggers new block
        $this->_curnode = $this->get_selected_ancestor($this->_curnode, 
			array(SimpleWiki_DocNode::DOCUMENT,SimpleWiki_DocNode::BLOCKDEF));
	}
/** Creates paragraph node. SimpleWiki_DocNode::PARAGRAPH.
	If the current node is a table, table_row, or a list, 
		then the current node is set to the nearest ancestor block or document.
		
	If the current node is a block or document, a new paragraph node is created.
	
	Then the paragraph is parsed for inline parsing.
	
	Sets property $node->decoration.
	
	@param array $preg_groups as returned by preg_replace_callback
	@return void which removes the found string from the raw text
	@todo why not just go straight to nearest document or blockdef?
*/
    protected function _paragraph_node($preg_groups) // can create paragraph for new text
	{
		# text not otherwise classified, triggers creation of paragraph for new set
		isset($preg_groups['text_chars']) or ($preg_groups['text_chars'] = '');
		isset($preg_groups['text_charstream']) or ($preg_groups['text_charstream'] = '');
		isset($preg_groups['paragraph_decorator']) or ($preg_groups['paragraph_decorator'] = '');
        $text = $preg_groups['text_chars'] . $preg_groups['text_charstream'];
		$decorator = $preg_groups['paragraph_decorator'];
        if (in_array($this->_curnode->type, 
			array(
				SimpleWiki_DocNode::TABLE, 
				SimpleWiki_DocNode::TABLE_ROW, 
				SimpleWiki_DocNode::UNORDERED_LIST, 
				SimpleWiki_DocNode::ORDERED_LIST,
				SimpleWiki_DocNode::DEF_LIST))) // text cannot exist in these blocks
		{
            $this->_curnode = $this->get_selected_ancestor($this->_curnode,
                array(SimpleWiki_DocNode::DOCUMENT,SimpleWiki_DocNode::BLOCKDEF));
		}
        if (in_array($this->_curnode->type, array(SimpleWiki_DocNode::DOCUMENT,SimpleWiki_DocNode::BLOCKDEF)))
		{
            $node = $this->_curnode = new SimpleWiki_DocNode(SimpleWiki_DocNode::PARAGRAPH, $this->_curnode);
			if ($decorator) $this->set_node_decoration($node,$decorator);
		} else {
			$text = ' ' . $text;
		}
        $this->_parse_inline($text);
        $this->_leaftextnode = NULL;
	}
	#================================[ core markup ]===============================#
	#--------------------------------[ basic markup ]------------------------------#
/** Creates heading node. SimpleWiki_DocNode::HEADING.
	Changes current node to nearest block or document.
	
	Creates a heading node, and does an inline parse of the heading text.
	
	Sets property $node->level.
	Sets property $node->decoration.
	@param array $preg_groups as returned by preg_replace_callback
	@return void which removes the found string from the raw text
*/
    protected function _heading_node($preg_groups)
	{
		# headings
		isset($preg_groups['heading_text']) or ($preg_groups['heading_text'] = '');
		isset($preg_groups['heading_head']) or ($preg_groups['heading_head'] = '');
		isset($preg_groups['heading_decorator']) or ($preg_groups['heading_decorator'] = '');
		$headtext = $preg_groups['heading_text'];
		$headhead = $preg_groups['heading_head'];
		$decorator = $preg_groups['heading_decorator'];
		
        $this->_curnode = $this->get_selected_ancestor($this->_curnode, 
			array(SimpleWiki_DocNode::DOCUMENT,SimpleWiki_DocNode::BLOCKDEF));
		
        $node = new SimpleWiki_DocNode(SimpleWiki_DocNode::HEADING,$this->_curnode);
        $node->level = strlen($headhead);
		if ($decorator) $this->set_node_decoration($node,$decorator);

        $parent = $this->_curnode;
        $this->_curnode = $node;
        $this->_leaftextnode = NULL;

        $this->_parse_inline($headtext);

        $this->_curnode = $parent;
        $this->_leaftextnode = NULL;
	}
/** Create an emphasis node, or return from one. SimpleWiki_DocNode::EMPHASIS.

	If we are in an emphasis node, the emphasis markup denotes and end to the node. 
	Otherwise a new emphasis node is created.
	@param array $preg_groups as returned by preg_replace_callback
	@return void which removes the found string from the raw text
*/
    protected function _emph_node($preg_groups)
	{
		# emphasis
        if ($this->_curnode->type != SimpleWiki_DocNode::EMPHASIS)
            $this->_curnode = new SimpleWiki_DocNode(SimpleWiki_DocNode::EMPHASIS, $this->_curnode);
        else
		{
			if (!empty($this->_curnode->parent))
				$this->_curnode = $this->_curnode->parent;
		}
        $this->_leaftextnode = NULL;
	}
/** Create a strong node, or return from one. SimpleWiki_DocNode::STRONG.

	If we are in an strong node, the strong markup denotes and end to the node. 
	Otherwise a new strong node is created.
	@param array $preg_groups as returned by preg_replace_callback
	@return void which removes the found string from the raw text
*/
    protected function _strong_node($preg_groups)
	{
		# strong
        if ($this->_curnode->type != SimpleWiki_DocNode::STRONG)
            $this->_curnode = new SimpleWiki_DocNode(SimpleWiki_DocNode::STRONG, $this->_curnode);
        else
		{
			if (!empty($this->_curnode->parent))
				$this->_curnode = $this->_curnode->parent;
		}
        $this->_leaftextnode = NULL;
	}
/** Create a linebreak node. SimpleWiki_DocNode::LINEBREAK.
	Creates a linebreak node unconditionally.
	@param array $preg_groups as returned by preg_replace_callback
	@return void which removes the found string from the raw text
*/
    protected function _linebreak_node($preg_groups)
	{
		# line break
        new SimpleWiki_DocNode(SimpleWiki_DocNode::LINEBREAK, $this->_curnode);
        $this->_leaftextnode = NULL;
	}
/** Create a horizontal rule node. SimpleWiki_DocNode::HORIZONTALRULE.
	Moves to the nearest document or block ancestor, and 
	creates a horizontalrule node.
	@param array $preg_groups as returned by preg_replace_callback
	@return void which removes the found string from the raw text
*/
    protected function _horizontalrule_node($preg_groups)
	{
        $this->_curnode = $this->get_selected_ancestor($this->_curnode, 
			array(SimpleWiki_DocNode::DOCUMENT,SimpleWiki_DocNode::BLOCKDEF));
        new SimpleWiki_DocNode(SimpleWiki_DocNode::HORIZONTALRULE, $this->_curnode);
	}
/** Create a link node. SimpleWiki_DocNode::LINK.
	Parse the link text.
	
	Sets property $node->target.
	Sets property $node->decoration.
	@param array $preg_groups as returned by preg_replace_callback
	@return void which removes the found string from the raw text
*/
    protected function _link_node($preg_groups)
	{
        # Handle all types of links.
		isset($preg_groups['link_target']) or ($preg_groups['link_target'] = '');
		isset($preg_groups['link_text']) or ($preg_groups['link_text'] = '');
		isset($preg_groups['link_title']) or ($preg_groups['link_title'] = '');
		isset($preg_groups['link_decorator']) or ($preg_groups['link_decorator'] = '');
        $target = trim($preg_groups['link_target']);
        $text = trim($preg_groups['link_text']);
		$title = trim($preg_groups['link_title']);
		$decorator = trim($preg_groups['link_decorator']);

		$node =  new SimpleWiki_DocNode(SimpleWiki_DocNode::LINK, $this->_curnode);
		$node->target = $target;
		if ($decorator) $this->set_node_decoration($node,$decorator);
		if ($title) $node->title = $title;

        $parent = $this->_curnode;
        $this->_curnode = $node;
        $this->_leaftextnode = NULL;

        preg_replace_callback($this->link_inline_re, array($this,'_create_node'), $text);

        $this->_curnode = $parent;
        $this->_leaftextnode = NULL;
	}
	#--------------------------------[ images ]-------------------------------------#
/** Create an image node. SimpleWiki_DocNode::IMAGE.
	Parse the image text.
	
	Sets property $node->target.
	Sets property $node->decoration.
	@param array $preg_groups as returned by preg_replace_callback
	@return void which removes the found string from the raw text
*/
	protected function _image_node($preg_groups)
	{
        # Handles images included in the page.
		isset($preg_groups['image_target']) or ($preg_groups['image_target'] = '');
		isset($preg_groups['image_text']) or ($preg_groups['image_text'] = '');
		isset($preg_groups['image_title']) or ($preg_groups['image_title'] = '');
		isset($preg_groups['image_decorator']) or ($preg_groups['image_decorator'] = '');
        $target = trim($preg_groups['image_target']);
        $text = trim($preg_groups['image_text']);
		$title = trim($preg_groups['image_title']);
		$decorator = trim($preg_groups['image_decorator']);

        $node = new SimpleWiki_DocNode(SimpleWiki_DocNode::IMAGE, $this->_curnode);
		$node->target = $target;
		if ($decorator) $this->set_node_decoration($node,$decorator);
		if ($title != '') $node->title = $title;

        $parent = $this->_curnode;
        $this->_curnode = $node;
        $this->_leaftextnode = NULL;

        preg_replace_callback($this->image_inline_re, array($this,'_create_node'), $text);

        $this->_curnode = $parent;
        $this->_leaftextnode = NULL;
	}
	#--------------------------------[ lists ]-------------------------------------#
/** Create a list node.
	Parses the list markup as a whole, components to be processed by _item_node.
	@param array $preg_groups as returned by preg_replace_callback
	@return void which removes the found string from the raw text
*/
    protected function _list_node($preg_groups)
	{
		# collect list markup, detail processing by item
        $text = $preg_groups['list'];
        preg_replace_callback($this->item_inline_re,array($this,'_create_node'), $text);
	}
/** Create a list item, and a list node if necessary. 
	SimpleWiki_DocNode::ORDERED_LIST, SimpleWiki_DocNode::UNORDERED_LIST, SimpleWiki_DocNode::LIST_ITEM.

	Sets property $list->level.
	Sets property $list->decoration.
	Sets property $item->decoration.
	@param array $preg_groups as returned by preg_replace_callback
	@return void which removes the found string from the raw text
*/
   protected function _item_node($preg_groups)
	{
		# list item
		isset($preg_groups['item_head']) or ($preg_groups['item_head'] = '');
		isset($preg_groups['item_text']) or ($preg_groups['item_text'] = '');
		isset($preg_groups['list_decorator']) or ($preg_groups['list_decorator'] = '');
		isset($preg_groups['item_decorator']) or ($preg_groups['item_decorator'] = '');
        $bullet = $preg_groups['item_head'];
        $text = $preg_groups['item_text'];
		$listdecorator = $preg_groups['list_decorator'];
		$itemdecorator = $preg_groups['item_decorator'];
		// determine the type of list being processed
        if ($bullet{0} == '#')
            $listtype = SimpleWiki_DocNode::ORDERED_LIST;
        else
            $listtype = SimpleWiki_DocNode::UNORDERED_LIST;
		// determine the level by measuring the number of list markup characters
        $level = strlen($bullet);
        # Find a node of the same type and level up the tree, or a block to start a list
        $candidate_node = $this->_curnode;
        while // find a reference node if current list doesn't match, and if we're not in a block node to start
		(
			($candidate_node) // searching an existing node
			and ! // this is a not a list of the same level...
			(
				in_array($candidate_node->type, array(SimpleWiki_DocNode::ORDERED_LIST, SimpleWiki_DocNode::UNORDERED_LIST)) 
				and $candidate_node->level == $level
			)
			and ! // ... and this is not a block ...
			(
				in_array($candidate_node->type, array(SimpleWiki_DocNode::DOCUMENT,SimpleWiki_DocNode::BLOCKDEF))
			)
		) // ... so keep looking.
		{
            $candidate_node = $candidate_node->parent;
		}
		# set the found list as the current node for the list item... 
		# (if $candidate_node is null then no reference candidate was found)
        if ($candidate_node and ($candidate_node->type == $listtype)) // found a match for list
            $this->_curnode = $candidate_node;
        else # ... or create a new level of list
		{
			// get the nearest ancestor candidate for creating a new list
            $this->_curnode = $this->get_selected_ancestor($this->_curnode,
                array(SimpleWiki_DocNode::LIST_ITEM, SimpleWiki_DocNode::DOCUMENT,SimpleWiki_DocNode::BLOCKDEF));
			// create the list
            $listnode = $this->_curnode = new SimpleWiki_DocNode($listtype, $this->_curnode);
			if ($listdecorator) $this->set_node_decoration($listnode,$listdecorator);
            $listnode->level = $level;
		}
		# now add the list item to the list
        $itemnode = $this->_curnode = new SimpleWiki_DocNode(SimpleWiki_DocNode::LIST_ITEM, $this->_curnode);
		if ($itemdecorator) $this->set_node_decoration($itemnode,$itemdecorator);
		$this->_leaftextnode = NULL;
		# parse the text of the list item
        $this->_parse_inline($text);
        $this->_leaftextnode = NULL;
	}
	#--------------------------------[ definition list ]-------------------------------------#
/** Create a definition list node.
	Parses the definition list markup as a whole, components to be processed by _item_node.
	@param array $preg_groups as returned by preg_replace_callback
	@return void which removes the found string from the raw text
*/
    protected function _deflist_node($preg_groups)
	{
		# collect list markup, detail processing by item
        $text = $preg_groups['deflist'];
        preg_replace_callback($this->defitem_inline_re,array($this,'_create_node'), $text);
	}
/** Create a definition term and description, and a definition list node if necessary. 
	SimpleWiki_DocNode::DEF_LIST, SimpleWiki_DocNode::DEF_TERM, SimpleWiki_DocNode::DEF_DESC.
	
	Sets property $list->level.
	Sets property $list->decoration.
	Sets property $term->decoration.
	Sets property $desc->decoration.
	@param array $preg_groups as returned by preg_replace_callback
	@return void which removes the found string from the raw text
*/
    protected function _defitem_node($preg_groups)
	{
		# list item
		isset($preg_groups['defitem_head']) or ($preg_groups['defitem_head'] = '');
		isset($preg_groups['defterm_text']) or ($preg_groups['defterm_text'] = '');
		isset($preg_groups['defdesc_text']) or ($preg_groups['defdesc_text'] = '');
		isset($preg_groups['deflist_decorator']) or ($preg_groups['deflist_decorator'] = '');
		isset($preg_groups['defterm_decorator']) or ($preg_groups['defterm_decorator'] = '');
		isset($preg_groups['defdesc_decorator']) or ($preg_groups['defdesc_decorator'] = '');
        $head = $preg_groups['defitem_head'];
        $term = trim($preg_groups['defterm_text']);
        $desc = $preg_groups['defdesc_text'];
		$listdecorator = $preg_groups['deflist_decorator'];
		$termdecorator = $preg_groups['defterm_decorator'];
		$descdecorator = $preg_groups['defdesc_decorator'];
		// set the type of list being processed
        $listtype = SimpleWiki_DocNode::DEF_LIST;
		// determine the level by measuring the number of list markup characters
        $level = strlen($head);
        # Find a node of the same type and level up the tree, or a block to start a list
        $candidate_node = $this->_curnode;
        while // find a reference node if current list doesn't match, and if we're not in a block node to start
		(
			($candidate_node) // searching an existing node
			and ! // this is a not a list of the same level...
			(
				($candidate_node->type == SimpleWiki_DocNode::DEF_LIST) 
				and $candidate_node->level == $level
			)
			and ! // ... and this is not a block ...
			(
				in_array($candidate_node->type, array(SimpleWiki_DocNode::DOCUMENT,SimpleWiki_DocNode::BLOCKDEF))
			)
		) // ... so keep looking.
        $candidate_node = $candidate_node->parent;
		# set the found list as the current node for the list item... 
		# (if $candidate_node is null then no reference candidate was found)
        if ($candidate_node and ($candidate_node->type == $listtype)) // found a match for list
            $this->_curnode = $candidate_node;
        else # ... or create a new level of list
		{
			// get the nearest ancestor candidate for creating a new list
            $this->_curnode = $this->get_selected_ancestor($this->_curnode,
                array(SimpleWiki_DocNode::DEF_DESC, SimpleWiki_DocNode::DOCUMENT,SimpleWiki_DocNode::BLOCKDEF));
			// create the list
            $listnode = $this->_curnode = new SimpleWiki_DocNode($listtype, $this->_curnode);
			if ($listdecorator) $this->set_node_decoration($listnode,$listdecorator);
            $listnode->level = $level;
		}
		# now add the term to the list, if present
		if ($term)
		{
			$curnode = $this->_curnode;
			$termnode = $this->_curnode = new SimpleWiki_DocNode(SimpleWiki_DocNode::DEF_TERM, $this->_curnode);
			if ($termdecorator) $this->set_node_decoration($termnode,$termdecorator);
			$this->_leaftextnode = NULL;
			# parse the text of the term
			$this->_parse_inline($term);
			$this->_leaftextnode = NULL;
			$this->_curnode = $curnode;
		}
		# ...and add the desc to the list
		$descnode = $this->_curnode = new SimpleWiki_DocNode(SimpleWiki_DocNode::DEF_DESC, $this->_curnode);
		if ($descdecorator) $this->set_node_decoration($descnode,$descdecorator);
		$this->_leaftextnode = NULL;
		# parse the text of the desc
		$this->_parse_inline($desc);
		$this->_leaftextnode = NULL;
	}
	#--------------------------------[ tables ]-------------------------------------#
/** Process a quick table row.
	Create a table node if necessary. SimpleWiki_DocNode::TABLE.
	Create a table row node. SimpleWiki_DocNode::TABLE_ROW.
	Create and parse cell nodes. SimpleWiki_DocNode::TABLE_HEADCELL, SimpleWiki_DocNode::TABLE_CELL.
	
	Sets property $table->decoration.
	Sets property $row->decoration.
	Sets property $cell->decoration.
	@param array $preg_groups as returned by preg_replace_callback
	@return void which removes the found string from the raw text
*/
	protected function _table_node($preg_groups)
	{
		# process a table row (any line beginning with '|')
		isset($preg_groups['table_row']) or ($preg_groups['table_row'] = '');
		isset($preg_groups['table_decorator']) or ($preg_groups['table_decorator'] = '');
		isset($preg_groups['row_decorator']) or ($preg_groups['row_decoratpor'] = '');
		$rowmarkup = trim($preg_groups['table_row']);
		# set aside rowmarkup links, preformats, macros and images to simplify markup
		$rowmarkup = preg_replace_callback(
			$this->tablerow_setaside_re, array($this,'add_tablerow_markers'),$rowmarkup);
		# assure at least content of a space in every cell.
		$rowmarkup = preg_replace('/((?<!:)\|(?=\|))/','| ',$rowmarkup); // ensure content for every cell
		$tabledecorator = trim($preg_groups['table_decorator']);
		$rowdecorator = trim($preg_groups['row_decorator']);
		
		# set reference node to nearest table, document, or block ancestor
		$this->_curnode = $this->get_selected_ancestor($this->_curnode, 
			array(SimpleWiki_DocNode::TABLE,SimpleWiki_DocNode::DOCUMENT,SimpleWiki_DocNode::BLOCKDEF));
		# create new table node if necessary
		if ($this->_curnode->type != SimpleWiki_DocNode::TABLE)
			$this->_curnode = new SimpleWiki_DocNode(SimpleWiki_DocNode::TABLE, $this->_curnode);
		# set decoration for table
		$tablenode = $this->_curnode;
		if ($tabledecorator) $this->set_node_decoration($tablenode,$tabledecorator);
		
		# create a new row node
		$row_node = $this->_curnode = new SimpleWiki_DocNode(SimpleWiki_DocNode::TABLE_ROW, $tablenode);
		# add decoration to new row
		if ($rowdecorator) $this->set_node_decoration($row_node,$rowdecorator);
		
		# collect all cell markup into $cell_matches
		preg_match_all($this->cell_re, $rowmarkup, $cell_matches, PREG_SET_ORDER);
		# process cell_matches
		$this->_leaftextnode = NULL;
		foreach ($cell_matches as $cell_groups) {
			# get cell markup
			isset($cell_groups['cell']) or ($cell_groups['cell'] = '');
			isset($cell_groups['head']) or ($cell_groups['head'] = '');
			isset($cell_groups['cell_decorator']) or ($cell_groups['cell_decorator'] = '');
			$cellmarkup = $cell_groups['cell'];
			$cellhead = $cell_groups['head'];
			$celldecorator = $cell_groups['cell_decorator'];
			# create table header cell or table data cell
			if ($cellhead) {
				$cellmarkup = trim($cellhead,'=');
				$cell_node = $this->_curnode = new SimpleWiki_DocNode(SimpleWiki_DocNode::TABLE_HEADCELL, $row_node);
			} else {
				$cell_node = $this->_curnode = new SimpleWiki_DocNode(SimpleWiki_DocNode::TABLE_CELL, $row_node);
			}
			# apply decoration to cell node
			if ($celldecorator) $this->set_node_decoration($cell_node,$celldecorator);
			# restore links, preformats, macros and images to current cell
			$cellmarkup = preg_replace($this->_tablerow_markers,$this->_tablerow_markup,$cellmarkup);
			# process cell inline markup
			$this->_leaftextnode = NULL;
			preg_replace_callback($this->inline_re, array($this,'_create_node'), $cellmarkup);
		}
		# set reference back to table node
		$this->_curnode = $tablenode;
		$this->_leaftextnode = NULL;
		# reset table setaside structure
		$this->_tablerow_markers = array();
		$this->_tablerow_markup = array();
		$this->_tablerow_count = 0;
	}
	#================================[ special decorators ]=============================#
	#--------------------------------[ span decoration ]--------------------------------#
/** Create a span node. SimpleWiki_DocNode::SPAN.
	A span with a decorator opens a new span. 
	An empty span (%%) closes an existing span, or is written as text if no span has been opened.
	
	Sets property $leaftextnode->textcontent from markup with error. SimpleWiki_DocNode::TEXT.
	Sets property $node->decoration.
	@param array $preg_groups as returned by preg_replace_callback
	@return void which removes the found string from the raw text
*/
	protected function _span_node($preg_groups)
	{
		# span
		isset($preg_groups['span_decorator']) or ($preg_groups['span_decorator'] = '');
		$decorator = $preg_groups['span_decorator'];
        if ($decorator) // new span
		{
            $node = $this->_curnode = new SimpleWiki_DocNode(SimpleWiki_DocNode::SPAN, $this->_curnode);
			$this->set_node_decoration($node,$decorator);
			$this->_leaftextnode = NULL;
		}
        elseif ($this->_curnode->type == SimpleWiki_DocNode::SPAN) // closing existing span
		{
			if (!empty($this->_curnode->parent))
			{
				$this->_curnode = $this->_curnode->parent;
				$this->_leaftextnode = NULL;
			}
		}
		else // error, return text
		{
			if (is_null($this->_leaftextnode))
				$this->_leaftextnode = new SimpleWiki_DocNode(SimpleWiki_DocNode::TEXT, $this->_curnode);
			$this->_leaftextnode->textcontent .= $preg_groups['span'];
		}
	}
	#--------------------------------[ block dividers ]--------------------------------#
/** Block divider, a standalone decorator. SimpleWiki_DocNode::BLOCKDIVIDER.
	
	|:b decoration:|

	Sets property $node->decoration.
	@param array $preg_groups as returned by preg_replace_callback
	@return void which removes the found string from the raw text
*/
    protected function _blockdivider_node($preg_groups)
	{
		# empty block acting as block divider
		$decorator = $preg_groups['blockdivider_decorator'];
        $this->_curnode = $this->get_selected_ancestor($this->_curnode, 
			array(SimpleWiki_DocNode::DOCUMENT,SimpleWiki_DocNode::BLOCKDEF));
        $node = new SimpleWiki_DocNode(SimpleWiki_DocNode::BLOCKDIVIDER, $this->_curnode);
		if ($decorator) $this->set_node_decoration($node,$decorator);
	}
	#============================[ preformatted text ]=================================#
/** Create an inline code node. SimpleWiki_DocNode::CODE.
	
	Sets property $node->textcontent.
	Sets property $node->decoration.
	@param array $preg_groups as returned by preg_replace_callback
	@return void which removes the found string from the raw text
*/
    protected function _code_node($preg_groups)
	{
		# preformatted inline text
		isset($preg_groups['code_text']) or ($preg_groups['code_text'] = '');
		isset($preg_groups['code_decorator']) or ($preg_groups['code_decorator'] = '');
		$codetext = $preg_groups['code_text'];
		$decorator = trim($preg_groups['code_decorator']);
		
        $node = new SimpleWiki_DocNode(SimpleWiki_DocNode::CODE, $this->_curnode);
		$node->textcontent = $codetext;
		if ($decorator) $this->set_node_decoration($node,$decorator);
        $this->_leaftextnode = NULL;
	}
/** Create a block preformatted node. SimpleWiki_DocNode::PREFORMATTED.
	
	Sets property $node->textcontent.
	Sets property $node->decoration.
	@param array $preg_groups as returned by preg_replace_callback
	@return void which removes the found string from the raw text
*/
    protected function _pre_node($preg_groups)
	{
		# process preformatted text
		isset($preg_groups['pre_type']) or ($preg_groups['pre_type'] = '');
		isset($preg_groups['pre_markup']) or ($preg_groups['pre_markup'] = '');
		isset($preg_groups['pre_decorator']) or ($preg_groups['pre_decorator'] = '');
        $type = $preg_groups['pre_type'];
        $text = $preg_groups['pre_markup'];
		$decorator = $preg_groups['pre_decorator'];
		
        $this->_curnode = $this->get_selected_ancestor($this->_curnode, 
			array(SimpleWiki_DocNode::DOCUMENT,SimpleWiki_DocNode::BLOCKDEF));
        $text = preg_replace_callback($this->pre_escape_re,array($this,'remove_tilde'), $text);
        $node = new SimpleWiki_DocNode(SimpleWiki_DocNode::PREFORMATTED, $this->_curnode);
		$node->textcontent = $text;
        $node->section = $type?$type:'';
		if ($decorator) $this->set_node_decoration($node,$decorator);
        $this->_leaftextnode = NULL;
	}
/** Used in preprocessing of pre element. Removes escape char from embedded closing pre markup.
*/
    private function remove_tilde($preg_groups)
	{
		# used in pre processing of pre element
        return $preg_groups['indent'] . $preg_groups['rest'];
	}
	#================================[ advanced markup ]===============================#
	#--------------------------------[ block declarations ]------------------------------#
/** Create a block node. SimpleWiki_DocNode::BLOCKDEF. Set reference to closest document, blockdef, list_item or def_desc ancestor.
	Create node, set decoration, parse inline portion and parse block portion.
	
	Sets property $node->blocktag.
	Sets property $node->decoration.
	@param array $preg_groups as returned by preg_replace_callback
	@return void which removes the found string from the raw text
*/
	protected function _blockdef_node($preg_groups)
	{
		# block definitions
		isset($preg_groups['block_selector']) or ($preg_groups['block_selector'] = '');
		isset($preg_groups['block_content']) or ($preg_groups['block_content'] = '');
		isset($preg_groups['block_decorator']) or ($preg_groups['block_decorator'] = '');
		isset($preg_groups['block_inline']) or ($preg_groups['block_inline'] = '');
		$name = $preg_groups['block_selector'];
		$content = $preg_groups['block_content'];
		$decorator = $preg_groups['block_decorator'];
		$inline = $preg_groups['block_inline'];
		
        $container = $this->_curnode = $this->get_selected_ancestor($this->_curnode,
            array(SimpleWiki_DocNode::DOCUMENT,
				SimpleWiki_DocNode::BLOCKDEF,
				SimpleWiki_DocNode::LIST_ITEM,
				SimpleWiki_DocNode::DEF_DESC));
        $node = $this->_curnode = new SimpleWiki_DocNode(SimpleWiki_DocNode::BLOCKDEF, $container);
		$node->blocktag = $name;
		if ($decorator) $this->set_node_decoration($node,$decorator);
		
		$this->_leaftextnode = NULL;
        if ($inline) $this->_parse_inline($inline);
		$this->_leaftextnode = NULL;
        if ($content) $this->_parse_block($content);
		$this->_curnode = $container;
        $this->_leaftextnode = NULL;
		
	}
	#-----------------------------------[ macros ]-------------------------------------#
/** Create inline macro. SimpleWiki_DocNode::MACRO.
	The text portion is both saved as text and parsed.
	
	Sets property $node->macroname.
	Sets property $node->textcontent if text is present.
	Sets property $node->decoration.
	@param array $preg_groups as returned by preg_replace_callback
	@return void which removes the found string from the raw text. 
*/
	protected function _macro_node($preg_groups)
	{
        # Handles macros using the placeholder syntax.
		isset($preg_groups['macro_name']) or ($preg_groups['macro_name'] = '');
		isset($preg_groups['macro_text']) or ($preg_groups['macro_text'] = '');
		isset($preg_groups['macro_args']) or ($preg_groups['macro_args'] = '');
        $name = $preg_groups['macro_name'];
        $text = trim($preg_groups['macro_text']);
		$decorator = $preg_groups['macro_args'];

		$container = $this->_curnode;
        $node = new SimpleWiki_DocNode(SimpleWiki_DocNode::MACRO, $container);
		$node->macroname = $name;

        if ($decorator) $this->set_node_decoration($node,$decorator);
		if ($text)
		{
			$node->textcontent = $text;
			$this->_curnode = $node;
			$this->_leaftextnode = NULL;
			$this->_parse_inline($text);
			$this->_curnode = $container;
		}
        $this->_leaftextnode = NULL;
	}
/** Create block macro. SimpleWiki_DocNode::MACRO.
	The text portion is both saved as text and parsed.
	
	Sets property $node->macroname.
	Sets property $node->textcontent if text is present.
	Sets property $node->decoration.
	@param array $preg_groups as returned by preg_replace_callback
	@return void which removes the found string from the raw text.
*/
	protected function _blockmacro_node($preg_groups)
	{
        # Handles macros using the placeholder syntax. block version
		isset($preg_groups['blockmacro_name']) or ($preg_groups['blockmacro_name'] = '');
		isset($preg_groups['blockmacro_text']) or ($preg_groups['blockmacro_text'] = '');
		isset($preg_groups['blockmacro_args']) or ($preg_groups['blockmacro_args'] = '');
        $name = $preg_groups['blockmacro_name'];
        $text = trim($preg_groups['blockmacro_text']);
		$decorator = $preg_groups['blockmacro_args'];
		
        $container = $this->_curnode = $this->get_selected_ancestor($this->_curnode, 
			array(SimpleWiki_DocNode::DOCUMENT,SimpleWiki_DocNode::BLOCKDEF)); // different from macro
        $node = new SimpleWiki_DocNode(SimpleWiki_DocNode::MACRO, $this->_curnode);
		$node->macroname = $name;
        if ($decorator) $this->set_node_decoration($node,$decorator);
		if ($text)
		{
			$node->textcontent = $text;
			$this->_curnode = $node;
			$this->_leaftextnode = NULL;
			$this->_parse_inline($text);
			$this->_curnode = $container;
		}
        $this->_leaftextnode = NULL;
	}
/**@}*/

/**@{ @name Debug functions */
	#------------------------------------------------------------------------------#
	#---------------------------[ debug functions ]--------------------------------#
	#------------------------------------------------------------------------------#
/** Display the regex assemblies.
*/
	public function display_regex() // for debug
	{
		echo 'BLOCK_RE ';
		var_dump($this->block_re);
		echo 'INLINE_RE ';
		var_dump($this->inline_re);
		echo 'link_inline_re ';
		var_dump($this->link_inline_re);
		echo 'item_inline_re ';
		var_dump($this->image_inline_re);
		echo 'item_inline_re ';
		var_dump($this->item_inline_re);
		echo 'defitem_inline_re ';
		var_dump($this->defitem_inline_re);
		echo 'CELL_RE ';
		var_dump($this->cell_re);
		echo 'PRE_ESCAPE_RE ';
		var_dump($this->pre_escape_re);
		echo 'DECORATOR_RE ';
		var_dump($this->decorator_re);
		echo 'TABLEROW_SETASIDE_RE ';
		var_dump($this->tablerow_setaside_re);
	}
/** Display the dom.
*/
	public function display_dom($root) // for debug
	{
		$count = 1;
		$rootarray = array();
		$count += $this->display_dom_add_child($root,$rootarray);
		$rootarray = $rootarray[0];
		print_r($rootarray);
		return $count;
	}
/** Support for display the dom.
	@sa display_dom
*/
	protected function display_dom_add_child($node,&$childarray) // for debug
	{
		$nodearray = $node->get_display_list();
		$children = $node->children;
		$count = 0;
		if (!empty($children))
		{
			$nodearray['children'] = array();
			foreach ($children as $child)
				$count+= $this->display_dom_add_child($child,$nodearray['children']);
		}
		$childarray[] = $nodearray;
		return count($children) + $count;
	}
 /**@}*/
}

