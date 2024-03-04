<?php
/**
 * Gallery
 * 
 * Shortcode that allows to create a gallery based on images selected from the media library
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if ( ! class_exists( 'avia_sc_gallery' ) )
{
	class avia_sc_gallery extends aviaShortcodeTemplate
	{
		/**
		 *
		 * @var int 
		 */
		static public $gallery = 0;

		/**
		 *
		 * @var string 
		 */
		public $extra_style = '';

		/**
		 *
		 * @var string 
		 */
		public $non_ajax_style = '';

		/**
		 * Create the config array for the shortcode button
		 */
		function shortcode_insert_button()
		{
			$this->config['version']		= '1.0';
			$this->config['self_closing']	= 'yes';

			$this->config['name']			= __( 'Gallery', 'avia_framework' );
			$this->config['tab']			= __( 'Media Elements', 'avia_framework' );
			$this->config['icon']			= AviaBuilder::$path['imagesURL'] . 'sc-gallery.png';
			$this->config['order']			= 6;
			$this->config['target']			= 'avia-target-insert';
			$this->config['shortcode'] 		= 'av_gallery';
			$this->config['modal_data']     = array( 'modal_class' => 'mediumscreen' );
			$this->config['tooltip']        = __( 'Creates a custom gallery', 'avia_framework' );
			$this->config['preview'] 		= 1;
			$this->config['disabling_allowed'] = 'manually'; //only allowed manually since the default [gallery shortcode] is also affected
			$this->config['id_name']		= 'id';
			$this->config['id_show']		= 'yes';
			$this->config['alb_desc_id']	= 'alb_description';
		}
			
		function extra_assets()
		{
			//load css
			wp_enqueue_style( 'avia-module-gallery', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/gallery/gallery.css', array( 'avia-layout' ), false );

			wp_enqueue_script( 'avia-module-gallery', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/gallery/gallery.js', array( 'avia-shortcodes' ), false, true );

		}

		/**
		 * Popup Elements
		 *
		 * If this function is defined in a child class the element automatically gets an edit button, that, when pressed
		 * opens a modal window that allows to edit the element properties
		 *
		 * @return void
		 */
		function popup_elements()
		{
			$this->elements = array(
				
				array(
						'type' 	=> 'tab_container', 
						'nodescription' => true
					),
						
				array(
						'type' 	=> 'tab',
						'name'  => __( 'Content', 'avia_framework' ),
						'nodescription' => true
					),
				
					array(	
								'type'			=> 'template',
								'template_id'	=> $this->popup_key( 'content_entries' )
							),
				
				array(
						'type' 	=> 'tab_close',
						'nodescription' => true
					),

				array(
						'type' 	=> 'tab',
						'name'  => __( 'Styling', 'avia_framework' ),
						'nodescription' => true
					),
				
					array(
							'type'			=> 'template',
							'template_id'	=> $this->popup_key( 'styling_gallery' )
						),
				
				array(
						'type' 	=> 'tab_close',
						'nodescription' => true
					),
				
				array(
						'type' 	=> 'tab',
						'name'  => __( 'Advanced', 'avia_framework' ),
						'nodescription' => true
					),
				
					array(
							'type' 	=> 'toggle_container',
							'nodescription' => true
						),
				
						array(	
								'type'			=> 'template',
								'template_id'	=> $this->popup_key( 'advanced_link' )
							),
				
						array(	
								'type'			=> 'template',
								'template_id'	=> $this->popup_key( 'advanced_animation' )
							),
				
						array(
								'type'			=> 'template',
								'template_id'	=> 'lazy_loading_toggle',
								'id'			=> 'html_lazy_loading'
							),
				
						array(	
								'type'			=> 'template',
								'template_id'	=> 'screen_options_toggle'
							),
				
						array(	
								'type'			=> 'template',
								'template_id'	=> 'developer_options_toggle',
								'args'			=> array( 'sc' => $this )
							),
				
					array(
							'type' 	=> 'toggle_container_close',
							'nodescription' => true
						),
				
				array(
						'type' 	=> 'tab_close',
						'nodescription' => true
					),

				array(
						'type' 	=> 'tab_container_close',
						'nodescription' => true
					)

				);

		}
		
		/**
		 * Create and register templates for easier maintainance
		 * 
		 * @since 4.6.4
		 */
		protected function register_dynamic_templates()
		{
			
			/**
			 * Content Tab
			 * ===========
			 */
			
			$c = array(
						array(
							'name' 	=> __( 'Edit Gallery','avia_framework' ),
							'desc' 	=> __( 'Create a new Gallery by selecting existing or uploading new images', 'avia_framework' ),
							'id' 	=> 'ids',
							'type' 	=> 'gallery',
							'modal_class' => 'av-show-image-custom-link',
							'title'		=> __( 'Add/Edit Gallery', 'avia_framework' ),
							'button'	=> __( 'Insert Images', 'avia_framework' ),
							'std' 	=> ''
						),

				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_entries' ), $c );
			
			/**
			 * Styling Tab
			 * ===========
			 */
			
			$c = array(
						array(
							'name' 	=> __( 'Gallery Style', 'avia_framework' ),
							'desc' 	=> __( 'Choose the layout of your Gallery', 'avia_framework' ),
							'id' 	=> 'style',
							'type' 	=> 'select',
							'std' 	=> 'thumbnails',
							'subtype'	=> array(
												__( 'Small Thumbnails', 'avia_framework' )					=> 'thumbnails',
												__( 'Big image with thumbnails below', 'avia_framework' )	=> 'big_thumb',
												__( 'Big image only, other images can be accessed via lightbox', 'avia_framework' ) => 'big_thumb lightbox_gallery',
											)
						),

						array(
							'name' 	=> __( 'Gallery Big Preview Image Size', 'avia_framework' ),
							'desc' 	=> __( 'Choose image size for the Big Preview Image', 'avia_framework' ),
							'id' 	=> 'preview_size',
							'type' 	=> 'select',
							'std' 	=> 'portfolio',
							'required'	=> array( 'style', 'contains', 'big_thumb' ),
							'subtype'	=> AviaHelper::get_registered_image_sizes( array( 'logo' ) )
						),

						array(
							'name' 	=> __( 'Force same size for all big preview images?', 'avia_framework' ),
							'desc' 	=> __( 'Depending on the size you selected above, preview images might differ in size. Should the theme force them to display at exactly the same size?', 'avia_framework' ),
							'id' 	=> 'crop_big_preview_thumbnail',
							'type' 	=> 'select',
							'std' 	=> 'avia-gallery-big-crop-thumb',
							'required'	=> array( 'style', 'equals', 'big_thumb' ),
							'subtype'	=> array(
												__( 'Yes, force same size on all Big Preview images, even if they use a different aspect ratio', 'avia_framework' ) => 'avia-gallery-big-crop-thumb', 
												__( 'No, do not force the same size', 'avia_framework' ) => 'avia-gallery-big-no-crop-thumb'
											)
						),

						array(
							'name' 	=> __( 'Gallery Preview Image Size', 'avia_framework' ),
							'desc' 	=> __( 'Choose image size for the small preview thumbnails', 'avia_framework' ),
							'id' 	=> 'thumb_size',
							'type' 	=> 'select',
							'std' 	=> 'portfolio',
							'required' 	=> array( 'style', 'not', 'big_thumb lightbox_gallery' ),
							'subtype'	=>  AviaHelper::get_registered_image_sizes( array( 'logo' ) )
						),

						array(
							'name' 	=> __('Thumbnail Columns', 'avia_framework' ),
							'desc' 	=> __('Choose the column count of your Gallery', 'avia_framework' ),
							'id' 	=> 'columns',
							'type' 	=> 'select',
							'std' 	=> '5',
							'required'	=> array( 'style', 'not', 'big_thumb lightbox_gallery' ),
							'subtype'	=> AviaHtmlHelper::number_array( 1, 12, 1 )
						),

						
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_gallery' ), $c );
			
			/**
			 * Advanced Tab
			 * ============
			 */
			
			$c = array(
						array(
							'name' 	=> __( 'Image Link', 'avia_framework' ),
							'desc' 	=> __( 'By default images link to a larger image version in a lightbox. You can change this here. A custom link can be added when editing the images in the gallery.', 'avia_framework' ),
							'id' 	=> 'imagelink',
							'type' 	=> 'select',
							'std' 	=> 'lightbox',
							'required'	=> array( 'style', 'not', 'big_thumb lightbox_gallery' ),
							'subtype'	=> array(
												__( 'Lightbox linking active', 'avia_framework' )						=> 'lightbox',
												__( 'Use custom link (fallback is image link)', 'avia_framework' )		=> 'custom_link',
												__( 'Open the images in the browser window', 'avia_framework' )			=> 'aviaopeninbrowser noLightbox',
												__( 'Open the images in a new browser window/tab', 'avia_framework' )	=> 'aviaopeninbrowser aviablank noLightbox',
												__( 'No, don\'t add a link to the images at all', 'avia_framework' )	=> 'avianolink noLightbox'
											)
						),
				
						array(
							'name'		=> __( 'Custom link destination', 'avia_framework' ),
							'desc'		=> __( 'Select where an existing custom link should be opened.', 'avia_framework' ),
							'id'		=> 'link_dest',
							'type'		=> 'select',
							'std'		=> '',
							'required'	=> array( 'imagelink', 'equals', 'custom_link' ),
							'subtype'	=> array(
												__( 'Open in same window', 'avia_framework' )		=> '',
												__( 'Open in a new window', 'avia_framework' )		=> '_blank'
											)
						),
				
						array(
							'name'		=> __( 'Lightbox image description text', 'avia_framework' ),
							'desc'		=> __( 'Select which text defined in the media gallery is displayed below the lightbox image.', 'avia_framework' ),
							'id'		=> 'lightbox_text',
							'type'		=> 'select',
							'std'		=> 'caption',
							'required'	=> array( 'imagelink', 'equals', 'lightbox' ),
							'subtype'	=> array(
												__( 'No text', 'avia_framework' )										=> 'no_text',
												__( 'Image title', 'avia_framework' )									=> '',
												__ ('Image description (or image title if empty)', 'avia_framework' )	=> 'description',
												__( 'Image caption (or image title if empty)', 'avia_framework' )		=> 'caption'
											)
						)
						
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Link Settings', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'advanced_link' ), $template );
			
			$c = array(
						array(
							'name'		=> __( 'Thumbnail fade in effect', 'avia_framework' ),
							'desc'		=> __( 'You can set when the gallery thumbnail animation starts', 'avia_framework' ),
							'id'		=> 'lazyload',
							'type'		=> 'select',
							'std'		=> 'avia_lazyload',
							'required'	=> array( 'style', 'not', 'big_thumb lightbox_gallery' ),
							'subtype'	=> array(
												__( 'Disable all animations', 'avia_framework' )								=> 'animations_off',
												__( 'Show the animation when user scrolls to the gallery', 'avia_framework' )	=> 'avia_lazyload',
												__( 'Activate animation on page load (might be preferable on large galleries)', 'avia_framework' ) => 'deactivate_avia_lazyload'
											)
						)
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Animation', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'advanced_animation' ), $template );
			
			
		}
		

		/**
		 * Editor Element - this function defines the visual appearance of an element on the AviaBuilder Canvas
		 * Most common usage is to define some markup in the $params['innerHtml'] which is then inserted into the drag and drop container
		 * Less often used: $params['data'] to add data attributes, $params['class'] to modify the className
		 *
		 *
		 * @param array $params this array holds the default values for $content and $args.
		 * @return $params the return array usually holds an innerHtml key that holds item specific markup.
		 */
		function editor_element( $params )
		{
			$params = parent::editor_element( $params );
			$params['content'] 	 = null; //remove to allow content elements
			return $params;
		}

		/**
		 * Frontend Shortcode Handler
		 *
		 * @param array $atts array of attributes
		 * @param string $content text within enclosing form of shortcode element
		 * @param string $shortcodename the shortcode found, when == callback name
		 * @return string $output returns the modified html string
		 */
		function shortcode_handler( $atts, $content = '', $shortcodename = '', $meta = '' )
		{
				
			extract( AviaHelper::av_mobile_sizes( $atts ) ); //return $av_font_classes, $av_title_font_classes and $av_display_classes 

			$output  = '';
			$first   = true;

			if( empty( $atts['columns'] ) && isset( $atts['ids'] ) )
			{
				$atts['columns'] = count( explode( ',', $atts['ids'] ) );
				if( $atts['columns'] > 10 ) 
				{ 
					$atts['columns'] = 10; 
				}
			}
				
			extract( shortcode_atts( array(
						'order'      	=> 'ASC',
						'thumb_size' 	=> 'thumbnail',
						'size' 			=> '',
						'lightbox_size' => 'large',
						'preview_size'	=> 'portfolio',
						'ids'    	 	=> '',
						'ajax_request'	=> false,
						'imagelink'     => 'lightbox',
						'link_dest'		=> '',
						'lightbox_text'	=> 'caption',
						'style'			=> 'thumbnails',
						'columns'		=> 5,
						'lazyload'      => 'avia_lazyload',
						'html_lazy_loading'				=> 'disabled',
						'crop_big_preview_thumbnail'	=> 'avia-gallery-big-crop-thumb'
				
					), $atts, $this->config['shortcode'] ) );


			$attachments = get_posts( array(
								'include'		=> $ids,
								'post_status'	=> 'inherit',
								'post_type'		=> 'attachment',
								'post_mime_type' => 'image',
								'order'			=> $order,
								'orderby'		=> 'post__in'
								)
						);

				
			//compatibility mode for default wp galleries
			if( ! empty( $size ) ) 
			{
				$thumb_size = $size;
			}
			
			$rel = '';

			if( 'big_thumb lightbox_gallery' == $style )
			{
				$imagelink = 'lightbox';
				$lazyload  = 'deactivate_avia_lazyload';
				$meta['el_class'] .= ' av-hide-gallery-thumbs';
			}
			else
			{
				if( 'custom_link' == $imagelink )
				{
					$imagelink .= ' aviaopeninbrowser noLightbox';
					
					if( '_blank' == $link_dest )
					{
						$imagelink .= ' aviablank';
						$rel = 'rel="noopener noreferrer"';
					}
				}
			}

			// animation
			$animation_class = '';
			if( $lazyload != 'animations_off' )
			{
				$animation_class = 'avia-gallery-animate';
			}

			if( ! empty( $attachments ) && is_array( $attachments ) )
			{
				self::$gallery++;
				$thumb_width = round( 100 / $columns, 4 );

				$markup = avia_markup_helper( array( 'context' => 'image', 'echo' => false, 'custom_markup' => $meta['custom_markup'] ) );
				$output .= "<div {$meta['custom_el_id']} class='avia-gallery {$av_display_classes} avia-gallery-".self::$gallery." ".$lazyload." ".$animation_class." avia_animate_when_visible ".$meta['el_class']."' $markup>";
				$thumbs = '';
				$counter = 0;

				foreach( $attachments as $attachment )
				{
					$link = wp_get_attachment_image_src( $attachment->ID, $lightbox_size );
					
					if( false !== strpos( $imagelink, 'custom_link') )
					{
						$c_link = $custom_url = get_post_meta( $attachment->ID, 'av-custom-link', true );
						if( ! empty( $c_link ) )
						{
							$link[0] = $c_link;
						}
					}
					
					$link =  apply_filters( 'avf_avia_builder_gallery_image_link', $link, $attachment, $atts, $meta );
					
					$custom_link_class = ! empty( $link['custom_link_class'] ) ? $link['custom_link_class'] : '';
					$class = $counter++ % $columns ? "class='$imagelink $custom_link_class'" : "class='first_thumb $imagelink $custom_link_class'";
					
					$img = wp_get_attachment_image_src( $attachment->ID, $thumb_size );
					$prev = wp_get_attachment_image_src( $attachment->ID, $preview_size );

					$caption = trim( $attachment->post_excerpt ) ? wptexturize( $attachment->post_excerpt ) : '';
					$tooltip = $caption ? "data-avia-tooltip='{$caption}'" : '';

					$alt = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );
					$alt = ! empty( $alt ) ? esc_attr( $alt ) : '';
					
					$title = trim( $attachment->post_title ) ? esc_attr( $attachment->post_title ) : '';
					$description = trim( $attachment->post_content ) ? esc_attr( $attachment->post_content ) : '';
					
					$lightbox_title = $title;
					switch( $lightbox_text )
					{
						case 'caption':
							$lightbox_title = ( '' != $caption ) ? $caption : $title;
							break;
						case 'description':
							$lightbox_title = ( '' != $description ) ? $description : $title;
							break;
						case 'no_text':
							$lightbox_title = '';
					}
					
					$markup_url = avia_markup_helper( array( 'context' => 'image_url', 'echo' => false, 'id' => $attachment->ID, 'custom_markup' => $meta['custom_markup'] ) );

					if( strpos( $style, 'big_thumb' ) !== false && $first )
					{
						$img_tag = "<img width='{$prev[1]}' height='{$prev[2]}' src='{$prev[0]}' title='{$title}' alt='{$alt}' />";
						$img_tag = Av_Responsive_Images()->prepare_single_image( $img_tag, $attachment->ID, $html_lazy_loading );
						
						$output .= "<a class='avia-gallery-big fakeLightbox $imagelink $crop_big_preview_thumbnail $custom_link_class' href='" . $link[0] . "'  data-onclick='1' title='{$lightbox_title}' {$rel}><span class='avia-gallery-big-inner' {$markup_url}>";
						$output .=			$img_tag;
						
						if( $caption ) 
						{
							$output .= "	<span class='avia-gallery-caption'>{$caption}</span>"; 
						}
						$output .= '</span></a>';
					}

					$img_tag = "<img {$tooltip} src='{$img[0]}' width='{$img[1]}' height='{$img[2]}'  title='{$title}' alt='{$alt}' />";
					$img_tag = Av_Responsive_Images()->prepare_single_image( $img_tag, $attachment->ID, $html_lazy_loading );
					
					$thumbs .= " <a href='{$link[0]}' data-rel='gallery-" . self::$gallery . "' data-prev-img='{$prev[0]}' {$class} data-onclick='{$counter}' title='{$lightbox_title}' {$markup_url} {$rel}>{$img_tag}</a>";
					$first = false;
				}

				$output .= "<div class='avia-gallery-thumb'>{$thumbs}</div>";
				$output .= '</div>';

				$selector = ! empty( $atts['ajax_request'] ) ? '.ajax_slide' : '';

				//generate thumb width based on columns
				$this->extra_style .= "<style type='text/css'>";
				$this->extra_style .= "#top #wrap_all {$selector} .avia-gallery-" . self::$gallery . " .avia-gallery-thumb a{width:{$thumb_width}%;}";
				$this->extra_style .= '</style>';

				if( ! empty( $this->extra_style ) )
				{
						
					if( ! empty( $atts['ajax_request'] ) || ! empty( $_POST['avia_request'] ) )
					{
						$output .= $this->extra_style;
						$this->extra_style = '';
					}
					else
					{
						$this->non_ajax_style = $this->extra_style;
						add_action( 'wp_footer', array( $this, 'print_extra_style' ) );
					}
				}

			}
			
			return Av_Responsive_Images()->make_content_images_responsive( $output );
		}


		/**
		 * Handler printed in footer
		 */
		public function print_extra_style()
		{
			echo $this->non_ajax_style;
		}

	}
}

