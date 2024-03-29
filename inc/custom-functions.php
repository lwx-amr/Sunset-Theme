<?php
/*
@package Sunset-theme
    ===============================
      Post Data Retrievers
    ===============================
*/
	function sunset_posted_meta(){
		$postedOn = human_time_diff ( get_the_time('U'), current_time('timestamp') );
		$cats = get_the_category();
		$postedIn='';
		$sep = ', ';
		$i=0;
		if(!empty($cats)){
			foreach($cats as $cat){
				$i++;
				if($i>1){ $postedIn.=$sep; }
				$postedIn .= '<a href="'.esc_url( get_category_link( $cat->term_id ) ).'" alt="'.esc_attr('View All Posts in%s', $cat->name ).'" >'.esc_html($cat->name).'</a>';

			}
		}
		$output = '<h6><span class="posted-on">Posted <a href="'.esc_url( get_permalink() ).'">'.$postedOn.'</a> ago / </span><span class="posted-in">'.$postedIn.'</span></h6>';
		return $output;
	}
	function sunset_get_comments_statement(){
		$comments_num = get_comments_number();
		$comments = '';
		if($comments_num==0){
			$comments = __('No Comments');
		}elseif($comments_num > 1 ){
			$comments = $comments_num . __(' Comments');
		}elseif($comments_num ==1 ){
			$comments = __('1 Comment');
		}
		return $comments;
	}
	function sunset_posted_footer(){
		if(comments_open()){
			$comments = sunset_get_comments_statement();
			$comments = '<a href="'.get_comments_link().'">'.$comments.' <span class="sunset-icon sunset-comment"></span></a>';
		}else{
			$comments = __('Comments closed');
		}
		return '<div class="post-footer-container"><div class="row"><div class="col-xs-12 col-sm-6">'. get_the_tag_list('<div class="tags-list"><span class="sunset-icon sunset-tag"></span>', ' ', '</div>') .'</div><div class="col-xs-12 col-sm-6 text-right">'. $comments .'</div></div></div>';
	}
/*
  	===============================
      Get Attachments of Post
    ===============================
*/
  function sunset_get_attachment($num = 1){
			$output='';
    	if(has_post_thumbnail() && $num == 1){
    		$output = wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) );
    	}else{
    		$attachments = get_posts(array(
    				'post_type' => 'attachment',
    				'posts_per_page' => $num,
    				'post_parent' => get_the_ID(),
    			)
    		);
    		if($attachments && $num == 1){
					$output = wp_get_attachment_url( $attachments[0]->ID);
    		}elseif($attachments && $num > 1){
					$output = $attachments;
				}
    		wp_reset_postdata();
    	}
    	return $output;
    }
/*
  	=======================================
      Get Audio IFrame with visual false
    =======================================
*/
  function sunset_get_embeded_media( $types = array()){
			$content = do_shortcode( apply_filters('the_content', get_the_content()));
			$embed = get_media_embedded_in_content( $content, $array);
			return str_replace('?visual=true', '?visual=false', $embed[0]);
    }
/*
  	=======================================
     	Grab Link Inside Post Content
    =======================================
*/

  function sunset_grab_link(){
    	if( !preg_match( '/<a\s[^>]*?href=[\'"](.+?)[\'"]/i' , get_the_content() ,$output) ){ // to get href value inside the link
    		return false;
    	}
    	return esc_url_raw($output[1]);
    }

/*
  	=======================================
     	Clean Gallery Format Code
    =======================================
*/
	function sunset_get_bs_slides($postImages){
			$output = array();
			$count = count($postImages);
			for ($i=0; $i < $count ; $i++){
				$active  = (!$i) ? ' active' : '';
				$imageUrl = wp_get_attachment_url( $postImages[$i]->ID );
				$n = ( $i == $count-1 ) ? 0 : $i+1;
				$nextImage = wp_get_attachment_url( $postImages[$n]->ID );
				$p = ( $i == 0 ) ? $count-1 : $i-1;
				$prevImage = wp_get_attachment_url( $postImages[$p]->ID );
				$caption = $postImages[$i]->post_excerpt;
				$output[$i] = array(
					'class' 	=> $active,
					'url' 		=> $imageUrl,
					'next' 		=> $nextImage,
					'prev' 		=> $prevImage,
					'caption' => $caption,
				);
			}
			return $output;
		}

/*
  	=======================================
     	Get Current Page Url
    =======================================
*/
	function susnet_grab_page_url(){
		$http = ( isset( $_SERVER["HTTPS"] ) ? 'https://' : 'http://' );
		$prefix = $http . $_SERVER["HTTP_HOST"];
		$finalUrl = $prefix . $_SERVER["REQUEST_URI"];
		return $finalUrl;
	}

/*
  	=======================================
     	Get Element index
    =======================================
*/
	function sunset_get_archType_index($arr){
		$types = array('category','tag','author');
		$arrSize = count($arr);
		for ($i=3; $i < $arrSize ; $i++) {
			if(in_array($arr[$i],$types)){
				return $i;
			}
		}
		return -1;
	}
/*
  	=======================================
     	Get Post Navigations to single.php
    =======================================
*/
	function sunset_post_navigation(){

		$prev = get_previous_post_link( '<div class="post-nav-link"><span class="sunset-icon sunset-chevron-left"></span>%link</div>', '%title' );
		$next = get_next_post_link( '<div class="post-nav-link text-right">%link<span class="sunset-icon sunset-chevron-right"></span></div>', '%title' );

		$nav ='<div class="row">';
		$nav .='<div class="col-6">'.$prev.'</div>';
		$nav .='<div class="col-6">'.$next.'</div>';
		$nav .= '</div>';

		return $nav;
	}
/*
  	=================================================
     	Include Sharing options with post content
    =================================================
*/
	function sunset_sharing_options($content){

		if(is_single()){

			$title = get_the_title();
			$permalink = get_permalink();
			$twitterHandler = ( get_option('twitter_handler') ? '&amp;via='.esc_attr(get_option('twitter_handler')) :	'' );
			// $permalink= 'https://www.google.com';
			$twitter = 'https://twitter.com/intent/tweet?text=Hey!'.$title.'&amp;url='.$permalink.$twitterHandler.'';
			$facebook = 'https://www.facebook.com/sharer/sharer.php?u='.$permalink;
			$googlePlus = 'https://wwww.plus.google.com/share?url='.$permalink.'';

			$output = '<div class="post-sharing">';
			$output .= '<ul>';
			$output .= '<li><a target="_blank" rel="nofollow" href="'.$facebook.'">face</a></li>';
			$output .= '<li><a target="_blank" rel="nofollow" href="'.$twitter.'">twitter</a></li>';
			$output .= '<li><a target="_blank" rel="nofollow" href="'.$googlePlus.'">gPlus</a></li>';
			$output .= '</ul>';
			$output .= '</div>';

			return $content.$output;

		}else{
			return $content;
		}

	}
	add_filter( 'the_content', 'sunset_sharing_options'); // Mean TO Do fn in a certain situation
																												// Situation Here is  when the_content() function is caled
/*
  	=================================================
     	Get custom navigations for comments pages
    =================================================
*/
function sunset_get_comments_nav(){
	// if( get_comment_pages_count() > 1 /*&& get_option('page_comments')*/ ) {
			require( get_template_directory().'/template-parts/sunset-comment-nav.php' );
	// }
}

/*
  	=================================================
     	mailtrap to test contact form email send
    =================================================
*/

/*
	function mailtrap($phpmailer) {
	  $phpmailer->isSMTP();
	  $phpmailer->Host = 'smtp.mailtrap.io';
	  $phpmailer->SMTPAuth = true;
	  $phpmailer->Port = 2525;
	  $phpmailer->Username = 'df8a25f8148c9b';
	  $phpmailer->Password = '3ef728b5945361';
	}
	add_action('phpmailer_init', 'mailtrap');
*/

/*
		=======================================
			Initialize Mobile Detect Vairable
		=======================================
*/
	function sunset_mobile_detection(){
		global $detect;
		$detect = new Mobile_Detect;
	}
	add_action( 'after_setup_theme', 'sunset_mobile_detection');
