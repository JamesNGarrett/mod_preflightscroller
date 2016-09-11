<?php
defined('_JEXEC') or die('Restricted access');

// general vars
$assets_path = "/modules/mod_preflightscroller/assets/";

// initialise Zebra Image 
require_once(JPATH_ROOT . '/modules/mod_preflightscroller/zebra/Zebra_Image.php');
$image = new Zebra_Image();
$image->auto_handle_exif_orientation = true;

// global settings
$width = (int) preg_replace("/[^\d]+/","", $params->get('width',1200) );
$height = (int) preg_replace("/[^\d]+/","", $params->get('height',360) );
$small_width = (int) preg_replace("/[^\d]+/","", $params->get('smallwidth',600) );
$small_height = round(($small_width / $width) * $height);
$quality = 80;

// default zebra settings
$image->preserve_aspect_ratio = true;
$image->enlarge_smaller_images = true;
$image->preserve_time = true;
$image->handle_exif_orientation_tag = true;
$image->jpeg_quality = $quality;

// items
$items = (array) $params->get('items',[]);

// put filenames that are not in the large image folder into an array to process
$images_to_process = [];
foreach($items as $key => $item){
	if($item->item_image){
		$image_path_bits = explode('/',$item->item_image);
		$original_file_name = array_pop($image_path_bits);
		$items[$key]->image_path = $assets_path . "images/large/" . $original_file_name;
		if(!file_exists(JPATH_ROOT . $items[$key]->image_path)){
			$images_to_process[$original_file_name] = $item->item_image;
		}
	}
	if($item->item_show == 0){
		unset($items[$key]);	
	}

}

foreach($images_to_process as $filename => $fullpath){
	$image->source_path = JPATH_ROOT . '/' . $fullpath;
	
	// make the large image
	$image->target_path = JPATH_ROOT . $assets_path . "images/large/" . $filename;
	$image->resize($width, $height);

	// make the small image
	$image->target_path = JPATH_ROOT . $assets_path . "images/small/" . $filename;
	$image->resize($small_width, $small_height);
}

// LAYOUT ---------------------------------------------------------------------------------------------------------

// Add scripts
$doc = JFactory::getDocument();
$doc->addScript($assets_path . "js/responsiveslides.min.js");
$doc->addScript($assets_path . "js/display.js");
$doc->addStyleSheet($assets_path . "css/display.css");

// html
if(count($items)): ?>
	<div class="pf-scroller-container"><ul class="rslides">
		<?php foreach($items as $item): ?>
			<li class='pf-scroller-item <?php echo (($item->item_link) && ($item->item_title || $item->item_desc)) ? "jslinked" : "nojslink"; ?>'>
				<?php if(!$item->item_title && !$item->item_desc && $item->item_link){echo "<a href='" . $item->item_link . "'>"; } ?>
				<img src='<?php echo $item->image_path; ?>' >
				<?php if(!$item->item_title && !$item->item_desc && $item->item_link){echo "</a>"; } 
				if($item->item_title || $item->item_desc): ?>
					<div class="pf-scroller-text-box">
						<?php if($item->item_title){echo "<h4>" . $item->item_title . "</h4>";} ?>
						<?php if($item->item_desc){echo "<p>" . $item->item_desc . "</p>";} ?>
						<?php if($item->item_link){echo "<div><a href='" . $item->item_link . "'>Read more ... </a></div>";} ?>
					</div>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul></div>

	<script>
	 jQuery(document).ready(function() {
	 	jQuery(".rslides").responsiveSlides({
	 		timeout:6000

	 	});
	 });
	 </script>
<?php endif;

