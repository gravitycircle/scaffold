<?php
class sampleBlock extends blockHelper {
	private $post;
	private $fieldData;
	private $blockData;
	private $preview = false; //denotes ajax loading in admin

	function __construct($id, $blockid, $preview = false) {
		
		//verify
		if(!is_object($id)) {
			$post = get_post($id);
		}
		else{
			$post = $id;
		}

		if(!$post) {
			throw new Exception('Post Object with ID '.$id.' does not exist. Cannot finish construction.');
			wp_die('FATAL: Post Object with ID '.$id.' does not exist. Cannot finish construction.');
		}
		else {
			$this->post = $post;
		}

		if($blockid == '' || $blockid == false) {
			throw new Exception('Block ID not specified.');
			wp_die('FATAL: Block ID not specified.');
		}

		$fetch = $this->fetchblockdata($this->post->ID, $blockid, $preview);

		$this->fieldData = $fetch['fields'];
		$this->blockData = $fetch['block'];
	}

	public function render_admin_block() {
		ob_start();
		$divid = $this->render_id(true, 'banner', $this->post, $this->blockData);
		?>
		<div id="<?=$divid?>" class="<?=$this->render_cssclass('banner', $this->blockData)?>">
			<div class="kld-block-content">
				This is a sample gutenberg block -- as shown in admin.
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function __toString() {
		ob_start();
		?>
		--- json
		<?php
		return ob_get_clean();
	}
}
?>