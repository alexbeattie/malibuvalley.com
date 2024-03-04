<?php
/**
 * Icon List Shortcode
 * 
 * Creates a list with nice icons beside
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

if ( ! class_exists( 'avia_sc_iconlist' ) )
{
	class avia_sc_iconlist extends aviaShortcodeTemplate
	{
			
		/**
		 *
		 * @since 4.5.5
		 * @var array 
		 */
		protected $screen_options;
		
		/**
		 * @since 4.5.5
		 * @var string 
		 */
		protected $icon_html_styling;
		
		/**
		 * @since 4.5.5
		 * @var string 
		 */
		protected $title_styling;
		
		/**
		 * @since 4.5.5
		 * @var string 
		 */
		protected $content_styling;
		
		/**
		 * @since 4.5.5
		 * @var string 
		 */
		protected $content_class;
		
		/**
		 * @since 4.5.5
		 * @var string 
		 */
		protected $title_class;
		
		/**
		 * @since 4.5.5
		 * @var string 
		 */
		protected $iconlist_styling;
		
		/**
		 * 
		 * @since 4.5.5
		 * @param AviaBuilder $builder
		 */
		public function __construct( $builder ) 
		{
			$this->screen_options = array();
			$this->icon_html_styling = '';
			$this->title_styling = '';
			$this->content_styling = '';
			$this->content_class = '';
			$this->title_class = '';
			$this->iconlist_styling = '';
				
			parent::__construct( $builder );
		}
		
		/**
		 * @since 4.5.5
		 */
		public function __destruct() 
		{
			parent::__destruct();
			
			unset( $this->screen_options );
		}
		
		/**
		 * Create the config array for the shortcode button
		 */
		function shortcode_insert_button()
		{
			$this->config['version']		= '1.0';
			$this->config['self_closing']	= 'no';

			$this->config['name']		= __( 'Icon List', 'avia_framework' );
			$this->config['tab']		= __( 'Content Elements', 'avia_framework' );
			$this->config['icon']		= AviaBuilder::$path['imagesURL'] . 'sc-iconlist.png';
			$this->config['order']		= 90;
			$this->config['target']		= 'avia-target-insert';
			$this->config['shortcode'] 	= 'av_iconlist';
			$this->config['shortcode_nested'] = array( 'av_iconlist_item' );
			$this->config['tooltip'] 	= __( 'Creates a list with nice icons beside', 'avia_framework' );
			$this->config['preview'] 	= true;
			$this->config['disabling_allowed'] = true;
			$this->config['id_name'] = 'id';
			$this->config['id_show'] = 'yes';
			$this->config['alb_desc_id']	= 'alb_description';
		}
			
		function extra_assets()
		{
			wp_enqueue_style( 'avia-module-icon', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/icon/icon.css', array( 'avia-layout' ), false );
			wp_enqueue_style( 'avia-module-iconlist', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/iconlist/iconlist.css', array( 'avia-layout' ), false );

			wp_enqueue_script( 'avia-module-iconlist', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/iconlist/iconlist.js', array( 'avia-shortcodes' ), false, true );

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
							'template_id'	=> $this->popup_key( 'content_iconfont' )
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
													$this->popup_key( 'styling_general' ),
													$this->popup_key( 'styling_font_sizes' ),
													$this->popup_key( 'styling_font_colors' ),
													$this->popup_key( 'styling_icon_font_colors' )
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
								'template_id'	=> $this->popup_key( 'advanced_animation' )
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
							'name'	=> __( 'Add/Edit List items', 'avia_framework' ),
							'desc'	=> __( 'Here you can add, remove and edit the items of your item list.', 'avia_framework' ),
							'type'	=> 'modal_group',
							'id'	=> 'content',
							'modal_title'	=> __( 'Edit List Item', 'avia_framework' ),
							'std'	=> array(
											array(
												'title'		=> __( 'List Title 1', 'avia_framework' ), 
												'icon'		=> '43', 
												'content'	=> __( 'Enter content here', 'avia_framework' )
											),
											array(
												'title'		=> __( 'List Title 2', 'avia_framework' ), 
												'icon'		=> '25', 
												'content'	=> __( 'Enter content here', 'avia_framework' )
											),
											array(
												'title'		=> __( 'List Title 3', 'avia_framework' ), 
												'icon'		=> '64', 
												'content'	=> __( 'Enter content here', 'avia_framework' )
											),
										),

							'subelements'	=> $this->create_modal()
						)
				);
			
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_iconfont' ), $c );
			
			
			/**
			 * Styling Tab
			 * ============
			 */
			
			
			$c = array(
						array(
							'name' 	=> __( 'Icon Position', 'avia_framework' ),
							'desc' 	=> __( 'Set the position of the icons', 'avia_framework' ),
							'id' 	=> 'position',
							'type' 	=> 'select',
							'std' 	=> 'left',
							'subtype'	=> array(	
											__( 'Left', 'avia_framework' )	=> 'left',
											__( 'Right', 'avia_framework' )	=> 'right',
										)
							),
				
						array(
							'name' 	=> __( 'List Styling', 'avia_framework' ),
							'desc' 	=> __( 'Change the styling of your iconlist', 'avia_framework' ),
							'id' 	=> 'iconlist_styling',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype'	=> array(	
											__( 'Default (Big List)', 'avia_framework' )	=> '',
											__( 'Minimal small list', 'avia_framework' )	=> 'av-iconlist-small',
										)
						),
				
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'General Styling', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_general' ), $template );
			
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
							'name' 	=> __( 'Custom Title Font Color', 'avia_framework' ),
							'desc' 	=> __( 'Select a custom font color. Leave empty to use the default', 'avia_framework' ),
							'id' 	=> 'custom_title',
							'type' 	=> 'colorpicker',
							'std' 	=> '',
							'container_class' => 'av_half av_half_first',
							'required'	=> array( 'font_color', 'equals', 'custom' )
						),	
						
						array(	
							'name' 	=> __( 'Custom Content Font Color', 'avia_framework' ),
							'desc' 	=> __( 'Select a custom font color. Leave empty to use the default', 'avia_framework' ),
							'id' 	=> 'custom_content',
							'type' 	=> 'colorpicker',
							'std' 	=> '',
							'container_class' => 'av_half',
							'required'	=> array( 'font_color', 'equals','custom'	)
						)
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Font Colors', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_font_colors' ), $template );
			
			$c = array(
						array(
							'name' 	=> __( 'Icon Colors', 'avia_framework' ),
							'desc' 	=> __( 'Either use the themes default colors or apply some custom ones', 'avia_framework' ),
							'id' 	=> 'color',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype'	=> array( 
												__( 'Default', 'avia_framework' )	=> '',
												__( 'Define Custom Colors', 'avia_framework' )	=> 'custom'
											),
												
						),
					
						array(	
							'name' 	=> __( 'Custom Background Color', 'avia_framework' ),
							'desc' 	=> __( 'Select a custom background color. Leave empty to use the default', 'avia_framework' ),
							'id' 	=> 'custom_bg',
							'type' 	=> 'colorpicker',
							'std' 	=> '',
							'container_class' => 'av_third av_third_first',
							'required'	=> array( 'color', 'equals', 'custom' )
						),	
						
						array(	
							'name' 	=> __( 'Custom Icon Font Color', 'avia_framework' ),
							'desc' 	=> __( 'Select a custom icon font color. Leave empty to use the default', 'avia_framework' ),
							'id' 	=> 'custom_font',
							'type' 	=> 'colorpicker',
							'std' 	=> '',
							'container_class' => 'av_third',
							'required' => array( 'color', 'equals', 'custom' )
						),	
					
						array(	
							'name' 	=> __( 'Custom Border Color', 'avia_framework' ),
							'desc' 	=> __( 'Select a custom border color. Leave empty to use the default', 'avia_framework' ),
							'id' 	=> 'custom_border',
							'type' 	=> 'colorpicker',
							'std' 	=> '',
							'container_class' => 'av_third',
							'required' => array( 'color', 'equals', 'custom' )
						)
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Icon Font Colors', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_icon_font_colors' ), $template );
			
			$c = array(
						array(
							'name'			=> __( 'Title Font Sizes', 'avia_framework' ),
							'desc'			=> __( 'Select a custom font size for the titles.', 'avia_framework' ),
							'type'			=> 'template',
							'template_id'	=> 'font_sizes_icon_switcher',
							'subtype'		=> array(
												'default'	=> AviaHtmlHelper::number_array( 10, 50, 1, array( __( 'Use Default', 'avia_framework' ) => '' ), 'px' ),
												'medium'	=> AviaHtmlHelper::number_array( 10, 50, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
												'small'		=> AviaHtmlHelper::number_array( 10, 50, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
												'mini'		=> AviaHtmlHelper::number_array( 10, 50, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' )
											),
							'id_sizes'		=> array(
												'default'	=> 'custom_title_size',
												'medium'	=> 'av-medium-font-size-title',
												'small'		=> 'av-small-font-size-title',
												'mini'		=> 'av-mini-font-size-title'
											)
						),
				
						array(
							'name'			=> __( 'Content Font Sizes', 'avia_framework' ),
							'desc'			=> __( 'Select a custom font size for the content.', 'avia_framework' ),
							'type'			=> 'template',
							'template_id'	=> 'font_sizes_icon_switcher',
							'subtype'		=> array(
												'default'	=> AviaHtmlHelper::number_array( 10, 50, 1, array( __( 'Use Default', 'avia_framework' ) => '' ), 'px' ),
												'medium'	=> AviaHtmlHelper::number_array( 10, 50, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
												'small'		=> AviaHtmlHelper::number_array( 10, 50, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
												'mini'		=> AviaHtmlHelper::number_array( 10, 50, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' )
											),
							'id_sizes'		=> array(
												'default'	=> 'custom_content_size',
												'medium'	=> 'av-medium-font-size',
												'small'		=> 'av-small-font-size',
												'mini'		=> 'av-mini-font-size'
											)
						)
				
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Font Sizes', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_font_sizes' ), $template );
				
			/**
			 * Advanced Tab
			 * ============
			 */
			
			$c = array(
						 array(
							'name' 	=> __( 'Animation', 'avia_framework' ),
							'desc' 	=> __( 'Should the items appear in an animated way?', 'avia_framework' ),
							'id' 	=> 'animation',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype' => array(
											__( 'Animation activated', 'avia_framework' )	=> '',
											__( 'Animation deactivated', 'avia_framework' )	=> 'deactivated',
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
							'template_id'	=> $this->popup_key( 'modal_content_content' )
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
													$this->popup_key( 'modal_advanced_link' )
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
								'name' 	=> __( 'List Item Title', 'avia_framework' ),
								'desc' 	=> __( 'Enter the list item title here (Better keep it short)', 'avia_framework' ) ,
								'id' 	=> 'title',
								'std' 	=> 'List Title',
								'type' 	=> 'input'
						),
				
						array(
							'name' 	=> __( 'List Item Content', 'avia_framework' ),
							'desc' 	=> __( 'Enter some content here', 'avia_framework' ) ,
							'id' 	=> 'content',
							'type' 	=> 'tiny_mce',
							'std' 	=> __( 'List Content goes here', 'avia_framework' ),
						),
				
						array(
							'name' 	=> __( 'List Item Icon', 'avia_framework' ),
							'desc' 	=> __( 'Select an icon for your list item below', 'avia_framework' ),
							'id' 	=> 'icon',
							'type' 	=> 'iconfont',
							'std' 	=> '',
						)
				
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_content_content' ), $c );
			
			
			$c = array(
						array(	
							'type'			=> 'template',
							'template_id'	=> 'linkpicker_toggle',
							'name'			=> __( 'Title Link?', 'avia_framework' ),
							'desc'			=> __( 'Do you want to apply a link to the title?', 'avia_framework' ),
							'subtypes'		=> array( 'no', 'manually', 'single', 'taxonomy' ),
							'no_toggle'		=> true
						),
				
						array(
							'name' 	=> __( 'Apply link to icon', 'avia_framework' ),
							'desc' 	=> __( 'Do you want to apply the link to the icon?', 'avia_framework' ),
							'id' 	=> 'linkelement',
							'required' 	=> array( 'link', 'not', '' ),
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype' => array(
											__( 'No, apply link to the title', 'avia_framework' )		=> '',
											__( 'Yes, apply link to icon and title', 'avia_framework' )	=> 'both',
											__( 'Yes, apply link to icon only', 'avia_framework' )		=> 'only_icon'
										)
						),
						
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Link Behaviour', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_advanced_link' ), $template );
			
			$c = array(
						array(	
							'type'				=> 'template',
							'template_id'		=> 'heading_tag',
							'theme_default'		=> 'h4',
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
		}

		/**
		 * Editor Sub Element - this function defines the visual appearance of an element that is displayed within a modal window and on click opens its own modal window
		 * Works in the same way as Editor Element
		 * @param array $params this array holds the default values for $content and $args.
		 * @return $params the return array usually holds an innerHtml key that holds item specific markup.
		 */
		function editor_sub_element( $params )
		{
			$template = $this->update_template( 'title', __( 'Element', 'avia_framework' ). ': {{title}}' );

			extract( av_backend_icon( $params ) ); // creates $font and $display_char if the icon was passed as param 'icon' and the font as 'font' 

			$params['innerHtml']  = '';
			$params['innerHtml'] .=		"<div class='avia_title_container'>";
			$params['innerHtml'] .=			'<span ' . $this->class_by_arguments( 'font', $font ) . '>';
			$params['innerHtml'] .=				"<span data-update_with='icon_fakeArg' class='avia_tab_icon'>{$display_char}</span>";
			$params['innerHtml'] .=			'</span>';
			$params['innerHtml'] .=			"<span {$template} >" . __( 'Element', 'avia_framework' ) . ": {$params['args']['title']}</span>";
			$params['innerHtml'] .=		'</div>';
			
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
			$this->screen_options = AviaHelper::av_mobile_sizes( $atts );

			$this->icon_html_styling = '';
			$this->title_styling = '';
			$this->content_styling = '';
			$this->content_class = '';
			$this->title_class = '';
			$this->iconlist_styling = '';

			extract( $this->screen_options ); //return $av_font_classes, $av_title_font_classes and $av_display_classes

			extract( shortcode_atts( array(
						'position'				=> 'left',
						'color'					=> '', 
						'custom_bg'				=> '', 
						'custom_border'			=> '', 
						'custom_font'			=> '',
						'font_color'			=> '',
						'custom_title'			=> '',
						'custom_content'		=> '',
						'custom_title_size'		=> '',
						'custom_content_size'	=> '',
						'iconlist_styling'		=> '',
						'animation'				=> ''

				), $atts, $this->config['shortcode'] ) );
				
				
			$this->iconlist_styling = $iconlist_styling == 'av-iconlist-small' ? 'av-iconlist-small' : 'av-iconlist-big';
				
			if( $color == 'custom' )
			{
				$this->icon_html_styling .= !empty($custom_bg) ? "background-color:{$custom_bg}; " : '';
				$this->icon_html_styling .= !empty($custom_border) ? "border:1px solid {$custom_border}; " : '';
				$this->icon_html_styling .= !empty($custom_font) ? "color:{$custom_font}; " : '';
			}
				
			if( $font_color == 'custom' )
			{
				$this->title_styling 		.= ! empty( $custom_title ) ? "color:{$custom_title}; " : '';
				$this->content_styling 		.= ! empty( $custom_content ) ? "color:{$custom_content}; " : '';

				if($this->content_styling) 	
				{
					$this->content_class = 'av_inherit_color';
				}

				if($this->title_styling)
				{
					$this->title_class = 'av_inherit_color';
				}

			}
				
			if( $custom_title_size )
			{
				$this->title_styling .= "font-size:{$custom_title_size}px; ";
				if($this->iconlist_styling  == 'av-iconlist-small')
				{
					$this->icon_html_styling .= "font-size:{$custom_title_size}px; ";
				}
			}
				
			if( $custom_content_size )
			{
				$this->content_styling .= "font-size:{$custom_content_size}px; ";
			}
				
				
			if( $this->icon_html_styling ) 
			{
				$this->icon_html_styling = " style='{$this->icon_html_styling}'";
			}
			
			if( $this->title_styling )  
			{	
				$this->title_styling = " style='{$this->title_styling}'"; 
			}
			
			if( $this->content_styling )
			{ 
				$this->content_styling = " style='{$this->content_styling}'";
			}


			// animation
			$animation_class = '';
			if( $animation == '' )
			{
				$animation_class = 'avia-iconlist-animate';
			}
					
					
			$output	 =	'';
			$output .=	"<div {$meta['custom_el_id']} class='avia-icon-list-container {$av_display_classes} {$meta['el_class']}'>";
			$output .=		"<ul class='avia-icon-list avia-icon-list-{$position} {$this->iconlist_styling} avia_animate_when_almost_visible {$animation_class}'>";
			$output .=			ShortcodeHelper::avia_remove_autop( $content, true );
			$output .=		'</ul>';
			$output .=	'</div>';


			return $output;
		}

		/**
		 * Shortcode Handler
		 * 
		 * @param array $atts
		 * @param string $content
		 * @param string $shortcodename
		 * @return string
		 */
		public function av_iconlist_item( $atts, $content = '', $shortcodename = '' )
		{
			/**
			 * Fixes a problem when 3-rd party plugins call nested shortcodes without executing main shortcode  (like YOAST in wpseo-filter-shortcodes)
			 */
			if( empty( $this->screen_options ) )
			{
				return '';
			}

			extract( $this->screen_options ); //return $av_font_classes, $av_title_font_classes and $av_display_classes

			$meta = aviaShortcodeTemplate::set_frontend_developer_heading_tag( $atts );

			$atts =  shortcode_atts( array(
							'title'			=> '', 
							'link'			=> '', 
							'icon'			=> '', 
							'font'			=> '', 
							'linkelement'	=> '', 
							'linktarget'	=> '', 
							'custom_markup' => '', 

						), $atts, 'av_iconlist_item' );

			$display_char = av_icon($atts['icon'], $atts['font']);
			$display_char_wrapper = array();

			$blank = AviaHelper::get_link_target( $atts['linktarget'] );
			if( ! empty( $atts['link'] ) )
			{
				$atts['link'] = AviaHelper::get_url($atts['link']);

				if( ! empty($atts['link'] ) )
				{
					$linktitle = $atts['title'];

					switch( $atts['linkelement'] )
					{
						case 'both':
							if( $atts['title'] ) 
							{
								$atts['title'] = "<a href='{$atts['link']}' title='" . esc_attr( $linktitle ) . "'{$blank}>{$linktitle}</a>";
							}
							
							$display_char_wrapper['start'] = "a href='{$atts['link']}' title='".esc_attr($linktitle)."' {$blank}";
							$display_char_wrapper['end'] = 'a';
							break;
						case 'only_icon':
							$display_char_wrapper['start'] = "a href='{$atts['link']}' title='".esc_attr($linktitle)."' {$blank}";
							$display_char_wrapper['end'] = 'a';
							break;
						default:
							if( $atts['title'] ) 
							{
								$atts['title'] = "<a href='{$atts['link']}' title='" . esc_attr( $linktitle) . "'{$blank}>{$linktitle}</a>";
							}
							
							$display_char_wrapper['start'] = 'div';
							$display_char_wrapper['end'] = 'div';
							break;
					}
				}
			}

			if( empty( $display_char_wrapper ) )
			{
				$display_char_wrapper['start'] = 'div';
				$display_char_wrapper['end'] = 'div';
			}

			$contentClass = '';
			if( trim( $content ) == '' )
			{
				$contentClass = 'av-iconlist-empty';
			}
              	
			$default_heading = ( $this->iconlist_styling == 'av-iconlist-small' ) ? 'div' : 'h4';
			$default_heading = ! empty( $meta['heading_tag'] ) ? $meta['heading_tag'] : $default_heading;
			
			$args = array(
						'heading'		=> $default_heading,
						'extra_class'	=> $meta['heading_class']
					);

			$extra_args = array( $this, $atts, $content, $shortcodename );

			/**
			 * @since 4.5.7.2
			 * @return array
			 */
			$args = apply_filters( 'avf_customize_heading_settings', $args, __CLASS__, $extra_args );

			$heading = ! empty( $args['heading'] ) ? $args['heading'] : $default_heading;
			$css = ! empty( $args['extra_class'] ) ? $args['extra_class'] : $meta['heading_class'];

			$title_el = $heading;
			$iconlist_title = ( $this->iconlist_styling == 'av-iconlist-small' ) ? '_small' : '';
			
			$output  = '';
			$output .= '<li>';
			$output .= 		"<{$display_char_wrapper['start']} {$this->icon_html_styling} class='iconlist_icon  avia-font-".$atts['font']."'><span class='iconlist-char ' {$display_char}></span></{$display_char_wrapper['end']}>";
			$output .=          '<article class="article-icon-entry '.$contentClass.'" '.avia_markup_helper(array('context' => 'entry','echo'=>false, 'custom_markup'=>$atts['custom_markup'])).'>';
			$output .=              "<div class='iconlist_content_wrap'>";
			$output .=                  '<header class="entry-content-header">';

			$markup = avia_markup_helper( array( 'context' => 'entry_title', 'echo' => false, 'custom_markup' => $atts['custom_markup'] ) );
			if( ! empty( $atts['title'] ) ) 
			{
				$output .=					"<{$title_el} class='av_iconlist_title iconlist_title{$iconlist_title} {$css} {$this->title_class} {$av_title_font_classes}' {$markup} {$this->title_styling}>".$atts['title']."</{$title_el}>";
			}
			$output .=                  '</header>';

			$markup = avia_markup_helper( array( 'context' => 'entry_content', 'echo' => false, 'custom_markup' => $atts['custom_markup'] ) );
			$output .=                  "<div class='iconlist_content {$this->content_class} {$av_font_classes}' {$markup} {$this->content_styling}>" . ShortcodeHelper::avia_apply_autop( ShortcodeHelper::avia_remove_autop( $content ) ) . '</div>';
			$output .=              '</div>';
			$output .=              '<footer class="entry-footer"></footer>';
			$output .=          '</article>';
			$output .=      "<div class='iconlist-timeline'></div>";
			$output .= '</li>';

			return $output;
		}

	}
}
