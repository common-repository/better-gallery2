<?php

/*

Plugin Name: Better Gallery
Plugin URI: http://kruyt.org/projects/wp-plugins/better-gallery
Description: This plugin replaces the default gallery feature in WordPress 2.5 Lightbox/Slimbox/Greybox/Thickbox/lightview, Exif and Geo/GPS support.
Version: 0.1 Beta Release
Author: Dennis Kruyt
Author URI: http://kruyt.org
License: GPL

Orignal by Justin Tadlock (http://justintadlock.com)
I added some extra features like GEO/GPS support, and a lot of boxes out of the box ;) support.
This is still very beta, and there will be some bugs, I need to do some valid XHTML and code cleanup.

Highslide and Lightview are not included, becuase of their licenses. If you want to
use them, you should download them from their site and add them in the better-gallery
plugin directory.

To change the box which u want to use, change the veriable below.
*/

// Use lightbox/thickbox/greybox/slimbox/highslide/lightview?	
$whichbox = 'greybox';

// Remove original gallery shortcode
	remove_shortcode(gallery);

// Add a new shortcode
	add_shortcode('gallery', 'jt_gallery_shortcode');

// Get the CSS required and it to blog head
	add_action('wp_head', 'jt_gallery_css');

/************************************************
Load CSS and JavaScript for Lightbox/(Slimbox/Mediabox)/Thickbox/Greybox
Comment out if already embedded in your theme, thickbox is already included in WP.
************************************************/

if ($whichbox == greybox) {
	// Load greybox js and css if we need it, comment out if it is already in your theme...
	add_action('wp_head', 'greybox');

} elseif ($whichbox == thickbox) {
	// Load thickbox js and css if we need it, comment out if it is already in your theme...
	wp_enqueue_script('thickbox');
	add_action('wp_head', 'thickbox_css');
	
} elseif ($whichbox == slimbox) {
	// Slimbox and Mediabox
	add_action('wp_head', 'slimbox');
	
} elseif ($whichbox == lightview) {
	// lightview

	//wp_enqueue_script('scriptaculous');
	//wp_enqueue_script('prototype');
	//wp_enqueue_script('scriptaculous-builder');
	//wp_enqueue_script('scriptaculous-effects');
	add_action('wp_head', 'lightview');
	
} elseif ($whichbox == litebox) {
	// litebox
	wp_enqueue_script('scriptaculous');
	wp_enqueue_script('prototype');
	wp_enqueue_script('scriptaculous-builder');
	wp_enqueue_script('scriptaculous-effects');
	// litebox it self, still to be added :(
	
} elseif ($whichbox == highslide) {
	// HighSlide js and css
	add_action('wp_head', 'highslide');
	
}
	
/************************************************
Create our new gallery shortcode based off the original
We don't want to change any core elements
Just make them better
************************************************/
function jt_gallery_shortcode($attr) {
	global $post;
// Show gallery in excerpts
	// add_filter('the_excerpt', 'jt_gallery_shortcode');

// initialize variables for lightbox/thickbox/greybox

	global $whichbox;
	
	if ($whichbox == greybox) {
		// greybox
		$a_rel = 'gb_imageset[better-gallery-'.$post->ID.']';
		$a_class = 'greybox';
		$a_rel_geo = 'gb_pageset['.$id.']';
	
	} elseif ($whichbox == thickbox) {
		// thickbox
		$a_rel = 'better-gallery-'.$post->ID.'';
		$a_class = 'thickbox';
		$a_rel_geo = 'thickbox['.$id.']';

	} elseif ($whichbox == lightview) {
		// thickbox
		$a_rel = 'gallery[better-gallery-'.$post->ID.']';
		$a_class = 'lightview';
		$a_rel_geo = 'iframe';
		
	} elseif ($whichbox == slimbox) {
		// litebox
		$a_rel = 'lightbox[better-gallery-'.$post->ID.']';
		$a_class = 'false';
		$a_rel_geo = 'mediabox[650 450]';
		
	}	elseif ($whichbox == litebox) {
		// litebox
		$a_rel = 'lightbox[better-gallery-'.$post->ID.']';
		$a_class = 'false';
		$a_rel_geo = 'false';
		
	}	elseif ($whichbox == highslide) {
		// HighSlide
			//$a_rel = 'lightbox[better-gallery-'.$post->ID.']';
			$a_class = 'highslide';
			//$a_onclick = 'return hs.expand(this)';
			$a_onclick = 'return hs.expand(this, {slideshowGroup: \''.$post->ID.'\', wrapperClassName : \'highslide-white\', spaceForCaption: 30, outlineType: \'rounded-white\'})';
			$a_onclick_geo = 'return hs.htmlExpand(this, { objectType: \'iframe\', objectWidth: 650, objectHeight: 600, objectLoadTime: \'after\', allowWidthReduction: 1} )';
}

// Show caption link?
	$cap_link = false;

// Allow plugins/themes to override the default gallery template.
	$output = apply_filters('post_gallery', '', $attr);
	if ( $output != '' )
		return $output;

	extract(shortcode_atts(array(
		'orderby' => 'menu_order ASC, ID ASC',
		'id' => $post->ID,
		'itemtag' => 'dl',
		'icontag' => 'dt',
		'captiontag' => 'dd',
		'columns' => 3,
		'size' => 'thumbnail',
	), $attr));

	$id = intval($id);
	$orderby = addslashes($orderby);
	$attachments = get_children("post_parent=$id&post_type=attachment&post_mime_type=image&orderby=\"{$orderby}\"");

	if ( empty($attachments) )
		return '';

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $id => $attachment )
			$output .= wp_get_attachment_link($id, $size, true) . "\n";
		return $output;
	}

	$listtag = tag_escape($listtag);
	$itemtag = tag_escape($itemtag);
	$captiontag = tag_escape($captiontag);
	$columns = intval($columns);
	$itemwidth = $columns > 0 ? floor(100/$columns) : 100;

// Remove the style output
// Why add style in the middle of a freakin' page?
// This needs to be added to the header (width applied through CSS but limits it a bit)
	//$output = apply_filters('gallery_style', "<div class='gallery'>");
	$output = apply_filters('gallery_style', "<div class='gallery gallery-$post->ID'>");


	foreach ( $attachments as $id => $attachment ) {
	// Larger image URL (Lightbox/Thickbox)
		$a_img = wp_get_attachment_url($id);
	// Attachment page ID
		$att_page = get_attachment_link($id);
	// Returns array
		$img = wp_get_attachment_image_src($id, $size);
		$img = $img[0];
		
	// Get EXIF from WP and set in title
		$metadata = wp_get_attachment_metadata($id);
		$image_meta = $metadata[image_meta];
		
		//print_r ($metadata);
		
		if($image_meta[camera]) $title = ''.$image_meta[camera].' ';
		if($image_meta[focal_length]) $title .= '@ '.$image_meta[focal_length].' mm ';
		if($image_meta[shutter_speed]) $title .= '- &#185;/'.(1/($image_meta[shutter_speed])).' sec';
		if($image_meta[aperture]) $title .= ', &#131;/'.$image_meta[aperture].'';
		if($image_meta[iso]) $title .= ', ISO '.$image_meta[iso].'';
		if($image_meta[created_timestamp]) $title .= ' on '.date('j F, Y',$image_meta[created_timestamp]).'';
		
		
	// Get GPS from EXIF via file
	
		// Get file location on system
		$file = get_attached_file($id);

		// Read Exif GPS info from file
		$arrPhotoExif = exif_read_data($file,'GPS');

		// Unset previous gps data
		unset ($intLatitude);
		unset ($intLongtitude);
		unset ($geoloc);
		
		// returns false if there's no GPS section in the EXIF data
		if($arrPhotoExif)
		{

		$arrLatDeg = split("/",$arrPhotoExif["GPSLatitude"][0]);
		$intLatDeg = $arrLatDeg[0]/$arrLatDeg[1];
		$arrLatMin = split("/",$arrPhotoExif["GPSLatitude"][1]);
		$intLatMin = $arrLatMin[0]/$arrLatMin[1];
		$arrLatSec = split("/",$arrPhotoExif["GPSLatitude"][2]);
		$intLatSec = $arrLatSec[0]/$arrLatSec[1];
		$arrLongDeg = split("/",$arrPhotoExif["GPSLongitude"][0]);
		$intLongDeg = $arrLongDeg[0]/$arrLongDeg[1];
		$arrLongMin = split("/",$arrPhotoExif["GPSLongitude"][1]);
		$intLongMin = $arrLongMin[0]/$arrLongMin[1];
		$arrLongSec = split("/",$arrPhotoExif["GPSLongitude"][2]);
		$intLongSec = $arrLongSec[0]/$arrLongSec[1];

		// round to 5 = approximately 1 meter accuracy
		$intLatitude = round(DegToDec($arrPhotoExif["GPSLatitudeRef"],
		$intLatDeg,$intLatMin,$intLatSec),5);
	
		$intLongtitude = round(DegToDec($arrPhotoExif["GPSLongitudeRef"],
		$intLongDeg,$intLongMin,$intLongSec), 5);
		
		//format nice for title
		$geoloc = ''.$intLatDeg.'&#176;'.$intLatMin.'&#039;'.round ($intLatSec,2).'&#034;'.$arrPhotoExif["GPSLatitudeRef"].' by ';
		$geoloc .= ''.$intLongDeg.'&#176;'.$intLongMin.'&#039;'.round ($intLongSec,2).'&#034;'.$arrPhotoExif["GPSLongitudeRef"].'';
		
		}
	
	// If using Lightbox, set the link to the img URL
	// Else, set the link to the attachment URL
		if($a_rel == true) $link = $a_img;
		elseif($a_class == true) $link = $a_img;
		else $link = $att_page;
	// Open the gallery stuff
		$output .= "<{$itemtag} class='gallery-item col-$columns'>";
		$output .='<'.$icontag.' class="gallery-icon"><a';
	// Image link and title
		$output .= ' href="'.$link.'" title="'.$title.'"';
	// Set the "class" tag if using it
		if($a_class == true) $output.= ' class="'.$a_class.'"';
	// Set the "onclick" if we use it
		if($a_onclick == true) $output.= ' onclick="'.$a_onclick.'" id="'.$id.'"';
	// Set the "rel" tag if using it
		if($a_rel == true) $output.= ' rel="' .$a_rel.'"';
	// Output the image and close off some open tags
		if ($whichbox == highslide) {
			$output .= '><img src="'.$img.'" alt="'.$attachment->post_excerpt.'" class="'.$size.'" /></a>';
			$output .= '<div class=\'highslide-caption\'>';
		
			// If there is a longtitude and latitude, then make link to map
			if (($intLongtitude) && ($intLatitude)) {
				$output .= '<'.$captiontag.' class="gallery-caption">';
				$output .= ''.$title.' at ';
				$output .= '<a href="'.get_bloginfo('wpurl').'/wp-content/plugins/better-gallery/femap.php?Latitude='.$intLatitude.'&amp;Longtitude='.$intLongtitude.'"';
				$output .= ' title="Flash Earth - '.$geoloc.'"';
				if ($a_onclick_geo)	$output .= ' onclick="'.$a_onclick_geo.'"';
				$output .= ' rel="gb_pageset['.$id.']">'.$geoloc.'</a></'.$captiontag.'>';
			} else {
				$output .= '<'.$captiontag.' class="gallery-caption">'.$title.'</'.$captiontag.'>';
			}
				$output .= '</div></'.$icontag.'>';
		
		} else {
			$output .= '><img src="'.$img.'" alt="'.$attachment->post_excerpt.'" class="'.$size.'" /></a></'.$icontag.'>';
			// If there is a longtitude and latitude, then make link to map
			if (($intLongtitude) && ($intLatitude)) {
				$output .= '<'.$captiontag.' class="gallery-caption">';
				$output .= '<a href="'.get_bloginfo('wpurl').'/wp-content/plugins/better-gallery/femap.php?Latitude='.$intLatitude.'&amp;Longtitude='.$intLongtitude.'&amp;keepThis=true&amp;TB_iframe=true&amp;height=450&amp;width=650"';
				if ($whichbox == lightview) {
					$output .= ' title="Flash Earth - '.$geoloc.' :: :: fullscreen: true"';
				 } else {
					$output .= ' title="Flash Earth - '.$geoloc.'"';
				}
				if ($a_onclick_geo)	$output .= ' onclick="'.$a_onclick_geo.'"';
				$output .= ' class="'.$a_class.'" rel="'.$a_rel_geo.'"><img src="'.get_bloginfo('wpurl').'/wp-content/plugins/better-gallery/gearth.gif" alt="Flash Earth - '.$geoloc.'" title="Flash Earth - '.$geoloc.'" /></a></'.$captiontag.'>';
				
			}
		}
		
		if ( $cap_link == true && $captiontag && trim($attachment->post_excerpt) ) {
			$output .= '<'.$captiontag.' class="gallery-caption"><a href="'.$att_page.'" title="'.$attachment->post_excerpt.'">'.$attachment->post_excerpt.'</a></'.$captiontag.'>';
		}
		$output .= "</{$itemtag}>";
		if ( $columns > 0 && ++$i % $columns == 0 )
			$output .= '<div style="clear:both;" class="clear"><!-- --></div>';
			
	}

	// add controlbar if we use highslide	
	if ($whichbox == highslide) {
	$output .= '<div id="controlbar" class="highslide-overlay controlbar">';
	$output .= '<a href="#" class="previous" onclick="return hs.previous(this)" title="Previous (left arrow key)"></a>';
	$output .= '<a href="#" class="next" onclick="return hs.next(this)" title="Next (right arrow key)"></a>';
	$output .= '<a href="#" class="highslide-move" onclick="return false" title="Click and drag to move"></a>';
	$output .= '<a href="#" class="close" onclick="return hs.close(this)" title="Close"></a></div>';
	}
	
	$output .= "</div>\n";

	return $output;
}

/************************************************
Function for outputting the CSS
************************************************/
function jt_gallery_css () {
	global $site_url;
	$css = get_bloginfo('wpurl') . '/wp-content/plugins/better-gallery/better-gallery.css';
	$css = '<link rel="stylesheet" href="'.$css.'" type="text/css" media="screen" />';
	$css = "<!-- User is using the better WP Gallery plugin -->\n$css\n";
	echo $css;
}

// Add the thickbox  css
function thickbox_css() {

    $thickbox_csspath = get_bloginfo('wpurl')."/wp-includes/js/thickbox/thickbox.css";
    $thickboxscript = "<link rel=\"stylesheet\" href=\"".$thickbox_csspath."\"/>\n";
    print($thickboxscript);
}

/************************************************
Add the greybox scripts
************************************************/

function greybox() {
	
	$greyboxscript = '<script type="text/javascript">';
	$greyboxscript .= '    var GB_ROOT_DIR = "'.get_bloginfo('wpurl').'/wp-content/plugins/better-gallery/greybox/"';
	$greyboxscript .= '</script>';
	$greyboxscript .= '<script type="text/javascript" src="'.get_bloginfo('wpurl').'/wp-content/plugins/better-gallery/greybox/AJS.js"></script>';
	$greyboxscript .= '<script type="text/javascript" src="'.get_bloginfo('wpurl').'/wp-content/plugins/better-gallery/greybox/AJS_fx.js"></script>';
	$greyboxscript .= '<script type="text/javascript" src="'.get_bloginfo('wpurl').'/wp-content/plugins/better-gallery/greybox/gb_scripts.js"></script>';
	
	$greyboxscript .= '<link href="'.get_bloginfo('wpurl').'/wp-content/plugins/better-gallery/greybox/gb_styles.css" rel="stylesheet" type="text/css" />';
	print($greyboxscript);
}

/************************************************
Add the slimbox / mediabox scripts
************************************************/


function slimbox() {
	
	$slimboxscript = '<script type="text/javascript" src="'.get_bloginfo('wpurl').'/wp-content/plugins/better-gallery/mediabox/mootools.js"></script>';
	$slimboxscript .= '<script type="text/javascript" src="'.get_bloginfo('wpurl').'/wp-content/plugins/better-gallery/slimbox/js/slimbox.js"></script>';
	$slimboxscript .= '<script src="'.get_bloginfo('wpurl').'/wp-content/plugins/better-gallery/mediabox/mediabox.js" type="text/javascript"></script>';

	//$slimboxscript .= '<link rel="stylesheet" href="'.get_bloginfo('wpurl').'/wp-content/plugins/better-gallery/slimbox/css/slimbox.css" type="text/css" media="screen" />';
	$slimboxscript .= '<link rel="stylesheet" href="'.get_bloginfo('wpurl').'/wp-content/plugins/better-gallery/mediabox/mediabox.css" type="text/css" media="screen" />';
	print($slimboxscript);
}

/************************************************
Add the lightview scripts and css
************************************************/

function lightview() {

	$lightviewscript = '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('wpurl').'/wp-content/plugins/better-gallery/lightview/css/lightview.css" media="screen" />';
	$lightviewscript .= '<script type="text/javascript" src="'.get_bloginfo('wpurl').'/wp-content/plugins/better-gallery/lightview/js/protoculous.js"></script>';	
	$lightviewscript .= '<script type="text/javascript" src="'.get_bloginfo('wpurl').'/wp-content/plugins/better-gallery/lightview/js/lightview.js"></script>';

	print($lightviewscript);
}

/************************************************
Add the highslide scripts
************************************************/


function highslide() {
	
	$highslidescript = '<script type="text/javascript" src="'.get_bloginfo('wpurl').'/wp-content/plugins/better-gallery/highslide/highslide-with-html.js"></script>';
	$highslidescript .= '<script type="text/javascript">hs.graphicsDir = \''.get_bloginfo('wpurl').'/wp-content/plugins/better-gallery/highslide/graphics/\';</script>';
	// iframe html
	$highslidescript .= '<script type="text/javascript">hs.outlineType = \'rounded-white\'; hs.outlineWhileAnimating = true;</script>';
	// gallery
	//$highslidescript .= '<script type="text/javascript">hs.registerOverlay( { thumbnailId: null, overlayId: \'controlbar\', position: \'top right\', hideOnMouseOut: true } ); hs.captionEval = \'this.a.title\'; </script>';
	$highslidescript .= '<script type="text/javascript">hs.registerOverlay( { thumbnailId: null, overlayId: \'controlbar\', position: \'top right\', hideOnMouseOut: true } ); hs.captionId = \'the-caption\'; </script>';
	$highslidescript .= '<link rel="stylesheet" href="'.get_bloginfo('wpurl').'/wp-content/plugins/better-gallery/highslide/highslide.css" type="text/css" media="screen" />';
		
	print($highslidescript);
}

/**************************************************
Type: FUNCTION Function: DegToDec
Purpose: Converts degrees to decimal
Input: $deg,$min,$sec
Output: decimal value Requires:
**************************************************/

function DegToDec($strRef,$intDeg,$intMin,$intSec)
{
	$arrLatLong = array();
	$arrLatLong["N"] = 1;
	$arrLatLong["E"] = 1;
	$arrLatLong["S"] = -1; 
	$arrLatLong["W"] = -1;

	return ($intDeg+((($intMin*60)+($intSec))/3600)) * $arrLatLong[$strRef];
}

?>