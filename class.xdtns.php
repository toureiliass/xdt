<?php
/** 
 * XDTNS at first glance, aims to behave like jQuery on server side. We all know that there already so 
 * many powerful tools that can traverse easly XML DOM such as simpleXML, XPath and so on. But sometimes, 
 * we need something that is familiar, intitive; not only because it can do more than existing ones, 
 * but because it remind us things we know already. XDTNS a voulu perpetre le slogan de jQuery qui consiste 
 * a ecrire peu de lignes et faire beacoup. Pour y parvenir il imite la technique d'enchainement des methodes
 * de jQuery, la technique de selection de jQuery, et ce n'est pas tout, il utilise aussi les meme nom de methodes 
 * que jQuery et qui font presque les meme choses.
 * 
 * ===========================================================================================================
 * @author Abdoul-Madjid Touré
 * Developper & Designer
 * ===========================================================================================================
 * Code website
 * @link
 * ===========================================================================================================
 * @copyright Abdoul-Madjid Touré All Right Reserved
 * @license MIT
 * ===========================================================================================================
 * @since 29/07/2015
 * @version 2.3
 * ===========================================================================================================
 *
 */

/**
 * XDTNS class has no dependencies. It supports PHP version 5.3 and ealier.
 * 
 * XDTNS traverses XML DOM; It implements various methods to facilidate elements selection; such as getElementById, 
 * getElementsByClass, getElementsByAttr, getElementsByPseudo; the selector used for elements selection respects 
 * CSS structure, it also defines jQuery special selectors, such as nth, first, last, eq, and not
 * 
 * @author Touré Iliass
 * @version 2.3
 *
 */
class XDTNS {
	
	/** =========================================================== **/
	/** ===================== CONSTANTS =========================== **/
	/**
	 * Class constants
	 */
	
	/**
	 * When set it detroys the last selection result set and starts over a new selection.
	 * 
	 * @var integer
	 */
	const SELECT_DESTROY = 0;
	
	/**
	 * When set it Filters the last selection result set.
	 * 
	 * @see XDTNSNodeList
	 * @var integer
	 */
	const SELECT_FILTER = 1;
	
	/**
	 * Holds the loaded file directory.
	 * 
	 * @var string
	 */
	protected $xml_dir;

	/**
	 * Holds XML document element.
	 * 
	 * @var DOMElement
	 */
	protected $parser;  

	/**
	 * Holds the document root element.
	 * 
	 * @var DOMElement
	 */
	protected $root;           
	
	/**
	 * A set of the current matched elements.
	 * 
	 * @var XDTNSNodeList
	 */
	protected $xml_query = null; 
	
	protected $namespace_uri = null;
	
	private $file_name = null; 
	
	/**
	 * Constructor
	 * 
	 * Create a new XDTNS's instance.
	 * 
	 * @param string $namespace_uri [optional] <p>
	 * 	   Namespace URI.</p>
	 * @param string $xml_dir [optional] <p>
	 *     Directory containing the XML file. When omitted and connect method is called instead, 
	 *     the file is search within the current directory.</p>
	 * @return void
	 */
	function __construct($namespace_uri = '*', $xml_dir = '.') {
		$this->namespace_uri = $namespace_uri;
		$this->xml_dir = dir($xml_dir);
	}
	
	/**
	 * Load XML from a file.
	 * 
	 * @see XDTNS::load
	 * @param string $file_name <p>
	 *     XML file to load. The file extension (.xml) is optional.</p>
	 * @param boolean $preserve_white_space [optional] <p>
	 * 		Do not remove redundant white space. Default to false.</p>
	 * @param boolean $format_output [optional] <p>
	 * 		Nicely formats output with indentation and extra space. Default to false.</p>
	 * @return boolean <p>
	 * 		Returns true on success, or false on failure.</p>
	 *     
	 */
	final function connect($file_name, $preserve_white_space = false, $format_output = false) { 
		
		if (!stristr($file_name, '.xml')) {
			$file_name .= '.xml';
		}
		
		$this->parser = new DOMDocument();
		$this->parser->preserveWhiteSpace = $preserve_white_space;
		$this->parser->formatOutput = $format_output;
		
		$this->file_name = $file_name;
		$this->xml_query = null;
		
		if ($this->parser->load($this->xml_dir->path . DIRECTORY_SEPARATOR . $file_name)) {
			$this->root = $this->parser->childNodes->item(0);
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Load a string containing XML structure.
	 * 
	 * @param string $xml <p>
	 * 		String containing XML structure.</p>
	 * @param boolean $preserve_white_space [optional] <p>
	 * 		Do not remove redundant white space. Default to false.</p>
	 * @param boolean $format_output [optional] <p>
	 * 		Nicely formats output with indentation and extra space. Default to false.</p>
	 * @return boolean <p>
	 * 		Returns true on success, or false on failure.</p>
	 */
	final function load ($xml, $preserve_white_space = false, $format_output = false) {
		
		$this->parser = new DOMDocument();
		$this->parser->preserveWhiteSpace = $preserve_white_space;
		$this->parser->formatOutput = $format_output;
		
		$this->xml_query = null;
		
		if ($this->parser->loadXML($xml)) {
			$this->root = $this->parser->childNodes->item(0);
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Set the directory.
	 * 
	 * @param string $new_dir <p>
	 * 		New directory.</p>
	 * @return void
	 */
	final function setDirectory ($new_dir) { $this->xml_dir = dir($new_dir); }
	
	/**
	 * Get the current directory.
	 * 
	 * @return string <p>
	 * 		The directory path.</p>
	 */
	final function getDirectory () { return $this->xml_dir->path; }
	
	/** 
	 * Get the document root element.
	 * 
	 * @return XDTNSNodeList
	 */
	final function getDocumentRootElement () { return new XDTNSNodeList($this->root); }
	
	/**
	 * Closes the current xml file and saves the changes to the file.
	 * 
	 * @return Boolean <br><br>
	 *     Returns TRUE on success, or FALSE on error or failure.
	 */
	final function close() { return $this->parser->save($this->xml_dir->path . DIRECTORY_SEPARATOR . $this->file_name, LIBXML_NOEMPTYTAG); }
	
	/**
	 * Save the loaded XML to a string.
	 * 
	 * @return mixed <p>
	 * 		Returns true on success, or false on failure.</p>
	 */
	final function save() { return $this->parser->saveXML($this->root, LIBXML_NOEMPTYTAG); }
	
	/**
	 * Parse a string containing a CSS selector expression, match the selected elements and 
	 * returns a XDTNSNodeList object containing the set of matched elements; XDTNSNodeList class 
	 * implements arrayAccess and iteratorAgregate interfaces, so its objects can to be treated like an array; jQuery specific selectors (first, last, nth, eq) are also supported.
	 *     
	 * @param string $selector [optional] <p>
	 *     A string containing the selector expression. You can provide multiples selectors 
	 *     by separating them with a comma (,).</p>
	 * @param DOMElement $context [optional] <p>
	 *     DOM element used as the selection context.</p>
	 * @param integer $flag [optional] <p>
	 * 	   Accepted flags are: <br>
	 *     <ul>
	 *       <li>XDTNS::SELECT_DESTROY end the current operation and start over a new operation.</li>
	 *       <li>XDTNS::SELECT_FILTER filter the current result set.</li>
	 *     </ul>
	 * @return XDTNSNodeList
	 */
	final function select($selector = '*', DOMElement $context = null, $flag = XDTNS::SELECT_DESTROY) {
		
		/** ------------------------------------------------------- **/
		/** ================ SELECTION FLAG ======================= **/
		/** ------------------------------------------------------- **/
		switch ($flag) {
			case XDTNS::SELECT_DESTROY: $this->xml_query = null; break;
			case XDTNS::SELECT_FILTER: ; break;
		}
		
		/** ------------------------------------------------------- **/
		/** ================ SELECTION CONTEXT ==================== **/
		/** ------------------------------------------------------- **/
	    if (isset($context)) {
	    	$all = $context->getElementsByTagName('*');
	    	$l = new XDTNSNodeList();
	    	foreach ($all as $node) {
	    		$l->add($node);
	    	}
	    	$this->xml_query = $l;
		    //$this->xml_query = new XDTNSNodeList($context);
		    //$this->xml_query = $this->xml_query->find(); 
		}
		
		/** ------------------------------------------------------- **/
		/** ================ SELECT MULTIPLE ====================== **/
		/** ------------------------------------------------------- **/
		if (preg_match('/[,]/', $selector)) return $this->mSelect($selector);
		
		$selector = $this->parse(trim($selector)); // Convert selector to code logic
		
		$chunks = preg_split('/[\s>+]/', $selector, -1, PREG_SPLIT_NO_EMPTY);
		$glues = preg_split('/[^\s>+]/', $selector, -1, PREG_SPLIT_NO_EMPTY);
		
		if (empty($glues)) return $this->query($selector);
		
		$this->xml_query = $this->query($chunks[0]);
		
		for ($index = 1; $index < count($chunks); $index++) {
			
			/**
			 * Cast query result to NodeList.
			 */
			if (is_object($this->xml_query) AND get_class($this->xml_query) === 'DOMElement') {
				$this->xml_query = new XDTNSNodeList($this->xml_query);
			}
			
			switch (trim($glues[$index-1])) {
				case '': // Select descendent elements
					
					$list = new XDTNSNodeList();
					
					foreach ($this->xml_query as $node) 
						foreach ($node->getElementsByTagName('*') as $n) $list->add($n);
					
					$this->xml_query = $list;
					$this->xml_query = $this->query($chunks[$index]); break;
				case '>': // Select children elements
				    
				    $list = new XDTNSNodeList();
					
					foreach ($this->xml_query as $node) {
						foreach ($node->childNodes as $child) {
							if ($child->nodeType == 3) continue;
							$list->add($child);
						}
					}
						
					$this->xml_query = $list; 
					$this->xml_query = $this->query($chunks[$index]); break;
				case '+': // Select adjacent-element
				    
					$list = new XDTNSNodeList();
					
					foreach ($this->xml_query as $node) {
						foreach ($node->parentNode->childNodes as $child) {
							if ($child->nodeType == 3) continue;
							$list->add($child);
						}
					}
					
					$this->xml_query = $list;
					$this->xml_query = $this->query($chunks[$index]); break;
			}
		}
		
		if (is_object($this->xml_query) AND get_class($this->xml_query) === 'DOMElement') {
			$this->xml_query = new XDTNSNodeList($this->xml_query);
		}
		
		return $this->xml_query;
	}
	
	private function mSelect ($selectors) {
		
		$chunks = preg_split('/[,]/', $selectors, -1, PREG_SPLIT_NO_EMPTY);
		
		$list = new XDTNSNodeList();
		
		foreach ($chunks as $selector) { 
			
			$list->merge($this->select($selector));
			$this->xml_query = null;
		}
		
		return $list;
	}
    
	private function parse ($selector) {
		
		$pattern = '/([\s\.:]([^:#\.]+:(first|first-child|last|last-child|nth|nth-child|eq)))/';
	    if (preg_match($pattern, $selector)) {
	    	
	    	$matches = array();
	    	preg_match_all($pattern, $selector, $matches);
	    	
			foreach ($matches[2] as $match) {
				
				$tmp = $match;
				$match = str_replace(':', ';', $match);
				$selector = str_replace($tmp, $match, $selector);
			}
		}
		
		$pattern = '/\[([^\[=\^\$<>\|\*]+)([=\^\$<>\|\*]+)?([^\.!&#%\]]+)?\]/';
		if (preg_match($pattern, $selector)) {
			
			$selector = str_replace('[', '%[', $selector);
			
			$matches = array();
			preg_match_all($pattern, $selector, $matches);
			
			foreach ($matches[2] as $match) {
				
				$tmp = $match;
				$match = str_replace('>', '|', $match);
				$selector = str_replace($tmp, $match, $selector);
			}
			
			foreach ($matches[3] as $match) {
				
				$tmp = $match;
				$match = trim($match, '\'"');
				$match = str_replace(' ', '~', $match);
				$selector = str_replace($tmp, $match, $selector);
			}
		}
		
		if (preg_match('/\([n0-9\+]+\)/', $selector)) {
			
			$matches = array();
			preg_match_all('/\([n0-9\+]+\)/', $selector, $matches);
			
			foreach ($matches[0] as $match) {
				$tmp = $match;
				$match = str_replace('+', 'p', $match);
				$selector = str_replace($tmp, $match, $selector);
			}
		}
		
		if (preg_match('/([^#]+)(#)/', $selector)) {
			
			$selector = preg_replace('/([^#]+)(#)/', '\\1&', $selector);
		}
		
		if (preg_match('/([^\.]+)(\.)/', $selector)) {
			
			$selector = preg_replace('/([^\.]+)(\.)/', '\\1!', $selector);
		}
		
		return $selector;
	}
	
	private function query ($selector) {
		
		$selector = trim($selector);
		$chunks = array();
		$glues = array();
		
		if(preg_match('/[^%!&\s]+[%!&]/', $selector)) {
			$chunks = preg_split('/[%!&]/', $selector, -1, PREG_SPLIT_NO_EMPTY);
		    $glues = preg_split('/[^%!&]/', $selector, -1, PREG_SPLIT_NO_EMPTY);
		}
		
		if(empty($glues)) { 
			$glue = substr($selector, 0, 1);
			$selector = substr($selector, 1);
			
			switch ($glue) {
				case '&':
		        case '#': return $this->getElementById($selector);
		        case '!':
		        case '.': return $this->getElementsByClass($selector);
				default: 
					
					if (strpos($selector, ';') !== false) { 
						
						$chunks = explode(';', $glue . $selector, 2);
						return $this->getElementsByPseudo($chunks[0] . '%' . $chunks[1]);
					}
					
					return $this->getElementsByTagName($glue . $selector);
			}
		} else {
			
			switch ($glues[0]) {
		        case '&': return $this->getElementById($chunks[1], $chunks[0]); 
		        case '!': return $this->getElementsByClass(join('!', array_splice($chunks, 1)), $chunks[0]);
				case '%': return $this->getElementsByAttr($selector);
				default: return $this->xml_query;
			}
		}
	}
	
	private function getElementById ($selector, $tag_name = null) {
		
		$l = new XDTNSNodeList();
		
		if (isset($tag_name)) {
			
			$this->xml_query = $this->getElementsByTagName($tag_name);
			
			foreach ($this->xml_query as $node) 
				if (strtolower($node->nodeName) === strtolower($tag_name) AND @$node->attributes->getNamedItemNS($this->namespace_uri, 'id')->value === $selector) {
					
					$l->add($node);
					$this->xml_query = $l;
					
					return $l;
				}
		}
		
		$this->xml_query = $this->getElementsByTagName('*');
		
		foreach ($this->xml_query as $node) 
			if (@$node->attributes->getNamedItemNS($this->namespace_uri, 'id')->value === $selector) {
				
				$l->add($node);
				$this->xml_query = $l;
				
				return $l;
			}
		
		$this->xml_query = $l;
		
		return $l;
	}
	
	private function getElementsByClass ($selector, $tag_name = null) {
		
		$l = new XDTNSNodeList(); 
		
		if (strpos($selector, ';')) { 
				
			$chunks = explode(';', $selector);
		    $pseudo = $chunks[1];
		    
		    if (preg_match('/(nth-child|nth|first|first-child|last|last-child|eq|not)(\(([evnodp0-9]+)\))?/', $pseudo) == false) $pseudo = null;
		    
		    $selector = $chunks[0];
		} else {
			
			$pseudo = null;
		}
		
		if (isset($tag_name)) $this->xml_query = $this->getElementsByTagName($tag_name);
		elseif ($this->xml_query == null AND get_class($this->root) === 'DOMElement') $this->xml_query = $this->getElementsByTagName('*');
		
		foreach ($this->xml_query as $node) {
			$node = $this->initListObject($node);
			$node->setNamespaceURI($this->namespace_uri);
			
			if ($node->hasAttr('class') === false) continue;
			
			$classes = preg_split('/\s/', $node->attr('class'), -1, PREG_SPLIT_NO_EMPTY);
			$values = array_unique(explode('!', $selector));
			$count = sizeof($values);
			while (list($key, $value) = each($values)) {
				if (in_array($value, $classes)) $count = $count-1;
			} 
			if ($count === 0) $l->add($node[0]);
		}
		
		$this->xml_query = $l;
		
		if (isset($pseudo)) return $this->getElementsByPseudo('*%' . $pseudo);
		else return $this->xml_query;
	}
	
	protected function getElementsByPseudo ($selector) {
		
		$chuncks = explode('%', $selector);
		$list = new XDTNSNodeList();
		
		//if ($chuncks[0] == 'root') return $this->getDocumentRootElement();
		
		if ($this->xml_query == null AND get_class($this->root) === 'DOMElement') $this->xml_query = $this->getElementsByTagName($chuncks[0]);
		
		foreach ($this->xml_query as $node) {
			
			$key = 0;
			foreach ($node->parentNode->childNodes as $n) {
				
				if ($n->nodeType === 3) continue;
				if ($list->index($n) !== -1 OR !$n->isSameNode($node)) {
					
					$key++;
					continue;
				}
				
				switch ($chuncks[1]) {
	    		case 'first-child':
	    		case 'first': 
	    			
	    			if (($key === 0 AND $n->isSameNode($node))) $list->add($node);
	    			
	    			break;
	    		case 'last-child':
	    		case 'last': 
	    			
	    			if ($n->isSameNode($node)) {
	    				
	    				$count = 1;
	    				while ($node->parentNode->childNodes->item($node->parentNode->childNodes->length-$count)->nodeType === 3) $count++;
	    				
	    				if ($n->isSameNode($node->parentNode->childNodes->item($node->parentNode->childNodes->length-$count))) $list->add($node);
	    			}
	    			
	    			break;
	    		default:
	    			
	    			$matches = array();
	    			preg_match('/(nth-child|nth|eq)\(([evnodp0-9]+)\)/', $chuncks[1], $matches);
	    			
	    			if (in_array($matches[1], array('nth', 'nth-child', 'eq'))) {
	    				
	    				if ($matches[2] === 'even') {
	    					if (($key+1)%2==0) $list->add($node);
	    				} elseif ($matches[2] === 'odd') {
	    					if (($key+1)%2!=0) $list->add($node);
	    				} elseif (strpos($matches[2], 'n') === false) {
	    					if ($matches[2] == $key+1) $list->add($node);
	    				} elseif (preg_match('/[np0-9]+/', $matches[2])) {
	    					
	    					if (count(explode('p', $matches[2])) > 1) { 
	    						$ops = array();
	    						preg_match('/([0-9]+)?np([0-9]+)?/', $matches[2], $ops);
	    						 
	    						if (empty($ops[1])) $ops[1] = 1;
	    					} else {
	    						$ops = array();
	    						preg_match('/([0-9]+)?n/', $matches[2], $ops);
	    						 
	    						if (empty($ops[1])) $ops[1] = 1;
	    					}
	    					
	    					if (!isset($ops[2])) $ops[2] = 0;
	    					
	    				    if ((($ops[1]*$key)+$ops[2]) === ($key+1)) $list->add($node);
	    				}
	    			}
	    		}
	    		
	    		$key++;
			} 
		}
		
		$this->xml_query = $list;
		
		return $this->xml_query;
	}
	
	private function getElementsByAttr ($selector) { 
		
		$matches = array();
		$pattern = '/^(([^:\.#%&\^@]+:)?[^:\.#&\^@]+)(%)\[([^=\*\^\$~\|<]+)([=\*\^\$~\|<]+)?([^=\*\^\$\|<]+)?\]$/';
		preg_match($pattern, $selector, $matches);
		
		$list = new XDTNSNodeList();
		
		// Verify if the attr structure is correct.
		if (empty($matches)) {
			
			$this->xml_query = new XDTNSNodeList();
			return $this->xml_query;
		}
		
		$this->xml_query = $this->getElementsByTagName($matches[1]);
		
		$attr_name = $matches[4];
		
		// Verify if the attr value is assigned.
		if (isset($matches[5]) AND isset($matches[6])) {
			
			foreach ($this->xml_query as $node) {
				
				$operator = $matches[5];
				$attr_value = str_replace('~', ' ', $matches[6]);
				
			    if ($node->attributes->getNamedItemNS($this->namespace_uri, $attr_name)) {
			    	
			    	switch ($operator) {
			    		/** Attr is egal to a specific value **/
			    		case '=': if (strcmp($node->attributes->getNamedItemNS($this->namespace_uri, $attr_name)->value, $attr_value) == 0) $list->add($node); break;
			    		/** Attr contains a specific value **/
			    		case '*=': if (strstr($node->attributes->getNamedItemNS($this->namespace_uri, $attr_name)->value, $attr_value)) $list->add($node); break;
			    		/** Attr value starts from a specific value **/
			    		case '^=': if (preg_match("/^$attr_value/", $node->attributes->getNamedItemNS($this->namespace_uri, $attr_name)->value)) $list->add($node); break;
			    		/** Attr value ends with a specific value **/
			    		case '$=': if (preg_match("/$attr_value$/", $node->attributes->getNamedItemNS($this->namespace_uri, $attr_name)->value)) $list->add($node); break;
			    		/** Attr value is greater than a value **/
			    		case '|': if ($node->attributes->getNamedItemNS($this->namespace_uri, $attr_name)->value > $attr_value) $list->add($node); break;
			    		/** Attr value is greater than or egal to a value **/
			    		case '|=': if ($node->attributes->getNamedItemNS($this->namespace_uri, $attr_name)->value >= $attr_value) $list->add($node); break;
			    		/** Attr value is less than a value **/
			    		case '<': if ($node->attributes->getNamedItemNS($this->namespace_uri, $attr_name)->value < $attr_value) $list->add($node); break;
			    		/** Attr value is less than or egal to a value **/
			    		case '<=': if ($node->attributes->getNamedItemNS($this->namespace_uri, $attr_name)->value <= $attr_value) $list->add($node); break;
			    		/** Operator does not exists **/
			    		//default: $list->add($node); break;
			    	}
			    }
			}
			
			$this->xml_query = $list;
			
			return $this->xml_query;
		}
		
		foreach ($this->xml_query as $node) {
			
		    if ($node->attributes->getNamedItemNS($this->namespace_uri, $attr_name)) $list->add($node);
		}
		
		$this->xml_query = $list;
			
		return $this->xml_query;
	}
	
	private function getElementsByTagName ($tag) {
		
		if ($this->xml_query == null AND get_class($this->root) === 'DOMElement') $this->xml_query = $this->root->getElementsByTagNameNS($this->namespace_uri, '*');
		
		if ($tag === '*') return $this->xml_query;
		
		$list = new XDTNSNodeList();
		
		foreach ($this->xml_query as $node) {
			if (($node->nodeType === 3)) continue;
			
			if (strtolower($node->nodeName) === $tag) { 
				$list->add($node);
			}
		}
		
		$this->xml_query = $list;
		
		return $this->xml_query;
	}
	
	private function process($content) {
		
		if (is_string($content)) {
			if (preg_match('/^<.*>$/', $content)) {
				$this->parser = $this[0]->ownerDocument;
				$content = $this->__($content);
			} else {
				$this->end();
				$content = $this->select($content);
			}
		} elseif (is_object($content)) {
			if (get_class($content) !== 'XDTNSNodeList' OR get_class($content) !== 'DOMElement') return null;
		}
		
		return $content;
	}
	
	protected function setNamespaceURI($namespace_uri) { $this->namespace_uri = $namespace_uri; }
	
	protected function getNodeList () { return $this->list; }
	
	protected function __($data) {
		
		$frag = $this->parser->createDocumentFragment();
		if ($frag->appendXML($data)) {
			return $frag;
		}
		
		return false;
	}
	
	/**
	 * Cast DOMElement to XDTNSNodeList object.
	 * 
	 * @param DOMElement $node
	 * @return XDTNSNodeList
	 */
	public function initListObject(DOMElement $node) { return new XDTNSNodeList($node); }
	
	/**
	 * Creates a new XML file.
	 * 
	 * If the file already exists, it will be destroyed.
	 * 
	 * @param String $file_name <p>
	 *     The name of the file to create. Do not specify path within the file name.</p>
	 * @param string $root [optional] <p>
	 *     A string containing the root element structure.</p>
	 * @return boolean <p>
	 * 	   True on success, or False on error or failure.</p>
	 */
	public function createNewXMLFile ($file_name, $root = null, $charset = 'UTF-8') {
		
		$file_name = $this->xml_dir->path . DIRECTORY_SEPARATOR . $file_name;
		
		$xml = "<?xml version=\"1.1\" encoding=\"$charset\"?>\n";
		
		if (isset($root)) {
			
			$xml .= $root;
		}
		
		$handle = fopen($file_name, "w+");
		fwrite($handle, $xml);
		fclose($handle);
	}
}

/**
 * Build a list of DOM elements maintaining the parental relationship.
 * And defines appropriate methods to work on the list elements.
 * 
 * This class contains jQuery like methods for document traversing and document altering.
 * It implements IteratorAggregate and ArrayAccess interfaces, which allow class objects to behave like an array.
 * 
 * @tutorial Changes made by a method of this class are not automaticaly saved. Explicit call to the XDTNS close method will be required to save changes.
 * 
 * @author Touré Iliass
 * @package XDTNS
 * @version 2.3
 *
 */
class XDTNSNodeList extends XDTNS implements IteratorAggregate,ArrayAccess {
	
	private $list = array(); 
	
	/**
	 * Holds the number of matched elements from the current operation
	 * 
	 * The property is supposed to be a readonly property but is not declared as so; setting its value will 
	 * truncate the internal result set.
	 * 
	 * @var integer
	 */
	public $length = 0;      
	
	/**
	 * Create a new instance of XDTNSNodeList.
	 * 
	 * @param mixed $element [optional] <p>
	 *     DOM element or array of DOM elements can be provided to instanciate the object with.</p>
	 */
	public function __construct($element = null) { 
		
	    if (isset($element)) {
			
			if (@get_class($element) === 'DOMElement') {
				$this->add($element);
			} elseif (is_array($element)) {
				foreach ($element as $node) {
					if (is_object($node) AND get_class($node) === 'DOMElement') {
						$this->add($node);
					}
				}
			}
		}
	}
	
	private function removeFromList ($index) { 
		
		if ($arr = array_splice($this->list, $index, 1)) {
			$this->length--;
			return $arr[0];
		}
		    
		return null;
	}
	
	private function replaceByIndex (DOMElement $new_node, $index) { 
	
	    if ($arr = array_splice($this->list, $index, 1, $new_node))
	    	return $arr[0]->parentNode->replaceChild($new_node, $arr[0]);
	    
	    return null;
	}
	
	private function getListElementIndex (DOMElement $node) {
		
		foreach ($this->list as $index => $elt) {
			if ($node->isSameNode($elt)) return $index;
		}
		
		return -1;
	}
	
	protected function setNamespaceURI($namespace_uri) { $this->namespace_uri = $namespace_uri; }
	
	/**
	 * Get an item at index n from the set of matched elements.
	 * 
	 * @param integer $index <p>
	 *     Index of the element in the set of matched elements.</p>
	 * @return DOMElement <p>
	 * 		Returns a DOM element or throws an OutOfRangeException if the element was not found.</p>
	 * @throws OutOfRangeException
	 */
	public function item($index) {
		
		if ($index > ($this->length-1) OR $index < 0) throw new OutOfRangeException('Undefined offset: ' . $index);
		
		return $this->list[$index];
    }
	
    /**
     * Add a DOM element to the current set of matched elements.
     * 
     * @param DOMElement $node <p>
     *     DOM element to add to the set.</p>
     * @return Boolean <p>
     *     Returns TRUE on success, or FALSE on error or failure.</p>
     */
	public function add (DOMElement $node) {
	     
	    if (array_push($this->list, $node)) {
			$this->length++;
			return true;
		} else return false;
	}
	
	/**
	 * Merge the provided set of elements into the set of matched elements.
	 * 
	 * @param XDTNSNodeList $list <p>
	 *     Set of elements to be merged into the current set.</p>
	 * @return void
	 */
	public function merge (XDTNSNodeList $list) { $this->list = array_merge($this->list, $list->getNodeList()); $this->length += $list->length; }
	
	/**
	 * Remove the set of matched elements from the DOM.
	 * 
	 * @return XDTNSNodeList <p>
	 * 		Returns the set of matched elements that was removed.</p>
	 */
	public function remove () {
		
		foreach ($this as $node) 
		    $node->parentNode->removeChild($node);
	}
	
	/**
	 * Remove all child nodes of the set of matched elements from the DOM.
	 * 
	 * @return XDTNSNodeList <p>
	 * 		Returns the set of matched elements whose children were removed.</p>
	 */
	public function emptyNode() {
		
		foreach ($this as $node) $node->nodeValue = '';
		
		return $this;
	}
	
	/**
	 * Replace each element in the set of matched elements with the provided new content and 
	 * return the set of elements that was removed.
	 * 
	 * @param mixed $content <p>
	 *     The content to insert may be an XML string, DOM element, array of DOM elements, or XDTNSNodeList object. 
	 *     When you pass a XDTNSNodeList collection containing more than one element, or a selector 
	 *     matching more than one element, the first element will be used.</p>
	 * @return XDTNSNodeList <p>
	 * 	   Returns the set of elements that was removed.</p>
	 */
	public function replaceWith ($content) {
		
		if ($this->length === 0) return null;
		
		$content = $this->process($content);
		
		if (!isset($content)) return null;
		
		if (get_class($content) === 'XDTNSNodeList') {
			$clone = $content[0]->cloneNode(true);
			$content->remove();
		} else {
			$clone = $content->cloneNode(true);
			if (get_class($content) === 'DOMElement') $content->parentNode->removeChild($content);
		}
		
		foreach ($this as $node) 
			$node->parentNode->replaceChild($clone->cloneNode(true), $node);
		
		return $this;
	}
	
	/**
	 * Insert the first element in the selection before the element passed in argument.
	 * 
	 * @see XDTNSNodeList::insertAfter
	 * @param mixed $target <p>
	 *     A selector, DOM element, XML string, or XDTNSNodeList object; the 
	 *     matched set of elements will be inserted before the element specified by this parameter.
	 * @return XDTNSNodeList <p>
	 *     Returns the inserted elements on success, for chaining purpose, or &null on failure.</p>
	 */
	public function insertBefore ($target) { 
		
		if ($target = $this->process($target)) 
			foreach ($this as $node) 
				if (get_class() === 'XDTNSNodeList') $target[0]->parentNode->insertBefore($node, $target[0]);
				else $target->parentNode->insertBefore($node, $target);
				
		return $this;
	}
	
	/**
	 * Insert the first element in the selection after the element passed in argument.
	 * 
	 * @see XDTNSNodeList::insertBefore
	 * @param mixed $target <p>
	 *     A selector, DOM element, XML string, or XDTNSNodeList object; the 
	 *     matched set of elements will be inserted after the element specified by this parameter. 
	 *     When you pass a XDTNSNodeList collection containing more than one element, or a selector 
	 *     matching more than one element, the first element will be used.</p>
	 * @return XDTNSNodeList <p>
	 *     Returns the inserted elements on success, for chaining purpose, or &null on failure.</p>
	 */
	public function insertAfter ($target) { 
		
		if ($target = $this->process($target)) 
			foreach ($this as $node) 
				if (get_class() === 'XDTNSNodeList') $target[0]->parentNode->insertBefore($node, $target[0]->nextSibling);
				else $target->parentNode->insertBefore($node, $target->nextSibling);  
		
		return $this;
	}
	
	/**
	 * Insert content, specified by the parameter, to the end of each element 
	 * in the set of matched elements.
	 * 
	 * @see XDTNSNodeList::prepend
	 * @param mixed $content <p>
	 *     DOM element, text node, array of elements and text nodes, XML string, or XDTNSNodeList object 
	 * 	   to insert at the end of each element in the set of matched elements. The method accepts undefined number of arguments.</p>
	 * @return XDTNSNodeList <p>
	 * 	   Returns the set of inserted elements on success, for chaining purpose, or &null on failure.</p>
	 */
	public function append ($content) {
		
		if ($this->length < 1) return null;
		
		$args = func_get_args();
		foreach ($args as $offset => $value) 
			if ($value = $this->process($value)) 
				foreach ($this as $node) 
					if (get_class($value) === 'XDTNSNodeList') $node->appendChild($value[0]);
					else $node->appendChild($value);
				
		return $this;
	}
	
	/**
	 * Insert content, specified by the parameter, to the begining of each element 
	 * in the set of matched elements.
	 * 
	 * @see XDTNSNodeList::append
	 * @param mixed $content<p>
	 * 		DOM element, text node, array of elements and text nodes, XML string, or XDTNSNodeList object 
	 * 		to insert at the begining of each element in the set of matched elements. The method accepts undefined number of arguments.</p>
	 * @return XDTNSNodeList <p>
	 *     Returns the set of inserted elements on success, for chaining purpose, or &null on failure.</p>
	 */
	public function prepend ($content) {
		
		if ($this->length < 1) return null;
		
		$args = func_get_args();
		foreach ($args as $offset => $value) 
			if ($value = $this->process($value)) 
				foreach ($this as $node) 
					if (get_class($value) === 'XDTNSNodeList') $node->insertBefore($value[0], $node->firstChild);
					else $node->insertBefore($value, $node->firstChild);
				
		return $this;
	}
	
	/**
	 * Insert every element in the set of matched elements to the end of the target.
	 * 
	 * @see XDTNSNodeList::preprendTo
	 * @param mixed $target <p>
	 *     A selector, element, XML string, array of elements, or XDTNSNodeList object; the 
	 *     matched set of elements will be inserted at the end of the element(s) specified by this parameter.
	 * @return XDTNSNodeList <p>
	 * 	   Returns the set of inserted elements, for chaining purpose, on success or &null on failure.</p>
	 */
	public function appendTo ($target) { 
		
		if ($target = $this->process($target))
			foreach ($this as $node) 
				if (get_class($target) === 'XDTNSNodeList' OR is_array($target)) 
					foreach ($target as $t) $t->appendChild($node);
				else $target->appendChild($node);
				
		return $this;
	}
	
	/**
	 * Insert every element in the set of matched elements to the begining of the target.
	 * 
	 * @see XDTNSNodeList::appendTo
	 * @param mixed $target <p>
	 *     A selector, element, XML string, array of elements, or XDTNSNodeList object; the 
	 *     matched set of elements will be inserted at the begining of the element(s) specified by this parameter.
	 * @return XDTNSNodeList <p>
	 *     Returns the set of inserted elements, for chaining purpose, on success or &null on failure.</p>
	 */
	public function prependTo ($target) { 
		
		if ($target = $this->process($target))
			foreach ($this as $node) 
				if (get_class($target) === 'XDTNSNodeList' OR is_array($target)) 
					foreach ($target as $t) $t->insertBefore($node, $t->firstChild);
				else $target->insertBefore($node, $target->firstChild);
				
		return $this;
	}
	
	/**
	 * Wrap an HTML structure around each element in the set of matched elements.
	 * 
	 * @param mixed $selector <p>
	 *     A selector, element, XML string, or XDTNSNodeList object specifying the structure 
	 *     to wrap around the matched elements. When you pass a XDTNSNodeList collection containing 
	 *     more than one element, or a selector matching more than one element, the first element will be used.</p>
	 * @return XDTNSNodeList <p>
	 * 		Returns the set of matched elements for chaining purpose.</p>
	 */
	public function wrap ($selector) {
		
		if ($selector = $this->process($selector))
			foreach ($this as $node) {
				if (get_class($selector) === 'XDTNSNodeList') {
					$n = $node->parentNode->insertBefore($selector[0], $node);
					$n->appendChild($node);
				} else {
					$n = $node->parentNode->insertBefore($selector, $node);
					$n->appendChild($node);
				}
			}
		
		return $this;
	}
	
	/**
	 * Get the text content of the first matched element.
	 * 
	 * @return string
	 */
	public function text () { return trim($this[0]->textContent); }
	
	/**
	 * Get the children of each element in the set of matched elements, 
	 * optionally filtered by a selector.
	 * 
	 * @param string $selector [optional] <p>
	 * 		A string containing the selector expression to match against.</p>
	 * @return XDTNSNodeList
	 */
	public function children ($selector = null) { 
		
		$l = new XDTNSNodeList();
			foreach ($this as $node) 
				foreach ($node->childNodes as $child) $l->add($child);
			
		if (!isset($selector)) return $l;
		
		$this->xml_query = $l;
		return $this->select($selector, null, XDTNS::SELECT_FILTER);
	}
	
	/**
	 * Verify whether any element in the set of matched elements is a single parent 
	 * or optionally verify whether the given element that match the selector is a child 
	 * of any element in the set of matched elements.
	 * 
	 * @param mixed $selector [optional] <p>
	 *     A string containing the selector expression or a DOMElement.
	 * @return boolean <p>
	 *     Returns TRUE on success, or FALSE on error or failure.
	 */
	public function hasChildren ($selector = null) {
		
		if (!isset($selector)) 
			foreach ($this as $node) 
				if ($node->childNodes->length === 0) return false;
		
		if (is_object($selector) AND get_class($selector) === 'DOMElement') {
			
			foreach ($this as $node) {
					
				$is_parent = false;
				
				foreach ($node->childNodes as $child) 
					if ($child->isSameNode($selector)) $is_parent = true;
				
				if ($is_parent === false) return false;
			}
			
		} elseif (is_string($selector)) {
			
			foreach ($this as $node) {
				$node = $this->initListObject($node);
				$this->xml_query = $node->children();
				if ($this->select($selector, null, XDTNS::SELECT_FILTER)->length === 0) return false;
			}
		} else return false;
		
		return true;
	}
	
	/**
	 * Get the ancestors of each element in the current set of matched elements, optionally 
	 * filtered by a selector.
	 * 
	 * @see XDTNSNodeList::parent
	 * @param string $selector [optional] <p>
	 *     A string containing the selector expression.</p>
	 * @return XDTNSNodeList
	 */
	public function parents ($selector = null) { 
		
		$list = new XDTNSNodeList();
		
		foreach ($this as $node) 
			while (!$node->isSameNode($node->ownerDocument)) {
				$node = $node->parentNode;
				
				if (!($node instanceof DOMElement)) continue;
				
				$list->add($node);
			}
			
		if (!isset($selector)) return $list;
		
		$this->xml_query = $list;
		return $this->select($selector, null, XDTNS::SELECT_FILTER);
	}
	
	/**
	 * Get the parent of each element in the current set of matched elements, optionally filtered by a selector.
	 * 
	 * @param string $selector [optional] <p>
	 * 		A string containing the selector expression.</p>
	 * @return XDTNSNodeList
	 */
	public function parent($selector = null) { 
		
		$list = new XDTNSNodeList();
		
		foreach ($this as $node) $list->add($node->parentNode);
		
		if (!isset($selector)) return $list;
		
		$this->xml_query = $list;
		return $this->select($selector, null, XDTNS::SELECT_FILTER);
	}
	
	/**
	 * Get the descendences of each element in the current set of matched elements, filtered by a selector
	 * 
	 * @param string $selector [optional] <p>
	 *     A string containing the selector expression.
	 *     If omitted the current set of matched elements is returned.</p>
	 * @return XDTNSNodeList  
	 */
	public function find ($selector = null) {
		
		if (!isset($selector) OR $this->length === 0) return $this;
		
		$l = new XDTNSNodeList();
		
		foreach ($this as $node) {
			
			foreach ($this->select($selector, $node) as $n) $l->add($n);
		}
		
		return $l;
	}
	
	/**
	 * Reduce the set of matched elements to those that match the selector
	 * 
	 * @param string $selector [optional] <p>
	 *     A string containing the selector expression.
	 *     If omitted the current set of matched elements is returned.</p>
	 * @return XDTNSNodeList
	 */
	public function filter ($selector = null) {
		
		if (!isset($selector) OR $this->length === 0) return $this;
		
		$this->xml_query = $this;
		return $this->select($selector, null, XDTNS::SELECT_FILTER);
	}
	
	/**
	 * Verify whethe any of the matched elements are assigned the given attribute;
	 * 
	 * @see XDTNSNodeList::hasAttr
	 * @param string $attr <p>
	 * 		A string representing the attribute to search for.
	 * @return boolean <p>
	 * 		Returns boolean true on success, or false on failure.</p>
	 */
	public function hasAttribute ($attr) {
		
		foreach ($this as $node) 
			if (!$node->attributes->getNamedItemNS($this->namespace_uri, $attr)) return false;
		
		return true;
	}
	
	/**
	 * Verify whethe any of the matched elements are assigned the given attribute;
	 * 
	 * @see XDTNSNodeList::hasAttribute
	 * @param string $attr <p>
	 * 		A string representing the attribute to search for.
	 * @return boolean <p>
	 * 		Returns boolean true on success, or false on failure.</p>
	 */
	public function hasAttr ($attr) { return $this->hasAttribute($attr); }
	
	/**
	 * Verify whether any of the matched elements are assigned the given class; 
	 * elements can have more than one class assigned to them, in html this is represented 
	 * by separating the class names with space
	 * 
	 * @param string $class <p>
	 * 		The class name to search for.
	 * @return boolean <p>
	 * 		Returns boolean true on success, or false on failure.</p>
	 */
	public function hasClass($class) {
		
		foreach ($this as $node) {
			$node = $this->initListObject($node);
			if ($node->hasAttr('class') === false) return false;
		}
			
		foreach ($this as $node) 
			if (preg_match("/$class/", $node->attributes->getNamedItemNS($this->namespace_uri, 'class')->value) == false) return false;
		
		return true;
	}
	
	/**
	 * Get attributes for the first matched element or set one or more attributes for every matched element.
	 * 
	 * @see XDTNSNodeList::attr
	 * @param array $data [optional] <br><br>
	 *     When set it contains attributes representing the key and value pairs of the array. 
	 *     If omitted the first matched element attributes are returned as a DOMNamedNodeMap object.
	 * @return DOMNamedNodeMap object otherwise void
	 */
	public function data ($data = null) {
		
		if (!isset($data)) return $this[0]->attributes;
		
		foreach ($this as $node) {
				
			foreach ($data as $name => $value) {
				
				$attrNode = $node->ownerDocument->createAttribute($name);
				$attrNode->value = $value;
				$node->appendChild($attrNode);
			}
		}
	}
	
	/**
	 * Get the value of an attribute for the first element in the set of matched elements or 
	 * set one or more attributes for every matched element.
	 * 
	 * @see XDTNSNodeList::data
	 * @param mixed $name <p>
	 *     A string representing the attribute name which value is to be set or an array 
	 *     of attributes which values are to be set.</p>
	 * @param mixed $value [Optional] <p>
	 *     A string representing the new value of the first matched element or an array of values 
	 *     in the same order as the names array giving as the first argument to the method. If omitted, the named attribute value is returned.</p>
	 * @return mixed <p>
	 * 		The current value of the named attribute of the first matched element when 
	 *      the method is used to get a value; otherwise void.</p>
	 */
	public function attr ($name, $value = null) {
		
		if (!isset($value) AND is_string($name)) 
		    return $this[0]->attributes->getNamedItemNS($this->namespace_uri, $name)->value;
		    
		if (isset($value)) {
			
			if (is_string($value) AND is_string($name)) {
				
				foreach ($this as $node) {
					
					$node = $this->initListObject($node);
					
					if ($node->hasAttr($name)) $node[0]->attributes->getNamedItemNS($this->namespace_uri, $name)->value = $value;
				}
			} elseif ((is_array($value) AND is_array($name)) AND (count($value) === count($name))) {
				
				foreach ($this as $node) {
					
					$node = $this->initListObject($node);
					
					foreach ($name as $i => $attr) {
						
						if ($node->hasAttr($attr)) $node[0]->attributes->getNamedItemNS($this->namespace_uri, $attr)->value = $value[$i];
					}
				}
			}
		}
	}
	
	/**
	 * Get the matched element index from among its siblings, or get a giving DOM element's 
	 * index from the set of matched elements, or get a filtered element's index from among 
	 * the set of matched elements
	 * 
	 * @param mixed $selector [optional] <p>
	 *     A string representing a selector expression to filter the set of matched elements against, 
	 *     or a DOMElement representing the element which index is search for.
	 * @return integer <p>
	 *     Returns the matched element's index on success, or -1 on failure.
	 */
	public function index ($selector = null) {
		
		if (!isset($selector)) {
			
			foreach ($this[0]->parentNode->childNodes as $index => $child) {
				
				if ($child->isSameNode($this[0])) return $index;
			}
		} elseif (is_object($selector) AND get_class($selector) === 'DOMElement') {
				
			foreach ($this->list as $index => $elt) {
				if ($selector->isSameNode($elt)) return $index;
			}
		} elseif (is_string($selector)) {
			
			$old_list = $this->list;
			
			foreach ($old_list as $index => $node) {
				
				if ($node->isSameNode($this->filter($selector)->first()->get(0))) return $index;
			}
		}
		
		
		return -1;
	}
	
	/**
	 * Iterate over the set of matched elements executing a function for each matched element
	 * 
	 * @see XDTNSNodeList::map
	 * @param string $func_name <p>
	 *     A function to execute for each matched element.</p>
	 * @return 
	 *     Returns null on failure.
	 */
	public function each ($func_name) { 
		
		if (!function_exists($func_name)) return null;
		
		foreach ($this as $index => $node) $func_name($index, $node); 
	}
	
	/**
	 * Translate all items in set of matched elements to a new set of elements by altering their 
	 * value through a call to a callback function passed in argument.
	 * 
	 * @see XDTNSNodeList::each
	 * @param string $func_name <p>
	 * 		A function to process each item against. The first argument to the function is the element 
	 * 		index and the second argument is a DOMElement object. The returned value of the function is 
	 * 		used as the current value of the element.</p>
	 * @return
	 *     Returns null on failure.
	 */
	public function map ($func_name) { 
		
		if (!function_exists($func_name)) return null;
		
		foreach ($this as $index => $node) $node->nodeValue = $func_name($index, $node); 
	}
	
	/**
	 * Get the current value of the first element in the set of matched elements 
	 * or optionally set the value of every matched element.
	 * 
	 * @see XDTNSNodeList::values
	 * @param string $value [optional] <p>
	 * 		A string representing the current value of the first matched element. Otherwise void.
	 * @return string <p>
	 * 		A string representing the current value of the first element in the set of matched elements.</p>
	 */
	public function val ($value = null) {
		
		if (isset($value)) {
			foreach ($this as $node) $node->nodeValue = $value;
		} else return $this[0]->nodeValue;
	}
	
	/**
	 * Get the node name of the first element in the set of matched elements.
	 * 
	 * @return string <p>
	 * 		A string representing the matched element's node name.</p>
	 */
	 public function name() { return $this[0]->nodeName; }
	
	/**
	 * Return an array of the values of the set of matched elements.
	 * [deprecated]
	 * 
	 * @return array
	 */
	public function values () {
		
		$values = array();
		foreach ($this as $node) {
			$values[] = $node->nodeValue;
		}
		
		return $values;
	}
	
	/**
	 * Get the first element in the set of matched elements.
	 * 
	 * @return XDTNSNodeList
	 */
	public function first () { return new XDTNSNodeList($this[0]); }
	
	/**
	 * Get the last element in the set of matched elements.
	 * 
	 * @return XDTNSNodeList
	 */
	public function last () { return new XDTNSNodeList($this[$this->length-1]); }
	
	/**
	 * Get the siblings of each element in the set of matched elements, optionally filtered by a selector.
	 * 
	 * @since Version 2.1
	 * @param string $selector [optional] <p>
	 * 		A string containing a selector expression to match against.</p>
	 * 		<p>When omitted, the siblings of each element in the set of matched elements are returned.</p>
	 * @return XDTNSNodeList <p>
	 * 		Returns the sibling of each element in the set of matched elements, optionally filtered by a selector.</p>
	 */
	public function siblings ($selector = null) { 
		
		$list = new XDTNSNodeList();
		
		foreach ($this as $node) {
			
			$node = $this->initListObject($node);
			$list->merge($node->parent()->children());
		}
		
		if (!isset($selector)) return $list;
		
		return $list->filter($selector);
	}
	
	/**
	 * Get the immediately following sibling of each element in the set of matched elements. 
	 * If a selector is provided, it retrieves the next sibling only if it matches that selector.</p>
	 * 
	 * @since Version 2.1
	 * @param string $selector [optional] <p>
	 * 		A string containing a selector expression to match elements against.</p>
	 * 		<p>If selector is omitted, all the next siblings of each element in the matched set are returned.</p>
	 * @return XDTNSNodeList <p>
	 * 		Returns the matched set.</p>
	 */
	public function next ($selector = null) { 
		
		$list = new XDTNSNodeList();
		
		foreach ($this as $node) {
			
			$node = $this->initListObject($node);
			$siblings = $node->siblings();
		
			for($i=$siblings->index($node[0])+1; $i<$siblings->length; $i++) $list->add($siblings->get($i));
		}
		
		if (!isset($selector)) return $list;
		else {
			
			$this->xml_query = $list;
			return $this->select($selector, null, XDTNS::SELECT_FILTER);
		}
	}
	
	/**
	 * Get the immediately preceding sibling of each element in the set of matched elements. 
	 * If a selector is provided, it retrieves the previous sibling only if it matches that selector.
	 * 
	 * @since Version 2.1
	 * @param string $selector [optional] <p>
	 * 		A string containing a selector expression to match elements against.</p>
	 * 		<p>If selector is omitted, all the previous siblings of each element in the matched set are returned.</p>
	 * @return XDTNSNodeList <p>
	 * 		Returns the matched set.</p>
	 */
	public function prev ($selector = null) {
		
		$list = new XDTNSNodeList();
		
		foreach ($this as $node) {
			
			$node = $this->initListObject($node);
			$siblings = $node->siblings();
		
			for($i=$siblings->index($node[0])-1; $i>=0; $i--) $list->add($siblings->get($i));
		}
		
		if (!isset($selector)) return $list;
		else {
			
			$this->xml_query = $list;
			return $this->select($selector, null, XDTNS::SELECT_FILTER);
		}
	}
	
	/**
	 * Select the element at index n within the matched set.
	 * 
	 * @see XDTNSNodeList::eq
	 * @param integer $index <p>
	 *     Zero-base index at which to select the element.</p>
	 * @return DOMElement <p>
	 * 		Returns the selected element object.</p>
	 */
	public function get ($index) { return $this[$index]; }
	
	/**
	 * Select the element at index n within the matched set.
	 * 
	 * @see XDTNSNodeList::get
	 * @since Version 2.3
	 * @param integer $index <p>
	 * 		Zero-based index at which to select the element.</p>
	 * 		<p>If negative index is given, the counting starts from the end of the matched set.</p>
	 * @return DOMElement <p>
	 * 		Returns the selected element object.</p>
	 */
	public function eq ($index) { 
		
		if ($index >= 0) return $this->get($index); 
		
		return $this->get($this->length+$index-1);
	}
	
	/**
	 * Select all elements that are nth-child of their parent.
	 * 
	 * @param string $selector <p>
	 * 		A string containing a selector expression to match against.</p>
	 * @return XDTNSNodeList <p>
	 * 		Returns the set of matched elements.</p>
	 */
	public function nth ($selector) { 
		
		$l = new XDTNSNodeList();
		$this->xml_query = $this;
		return $this->select($this->name() . ':nth(' . $selector . ')', null, XDTNS::SELECT_FILTER);
	}
	
	/**
	 * Remove elements from the set of matched elements.
	 * 
	 * @param mixed $selector [optional] <p>
	 * 		A string containing a selector expression, a DOM element, or an array
	 * 		of elements to match against the set.</p>
	 * 		<p>If selector is omitted, the filtering operation is canceled and the previous set of matched elements is returned.</p>
	 * @return XDTNSNodeList <p>
	 * 		Returns the filtered elements.</p>
	 */
	public function not($selector = null) {
		
		if (!isset($selector)) return $this;
		
		if (is_string($selector)) {
			
			foreach ($this as $index => $node) 
				foreach ($this->filter($selector) as $n)
					if ($node->isSameNode($n)) $this->removeFromList($index);
		} elseif (is_object($selector) AND get_class($selector) === 'DOMElement') {
			
			if (in_array($selector, $this->list)) $this->removeFromList($this->index($selector));
		} elseif (is_array($selector)) {
			
			foreach ($selector as $node) {
				if (is_object($node) == false) continue;
				
				if (in_array($node, $this->list)) $this->removeFromList($this->index($node));
			}
		}
		
		return $this;
	}
	
	/** 
	 * End the recent filtering operation in the current chain and return the set
	 * of matched element to its initial state.
	 * 
	 * @return XDTNSNodeList <p>
	 * 		Returns the current matched elements.</p>
	 **/
	public function end() { 
		
		$this->xml_query = null; 
		$this->root = $this[0]->ownerDocument->firstChild; 
		
		return $this; 
	}
	
	/**
	 * Get the html of the first element in the set of matched elements or set 
	 * the html element content of every matched element.
	 * 
	 * @param string $html [optional]<p>
	 * 		when set, the html content of every matched element will be set to that value.
	 * 		Otherwise the html content of the first element in the set of matched elements is returned.</p>
	 * @return HTML <p>
	 * 		Returns the html content of the first element in the set of matched elements. 
	 * 		Otherwise the elements in the set of matched elements which html content is set are returned.</p>
	 */
	public function html($html = null) { 
		
		if (!isset($html)) return $this[0]->C14N(); 
		
		foreach ($this as $node) {
			
			$node = $this->initListObject($node);
			$node->emptyNode();
			$node->append($html);
		}
		
		return $this;
	}
	
	public function toArray() { 
		
		$arr = array();
		
		foreach ($this as $node) {
			$arr[] = $node;
		}
		
		return $arr;
	}
	
	public function toString() { return $this->values(); }
	
	public function getIterator() { return new XDTNSIterator($this); }
	
	public function offsetExists ($index) { return is_null($this->item($index)) ? false: true; }
	
	public function offsetGet ($index) { return $this->item($index); }
	
	public function offsetSet ($index, $new_node) { return $this->replaceByIndex($new_node, $index); }
	
	public function offsetUnset ($index) { return $this->removeFromList($index); }
}

/**
 * XDTNSIterator Class
 * 
 * @author Touré Iliass
 * @package XDTNS
 *
 */
class XDTNSIterator implements Iterator {
	
	private $obj;
	
	/** Iterator index **/
	private $key;
	
	public function __construct ($obj) { $this->obj = $obj; }
	
	public function rewind () { $this->key = 0; }
	
	public function key () { return $this->key; }
	
	public function next () { $this->key++; }
	
	public function valid () { return $this->key < $this->obj->length ? true: false; }
	
	public function current () { return $this->obj->item($this->key); }
}
?>