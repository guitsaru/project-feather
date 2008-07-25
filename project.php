<?php
	class Project extends Feathers implements Feather {
		public function __init() {
			$this->setField(
				array("attr" => "title",
					  "type" => "text",
					  "label" => __("Title", "project"),
					  "optional" => true));
			$this->setField(
				array("attr" => "description",
					  "type" => "text_block",
					  "label" => __("Description", "project"),
					  "optional" => false));
			$this->setField(
				array("attr" => "image",
					  "type" => "file",
					  "label" => __("Image", "project")));
			$this->setField(
				array("attr" => "project_url",
					  "type" => "text",
					  "label" => __("URL", "project"),
					  "optional" => false));
			
			$this->respondTo("admin_write_post", "swfupload");
			$this->respondTo("admin_edit_post", "swfupload");
		}
		public function submit() {
			if(empty($_POST['description']))
				error(__("Error"), __("Description can't be blank."));
			if(empty($_POST['project_url']))
				error(__("Error"), __("URL can't be blank."));
			
			fallback($_POST['slug'], sanitize($_POST['title']));
			
			if (!isset($_POST['filename'])) {
				if (isset($_FILES['image']) and $_FILES['image']['error'] == 0)
					$filename = upload($_FILES['image'], array("jpg", "jpeg", "png", "gif", "bmp"));
				else
					error(__("Error"), __("Couldn't upload photo."));
			} else
				$filename = $_POST['filename'];
			
			echo $filename;
			
			return Post::add(
				array("title" => $_POST['title'],
					  "description" => $_POST['description'],
					  "filename" => $filename),
				$_POST['slug'],
			    Post::check_url($_POST['slug']));
		}
		public function update() {
			if(empty($_POST['description']))
				error(__("Error"), __("Description can't be blank."));
			if(empty($_POST['project_url']))
				error(__("Error"), __("URL can't be blank."));
				
			$post = new Post($_POST['id']);
				$post->update(
					array("title" => $_POST['title'],
			 			  "description" => $_POST['body'],
						  "filename" => $filename));
		}
		public function title($post) {
			return fallback($post->title, $post->title_from_excerpt(), true);
		}
		public function excerpt($post) {
			return $post->description;
		}
		public function feed_content($post) {
			return $post->description;
		}
		public function swfupload($post = null) {
			if (isset($post) and $post->feather != "project" or
			    isset($_GET['feather']) and $_GET['feather'] != "project") return;
			Trigger::current()->call("prepare_swfupload", "image", "*.jpg;*.jpeg;*.png;*.gif;*.bmp");
		}
		public function image_tag_for($post, $max_width = 500, $max_height = null, $more_args = "quality=100") {
			$filename = $post->filename;
			$config = Config::current();
			return '<a href="'.$config->chyrp_url.$config->uploads_path.$filename.'"><img src="'.$config->chyrp_url.'/includes/thumb.php?file=..'.$config->uploads_path.urlencode($filename).'&amp;max_width='.$max_width.'&amp;max_height='.$max_height.'&amp;'.$more_args.'" alt="'.fallback($post->alt_text, $filename, true).'" /></a>';
		}
	}
?>