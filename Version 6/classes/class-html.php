<?php

class Html {
	
	/**
	 *
	 *
	 */
	private $element;
	
	public function __construct() {
		$this->set_elements();
	}
	
	public function __set( $name, $value ) {
		$this->element->{$name} = $value;
	}
	
	public function __get( $name ) {
		if ( isset ( $this->element->{$name} ) ) {
			return $this->element->{$name};
		}
		else {
			return NULL;
		}
	}
	
	
	// COMMON HTML5 METHODS.
	
	public function set_elements() {
		$this->element = new \stdClass();
		
		$this->tab        = "\t";
		for ( $x = 2; $x <= 12; $x++ ) {
			$this->tab_{$x} = str_repeat ( $this->tab, $x );
		}
		$this->crlf       = "\r\n";
		
		$this->doctype    = '<!DOCTYPE html>' .$this->crlf;
		$this->html       = '<html lang="en-AU" xml:lang="en-AU">' .$this->crlf;
		$this->endhtml    = '</html><!-- html -->';
		$this->head       = $this->tab .'<head>' .$this->crlf;
		$this->endhead    = $this->tab .'</head><!-- head -->' .$this->crlf;
		$this->body       = $this->tab .'<body>' .$this->crlf;
		$this->endbody    = $this->tab .'</body><!-- body -->' .$this->crlf;
		
		$this->br         = '<br />' .$this->crlf;
		$this->enda       = '</a>';
		$this->endarticle = '</article><!-- article -->' .$this->crlf;
		$this->endaside   = '</aside><!-- aside -->' .$this->crlf;
		$this->enddiv     = '</div>';
		$this->endform    = '</form><!-- form -->' .$this->crlf;
		$this->endlabel   = '</label>';
		$this->endnav     = '</nav><!-- nav -->' .$this->crlf;
		$this->endspan    = '</span>';
		
		$this->b          = '<strong>';
		$this->endb       = '</strong>';
		$this->caption    = '<figcaption>';
		$this->endcaption = '</figcaption>' .$this->crlf;
		$this->cite       = '<cite>';
		$this->endcite    = '</cite>';
		$this->code       = '<code>';
		$this->endcode    = '</code>' .$this->crlf;
		$this->figure     = '<figure>';
		$this->endfigure  = '</figure>' .$this->crlf;
		$this->footer     = '<footer>' .$this->crlf;
		$this->endfooter  = '</footer><!-- footer -->' .$this->crlf;
		$this->header     = '<header>' .$this->crlf;
		$this->endheader  = '</header><!-- header -->' .$this->crlf;
		$this->i          = '<em>';
		$this->endi       = '</em>';
		$this->li         = '<li>';
		$this->endli      = '</li>';
		$this->ol         = '<ol>' .$this->crlf;
		$this->endol      = '</ol>' .$this->crlf;
		$this->p          = '<p>';
		$this->endp       = '</p>' .$this->crlf;
		$this->quote      = '<blockquote>';
		$this->endquote   = '</blockquote>' .$this->crlf;
		$this->ul         = '<ul>' .$this->crlf;
		$this->endul      = '</ul>' .$this->crlf;
	}
	
	/**
	 * @param array $attributes {
	 *   @type string html attribute Value.
	 * }
	 * @return string HTML 5 attributes. i,e, ' $attribute="$value"'.
	 */
	public function attributes( $attributes ) {
		$no_value_attributes = array ( 'autofocus', 'checked', 'disabled', 'formnovalidate', 'multiple', 'readonly', 'required' );
		foreach ( $attributes as $attribute => $value ) {
			if ( in_array ( $attribute, $no_value_attributes ) {
				$output .= ' ' .$attribute;
			}
			else {
				$output .= ' ' .$attribute .'="' .$value .'"';
			}
		}
		return $output;
	}
	
	/**
	 * @param string $comment HTML 5 comment.
	 * @return '<!-- $comment -->'.
	 */
	 public function comment( $comment ){
		 return '<!-- ' .$comment .' -->' .$this->crlf;
	 }
	
	/**
	 * @param string $tag     HTML 5 tag.
	 * @param string $comment HTML 5 comment.
	 * @return '<!-- $comment -->'.
	 */
	 public function endtag( $tag, $comment ){
		 return '</' .$tag .'><!-- ' .$comment .' -->' .$this->crlf;
	 }
	
	
	// <head> METHODS
	
	/**
	 * @para, string $tag       HTML 5 tag.
	 * @param array  $arguments HTML 5 attributes. {
	 *   @type array key {
	 *     @type string html5 attribute Attribute value.
	 *   }
	 * }
	 * @return string <$tag $attribute="$value" /> tags.
	 */
	public function head_tags( $tag, $arguments ) {
		foreach ( $arguments as $attributes ) {
			$output 	.= $this->tab_2 .'<' .$tag;
			$output 	.= $this->attributes( $attributes );
			if ( $tag == 'script' ) {
				$output .= '></' .$tag .'>';
			}
			else {
				$output .= ' />';
			}
		}
		return $output .$this->crlf;
	}
	
	/**
	 * Returns a script enclosed in <script> tags.
	 *
	 * $param string $script Script.
	 * @return string <$tag> $content </$tag> tags and content.
	 */
	public function get_inline_content( $tag, $content ) {
		$output 	 = $this->tab_2 .'<' .$tag .'>' .$this->crlf;
		$lines = explode ( $this->crlf, $content );
		foreach ( $lines as $line ) {
			$output .= $this->tab_3 .$line .$this->crlf;
		}
		$output 	.= $this->tab_2 .'</' .$tag .'><!-- ' .$tag .' -->' .$this->crlf;
		return $output;
	}
	
	/**
	 * @para, string                  $tag     HTML 5 tag.
	 * @param string, array or object $content Script or inline CSS3.
	 * @return string <$tag> $content </$tag> tags and content.
	 */
	public function inline_content( $tag, $content ) {
		if ( is_string ( $content ) ) {
			$output .= $this->get_script( $content );
		}
		else (
			foreach ( $content as $data ) {
				$output .= $this->get_script( $data );
			}
		}
		return $output;
	}
	
	
	/**
	 * BODY METHODS
	 */
	
	public function time( $time, $datetime = NULL ) {
		$output  = '<time';
		if ( $datetime ) {
			$output .= ' datetime="' .$datetime .'"';
		}
		$output 	.= '>' .$time .'</time>';
		return $output;
	}
	
}