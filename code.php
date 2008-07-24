<?php 
  require "lib/geshi.php";
  class Code extends Feathers implements Feather {
    
    public function __construct() {
      $this->setField(array("attr" => "title",
        "type" => "text",
        "label" => __("Title", "text"),
        "optional" => true,
        "bookmarklet" => "title"));
        
      $this->setField(array("attr" => "code",
        "type" => "text_block",
        "label" => __("Code", "text"),
        "optional" => false,
        "bookmarklet" => "selection"));
        
      $this->setField(array("attr" => "language",
        "type" => "select",
        "label" => __("Language", "text"),
        "options" => getLanguages(),
        "optional" => false));
      
      $this->customFilter("code", "format_code");

			$this->setFilter("title", "markup_post_title");
    }
    public function submit() {
      if (empty($_POST['code']))
        error(__("Error"), __("Body can't be blank."));
      if (empty($_POST['language']))
        error(__("Error"), __("Language can't be blank."));
      fallback($_POST['slug'], sanitize($_POST['title']));

      return Post::add(array(
        "title" => $_POST['title'],
        "code" => $_POST['code'],
        "language" => $_POST['language']), 
        $_POST['slug'],
        Post::check_url($_POST['slug']));
    }
    
    public function update() {
      if (empty($_POST['code']))
        error(__("Error"), __("Body can't be blank."));
      if (empty($_POST['language']))
        error(__("Error"), __("Language can't be blank."));
        $post = new Post($_POST['id']);
        $post->update(array("title" => $_POST['title'], "language" => $_POST['language'],
          "code" => $_POST['code']));
    }
    
    public function title($post) {
      return fallback($post->title, $post->title_from_excerpt(), true);
    }
    
    public function excerpt($post) {
      return $post->code;
    }
    
    public function feed_content($post) {
      return $post->code;
    }
    
    public function format_code($text, $post = null) {
      if (isset($post)) {
				$post->code_unformatted = $post->code;
				$code = $text;
				$languages = getLanguages();
				$geshi = new GeSHi($code, $languages[$post->language]);
				
				if($geshi->error() !== false)
				  return "<pre id='geshi_code'>" . $geshi->error() . "</pre>";
				
        $geshi->set_overall_id('geshi_code');
		    $return = $geshi->parse_code();
		    return $return;
	    }
	    else
	    {
	      return "<pre id='geshi_code'>" . $text . "</pre>";
	    }
    }
  }
  
  function getLanguages() {
    return array("abap",
    "actionscript",
    "actionscript3",
    "ada",
    "apache",
    "applescript",
    "asm",
    "asp",
    "autoit",
    "bash",
    "basic4gl",
    "blitzbasic",
    "bnf",
    "c",
    "c_mac",
    "caddcl",
    "cadlisp",
    "cfdg",
    "cfm",
    "cpp-qt",
    "cpp",
    "csharp",
    "css",
    "d",
    "delphi",
    "diff",
    "div",
    "dos",
    "dot",
    "eiffel",
    "fortran",
    "freebasic",
    "genero",
    "gettext",
    "glsl",
    "gml",
    "groovy",
    "haskell",
    "html4strict",
    "idl",
    "ini",
    "inno",
    "io",
    "java",
    "java5",
    "javascript",
    "kixtart",
    "latex",
    "lisp",
    "lotusformulas",
    "lotusscript",
    "lua",
    "m68k",
    "matlab",
    "mirc",
    "mpasm",
    "mxml",
    "mysql",
    "nsis",
    "objc",
    "ocaml-brief",
    "ocaml",
    "oobas",
    "oracle8",
    "pascal",
    "per",
    "perl",
    "php-brief",
    "php",
    "plsql",
    "python",
    "qbasic",
    "rails",
    "reg",
    "robots",
    "ruby",
    "sas",
    "scala",
    "scheme",
    "sdlbasic",
    "smalltalk",
    "smarty",
    "sql",
    "tcl",
    "text",
    "thinbasic",
    "tsql",
    "vb",
    "vbnet",
    "verilog",
    "vhdl",
    "visualfoxpro",
    "winbatch",
    "xml",
    "xpp",
    "z80");
  }
?>