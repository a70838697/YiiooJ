<?php
/*
SimpleWiki module, version 1.0 Beta 3, January 6, 2011
copyright (c) Henrik Bechmann, 2009-2011, Toronto, Canada. All rights reserved. simplewiki.org
licence: BSD
*/
#==========================================================================
#-----------------------------[ PARSER ]----------------------------------
#==========================================================================
/*
	public methods:
	$parser = new SimpleWikiParser($text) - create instance of parser for text
	->parse() - main method, parses text, returns document object model (tree)
	->prepare($markup) - reset with new markup
	->metadata() - set with ```## arguments on first line
	->argchars($argchars = NULL) - get or set characters allowed for arguments
	->up_to($node, $kinds) - made available for callbacks, finds first
		ancestor instance of node of kind, including current node
	->parse_arguments($arguments) - utility, takes string of discreet values,
		attributes, or properties, returns object of arguments
	for debugging:
		->display_regex()
		->display_dom($dom)
*/
# see _replace($groups) below, which is the controller for the parsing process.
class Native_SimpleWiki_Parser
{
	protected $_rules;
	protected $pre_escape_re;
	protected $link_re;
	protected $item_re;
	protected $cell_re;
	protected $block_re;
	protected $inline_re;
	protected $decorator_re;
	
	protected $_raw;
	protected $_curnode;
	protected $_textleafnode;
	protected $_root;
	protected $_argchars = '\\w\\s:="\'%\\\#.-'; // for decorators, notably parentheses omitted for security
	protected $_metadata; // from first line ```## arguments
	
	public function __construct($text)
	{
		$this->_set_rules();
		$this->_set_re($this->_rules);
		$this->prepare($text);
	}
	public function metadata()
	{
		return $this->_metadata;
	}
	public function prepare($text)
	{
		$this->_raw = $text;
        $this->_root = new SimpleWiki_DocNode('document');
        $this->_curnode = $this->_root;        # The most recent document node
        $this->_textleafnode = NULL;           # The node to add inline characters to
		return $this;
	}
	public function argchars($argchars = NULL)
	{
		if (!is_null($argchars))
			$this->_argchars = $argchars;
		return $this->_argchars;
	}
	# set rules for parsing
	protected function _set_rules()
	{
		// the first group name of each rule, if set, is used by controller (_replace($groups)) 
		// for further processing of parsed data
		$rules = new StdClass();
		$argchars = $this->_argchars;
		#================================[ basic processing ]=================================#
		# no explicit action by user (other than include blank lines between blocks)
//		$rules->char =  '(?P<char> . )'; // slower, but allows capture or raw url's
		$rules->char =  '(?P<char> ([\\w\\s]+$|. ))'; //faster, but misses raw url's
		$rules->line = '(?P<line> ^ \\s* $ )'; # empty line that separates blocks, especially paragraphs
		$rules->text = '(?P<text>
			^(\\|:p\\s+(?P<paragraph_decorator>['.$argchars.']+?):\\|)?(?P<text_chars>.+)
			|
			(?P<text_charstream>.+) 
		)'; # text not otherwise parsed with block parsing - handed over to inline pasing
		#================================[ core markup ]===============================#
		#--------------------------------[ basic markup ]------------------------------#
		// headings
		$rules->heading = '(?P<heading>
            ^(\\|:h\\s+(?P<heading_decorator>['.$argchars.']+?):\\|)? \\s*
            (?P<heading_head>=+) \\s*
            (?P<heading_text> .*? ) \\s*
            (?P<heading_tail>=*) \\s*
            $
        )';
		// emphasis
		$rules->emph = '(?P<emph> (?<!:)\/\/|\/\/(?=\\s) )'; # there must be no : in front of the //
									# or there must be whitespace after the forwardslashes
									# - avoids italic rendering in urls with unknown protocols
		// strong
		$rules->strong = '(?P<strong> \\*\\* )';
		// linebreak
		$rules->linebreak = '(?P<break> \\\\\\\\ )';
		// horizontal rule
		$rules->separator = '(?P<separator>
            (?>
			^ \\s* ---- \\s* $ 
			)
		)';
		#--------------------------------[ links ]-------------------------------------#
		# supported protocols:
//		$proto = 'http|https|ftp|nntp|news|mailto|telnet|file|irc';
		$proto = 'http|https|mailto'; // commonly used protocols
/*		# unmarked urls... not used for performance reasons
		$rules->url =  "(?P<url>
            (?>
            (^ | (?<=\\s | [.,:;!?()\/=]))
            (?P<escaped_url>~)?
            (?P<url_target> (?P<url_proto> $proto ):\\S+? )
            ($ | (?=\\s | [,.:;!?()] (\\s | $)))
			)
		)";
*/
		# marked links...
		$rules->link = '(?P<link>
            (?>
            (%l\\s+(?P<link_decorator>['.$argchars.']+?)%)?
			\\[\\[
            (?P<link_target>.+?) \\s*
            ([|] \\s* (?P<link_text>.*?) \\s* ([|] \\s* (?P<link_title>[^|\\]}]+))? \\s*)?
            \\]\\](?!]) # allow embedded "]"
			)
		)';
		#--------------------------------[ images ]-------------------------------------#
		$rules->image = '(?P<image>
            (?>
            (%i\\s+(?P<image_decorator>['.$argchars.']+?)%)?{{
            (?P<image_target>.+?) \\s*
            ([|] \\s* (?P<image_text>.*?) \\s* ([|] \\s* (?P<image_title>[^|\\]}]+))? \\s*)?
            }}
			)
		)';
		#--------------------------------[ lists ]-------------------------------------#
		# ordered or unordered lists
		$rules->list = '(?P<list>
            ^ 
			(\\|:([uo]l|li) \\s+([\\w\\s:="\'-]+):\\|){0,2}
			[ \\t]* ([*](?=[^*\\#])|[\\#](?=[^\\#*])).* $
            ( \\n
			(\\|:([uo]l|li) \\s+([\\w\\s:="\'-]+):\\|){0,2}
			[ \\t]* [*\\#]+.* $ )*
        )'; # Matches the whole list, separate items are parsed later. The
			# list *must* start with a single bullet.

		$rules->item = '(?P<item>
            (?>
            ^
			(\\|:[uo]l\\s+(?P<list_decorator> (['.$argchars.']+?)):\\|)?
			(\\|:li\\s+(?P<item_decorator>(['.$argchars.']+)):\\|)?
			\\s*
            (?P<item_head> [\\#*]+) \\s*
            (?P<item_text> .*?)
            $
			)
        )'; # Matches single list item
		#--------------------------------[ tables ]-------------------------------------#
		# simple tables, one line per row
		$rules->table = '(?P<table>
            (?>
            ^
			(\\|:table\\s+(?P<table_decorator>(['.$argchars.']+?)):\\|)?
			(\\|:tr\\s+(?P<row_decorator>(['.$argchars.']+?)):\\|)? 
			\\s*
			(?P<table_row>
            ((\\|:(td|th)\\s+(['.$argchars.']+?):\\|)?[|](?!:[a-z]).*?)* \\s*
            [|] \\s*
			)
            $
			)
        )'; # table requires closing pipe
		# break table row into cells
		$rules->cell = '
            (\\|:(td|th)\\s+(?P<cell_decorator>['.$argchars.']+?)\\:\\|)?
			\\| \\s*
            (
                (?P<head> [=]([^|]|(?<=~)[|])+ ) |
                (?P<cell> ([^|]|(?<=~)[|])+ )
            ) \\s* 
        '; # used for preg_match in table_repl
		#================================[ escape character ]=================================#
		$rules->escape = '(?P<escape> ~ (?P<escaped_char>\\S) )'; # embedded in various regex's
		#================================[ special decorators ]===============================#
		#--------------------------------[ span decoration ]----------------------------------#
		$rules->span = '(?P<span> %(s\\s+(?P<span_decorator>['.$argchars.']+?))?% )';
		#--------------------------------[ block dividers ]-----------------------------------#
		$rules->blockdivider = '(?P<blockdivider>
            (?>
			^\\s* \\|:b \\s+(?P<blockdivider_decorator>(['.$argchars.']+?)):\\| \\s* $ 
			)
		)'; # generic block
		#===============================[ preformatted text ]=================================#
		// inline
		$rules->code = '(?P<code>
            (?>
			(%c\\s+(?P<code_decorator>['.$argchars.']+?)%)?{{{ (?P<code_text>.*?) }}} 
			)
		)';
		// block
		$rules->pre = '(?P<pre>
            (?>
            ^(\\|:pre\\s+(?P<pre_decorator>['.$argchars.']+?):\\|)?{{{ \\s* $
            (\\n)?
            (?P<pre_text>
                ([\\#]!(?P<pre_kind>\\w*?)(\\s+.*)?$)?
                (.|\\n)+?
            )
            (\\n)?
            ^}}} \\s*$
			)
        )';
		$rules->pre_escape = ' ^(?P<indent>\\s*) ~ (?P<rest> \\}\\}\\} \\s*) $';
		#================================[ advanced markup ]===============================#
		#--------------------------------[ block declarations ]------------------------------#
		$rules->blockdef = '
            (?>
			(?P<blockdef>
			^\\s*
			\\(:(?P<block_selector>\\w+?)(?P<block_id>\\d*)(\\s+(?P<block_decorator>['.$argchars.']+?))? \\s* :\\)
			\\s*?(?P<block_inline>.*) $
			(?P<block_content>((?!\\n\\s*\\(:(?P=block_selector)(?P=block_id)end\\s*:\\))\\n.*$)*)
			\\n\\s*\\(:(?P=block_selector)(?P=block_id)end\\s*:\\)\\s*$
			)
		)'; #block declarations
		#--------------------------------[ macros ]--------------------------------#
		// inline
		$rules->macro = '(?P<macro>
            (?>
			<<
            (?P<macro_name> \\w+)
            ((?P<macro_args> ['.$argchars.']*) )? \\s*
            ([|] \\s* (?P<macro_text> .+?) \\s* )?
            >>
			)
        )'; 
		// block version to prevent generation of <p> markup
		$rules->blockmacro = '(?P<blockmacro>
            (?>
			^ \\s*
			<<
            (?P<blockmacro_name> \\w+)
            ((?P<blockmacro_args> ['.$argchars.']*) )? \\s*
            ([|] \\s* (?P<blockmacro_text> .+?) \\s* )?
            >> \\s*
			$
			)
        )';
		$rules->decorator = '
			(?>(?P<variable>[\\w-]+)(?P<operator>[:=]))?	# optional attribute or property name, and operator applied
			(
				"(?P<ddelim_value>.*?)(?<!\\\)"				# double quote delimited
			|
				\'(?P<sdelim_value>.*?)(?<!\\\)\'			# single quote delimited
			|
				(?P<ndelim_value>\\S+)						# not delimited
			)
		';
		$this->_rules = $rules;
	}
	#---------------------------------------------------------------------------------------#
	#------------------------------[ set regular expressions ]------------------------------#
	#---------------------------------------------------------------------------------------#
	# combine (set) the above rules into regular expressions
	protected function _set_re($rules)
	{
		// from least to most general
		# For special case pre escaping, in creole 1.0 done with ~:
		$this->pre_escape_re = '/' . $rules->pre_escape . '/xm';
		# For sub-processing:
		$this->link_re = "/\n"
			. implode("\n|\n",
			array($rules->code, $rules->image, $rules->strong, 
				$rules->emph, $rules->span, $rules->linebreak, 
				$rules->escape, $rules->char))
			. "\n/x"; # for link captions
		$this->image_re = "/\n"
			. implode("\n|\n",
				array($rules->link, $rules->code, $rules->strong, 
				$rules->emph, $rules->span, $rules->linebreak, 
				$rules->escape, $rules->char))
			. "\n/x"; # for image captions
		$this->item_re = '/' . $rules->item . '/xm'; # for list items
		$this->cell_re = '/' . $rules->cell . '/x'; # for table cells
/*		$this->cellcontents_re = "/\n" // use full inline_re instead
			. implode("\n|\n",
				array($rules->link, $rules->macro, $rules->code, 
				$rules->image, $rules->escape, $rules->char))
			. "\n/x";*/
		# For inline elements:
		$this->inline_re = "/\n" 
			. implode("\n|\n", 
//				array($rules->link, $rules->url, $rules->macro, // url's not used for performace reasons
				array($rules->link, $rules->macro,
				$rules->code, $rules->image, $rules->strong, $rules->emph, 
				$rules->span, $rules->linebreak, $rules->escape, $rules->char))
			. "\n/x";
		$this->tablerow_setaside_re =  "/\n" 
			. implode("\n|\n", array($rules->link, $rules->macro,$rules->code,$rules->image))
			. "\n/x";
		# For block elements:
		$this->block_re = "/\n" 
			. implode("\n|\n",
				array($rules->line, $rules->blockdef, $rules->heading, 
				$rules->separator, $rules->blockdivider, $rules->blockmacro,
				$rules->pre, $rules->list, $rules->table, $rules->text)) 
			. "\n/xm";
		$this->decorator_re = '/' . $rules->decorator . '/x';
	}
	#---------------------[ process initiation ]--------------------------#
	// structures for setting aside preformatted data before reduction of newline characters
	protected $_pre_markers = array();
	protected $_pre_text = array();
	protected $_pre_count = 0;
	// mark locations of preformatted data; set aside preformatted data
	protected function add_pre_markers($groups)
	{
		$this->_pre_text[] = $groups['pre'];
		$this->_pre_count++;
		$marker = '{{{' . chr(255). $this->_pre_count . '}}}';
		$this->_pre_markers[] = '/{{\\{' . chr(255) . $this->_pre_count . '\\}}}/';
		return $marker;
	}
	protected function reduce_newlines($raw)
	{
		if (preg_match('/\A```##(.*$)/m',$raw,$matches))
		{
			$arguments = trim($matches[1]);
			$this->_metadata = $this->parse_arguments($arguments);
		}
		$raw = "\n".$raw."\n"; // in case there is comment on first line, lookahead on last
		# remove comments
		$raw = preg_replace('/\\n```.*$/m','',$raw);
		# remove line continuations
		$raw = preg_replace('/\\n``/','',$raw);
		# set aside preformatted blocks
		$raw = preg_replace_callback('/'.$this->_rules->pre .'/xm',array($this,'add_pre_markers'),$raw);
		# trim lines, and remove unnecessary newlines
		$raw = preg_replace('/^[ \\t]+|[ \\t]+$/m','',$raw); // trim all lines
		$raw = preg_replace('/((\\w|\\\\)\\n(\\w))+/m','$2 $3',$raw); // replace single line breaks with spaces
		# restore preformatted blocks
		$raw = preg_replace($this->_pre_markers,$this->_pre_text,$raw);
		return $raw;
	}
    public function parse() // initiate parsing
	{
		# try to clean $raw of unnecessary newlines
		$raw = $this->reduce_newlines($this->_raw);
        # parse the text given as $this->_raw...
        $this->_parse_block($raw);
		#...and return DOM tree.
        return $this->_root;
	}
    protected function _parse_block($raw)
	{
        # Recognize block elements.
        preg_replace_callback($this->block_re, array($this,'_replace'), $raw);
	}
    protected function _parse_inline($raw)
	{
        # Recognize inline elements inside blocks.
        preg_replace_callback($this->inline_re, array($this,'_replace'), $raw);
	}
	#---------------------[ process control ]--------------------------#
    protected function _replace($groups) // controller
	{
        # Invoke appropriate _*_repl method. Called for every matched group.
		foreach ($groups as $name => $text)
		{
			if ((!is_int($name)) and ($text != ''))
			{
				$replace = "_{$name}_repl";
				$this->$replace($groups);
				return;
			}
		}
		# special case: pick up empty line for block boundary
		$keys = array_keys($groups);
		$name = 'line';
		if ($keys[count($keys)-2]==$name) // last name in key array indicates returned as found
		{
			$replace = "_{$name}_repl";
			$this->$replace($groups);
			return;
		}
	}
	// common argument structure for decorators and block declarations
	// returns object
	public function parse_arguments($decorator) 
	{
		$arguments = new StdClass();
		$arguments->classes = array();
		$arguments->properties = array();
		$arguments->attributes = array();
		$terms = array();
		preg_match_all($this->decorator_re, $decorator, $terms, PREG_SET_ORDER); // returns terms
		foreach($terms as $term) 
		{
			$variable = $term['variable'];
			$operator = $term['operator'];
			$value = 
				@$term['ddelim_value'] 
				. @$term['sdelim_value'] 
				. @$term['ndelim_value']; // only one will have succeeded
			switch ($operator)
			{
				case '=':
					$arguments->attributes[$variable] = $value;
					break;
				case ':':
					$arguments->properties[$variable] = $value;
					break;
				default:
					$arguments->classes[] = $value;
					break;
			}
		}
		return $arguments;
	}
	// parse arguments from string to structure
	protected function set_decorator($node,$decorator)
	{
		$node->argumentstring = $decorator;
		$node->decorator = $this->parse_arguments($decorator);
		return $node;
	}
	#------------------------------------------------------------------------------#
	#----------------------------[ dom creation ]----------------------------------#
	#------------------------------------------------------------------------------#
    # The _*_repl methods called for matches in regex by 
	# controller (_replace($groups)) where $groups = returned regex (parenthesized) groups
	#=========================[ basic processing ]=================================#
    protected function _char_repl($groups) // can create text leaf node
	{
		# character by character added to text stream
		$char = $this->get_array_value($groups,'char', '');
        if (is_null($this->_textleafnode))
            $this->_textleafnode = new SimpleWiki_DocNode('text', $this->_curnode);
        $this->_textleafnode->content .= $char;
	}
    protected function _escape_repl($groups)
	{
		$char = $this->get_array_value($groups,'escaped_char', '');
        if (is_null($this->_textleafnode))
            $this->_textleafnode = new SimpleWiki_DocNode('text', $this->_curnode);
        $this->_textleafnode->content .= $char;
	}
    protected function _line_repl($groups)
	{
		# triggers new block
        $this->_curnode = $this->up_to($this->_curnode, array('document','blockdef'));
	}
    protected function _text_repl($groups) // can create paragraph for new text
	{
		# text not otherwise classified, triggers creation of paragraph for new set
        $text = $this->get_array_value($groups,'text_chars','') . $this->get_array_value($groups,'text_charstream','');
		$decorator = $this->get_array_value($groups,'paragraph_decorator','');
        if (in_array($this->_curnode->kind, 
			array('table', 'table_row', 'bullet_list', 'number_list'))) // text cannot exist in these blocks
		{
            $this->_curnode = $this->up_to($this->_curnode,
                array('document','blockdef'));
		}
        if (in_array($this->_curnode->kind, array('document','blockdef')))
		{
            $node = $this->_curnode = new SimpleWiki_DocNode('paragraph', $this->_curnode);
			if ($decorator != '') $node = $this->set_decorator($node,$decorator);
		}
        else
            $text = ' ' . $text;
        $this->_parse_inline($text);
        $this->_textleafnode = NULL;
	}
	#================================[ core markup ]===============================#
	#--------------------------------[ basic markup ]------------------------------#
    protected function _heading_repl($groups)
	{
		# headings
		$headtext = $this->get_array_value($groups,'heading_text','');
		$headhead = $this->get_array_value($groups,'heading_head','');
		$decorator = $this->get_array_value($groups,'heading_decorator','');
		
        $this->_curnode = $this->up_to($this->_curnode, array('document','blockdef'));
		
        $node = new SimpleWiki_DocNode('heading',$this->_curnode);
        $node->level = strlen($headhead);
		if ($decorator != '') $node = $this->set_decorator($node,$decorator);

        $parent = $this->_curnode;
        $this->_curnode = $node;
        $this->_textleafnode = NULL;

        $this->_parse_inline($headtext);

        $this->_curnode = $parent;
        $this->_textleafnode = NULL;
	}
    protected function _emph_repl($groups)
	{
		# emphasis
        if ($this->_curnode->kind != 'emphasis')
            $this->_curnode = new SimpleWiki_DocNode('emphasis', $this->_curnode);
        else
		{
			if (!empty($this->_curnode->parent))
				$this->_curnode = $this->_curnode->parent;
		}
        $this->_textleafnode = NULL;
	}
    protected function _strong_repl($groups)
	{
		# strong
        if ($this->_curnode->kind != 'strong')
            $this->_curnode = new SimpleWiki_DocNode('strong', $this->_curnode);
        else
		{
			if (!empty($this->_curnode->parent))
				$this->_curnode = $this->_curnode->parent;
		}
        $this->_textleafnode = NULL;
	}
    protected function _break_repl($groups)
	{
		# line break
        new SimpleWiki_DocNode('break', $this->_curnode);
        $this->_textleafnode = NULL;
	}
    protected function _separator_repl($groups)
	{
        $this->_curnode = $this->up_to($this->_curnode, array('document','blockdef'));
        new SimpleWiki_DocNode('separator', $this->_curnode);
	}
	#--------------------------------[ links ]-------------------------------------#
/* not used for performance reasons
    protected function _url_repl($groups)
	{
        # Handle raw urls in text.
        $target = $this->get_array_value($groups,'url_target','');
        if (empty($groups['escaped_url']))
		{
            # this url is NOT escaped
            $node = new SimpleWiki_DocNode('url', $this->_curnode, $target);
            new SimpleWiki_DocNode('text', $node, $target);
            $this->_textleafnode = NULL;
		}
        else
		{
            # this url is escaped, we render it as text
            if ($this->_textleafnode == NULL)
                $this->_textleafnode = new SimpleWiki_DocNode('text', $this->_curnode,'');
            $this->_textleafnode->content .= $target;
		}
	}
*/
    protected function _link_repl($groups)
	{
        # Handle all kinds of links.
        $target = trim($this->get_array_value($groups,'link_target', ''));
        $text = trim($this->get_array_value($groups,'link_text',''));
		$title = trim($this->get_array_value($groups,'link_title',''));
		$decorator = trim($this->get_array_value($groups,'link_decorator', ''));
		
		$node =  new SimpleWiki_DocNode('link', $this->_curnode,$target);
		if ($decorator != '') $node = $this->set_decorator($node,$decorator);
		if ($title != '') $node->title = $title;

        $parent = $this->_curnode;
        $this->_curnode = $node;
        $this->_textleafnode = NULL;

        preg_replace_callback($this->link_re, array($this,'_replace'), $text);

        $this->_curnode = $parent;
        $this->_textleafnode = NULL;
	}
	#--------------------------------[ images ]-------------------------------------#
	protected function _image_repl($groups)
	{
        # Handles images included in the page.
        $target = trim($this->get_array_value($groups,'image_target',''));
        $text = trim($this->get_array_value($groups,'image_text', ''));
		$title = trim($this->get_array_value($groups,'image_title',''));
		$decorator = trim($this->get_array_value($groups,'image_decorator', ''));
		
        $node = new SimpleWiki_DocNode('image', $this->_curnode, $target);
		if ($decorator != '') $node = $this->set_decorator($node,$decorator);
		if ($title != '') $node->title = $title;

        $parent = $this->_curnode;
        $this->_curnode = $node;
        $this->_textleafnode = NULL;

        preg_replace_callback($this->image_re, array($this,'_replace'), $text);

        $this->_curnode = $parent;
        $this->_textleafnode = NULL;
	}
	#--------------------------------[ lists ]-------------------------------------#
    protected function _list_repl($groups)
	{
		# collect list markup, detail processing by item
        $text = $this->get_array_value($groups,'list','');
        preg_replace_callback($this->item_re,array($this,'_replace'), $text);
	}
    protected function _item_repl($groups)
	{
		# list item
        $bullet = $this->get_array_value($groups,'item_head','');
        $text = $this->get_array_value($groups,'item_text','');
		$listdecorator = $this->get_array_value($groups,'list_decorator','');
		$itemdecorator = $this->get_array_value($groups,'item_decorator','');
        if ($bullet{0} == '#')
            $kind = 'number_list';
        else
            $kind = 'bullet_list';
        $level = strlen($bullet);
        $list = $this->_curnode;
        # Find a list of the same kind and level up the tree
        while 
		(
			$list // searching an existing node
			and ! // this is a not a list of the same level...
			(
				in_array($list->kind, array('number_list', 'bullet_list')) 
				and $list->level == $level
			)
			and ! // this is not a block
			(
				in_array($list->kind, array('document','blockdef'))
			)
		) // keep looking
		{
            $list = $list->parent;
		}
        if ($list and ($list->kind == $kind)) // found a match
            $this->_curnode = $list;
        else
		{
            # Create a new level of list
            $this->_curnode = $this->up_to($this->_curnode,
                array('list_item', 'document','blockdef'));
            $node = $this->_curnode = new SimpleWiki_DocNode($kind, $this->_curnode);
			if ($listdecorator != '') $node = $this->set_decorator($node,$listdecorator);
            $this->_curnode->level = $level;
		}
        $node = $this->_curnode = new SimpleWiki_DocNode('list_item', $this->_curnode);
		if ($itemdecorator != '') $node = $this->set_decorator($node,$itemdecorator);
		$this->_textleafnode = NULL;
		
        $this->_parse_inline($text);
        $this->_textleafnode = NULL;
	}
	#--------------------------------[ tables ]-------------------------------------#
	// structure to set aside row contents before parsing row structure itself
	protected $_tablerow_markers = array();
	protected $_tablerow_text = array();
	protected $_tablerow_count = 0;
	// contents marked to be replaced after row structure parsed
	protected function add_tablerow_markers($groups)
	{
		$value = 
			(
				@$groups['link']?
					@$groups['link']:(@$groups['macro']?
						@$groups['macro']:(@$groups['code']?
							@$groups['code']:@$groups['image'])));
		$this->_tablerow_text[] = $value;
		$this->_tablerow_count++;
		$marker = '{{{' . chr(255). $this->_tablerow_count . '}}}';
		$this->_tablerow_markers[] = '/{{\\{' . chr(255) . $this->_tablerow_count . '\\}}}/';
		return $marker;
	}
    protected function _table_repl($groups)
	{
		# process a table row (any line beginning with '|')
        $row = trim($this->get_array_value($groups,'table_row', ''));
		$tdecorator = trim($this->get_array_value($groups,'table_decorator', ''));
		$rdecorator = trim($this->get_array_value($groups,'row_decorator', ''));
		
		// set aside links and preformats and macros and images
		$row = preg_replace_callback(
			$this->tablerow_setaside_re,
			array($this,'add_tablerow_markers'),$row);
		
		$row = preg_replace('/((?<!:)[|](?=[|]))/','| ',$row); // ensure content for every cell

        $this->_curnode = $this->up_to($this->_curnode, array('table','document','blockdef'));
		$newtable = FALSE;
        if ($this->_curnode->kind != 'table')
		{
            $this->_curnode = new SimpleWiki_DocNode('table', $this->_curnode);
			$newtable = TRUE;
		}
        $tb = $this->_curnode;
		if ($tdecorator != '') $tb = $this->set_decorator($tb,$tdecorator);

        $text = '';
		$isheader = FALSE;
		$result = preg_match_all($this->cell_re, $row, $cells,PREG_SET_ORDER);
		if ($newtable)
		{
			foreach ($cells as $cellgroups)
			{
				if ($cellgroups['head'] != '')
				{
					$isheader = TRUE;
					break;
				}
			}
		}
		if ($isheader)
			$tr = new SimpleWiki_DocNode('table_headrow', $tb);
		else
			$tr = new SimpleWiki_DocNode('table_row', $tb);
		if ($rdecorator != '') $tr = $this->set_decorator($tr,$rdecorator);
		// now have table and row, can process cells
        $this->_textleafnode = NULL;
		foreach ($cells as $cellgroups)
		{
			$cell = $this->get_array_value($cellgroups,'cell','');
			$head = $this->get_array_value($cellgroups,'head','');
			$decorator = $this->get_array_value($cellgroups,'cell_decorator','');
            if ($head) 
			{
				$cell = trim($head,'=');
                $node = $this->_curnode = new SimpleWiki_DocNode('table_headcell', $tr);
			}
			else
			{
                $node = $this->_curnode = new SimpleWiki_DocNode('table_cell', $tr);
			}
            $this->_textleafnode = NULL;
			if ($decorator != '') $node = $this->set_decorator($node,$decorator);
			// restore links and preformats and macros and images
			$cell = preg_replace($this->_tablerow_markers,$this->_tablerow_text,$cell);
			preg_replace_callback($this->inline_re, array($this,'_replace'), $cell);
		}
        $this->_curnode = $tb;
        $this->_textleafnode = NULL;
		
		$this->_tablerow_markers = array();
		$this->_tablerow_text = array();
		$this->_tablerow_count = 0;
	}
	#================================[ special decorators ]=============================#
	#--------------------------------[ span decoration ]--------------------------------#
    protected function _span_repl($groups)
	{
		# span
		$decorator = $this->get_array_value($groups,'span_decorator','');
        if ($decorator != '') // new span
		{
            $node = $this->_curnode = new SimpleWiki_DocNode('span', $this->_curnode);
			$node = $this->set_decorator($node,$decorator);
			$this->_textleafnode = NULL;
		}
        elseif ($this->_curnode->kind == 'span') // closing existing span
		{
			if (!empty($this->_curnode->parent))
			{
				$this->_curnode = $this->_curnode->parent;
				$this->_textleafnode = NULL;
			}
		}
		else // error, return text
		{
			if ($this->_textleafnode == NULL)
				$this->_textleafnode = new SimpleWiki_DocNode('text', $this->_curnode);
			$this->_textleafnode->content .= $groups['span'];
		}
	}
	#--------------------------------[ block dividers ]--------------------------------#
    protected function _blockdivider_repl($groups)
	{
		# empty block acting as block divider
		$decorator = $this->get_array_value($groups,'blockdivider_decorator','');
        $this->_curnode = $this->up_to($this->_curnode, array('document','blockdef'));
        $node = new SimpleWiki_DocNode('blockdivider', $this->_curnode);
		if ($decorator != '') $node = $this->set_decorator($node,$decorator);
	}
	#============================[ preformatted text ]=================================#
    protected function _code_repl($groups)
	{
		# preformatted inline text
		$codetext = $this->get_array_value($groups,'code_text', '');
		$decorator = trim($this->get_array_value($groups,'code_decorator', ''));
		
        $node = new SimpleWiki_DocNode('code', $this->_curnode, trim($codetext));
		if ($decorator != '') $node = $this->set_decorator($node,$decorator);
        $this->_textleafnode = NULL;
	}
    protected function _pre_repl($groups)
	{
		# process preformatted text
        $kind = $this->get_array_value($groups,'pre_kind', NULL);
        $text = $this->get_array_value($groups,'pre_text', '');
		$decorator = $this->get_array_value($groups,'pre_decorator','');
		
        $this->_curnode = $this->up_to($this->_curnode, array('document','blockdef'));
        $text = preg_replace_callback($this->pre_escape_re,array($this,'remove_tilde'), $text);
        $node = new SimpleWiki_DocNode('preformatted', $this->_curnode, $text);
        $node->section = $kind?$kind:'';
		if ($decorator != '') $node = $this->set_decorator($node,$decorator);
        $this->_textleafnode = NULL;
	}
    private function remove_tilde($groups)
	{
		# used in pre processing
        return $groups['indent'] . $groups['rest'];
	}
	#================================[ advanced markup ]===============================#
	#--------------------------------[ block declarations ]------------------------------#
	protected function _blockdef_repl($groups)
	{
		# block definitions
		$name = $this->get_array_value($groups,'block_selector','');
		$content = $this->get_array_value($groups,'block_content','');
		$decorator = $this->get_array_value($groups,'block_decorator','');
		$inline = $this->get_array_value($groups,'block_inline','');
		$blockid = $this->get_array_value($groups,'block_id','');
		
        $container = $this->_curnode = $this->up_to($this->_curnode,
            array('document','blockdef','list_item'));
        $node = $this->_curnode = new SimpleWiki_DocNode('blockdef', $container);
		$node->blocktag = $name;
		$node->blockid = $blockid;
		if ($decorator != '') $node = $this->set_decorator($node,$decorator);
		
		$this->_textleafnode = NULL;
        if ($inline != '') $this->_parse_inline($inline);
		$this->_textleafnode = NULL;
        if ($content != '') $this->_parse_block($content);
		$this->_curnode = $container;
        $this->_textleafnode = NULL;
		
	}
	#-----------------------------------[ macros ]-------------------------------------#
	protected function _macro_repl($groups)
	{
        # Handles macros using the placeholder syntax.
        $name = $this->get_array_value($groups,'macro_name', '');
        $text = trim($this->get_array_value($groups,'macro_text',''));
		$decorator = $this->get_array_value($groups,'macro_args', '');

		$container = $this->_curnode;
        $node = new SimpleWiki_DocNode('macro', $container);
		$node->macroname = $name;

        if ($decorator != '') $node = $this->set_decorator($node,$decorator);
		if ($text != '')
		{
			$node->text = $text;
			$this->_curnode = $node;
			$this->_textleafnode = NULL;
			$this->_parse_inline($text);
			$this->_curnode = $container;
		}
        $this->_textleafnode = NULL;
	}
	protected function _blockmacro_repl($groups)
	{
        # Handles macros using the placeholder syntax. block version
        $name = $this->get_array_value($groups,'blockmacro_name', '');
        $text = trim($this->get_array_value($groups,'blockmacro_text',''));
		$decorator = $this->get_array_value($groups,'blockmacro_args', '');
		
        $container = $this->_curnode = $this->up_to($this->_curnode, array('document','blockdef')); // different from macro
        $node = new SimpleWiki_DocNode('macro', $this->_curnode);
		$node->macroname = $name;
        if ($decorator != '') $node = $this->set_decorator($node,$decorator);
		if ($text != '')
		{
			$node->text = $text;
			$this->_curnode = $node;
			$this->_textleafnode = NULL;
			$this->_parse_inline($text);
			$this->_curnode = $container;
		}
        $this->_textleafnode = NULL;
	}
	#------------------------------------------------------------------------------#
	#---------------------------[ utilities ]--------------------------------------#
	#------------------------------------------------------------------------------#
    public function up_to($node, $kinds) // public as can be used by registered callbacks
	{
        /*
        Look up the tree (starting with $node) to the first occurence
        of one of the listed kinds of nodes or root.
		If $node is in the list then the current node is returned.
        */
//        while ((!is_null($node->parent)) and (!in_array($node->kind,$kinds)))
        while ((!empty($node->parent)) and (!in_array($node->kind,$kinds)))
		{
            $node = $node->parent;
		}
        return $node;
	}
	protected function get_array_value($array,$index,$default)
	{
		# return default if array value not set
		return isset($array[$index])?$array[$index]:$default;
	}
	#------------------------------------------------------------------------------#
	#---------------------------[ debug functions ]--------------------------------#
	#------------------------------------------------------------------------------#
	public function display_regex() // for debug
	{
		echo 'BLOCK_RE ';
		var_dump($this->block_re);
		echo 'INLINE_RE ';
		var_dump($this->inline_re);
		echo 'LINK_RE ';
		var_dump($this->link_re);
		echo 'ITEM_RE ';
		var_dump($this->image_re);
		echo 'ITEM_RE ';
		var_dump($this->item_re);
		echo 'CELL_RE ';
		var_dump($this->cell_re);
/*		echo 'CELLCONTENTS_RE ';
		var_dump($this->cellcontents_re);*/
		echo 'PRE_ESCAPE_RE ';
		var_dump($this->pre_escape_re);
		echo 'DECORATOR_RE ';
		var_dump($this->decorator_re);
	}
	public function display_dom($root) // for debug
	{
		$count = 1;
		$rootarray = array();
		$count += $this->display_dom_add_child($root,$rootarray);
		$rootarray = $rootarray[0];
		print_r($rootarray);
		return $count;
	}
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
}

