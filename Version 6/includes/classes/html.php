<?php
namespace Ozraid\Includes\Classes;

/**
 * HTML5 class.
 *
 * Type:         Class
 * Dependencies: NULL
 * Description:  Generates HTML5 elements, tab characters, and carriage returns + line feeds.
 * Version:      6.0.0
 * Author:       Sethco
 * Licence:      Freeware
 *
 * Notes:        Public properties:
 *                - $this->crlf           Returns a carriage return and line break.
 *                - $this->tab            Returns a tab character.
 *                - $this->tab_{$integer} Returns a specified $integer number of tab characters via the __get() magic method.
 *                                         i.e. '$tab_2' returns 2 tab characters.
 *
 *               Public class methods:
 *                - content()             Returns content containing carriage returns and line feeds with tab indents.
 *                - attributes()          Returns a string of HTML5 attributes, or NULL.
 *                - start_tag()           Returns a HTML5 start tag.
 *                - end_tag()             Returns a specified $integer number of tab characters
 *                - comment()             Returns a HTML5 comment.
 *                - inline()              Returns a HTML5 inline element, with an optional HTML5 comment.
 *                - block()               Returns a HTML5 block level element, with an optional HTML5 comment.
 *                - container()           Returns a HTML5 block level element container, with an optional HTML5 comment.
 *                - iterate()             Returns HTML5 elements based on an object or array of $attributes = HTML5 attributes,
 *                                        and $content = content.
 *                - iterate_attributes()  Returns HTML5 elements based on an object or array of $attributes = HTML5 attributes.
 *                - iterate_content()     Returns HTML5 elements based on an object or array of $content = content.
 */

// Loads and initialises Status Error class and exception handler that blocks access to the PHP file if the 'AB_PATH' constant is undefined.
require_once $_SERVER['DOCUMENT_ROOT'] .'/includes/classes/status-error.php';

class Html {
	
	/**
	 * PUBLIC PROPERTIES
	 *
	 * @var string $crlf Carriage return and line feed characters.
	 * @var string $tab  Tab character.
	 */
	public $crlf  = "\r\n";
	public $tab   = "\t";
	
	/**
	 * PRIVATE PROPERTIES
	 *
	 * @var array $boolean_attributes HTML5 boolean attributes.
	 */
	private $boolean_attributes = array ( 'autofocus', 'checked', 'disabled', 'formnovalidate', 'multiple', 'readonly', 'required', 'selected' );
	
	/**
	 * Magic method that returns a specified number of tab characters.
	 *
	 * @param string $name 'tab_$integer', where $integer = the number of tab characters to return. i.e. 'tab_2' will return 2 tab characters.
	 * @return string Specified number of tab characters.
	 */
	public function __get( $name ) {
		if ( stripos ( $name, 'tab_' ) == 0 ) {
			$integer = intval ( str_ireplace ( 'tab_', '',  $name ) );
			return str_repeat ( $this->tab, $integer );
		}
	}
	
	/**
	 * Returns an object or array as an array.
	 *
	 * @param var $variable Array or object.
	 * @return array $variable as an array, otherwise NULL if $variable is not an array or object.
	 */
	private function get_array( $variable ) {
		if ( is_object ( $variable ) ) {
			return (array) $variable;
		}
		if ( is_array ( $variable ) ) {
			return $variable;
		}
	}
	
	/**
	 * Returns the end of a HTML5 element, with an optional HTML5 comment.
	 *
	 * @param string $tag        HTML5 tag.
	 * @param var    $attributes Array or oject of HTML5 attributes, or NULL. {
	 *   @type string html5 attribute Value.
	 * }
	 * @param string $content    Optional. Element content.
	 * @param var    $comment    Optional. HTML5 comment.
	 *                           If TRUE, the HTML5 end tag is used as the comment, otherwise the value appears in the comment.
	 * @param var    $tab        Optional. Tab characters.
	 * @return string End of a HTML5 element with either:
	 *                 - Element content and end tag, with or without a HTML5 comment
	 *                 - Element close tag, with or without a HTML5 comment.
	 */
	private function get_element( $tag, $attributes = NULL, $content = NULL, $comment = NULL, $tab = NULL ) {
		$output  = '<' .$tag .$this->attributes( $attributes );
		if ( isset ( $content ) ) {
			$output .= '>' .$this->content( $content, $tab ) .$this->end_tag( $tag, $comment, NULL, NULL );
		}
		else {
			if ( $comment === TRUE ) {
				$output .= ' />' .$this->comment( $tag );
			}
			else {
				$output .= ' />' .$this->comment( $comment );
			}
		}
		return $output;
	}
	
	/**
	 * Returns a string of HTML5 attributes, or NULL.
	 *
	 *  i.e. ' $attribute="$value"' or ' $attribute'.
	 *
	 * @param var $attributes Array or oject of HTML5 attributes, or NULL. {
	 *   @type string html5 attribute Value.
	 * }
	 */
	public function attributes( $attributes ) {
		if ( ! isset ( $attributes ) ) {
			return;
		}
		foreach ( $attributes as $attribute => $value ) {
			if ( in_array ( $attribute, $this->boolean_attributes ) && isset ( $value ) ) {
				$output .= ' ' .$attribute;
			}
			else {
				$output .= ' ' .$attribute .'="' .$value .'"';
			}
		}
		return $output;
	}
	
	/**
	 * Returns content containing carriage returns and line feeds with tab indents.
	 *
	 * @param string $content Element content.
	 * @param string $tab     Tab characters.
	 * @return string Element content with tab indents after each carriage return and line feed.
	 */
	public function content( $content, $tab ) {
		if ( ! isset( $content ) ) {
			return NULL;
		}
		if ( strpos ( $content, $this->crlf ) !== FALSE ) {
			return implode ( $this->crlf .$tab .$this->tab, explode ( $this->crlf, $content ) );
		}
		else {
			return $content;
		}
	}
	
	/**
	 * Returns a HTML5 start tag.
	 *
	 * @param string $tag        HTML5 tag.
	 * @param var    $attributes Optional. Array or object of HTML5 attributes, or NULL. {
	 *   @type string html5 attribute Value.
	 * }
	 * @param var    $tab        Optional. Tab characters.
	 * @param var    $crlf       Optional. Carriage return and line feed, otherwise NULL.
	 * @return string HTML5 start tag.
	 */
	public function start_tag( $tag, $attributes = NULL, $tab = NULL, $crlf = "\r\n" ) {
		return $tab .'<' .$tag .$this->attributes( $attributes ) .'>' .$crlf;
	}
	
	/**
	 * Returns a HTML5 end tag, with an optional HTML5 comment.
	 *
	 * @param string $tag     HTML5 tag.
	 * @param var    $comment Optional. HTML5 comment.
	 *                        If TRUE, the HTML5 end tag is used as the comment, otherwise the value appears in the comment.
	 * @param var    $tab     Optional. Tab characters.
	 * @param var    $crlf    Optional. Carriage return and line feed, otherwise NULL.
	 * @return string HTML5 end tag, with or without a HTML5 comment.
	 */
	public function end_tag( $tag, $comment = NULL, $tab = NULL, $crlf = "\r\n" ) {
		$output  = $tab .'</' .$tag .'>';
		if ( isset ( $comment ) ) {
			if ( $comment === TRUE ) {
				$output .= $this->comment( $tag );
			}
			else {
				$output .= $this->comment( $comment );
			}
		}
		return $output .$crlf;
	}
	
	/**
	 * Returns a HTML5 comment.
	 *
	 * @param string  $content Comment content.
	 * @return string HTML comment.
	 */
	public function comment( $content ) {
		if ( isset ( $content ) ) {
			return '<!-- ' .$content .' -->';
		}
	}
	
	/**
	 * Returns a HTML5 inline element, with an optional a HTML5 comment.
	 *
	 *  i.e. '<$tag $attribute=$value />'.
	 *       '<$tag $attribute=$value />$content</$tag>'.
	 *
	 * @param string $tag        HTML5 tag.
	 * @param var    $attributes Array or object of HTML5 attributes, or NULL. {
	 *   @type string html5 attribute Value.
	 * }
	 * @param string $content    Element content.
	 * @param var    $comment    Optional. HTML5 comment.
	 *                           If TRUE, the HTML5 end tag is used as the comment, otherwise the value appears in the comment.
	 * @param var    $tab        Tab characters.
	 * @return string HTML5 inline element, with an optional HTML5 comment.
	 */
	public function inline( $tag, $attributes = NULL, $content = NULL, $comment = NULL, $tab = NULL ) {
		return $this->get_element( $tag, $attributes, $content, $comment, $tab );
	}
	
	/**
	 * Returns a HTML5 block level element, with an optional a HTML5 comment.
	 *
	 *  i.e. '<$tag $attribute=$value />' + carriage return and line feed.
	 *       '<$tag $attribute=$value />$content</$tag>' + carriage return and line feed.
	 *
	 * @param string $tag        HTML5 tag.
	 * @param var    $attributes Array or object of HTML5 attributes, or NULL. {
	 *   @type string html5 attribute Value.
	 * }
	 * @param string $content    Element content.
	 * @param var    $comment    Optional. HTML5 comment.
	 *                           If TRUE, the HTML5 end tag is used as the comment, otherwise the value appears in the comment.
	 * @param var    $tab        Tab characters.
	 * @return string HTML5 block level element, with an optional HTML5 comment.
	 */
	public function block( $tag, $attributes = NULL, $content = NULL, $comment = NULL, $tab = NULL ) {
		return $tab .$this->get_element( $tag, $attributes, $content, $comment, $tab ) .$this->crlf;
	}
	
	/**
	 * Returns a HTML5 block level element container, with an optional a HTML5 comment.
	 *
	 *  i.e. '<$tag $attribute=$value>
	 *          $content
	 *       '</$tag>'.
	 *
	 * @param string $tag        HTML5 tag.
	 * @param var    $attributes Array or object of HTML5 attributes, or NULL. {
	 *   @type string html5 attribute Value.
	 * }
	 * @param string $content    Element content.
	 * @param var    $comment    Optional. HTML5 comment.
	 *                           If TRUE, the HTML5 end tag is used as the comment, otherwise the value appears in the comment.
	 * @param var    $tab        Tab characters.
	 * @return string HTML5 block level element container, with an optional HTML5 comment.
	 */
	public function container( $tag, $attributes = NULL, $content = NULL, $comment = NULL, $tab = NULL ) {
		$output  = $this->start_tag( $tag, $attributes, $tab );
		$output .= $tab .$this->tab .$this->content( $content, $tab ) .$this->crlf;
		$output .= $this->end_tag( $tag, $comment, $tab );
		return $output;
	}
	
	/**
	 * Returns HTML5 elements based on an object or array of $attributes = HTML5 attributes, and $content = content.
	 *
	 * @param string $method     'inline', 'block', or 'container'. Selection determines the type of element returned.
	 * @param var    $attributes Multi-dimensional array or object of HTML5 attributes. {
	 *   @var var key {
	 *     @string html attribute Value.
	 *   }
	 * }
	 * @param var    $content    Multi-dimensional array or object of element content. {
	 *   @var var key {
	 *     @string key Element content.
	 *   }
	 * }
	 * @see inline(), block(), and container() methods for arguments.
	 * @return string Inline, block level, or block level container elements, with an optional HTML5 comment after the last element,
	 *                otherwise NULL if 
	 */
	public function iterate( $method, $tag, $attributes, $content, $comment = NULL, $tab = NULL ) {
		$attributes     = $this->get_array( $attributes );
		$attribute_keys = array_keys ( $attributes );
		$content        = $this->get_array( $content );
		$content_keys   = array_keys ( $content );
		if ( $attribute_keys === $content_keys ) {
			$count = count ( $attribute_keys );
			for ( $x = 0; $x < $count; $x++ ) {
				if ( isset ( $comment ) && $x == $count - 1 ) {
					$output .= $this->{$method}( $tag, $attributes[$attribute_keys[$x]], $content[$content_keys[$x]], $comment, $tab );
				}
				else {
					$output 	.= $this->{$method}( $tag, $attributes[$attribute_keys[$x]], $content[$content_keys[$x]], NULL, $tab );
				}
			}
			return $output;
		}
		return NULL;
	}
	
	/**
	 * Returns HTML5 elements based on an object or array of $attributes = HTML5 attributes.
	 *
	 * @param string $method     'inline', 'block', or 'container'. Selection determines the type of element returned.
	 * @param var    $attributes Multi-dimensional array or object of HTML5 attributes. {
	 *   @var var key {
	 *     @string html attribute Value.
	 *   }
	 * }
	 * @see inline(), block(), and container() methods for arguments.
	 * @return string Inline, block level, or block level container elements, with an optional HTML5 comment after the last element,
	 *                otherwise NULL if 
	 */
	public function iterate_attributes( $method, $tag, $attributes, $content = NULL, $comment = NULL, $tab = NULL ) {
		$x = 0;
		$count = count ( $attributes );
		foreach ( $attributes as $html_attributes ) {
			$x++;
			if ( $x < $count ) {
				$output .= $this->{$method}( $tag, $html_attributes, $content, NULL, $tab );
			}
			else {
				$output .= $this->{$method}( $tag, $html_attributes, $content, $comment, $tab );
			}
		}
		return $output;
	}
	
	/**
	 * Returns HTML5 elements based on an object or array of $content = content.
	 *
	 * @param string $method  'inline', 'block', or 'container'. Selection determines the type of element returned.
	 * @param var    $content Multi-dimensional array or object of element content. {
	 *   @var var key {
	 *     @string key Element content.
	 *   }
	 * }
	 * @see inline(), block(), and container() methods for arguments.
	 * @return string Inline, block level, or block level container elements, with an optional HTML5 comment after the last element,
	 *                otherwise NULL if 
	 */
	public function iterate_content( $method, $tag, $attributes = NULL, $content, $comment = NULL, $tab = NULL ) {
		$x = 0;
		$count = count ( $content );
		foreach ( $content as $key => $value ) {
			$x++;
			if ( $x == $count ) {
				$output .= $this->{$method}( $tag, $attributes, $value, $comment, $tab );
			}
			else {
				$output .= $this->{$method}( $tag, $attributes, $value, NULL, $tab );
			}
		}
		return $output;
	}
	
}
