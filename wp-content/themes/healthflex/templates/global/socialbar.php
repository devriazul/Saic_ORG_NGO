<?php
/*
 ______ _____   _______ _______ _______ _______ ______ _______ 
|   __ \     |_|    ___|_     _|   |   |       |   __ \   _   |
|    __/       |    ___| |   | |       |   -   |      <       |
|___|  |_______|_______| |___| |___|___|_______|___|__|___|___|

P L E T H O R A T H E M E S . C O M 				   (c) 2015

Template part: Social Icons Bar

*/
Plethora_Theme::dev_comment('	========================= SOCIAL ICONS BAR ========================', 'layout');
$socialbar_status  = Plethora_Theme::option( METAOPTION_PREFIX .'socialbar-status', array() );          // Where to put the title

// Prepare icons list...if empty, don't show empty markup
$output = '';
if ( !empty($socialbar_status) && class_exists('Plethora_Module_Social') ) {
	
	/* 
	Temporary workaround to deal with this issue: https://core.trac.wordpress.org/ticket/36033
	Get allowed protocols, add skype related configuration and esc the url using protocols as esc_url_raw function argument
	*/
	$allowed_protocols = wp_allowed_protocols();
	$allowed_protocols[] = 'callto';
	$allowed_protocols[] = 'skype';
	foreach ( $socialbar_status as $social_slug=>$social_status ) { 
		$social_icon = $social_status == '1' ? Plethora_Module_Social::get_icon($social_slug) : array();
		if ( !empty($social_icon) ) { 
			$output .= '<a href="'. esc_url_raw( $social_icon[$social_slug]['url'], $allowed_protocols ).'" title="'. esc_attr( $social_icon[$social_slug]['title'] ).'" target="'. esc_attr( $social_icon[$social_slug]['url_target'] ).'"><i class="fa '. esc_attr( $social_icon[$social_slug]['icon'] ).'"></i></a>';
		}
	}
}

if ( !empty($output) ) {
?>
	<a title="" href="#" class="social_links"><i class="fa fa-share-alt"></i></a>
	<div class="team_social"><?php echo $output; ?></div>
<?php  } ?>
<?php Plethora_Theme::dev_comment('	  END ========================= SOCIAL ICONS BAR ========================', 'layout'); ?>
