<?php
/**
 * Image
 * 
 * Shortcode which inserts an image of your choice
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if ( ! class_exists( 'avia_sc_image' ) )
{
	class avia_sc_image extends aviaShortcodeTemplate
	{
		/**
		 * Create the config array for the shortcode button
		 */
		function shortcode_insert_button()
		{
			$this->config['version']		= '1.0';
			$this->config['self_closing']	= 'no';

			$this->config['name']			= __( 'Image', 'avia_framework' );
			$this->config['tab']			= __( 'Media Elements', 'avia_framework' );
			$this->config['icon']			= AviaBuilder::$path['imagesURL'] . 'sc-image.png';
			$this->config['order']			= 100;
			$this->config['target']			= 'avia-target-insert';
			$this->config['shortcode'] 		= 'av_image';
			//$this->config['modal_data']     = array( 'modal_class' => 'mediumscreen' );
			$this->config['tooltip'] 	    = __( 'Inserts an image of your choice', 'avia_framework' );
			$this->config['preview'] 		= 1;
			$this->config['disabling_allowed'] = true;
			$this->config['id_name']		= 'id';
			$this->config['id_show']		= 'yes';
		}

		function extra_assets()
		{
			//load css
			wp_enqueue_style( 'avia-module-image' , AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/image/image.css', array('avia-layout'), false );

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
							'template_id'	=> $this->popup_key( 'content_image' )
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
							'template_id'	=> 'toggle_container',
							'templates_include'	=> array( 
													$this->popup_key( 'styling_image_styling' ),
													$this->popup_key( 'styling_image_caption' )
												),
							'nodescription' => true
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
								'template_id'	=> $this->popup_key( 'advanced_animation' ),
							),
				
						array(	
								'type'			=> 'template',
								'template_id'	=> $this->popup_key( 'advanced_link' ),
							),
				
						array(	
								'type'			=> 'template',
								'template_id'	=> $this->popup_key( 'advanced_seo' ),
							),
				
						array(
								'type'			=> 'template',
								'template_id'	=> 'lazy_loading_toggle'
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
					),	
					
				array(	
						'id'	=> 'av_element_hidden_in_editor',
						'type'	=> 'hidden',
						'std'	=> '0'
					),
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
							'name'	=> __( 'Choose Image', 'avia_framework' ),
							'desc'	=> __( 'Either upload a new, or choose an existing image from your media library', 'avia_framework' ),
							'id'	=> 'src',
							'type'	=> 'image',
							'title'	=> __( 'Insert Image', 'avia_framework' ),
							'button'	=> __( 'Insert', 'avia_framework' ),
							'std'	=> AviaBuilder::$path['imagesURL'] . 'placeholder.jpg' 
						),
				
						array(
							'name' 	=> __( 'Copyright Info', 'avia_framework' ),
							'desc' 	=> __( 'Use the media manager to add/edit the copyright info.', 'avia_framework' ),
							'id' 	=> 'copyright',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype' => array(
											__( 'No', 'avia_framework' )									=> '',
											__( 'Yes, always display copyright info', 'avia_framework' )	=> 'always',
											__( 'Yes, display icon and reveal copyright info on hover', 'avia_framework' )	=> 'icon-reveal',
										)
						),
	
						array(
							'name' 	=> __( 'Image Caption', 'avia_framework' ),
							'desc' 	=> __( 'Display a caption overlay?', 'avia_framework' ),
							'id' 	=> 'caption',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype' => array(
											__( 'No', 'avia_framework' )	=> '',
											__( 'Yes', 'avia_framework' )	=> 'yes',
										)
						),
									
						array(
							'name' 		=> __( 'Caption', 'avia_framework' ),
							'desc'		=> __( 'Add your caption text', 'avia_framework' ),
							'id' 		=> 'content',
							'type' 		=> 'textarea',
							'required'	=> array( 'caption', 'equals', 'yes' ),
							'std' 		=> '',
						),

					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_image' ), $c );
			
			/**
			 * Styling Tab
			 * ===========
			 */
			
			$c = array(
						
						array(	
							'name' 	=> __( 'Caption custom font size?', 'avia_framework' ),
							'desc' 	=> __( 'Size of your caption in pixel', 'avia_framework' ),
							'id' 	=> 'font_size',
							'type' 	=> 'select',
							'required'	=> array( 'caption', 'equals', 'yes' ),
							'subtype' => AviaHtmlHelper::number_array( 10, 40, 1, array( 'Default' => '' ), 'px' ),
							'std' => ''
						),

						array(
							'name' 	=> __( 'Caption Overlay Opacity', 'avia_framework' ),
							'desc' 	=> __( 'Set the opacity of your overlay: 0.1 is barely visible, 1.0 is opaque ', 'avia_framework' ),
							'id' 	=> 'overlay_opacity',
							'type' 	=> 'select',
							'std' 	=> '0.4',
							'required'	=> array( 'caption', 'equals','yes' ),
							'subtype' => array(   
											__( '0.1', 'avia_framework' )	=> '0.1',
											__( '0.2', 'avia_framework' )	=> '0.2',
											__( '0.3', 'avia_framework' )	=> '0.3',
											__( '0.4', 'avia_framework' )	=> '0.4',
											__( '0.5', 'avia_framework' )	=> '0.5',
											__( '0.6', 'avia_framework' )	=> '0.6',
											__( '0.7', 'avia_framework' )	=> '0.7',
											__( '0.8', 'avia_framework' )	=> '0.8',
											__( '0.9', 'avia_framework' )	=> '0.9',
											__( '1.0', 'avia_framework' )	=> '1',
										)
						),
								  		
						array(
							'name' 	=> __( 'Caption Overlay Background Color', 'avia_framework' ),
							'desc' 	=> __( 'Select a background color for your overlay here.', 'avia_framework' ),
							'id' 	=> 'overlay_color',
							'type' 	=> 'colorpicker',
							'container_class' => 'av_half av_half_first',
							'required'	=> array( 'caption', 'equals', 'yes' ),
							'std' 	=> '#000000',
						),	
									
						array(	
							'name' 	=> __( 'Caption Font Color', 'avia_framework' ),
							'desc' 	=> __( 'Select a font color for your overlay here.', 'avia_framework' ),
							'id' 	=> 'overlay_text_color',
							'type' 	=> 'colorpicker',
							'std' 	=> '#ffffff',
							'container_class' => 'av_half',
							'required'	=> array( 'caption', 'equals', 'yes' ),
						),
				
						
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Image Caption', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_image_caption' ), $template );
			
			
			
			$c = array(
						array(
							'name' 	=> __( 'Image Styling', 'avia_framework' ),
							'desc' 	=> __( 'Choose a styling variaton', 'avia_framework' ),
							'id' 	=> 'styling',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype' => array(
											__( 'Default',  'avia_framework' )	=> '',
											__( 'Circle (image height and width must be equal)',  'avia_framework' )	=> 'circle',
											__( 'No Styling (no border, no border radius etc)',  'avia_framework' )		=> 'no-styling',
										)
						),

						array(
							'name' 	=> __( 'Image Alignment', 'avia_framework' ),
							'desc' 	=> __( 'Choose here, how to align your image', 'avia_framework' ),
							'id' 	=> 'align',
							'type' 	=> 'select',
							'std' 	=> 'center',
							'subtype' => array(
											__('Center',  'avia_framework' )	=> 'center',
											__('Right',  'avia_framework' )		=> 'right',
											__('Left',  'avia_framework' )		=> 'left',
											__('No special alignment', 'avia_framework' )	=> '',
										)
						)

				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Image Styling', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_image_styling' ), $template );
			
			
			
			/**
			 * Advanced Tab
			 * ============
			 */
			
			$c = array(
						array(
							'name' 	=> __( 'Image Fade in Animation', 'avia_framework' ),
							'desc' 	=> __( "Add a small animation to the image when the user first scrolls to the image position. This is only to add some 'spice' to the site and only works in modern browsers", 'avia_framework' ),
							'id' 	=> 'animation',
							'type' 	=> 'select',
							'std' 	=> 'no-animation',
							'subtype' => array(
											__( 'None', 'avia_framework' )	=> 'no-animation',
											__( 'Fade Animations', 'avia_framework')	=> array(
														__( 'Fade in', 'avia_framework' )	=> 'fade-in',
														__( 'Pop up', 'avia_framework' )	=> 'pop-up',
													),
											__( 'Slide Animations', 'avia_framework' ) => array(
														__( 'Top to Bottom', 'avia_framework' ) => 'top-to-bottom',
														__( 'Bottom to Top', 'avia_framework' ) => 'bottom-to-top',
														__( 'Left to Right', 'avia_framework' ) => 'left-to-right',
														__( 'Right to Left', 'avia_framework' ) => 'right-to-left',
													),
											__( 'Rotate',  'avia_framework' ) => array(
														__( 'Full rotation', 'avia_framework' )	=> 'av-rotateIn',
														__( 'Bottom left rotation', 'avia_framework' )	=> 'av-rotateInUpLeft',
														__( 'Bottom right rotation', 'avia_framework' )	=> 'av-rotateInUpRight',
													)
										)
						),
				
						array(
							'name' 	=> __( 'Image Hover effect', 'avia_framework' ),
							'desc' 	=> __( 'Add a mouse hover effect to the image', 'avia_framework' ),
							'id' 	=> 'hover',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype' => array(
											__( 'No', 'avia_framework' )	=> '',
											__( 'Yes, slightly increase the image size', 'avia_framework' )	=> 'av-hover-grow',
											__( 'Yes, slightly zoom the image', 'avia_framework' )			=> 'av-hover-grow av-hide-overflow',
										)
						),
				
						array(
							'name' 	=> __( 'Caption Appearance', 'avia_framework' ),
							'desc' 	=> __( 'When to display the caption?', 'avia_framework' ),
							'id' 	=> 'appearance',
							'type' 	=> 'select',
							'std' 	=> '',
							'required'	=> array( 'caption', 'equals', 'yes' ),
							'subtype' => array(
											__( 'Always display caption', 'avia_framework' )	=> '',
											__( 'Only display on hover', 'avia_framework' )		=> 'on-hover',
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
			
			$c = array(
						array(
							'name'	=> __( 'Image Link?', 'avia_framework' ),
							'desc'	=> __( 'Where should your image link to?', 'avia_framework' ),
							'id'	=> 'link',
							'type'	=> 'linkpicker',
							'fetchTMPL'	=> true,
							'subtype'	=> array(
											__( 'No Link', 'avia_framework' )	=> '',
											__( 'Lightbox', 'avia_framework' )	=> 'lightbox',
											__( 'Set Manually', 'avia_framework' )	=> 'manually',
											__( 'Single Entry', 'avia_framework' )	=> 'single',
											__( 'Taxonomy Overview Page',  'avia_framework' )	=> 'taxonomy',
										),
							'std' 	=> ''
						),
				
						array(
							'name'	=> __( 'Open new tab/window', 'avia_framework' ),
							'desc'	=> __( 'Do you want to open the link url in a new tab/window?', 'avia_framework' ),
							'id'	=> 'target',
							'type'	=> 'select',
							'std'	=> '',
							'required'	=> array( 'link', 'not_empty_and', 'lightbox' ),
							'subtype'	=> AviaHtmlHelper::linking_options()
						),

					);
						
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Image Link Settings', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'advanced_link' ), $template );
			
			$c = array(
						array(
							'name' 			=> __( 'Custom Title Attribute', 'avia_framework' ),
							'desc' 			=> __( 'Add a custom title attribute limited to this instance, replaces media gallery settings.', 'avia_framework' ),
							'id' 			=> 'title_attr',
							'type' 			=> 'input',
							'std' 			=> ''
						),

						array(
							'name' 			=> __( 'Custom Alt Attribute', 'avia_framework' ),
							'desc' 			=> __( 'Add a custom alt attribute limited to this instance, replaces media gallery settings.', 'avia_framework' ),
							'id' 			=> 'alt_attr',
							'type' 			=> 'input',
							'std' 			=> ''
						)
					);
			
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'SEO improvements', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'advanced_seo' ), $template );
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
			$template = $this->update_template( 'src', "<img src='{{src}}' alt=''/>" );
			$img	  = '';

			if( ! empty( $params['args']['attachment'] ) && ! empty( $params['args']['attachment_size'] ) )
			{
				$img = wp_get_attachment_image( $params['args']['attachment'], $params['args']['attachment_size'] );
			}
			else if( isset( $params['args']['src'] ) && is_numeric( $params['args']['src'] ) )
			{
				$img = wp_get_attachment_image( $params['args']['src'], 'large' );
			}
			else if( ! empty( $params['args']['src'] ) )
			{
				$img = "<img src='" . esc_attr( $params['args']['src'] ) . "' alt=''  />";
			}

			$params['innerHtml']  = "<div class='avia_image avia_image_style avia_hidden_bg_box'>";
			$params['innerHtml'] .=		'<div ' . $this->class_by_arguments( 'align', $params['args'] ) . '>';
			$params['innerHtml'] .=			"<div class='avia_image_container' {$template}>{$img}</div>";
			$params['innerHtml'] .=		'</div>';
			$params['innerHtml'] .= '</div>';
			$params['class'] = '';

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

			$output = '';
			$class = '';
			$src = '';
			$alt = '';
			$title = '';
			$copyright_text = '';
			$attachment_id = 0;

			$atts = shortcode_atts( array(	
						'src'				=> '', 
						'title_attr'		=> '',
						'alt_attr'			=> '',
						'animation'			=> 'no-animation', 
						'lazy_loading'		=> 'disabled',
						'link'				=> '', 
						'attachment'		=> '', 
						'attachment_size'	=> '', 
						'target'			=> '', 
						'styling'			=> '', 
						'caption'			=> '',
						'copyright'			=> '',
						'font_size'			=> '', 
						'appearance'		=> '', 
						'hover'				=> '',
						'align'				=> 'center',
						'overlay_opacity'	=> '0.4', 
						'overlay_color'		=> '#444444', 
						'overlay_text_color'	=> '#ffffff'
					), $atts, $this->config['shortcode'] );

			extract( $atts );

			$img_h = '';
			$img_w = '';
				
			if( ! empty( $attachment ) )
			{
				/**
				 * Allows e.g. WPML to reroute to translated image
				 */
				$posts = get_posts( array(
										'include'			=> $attachment,
										'post_status'		=> 'inherit',
										'post_type'			=> 'attachment',
										'post_mime_type'	=> 'image',
										'order'				=> 'ASC',
										'orderby'			=> 'post__in' 
									)
								);

				if( is_array( $posts ) && ! empty( $posts ) )
				{
					$attachment_entry = $posts[0];
					$attachment_id = $attachment_entry->ID;
					
					if( ! empty( $alt_attr ) )
					{
						$alt = $alt_attr;
					}
					else
					{
						$alt = get_post_meta( $attachment_entry->ID, '_wp_attachment_image_alt', true );
					}
					
					$alt = ! empty( $alt ) ? esc_attr( trim( $alt ) ) : '';
					
					if( ! empty( $title_attr ) )
					{
						$title = $title_attr;
					}
					else
					{
						$title = $attachment_entry->post_title;
					}
					
					$title = ! empty( $title ) ? esc_attr( trim( $title ) ) : '';
					
					if( $copyright !== '') 
					{
						$copyright_text = get_post_meta( $attachment_entry->ID, '_avia_attachment_copyright', true );
						
						/**
						 * Allow to filter the copyright text
						 * 
						 * @since 4.7.4.1
						 * @param string $copyright_text
						 * @param string $shortcodename			context calling the filter
						 * @param int $attachment_entry->ID
						 * @return string
						 */
						$copyright_text = apply_filters( 'avf_attachment_copyright_text', $copyright_text, $shortcodename, $attachment_entry->ID );
					}

					if( ! empty( $attachment_size ) )
					{
						$src = wp_get_attachment_image_src( $attachment_entry->ID, $attachment_size );

						$img_h = ! empty( $src[2] ) ? $src[2] : '';
						$img_w = ! empty( $src[1] ) ? $src[1] : '';
						$src   = ! empty( $src[0] ) ? $src[0] : '';
					}
				}
			}
			else
			{
				$attachment = false;
			}

			if( ! empty( $src ) )
			{
				$class  = $animation == 'no-animation' ? '' : "avia_animated_image avia_animate_when_almost_visible {$animation}";
				$class .= " av-styling-{$styling} {$hover}";

				if( is_numeric( $src ) )
				{
					//$output = wp_get_attachment_image( $src,'large' );
					$img_atts = array( 'class' => "avia_image {$class} " . $this->class_by_arguments( 'align', $atts, true ) );

					if( ! empty( $img_h ) ) 
					{
						$img_atts['height'] = $img_h;
					}
					if( ! empty( $img_w ) ) 
					{
						$img_atts['width'] = $img_w;
					}
					
					if( $lazy_loading != 'enabled' )
					{
						Av_Responsive_Images()->add_attachment_id_to_not_lazy_loading( $src );
					}

					$output = wp_get_attachment_image( $src, 'large', false, $img_atts );
				}
				else
				{
					$link = AviaHelper::get_url( $link, $attachment );
					$blank = AviaHelper::get_link_target( $target );

					$overlay = '';
					$style = '';
					$style .= AviaHelper::style_string( $atts, 'overlay_text_color', 'color' );
					if( $font_size )
					{
						// $style = "style='font-size: {$font_size}px;'";
						$style .= AviaHelper::style_string( $atts, 'font_size', 'font-size', 'px' );
					}
					
					$style = AviaHelper::style_string( $style );

					if( $caption == 'yes' )
					{	
						$caption_style = '';
						$caption_style .= AviaHelper::style_string( $atts, 'overlay_opacity', 'opacity' );
						$caption_style .= AviaHelper::style_string( $atts, 'overlay_color', 'background-color' );
						$caption_style  = AviaHelper::style_string( $caption_style );
						$overlay_bg = "<div class='av-caption-image-overlay-bg' {$caption_style}></div>";

						$content = ShortcodeHelper::avia_apply_autop( ShortcodeHelper::avia_remove_autop( $content ) );
						$overlay = "<div class='av-image-caption-overlay'>{$overlay_bg}<div class='av-image-caption-overlay-position'><div class='av-image-caption-overlay-center' {$style}>{$content}</div></div></div>";
						$class .= ' noHover ';

						if( empty( $appearance ) ) 
						{
							$appearance = 'hover-deactivate';
						}
						
						if( $appearance ) 
						{
							$class .= " av-overlay-{$appearance}";
						}
					}

					$copyright_tag = '';
					if( ! empty( $copyright_text ) )
					{
						$copyright_tag = "<small class='avia-copyright'>{$copyright_text}</small>";
						$class .= ' av-has-copyright';
						if( $copyright != '' ) 
						{
							$class .= ' av-copyright-' . $copyright;
						}
					}

					$markup_url = avia_markup_helper( array( 'context' => 'image_url', 'echo' => false, 'custom_markup' => $meta['custom_markup'] ) );
					$markup = avia_markup_helper( array( 'context' => 'image', 'echo' => false, 'custom_markup' => $meta['custom_markup'] ) );

					$output .= "<div {$meta['custom_el_id']} class='avia-image-container {$class} {$av_display_classes} " . $meta['el_class'] . " " . $this->class_by_arguments( 'align' ,$atts, true ) . "' {$markup} >";
					$output .= "<div class='avia-image-container-inner'>";

					$output .= "<div class='avia-image-overlay-wrap'>";

					if( $link )
					{
						$img_tag = "<img class='avia_image' src='{$src}' alt='{$alt}' title='{$title}' {$markup_url} />";
						$img_tag = Av_Responsive_Images()->prepare_single_image( $img_tag, $attachment_id, $lazy_loading );
						
						$output.= "<a href='{$link}' class='avia_image'  {$blank}>{$overlay}{$img_tag}</a>";
					}
					else
					{
						$hw = '';
						if( ! empty( $img_h ) ) 
						{
							$hw .= 'height="' . $img_h . '"';
						}

						if( ! empty( $img_w ) ) 
						{
							$hw .= ' width="' . $img_w . '"';
						}
						
						$img_tag = "<img class='avia_image' src='{$src}' alt='{$alt}' title='{$title}' {$hw} {$markup_url} />";
						$img_tag = Av_Responsive_Images()->prepare_single_image( $img_tag, $attachment_id, $lazy_loading );
						
						$output.= "{$overlay}{$img_tag}";
					}
					
					$output .= '</div>';
					$output .= $copyright_tag;

					$output .= '</div>';
					$output .= '</div>';
				}
			}

			return Av_Responsive_Images()->make_content_images_responsive( $output );
		}

	}
}

