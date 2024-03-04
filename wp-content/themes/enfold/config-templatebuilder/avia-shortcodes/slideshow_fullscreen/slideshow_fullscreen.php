<?php
/**
 * Fullscreen Slider
 * 
 * Shortcode that allows to display a fullscreen slideshow element
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if ( ! class_exists( 'avia_sc_slider_fullscreen' ) ) 
{
	class avia_sc_slider_fullscreen extends aviaShortcodeTemplate
	{
		/**
		 *
		 * @var int 
		 */
		static public $slide_count = 0;

		/**
		 * Create the config array for the shortcode button
		 */
		function shortcode_insert_button()
		{
			$this->config['version']		= '1.0';
			$this->config['is_fullwidth']	= 'yes';
			$this->config['self_closing']	= 'no';

			$this->config['name']			= __( 'Fullscreen Slider', 'avia_framework' );
			$this->config['tab']			= __( 'Media Elements', 'avia_framework' );
			$this->config['icon']			= AviaBuilder::$path['imagesURL'] . 'sc-fullscreen.png';
			$this->config['order']			= 60;
			$this->config['target']			= 'avia-target-insert';
			$this->config['shortcode'] 		= 'av_fullscreen';
			$this->config['shortcode_nested'] = array('av_fullscreen_slide');
			$this->config['tooltip'] 	    = __( 'Display a fullscreen slideshow element', 'avia_framework' );
			$this->config['tinyMCE'] 		= array( 'disable' => 'true' );
			$this->config['drag-level'] 	= 1;
			$this->config['disabling_allowed'] = true;
			$this->config['id_name']		= 'id';
			$this->config['id_show']		= 'yes';
		}
			
		function extra_assets()
		{
			//load css
			wp_enqueue_style( 'avia-module-slideshow', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/slideshow/slideshow.css', array('avia-layout'), false );
			wp_enqueue_style( 'avia-module-slideshow-fullsize', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/slideshow_fullsize/slideshow_fullsize.css', array( 'avia-module-slideshow' ), false );
			wp_enqueue_style( 'avia-module-slideshow-fullscreen', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/slideshow_fullscreen/slideshow_fullscreen.css', array( 'avia-module-slideshow' ), false );

				//load js
			wp_enqueue_script( 'avia-module-slideshow', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/slideshow/slideshow.js', array( 'avia-shortcodes' ), false, true );
			wp_enqueue_script( 'avia-module-slideshow-fullscreen', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/slideshow_fullscreen/slideshow_fullscreen.js', array( 'avia-module-slideshow' ), false, true );
			wp_enqueue_script( 'avia-module-slideshow-video', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/slideshow/slideshow-video.js', array( 'avia-shortcodes' ), false, true );

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
							'template_id'	=> $this->popup_key( 'styling_slideshow' )
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
								'template_id'	=> $this->popup_key( 'advanced_privacy' )
							),
				
						array(	
								'type'			=> 'template',
								'template_id'	=> $this->popup_key( 'advanced_animation_slider' )
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
			
			$this->register_modal_group_templates();
			
			/**
			 * Content Tab
			 * ===========
			 */
			
			$c = array(
						array(
							'type'	=> 'modal_group',
							'id'	=> 'content',
							'container_class'	=> 'avia-element-fullwidth avia-multi-img',
							'modal_title'	=> __( 'Edit Form Element', 'avia_framework' ),
							'add_label'		=> __( 'Add single image or video', 'avia_framework' ),
							'std'		=> array(),
							'creator'	=> array(
												'name'	=> __( 'Add Images', 'avia_framework' ),
												'desc'	=> __( 'Here you can add new Images to the slideshow.', 'avia_framework' ),
												'id'	=> 'id',
												'type'	=> 'multi_image',
												'title'		=> __( 'Add multiple Images', 'avia_framework' ),
												'button'	=> __( 'Insert Images', 'avia_framework' ),
												'std'	=> ''
											),
							'subelements'	=> $this->create_modal()
						),
				
						array(	
							'name' 	=> __( 'Use first slides caption as permanent caption', 'avia_framework' ),
							'desc' 	=> __( 'If checked the caption will be placed on top of the slider. Please be aware that all slideshow link settings and other captions will be ignored then', 'avia_framework' ) ,
							'id' 	=> 'perma_caption',
							'std' 	=> '',
							'type' 	=> 'checkbox'
						),
				
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_entries' ), $c );
			
			/**
			 * Styling Tab
			 * ===========
			 */
			
			$c = array(
						array(	
							'name' 	=> __( 'Slideshow Image Size', 'avia_framework' ),
							'desc' 	=> __( 'Choose image size for your slideshow.', 'avia_framework' ),
							'id' 	=> 'size',
							'type' 	=> 'select',
							'std' 	=> 'extra_large',
							'subtype'	=>  AviaHelper::get_registered_image_sizes( 1000, true )		
						),
					
						array(	
							'name' 	=> __( 'Slideshow control styling?', 'avia_framework' ),
							'desc' 	=> __( 'Here you can select if and how to display the slideshow controls', 'avia_framework' ),
							'id' 	=> 'control_layout',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype'	=> array(
												__( 'Default', 'avia_framework' )		=> 'av-control-default',
												__( 'Minimal White', 'avia_framework' )	=> 'av-control-minimal', 
												__( 'Minimal Black', 'avia_framework' )	=> 'av-control-minimal av-control-minimal-dark',
												__( 'Hidden', 'avia_framework' )		=> 'av-control-hidden'
											)
						),	
					
						array(	
							'name' 	=> __( 'Display a scroll down arrow', 'avia_framework' ),
							'desc' 	=> __( 'Check if you want to show a button at the bottom of the slider that takes the user to the next section by scrolling down', 'avia_framework' ),
							'id' 	=> 'scroll_down',
							'std' 	=> '',
							'type' 	=> 'checkbox'
						),
				
/*
						array(	
							'name' 	=> __('Slideshow custom height','avia_framework' ),
							'desc' 	=> __('Slideshow height is by default 100&percnt;. You can select a different size here. Will only work flawless with images, not videos','avia_framework' ),
							'id' 	=> 'slide_height',
							'type' 	=> 'select',
							'std' 	=> '100',
							'subtype'	=> array( '100%'=>'100', '75%'=>'75', '66%'=>'66', '50%'=>'50' )
						),
*/
				
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_slideshow' ), $c );
			
			
			/**
			 * Advanced Tab
			 * ============
			 */
			
			$c = array(
						array(	
							'name' 	=> __( 'Slideshow Image scrolling', 'avia_framework' ),
							'desc' 	=> __( 'Choose the behaviour of the slideshow image when scrolling up or down on the page', 'avia_framework' ),
							'id' 	=> 'image_attachment',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype'	=> array(
												__( 'Parallax','avia_framework' )	=> '', 
												__( 'Fixed','avia_framework' )		=> 'fixed',
												__( 'Scroll','avia_framework' )		=> 'scroll'
											),
						),
					
						array(	
							'name' 	=> __( 'Slideshow Transition', 'avia_framework' ),
							'desc' 	=> __( 'Choose the transition for your Slideshow.', 'avia_framework' ),
							'id' 	=> 'animation',
							'type' 	=> 'select',
							'std' 	=> 'slide',
							'subtype'	=> array(
												__( 'Slide sidewards', 'avia_framework' )	=> 'slide', 
												__( 'Slide up/down', 'avia_framework' )		=> 'slide_up', 
												__( 'Fade', 'avia_framework' )				=> 'fade'
											),
						),
				
						array(
							'name' 	=> __( 'Transition Speed', 'avia_framework' ),
							'desc' 	=> __( 'Selected speed in milliseconds for transition effect.', 'avia_framework' ),
							'id' 	=> 'transition_speed',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype'	=> AviaHtmlHelper::number_array( 100, 10000, 100, array( __( 'Use Default', 'avia_framework' ) => '' ) )		
						),
				
						array(	
							'name' 	=> __( 'Autorotation active?', 'avia_framework' ),
							'desc' 	=> __( 'Check if the slideshow should rotate by default', 'avia_framework' ),
							'id' 	=> 'autoplay',
							'type' 	=> 'select',
							'std' 	=> 'false',
							'subtype'	=> array(
												__( 'Yes', 'avia_framework' )	=> 'true',
												__( 'No', 'avia_framework' )	=> 'false'
											)
						),
						
						array(	
							'name' 	=> __( 'Stop Autorotation with the last slide', 'avia_framework' ),
							'desc' 	=> __( 'Check if you want to disable autorotation when this last slide is displayed', 'avia_framework' ) ,
							'id' 	=> 'autoplay_stopper',
							'required'	=> array( 'autoplay', 'equals', 'true' ),
							'std' 	=> '',
							'type' 	=> 'checkbox'
						),	
			
						array(	
							'name' 	=> __( 'Slideshow autorotation duration', 'avia_framework' ),
							'desc' 	=> __( 'Images will be shown the selected amount of seconds.', 'avia_framework' ),
							'id' 	=> 'interval',
							'required'	=> array( 'autoplay', 'equals', 'true' ),
							'type' 	=> 'select',
							'std' 	=> '5',
							'subtype'	=> array( '1'=>'1', '2'=>'2', '3'=>'3', '4'=>'4', '5'=>'5', '6'=>'6', '7'=>'7', '8'=>'8', '9'=>'9', '10'=>'10', '15'=>'15', '20'=>'20', '30'=>'30', '40'=>'40', '60'=>'60', '100'=>'100' ) 
						),
				
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Slider Animation', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'advanced_animation_slider' ), $template );
			
			
			$c = array(
						array(
							'name' 	=> __( 'Lazy Load videos', 'avia_framework' ),
							'desc' 	=> __( 'Option to only load the preview image of a video slide. The actual videos will only be fetched once the user clicks on the image (Waiting for user interaction speeds up the inital pageload)', 'avia_framework' ),
							'id' 	=> 'conditional_play',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype' => array(
											__( 'Always load videos', 'avia_framework' )		=> '',
											__( 'Wait for user interaction or for a slide with active autoplay to load the video', 'avia_framework' )	=> 'confirm_all'
										),
						),
				
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Privacy', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'advanced_privacy' ), $template );
			
		}
		
		/**
		 * Creates the modal popup for a single entry
		 * 
		 * @since 4.6.4
		 * @return array
		 */
		protected function create_modal()
		{
			$elements = array(
				
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
							'template_id'	=> 'toggle_container',
							'templates_include'	=> array(
													$this->popup_key( 'modal_content_slidecontent' ),
													$this->popup_key( 'modal_content_fallback' ),
													$this->popup_key( 'modal_content_caption' )
												),
							'nodescription' => true
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
													$this->popup_key( 'modal_styling_video' ),
													$this->popup_key( 'modal_styling_caption' ),
													$this->popup_key( 'modal_styling_fonts' ),
													$this->popup_key( 'modal_styling_colors' ),
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
							'type'			=> 'template',
							'template_id'	=> 'toggle_container',
							'templates_include'	=> array(
													$this->popup_key( 'modal_advanced_heading' ),
													$this->popup_key( 'modal_advanced_link' ),
													$this->popup_key( 'modal_advanced_overlay' )
												),
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
			
			return $elements;
		}
		
		/**
		 * Register all templates for the modal group popup
		 * 
		 * @since 4.6.4
		 */
		protected function register_modal_group_templates()
		{
			/**
			 * Content Tab
			 * ===========
			 */
			
			$c = array(
						array(	
							'name' 	=> __( 'Which type of slide is this?','avia_framework' ),
							'id' 	=> 'slide_type',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype' => array(   
												__( 'Image Slide', 'avia_framework' )	=> 'image',
												__( 'Video Slide', 'avia_framework' )	=> 'video',
											)
						),
									
						array(	
							'name'	=> __( 'Choose another Image', 'avia_framework' ),
							'desc'	=> __( 'Either upload a new, or choose an existing image from your media library', 'avia_framework' ),
							'id'	=> 'id',
							'fetch'	=> 'id',
							'type'	=> 'image',
							'required'	=> array( 'slide_type', 'is_empty_or', 'image' ),
							'title'	=> __( 'Change Image', 'avia_framework' ),
							'button'	=> __( 'Change Image', 'avia_framework' ),
							'std'	=> ''
						),
				
						array(	
							'name' 	=> __( 'Image Position', 'avia_framework' ),
							'id' 	=> 'position',
							'type' 	=> 'select',
							'std' 	=> 'center center',
							'required'	=> array( 'id', 'not','' ),
							'subtype'	=> array(   
												__( 'Top Left', 'avia_framework' )		=> 'top left',
												__( 'Top Center', 'avia_framework' )	=> 'top center',
												__( 'Top Right', 'avia_framework' )		=> 'top right', 
												__( 'Bottom Left', 'avia_framework' )	=> 'bottom left',
												__( 'Bottom Center', 'avia_framework' )	=> 'bottom center',
												__( 'Bottom Right', 'avia_framework' )	=> 'bottom right', 
												__( 'Center Left', 'avia_framework' )	=> 'center left',
												__( 'Center Center', 'avia_framework' )	=> 'center center',
												__( 'Center Right', 'avia_framework' )	=> 'center right'
											)
						),
				
						array(	
							'type'			=> 'template',
							'template_id'	=> 'video',
							'required'		=> array( 'slide_type', 'equals', 'video' ),
							'id'			=> 'video',
							'args'			=> array( 
													'sc'	=> $this
												)
						)
			
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Select Slide Content', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_content_slidecontent' ), $template );
			
			$c = array(
						array(
							'type'			=> 'template',
							'template_id'	=> 'slideshow_fallback_image'
						)
				
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Fallback images', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_content_fallback' ), $template );
			
			$c = array(
						array(	
							'name' 	=> __( 'Caption Title', 'avia_framework' ),
							'desc' 	=> __( 'Enter a caption title for the slide here', 'avia_framework' ) ,
							'id' 	=> 'title',
							'std' 	=> '',
							'type' 	=> 'input'
						),
				
						array(	
							'name' 	=> __('Caption Text', 'avia_framework' ),
							'desc' 	=> __('Enter some additional caption text', 'avia_framework' ) ,
							'id' 	=> 'content',
							'type' 	=> 'textarea',
							'std' 	=> '',
						),
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Caption', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_content_caption' ), $template );
			
			/**
			 * Styling Tab
			 * ===========
			 */
			
			$c = array(
						array(	
							'name' 	=> __( 'Video Display','avia_framework' ),
							'desc' 	=> __( 'You can either make sure that the whole video is visible and no cropping occurs or that the video is stretched to display full screen', 'avia_framework' ),
							'id' 	=> 'video_cover',
							'type' 	=> 'select',
							'std' 	=> '',
							'required'	=> array( 'slide_type', 'equals', 'video' ),
							'subtype'	=> array(   
												__( 'Display Video in default mode, black borders may occur but the whole video will be visible', 'avia_framework' )			=> '',
												__( 'Stretch Video so it covers the whole slideshow (Video must be 16:9 for this option to work properly)', 'avia_framework' )	=> 'av-element-cover',
											)
						),
				
						array(	
								'type'			=> 'template',
								'template_id'	=> 'slideshow_player',
								'required'		=> array( 'slide_type', 'equals', 'video' ),
								'content'		=> $c 
							),
						 
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Video Settings', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_styling_video' ), $template );
			
			$c = array(
						array(	
							'name' 	=> __( 'Caption Positioning', 'avia_framework' ),
							'id' 	=> 'caption_pos',
							'type' 	=> 'select',
							'std' 	=> 'caption_bottom',
							'subtype'	=> array(
												__( 'Right Framed', 'avia_framework' )		=> 'caption_right caption_right_framed caption_framed',
												__( 'Left Framed', 'avia_framework' )		=> 'caption_left caption_left_framed caption_framed', 
												__( 'Bottom Framed', 'avia_framework' )		=> 'caption_bottom caption_bottom_framed caption_framed',
												__( 'Center Framed', 'avia_framework' )		=> 'caption_center caption_center_framed caption_framed',
												__( 'Right without Frame', 'avia_framework' )	=> 'caption_right',
												__( 'Left without Frame', 'avia_framework' )	=> 'caption_left',
												__( 'Bottom without Frame', 'avia_framework' )	=> 'caption_bottom',
												__( 'Center without Frame', 'avia_framework' )	=> 'caption_center'
											),
						),
						
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Caption', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_styling_caption' ), $template );
			
			$c = array(
						array(
							'name'			=> __( 'Caption Title Font Size', 'avia_framework' ),
							'desc'			=> __( 'Select a custom font size for the titles.', 'avia_framework' ),
							'type'			=> 'template',
							'template_id'	=> 'font_sizes_icon_switcher',
							'subtype'		=> array(
												'default'	=> AviaHtmlHelper::number_array( 10, 120, 1, array( __( 'Use Default', 'avia_framework' ) => '' ), 'px' ),
												'medium'	=> AviaHtmlHelper::number_array( 10, 120, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
												'small'		=> AviaHtmlHelper::number_array( 10, 120, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
												'mini'		=> AviaHtmlHelper::number_array( 10, 120, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' )
											),
							'id_sizes'		=> array(
												'default'	=> 'custom_title_size',
												'medium'	=> 'av-medium-font-size-title',
												'small'		=> 'av-small-font-size-title',
												'mini'		=> 'av-mini-font-size-title'
											)
						),
				
						array(
							'name'			=> __( 'Caption Content Font Size', 'avia_framework' ),
							'desc'			=> __( 'Select a custom font size for the titles.', 'avia_framework' ),
							'type'			=> 'template',
							'template_id'	=> 'font_sizes_icon_switcher',
							'subtype'		=> array(
												'default'	=> AviaHtmlHelper::number_array( 10, 90, 1, array( __( 'Use Default', 'avia_framework' ) => '' ), 'px' ),
												'medium'	=> AviaHtmlHelper::number_array( 10, 90, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
												'small'		=> AviaHtmlHelper::number_array( 10, 90, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
												'mini'		=> AviaHtmlHelper::number_array( 10, 90, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' )
											),
							'id_sizes'		=> array(
												'default'	=> 'custom_content_size',
												'medium'	=> 'av-medium-font-size',
												'small'		=> 'av-small-font-size',
												'mini'		=> 'av-mini-font-size'
											)
						),
				
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Font Sizes', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_styling_fonts' ), $template );
			
			
			$c = array(
						array(
							'name' 	=> __( 'Font Colors', 'avia_framework' ),
							'desc' 	=> __( 'Either use the themes default colors or apply some custom ones', 'avia_framework' ),
							'id' 	=> 'font_color',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype'	=> array( 
												__( 'Default', 'avia_framework' )	=> '',
												__( 'Define Custom Colors', 'avia_framework' )	=> 'custom'
											),
						),	
								
						array(	
							'name' 	=> __( 'Custom Caption Title Font Color', 'avia_framework' ),
							'desc' 	=> __( 'Select a custom font color. Leave empty to use the default', 'avia_framework' ),
							'id' 	=> 'custom_title',
							'type' 	=> 'colorpicker',
							'std' 	=> '',
							'container_class' => 'av_half av_half_first',
							'required'	=> array( 'font_color', 'equals', 'custom' )
						),	
										
						array(	
							'name' 	=> __( 'Custom Caption Content Font Color', 'avia_framework' ),
							'desc' 	=> __( 'Select a custom font color. Leave empty to use the default', 'avia_framework' ),
							'id' 	=> 'custom_content',
							'type' 	=> 'colorpicker',
							'std' 	=> '',
							'container_class' => 'av_half',
							'required' => array( 'font_color', 'equals', 'custom' )
						),	
				
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Colors', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_styling_colors' ), $template );
			
			/**
			 * Advanced Tab
			 * ===========
			 */
			
			$c = array(
						array(	
							'type'				=> 'template',
							'template_id'		=> 'heading_tag',
							'theme_default'		=> 'h2',
							'context'			=> __CLASS__
						),
				
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Heading Tag', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_advanced_heading' ), $template );
			
			$c = array(
				
						array(	
							'type'				=> 'template',
							'template_id'		=> 'slideshow_button_links'
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
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_advanced_link' ), $template );
			
			$c = array(
						array(	
								'type'			=> 'template',
								'template_id'	=> 'slideshow_overlay'
							),
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Overlay', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_advanced_overlay' ), $template );
			
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
			return $params;
		}
			
		/**
		 * Editor Sub Element - this function defines the visual appearance of an element that is displayed within a modal window and on click opens its own modal window
		 * Works in the same way as Editor Element
		 * @param array $params this array holds the default values for $content and $args. 
		 * @return $params the return array usually holds an innerHtml key that holds item specific markup.
		 */
		function editor_sub_element( $params )
		{	
			
			$img_template 		= $this->update_template( 'img_fakeArg', '{{img_fakeArg}}' );
			$template 			= $this->update_template( 'title', '{{title}}' );
			$content 			= $this->update_template( 'content', '{{content}}' );
			$video 				= $this->update_template( 'video', '{{video}}' );
			$thumbnail = isset( $params['args']['id'] ) ? wp_get_attachment_image( $params['args']['id'] ) : '';


			$params['innerHtml']  = '';
			$params['innerHtml'] .= "<div class='avia_title_container'>";
			$params['innerHtml'] .=		"<div " . $this->class_by_arguments('slide_type' ,$params['args'] ) . ">";
			$params['innerHtml'] .=			"<span class='avia_slideshow_image' {$img_template} >{$thumbnail}</span>";
			$params['innerHtml'] .=			"<div class='avia_slideshow_content'>";
			$params['innerHtml'] .=				"<h4 class='avia_title_container_inner' {$template} >{$params['args']['title']}</h4>";
			$params['innerHtml'] .=				"<p class='avia_content_container' {$content}>" . stripslashes($params['content']) . '</p>';
			$params['innerHtml'] .=				"<small class='avia_video_url' {$video}>".stripslashes($params['args']['video'])."</small>";
			$params['innerHtml'] .=			'</div>';
			$params['innerHtml'] .=		'</div>';
			$params['innerHtml'] .= '</div>';
				
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

			$atts = shortcode_atts( array(
						'size'				=> 'featured',
						'animation'			=> 'slide',
						'transition_speed'	=> '',
						'ids'				=> '',
						'autoplay'			=> 'false',
						'interval'			=> 5,
						'handle'			=> $shortcodename,
						'stretch'			=> '',
						'bg_slider'			=> 'true',
						'slide_height'		=> '100',
						'scroll_down'		=> '',
						'control_layout'	=> '',
						'perma_caption'		=> '',
						'autoplay_stopper'	=>'',
						'image_attachment'	=>'',
						'content'			=> ShortcodeHelper::shortcode2array( $content, 1 ),
						'lazy_loading'		=> 'disabled'

					), $atts, $this->config['shortcode'] );

			extract( $atts );

				
			$output = '';
			$class = '';

			$skipSecond = false;
			avia_sc_slider_fullscreen::$slide_count++;

			$params['class'] = "avia-fullscreen-slider main_color {$av_display_classes} {$meta['el_class']} {$class}";
			$params['open_structure'] = false;

			//we dont need a closing structure if the element is the first one or if a previous fullwidth element was displayed before
			if( $meta['index'] == 0 ) 
			{
				$params['close'] = false;
			}
			
			if( ! empty( $meta['siblings']['prev']['tag'] ) && in_array( $meta['siblings']['prev']['tag'], AviaBuilder::$full_el_no_section ) ) 
			{
				$params['close'] = false;
			}

			if( $meta['index'] > 0 ) 
			{
				$params['class'] .= ' slider-not-first';
			}

			$atts['css_id']  = 'fullscreen_slider_' . avia_sc_slider_fullscreen::$slide_count;
			$params['id'] = AviaHelper::save_string( $meta['custom_id_val'], '-', $atts['css_id'] );

			$output .= avia_new_section( $params );

			$slider = new avia_slideshow( $atts );
			$slider->set_extra_class( $stretch );

			$output .= $slider->html();

			$output .= '</div>'; //close section


			//if the next tag is a section dont create a new section from this shortcode
			if( ! empty( $meta['siblings']['next']['tag'] ) && in_array( $meta['siblings']['next']['tag'],  AviaBuilder::$full_el ) )
			{
				$skipSecond = true;
			}

			//if there is no next element dont create a new section.
			if( empty( $meta['siblings']['next']['tag'] ) )
			{
				$skipSecond = true;
			}
				
			if( empty( $skipSecond ) ) 
			{
				$output .= avia_new_section( array( 'close' => false, 'id' => 'after_full_slider_' . avia_sc_slider_fullscreen::$slide_count ) );
			}

			return $output;

		}
			
	}
}



