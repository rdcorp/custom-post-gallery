
function display_projects_func($atts) {
	$out = "";
   
	$default = array(
        'cat' => '',
    );
    $cat = shortcode_atts($default, $atts);
	
	ob_start();
	 
	$out.="<div id='projects_container'>";
	$args = array(  
        'post_type' => 'project',
        'post_status' => 'publish',
        'posts_per_page' => 3
    );
	
	if(!empty($cat['cat'])){
		$tax_query = array(
			array(
				'taxonomy' => 'project_category',
				'field'    => 'term_id',
				'terms'    => $cat['cat']
			),
		);
		
		$args['tax_query'] = $tax_query;
		
	}
	
    $loop = new WP_Query( $args ); 
	$out.="<div id='projects_container_items'>";
		$out.= display_projects_template($loop);
	$out.="</div>";
	$cat_url= get_category_link( $cat['cat'] );
	
	$out.="<a href=".$cat_url.">Read More</a>";
	$out.="</div>"; /* Container Over */ 
	$out.= ob_get_contents();
	ob_end_clean();
	return $out;
    
}
add_shortcode('display_projects', 'display_projects_func');

function display_projects_template($loop){
	
	$out = "";
	
	ob_start();
	
	?>
	<link href="<?php echo get_stylesheet_directory_uri();?>/simple-lightbox/simple-lightbox.min.css" rel="stylesheet" />
	<!-- As A Vanilla JavaScript Plugin -->
	<script src="<?php echo get_stylesheet_directory_uri();?>/simple-lightbox//simple-lightbox.min.js"></script>
	<!-- For legacy browsers -->
	<script src="<?php echo get_stylesheet_directory_uri();?>/simple-lightbox//simple-lightbox.legacy.min.js"></script>
	<!-- As A jQuery Plugin -->
	<script src="<?php echo get_stylesheet_directory_uri();?>/simple-lightbox/simple-lightbox.jquery.min.js"></script>
	
	<script>
	
	jQuery(document).ready(function(){
		jQuery(".project_images_link").click(function(){
			var project_id = jQuery(this).data("id");
			console.log(project_id);
			var gallery_target = jQuery('.project_gallery[data-id='+project_id+'] a');
			//var gallery_target = jQuery(this);
			console.log(gallery_target);
			var gallery = gallery_target.simpleLightbox({
				/* options */
			});
			gallery.open();
		});
		
	});
	
	
	</script>
	
	<?php
	
	while ( $loop->have_posts() ) : $loop->the_post(); 
		$project_id = get_the_ID();
		$project_title = get_the_title();
		$project_thumbnail_url = get_the_post_thumbnail_url($project_id, 'full');
		$project_sub_title = get_field( "sub_title", $project_id );
		$project_location = get_field( "location", $project_id );
		$project_images = get_field( "images", $project_id );
		
	?>

		<div class="project_single_item" style="background-image:url(<?php echo $project_thumbnail_url;?>)">
			<div class="project_single_item_inner">
				<h3 class="project_title"><?php echo $project_title;?></h3>
				<p class="project_sub_title"><?php echo $project_sub_title;?></p>
				<?php if(!empty($project_images)){ ?>
					<a class="project_images_link" data-id="<?php echo $project_id;?>">View Images</a>
				<?php }?>
				<i class=" fa fa-pin" aria-hidden="true" role="img"></i>
				<p class="project_location"><?php echo $project_location;?></p>
				
				<?php if(!empty($project_images)){ ?>
				
				<div class="project_gallery" data-id="<?php echo $project_id;?>">  

					<?php foreach($project_images as $img) { ?>

					  <a href="<?php echo $img["image"]; ?>" class="big" rel="rel1">
						<img src="<?php echo $img["image"]; ?>" alt="" title="Image 1">
					  </a>
					<?php }?>
					 
				</div>
				
				<?php }?>
				
				
			</div>
		</div>
	   <?php
    endwhile;
	
    wp_reset_postdata(); 
	
	$out.=ob_get_contents();
	ob_end_clean();
	
	return $out;
}
