<?php
/**
 * Accordion and toggles
 * 
 * Creates toggles or accordions
 */
if( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if( ! class_exists( 'avia_sc_toggle' ) )
{
	class avia_sc_toggle extends aviaShortcodeTemplate
	{
		/**
		 *
		 * @var int 
		 */
		static protected $toggle_id = 1;

		/**
		 *
		 * @var int 
		 */
		static protected $counter = 1;

		/**
		 *
		 * @var int 
		 */
		static protected $initial = 0;

		/**
		 *
		 * @var array 
		 */
		static protected $tags = array();

		/**
		 *
		 * @var array 
		 */
		static protected $atts = array();

		/**
		 *
		 * @since 4.5.5
		 * @var array 
		 */
		protected $screen_options;
		
		
		/**
		 * 
		 * @since 4.5.5
		 * @param AviaBuilder $builder
		 */
		public function __construct( $builder ) 
		{
			$this->screen_options = array();
			
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

			$this->config['name']			= __( 'Accordion', 'avia_framework' );
			$this->config['tab']			= __( 'Content Elements', 'avia_framework' );
			$this->config['icon']			= AviaBuilder::$path['imagesURL'] . 'sc-accordion.png';
			$this->config['order']			= 70;
			$this->config['target']			= 'avia-target-insert';
			$this->config['shortcode']		= 'av_toggle_container';
			$this->config['shortcode_nested'] = array( 'av_toggle' );
			$this->config['tooltip']		= __( 'Creates toggles or accordions (can be used for FAQ)', 'avia_framework' );
			$this->config['disabling_allowed'] = true;
			$this->config['id_name']		= 'id';
			$this->config['id_show']		= 'yes';
			$this->config['alb_desc_id']	= 'alb_description';
		}

		function admin_assets()
		{
			$ver = AviaBuilder::VERSION;
			
			wp_register_script('avia_tab_toggle_js', AviaBuilder::$path['assetsURL'] . 'js/avia-tab-toggle.js', array( 'avia_modal_js' ), $ver, true );
			Avia_Builder()->add_registered_admin_script( 'avia_tab_toggle_js' );
		}
			
		function extra_assets()
		{
			//load css
			wp_enqueue_style( 'avia-module-toggles', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/toggles/toggles.css', array( 'avia-layout' ), false );

				//load js
			wp_enqueue_script( 'avia-module-toggles', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/toggles/toggles.js', array( 'avia-shortcodes' ), false, true );

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
							'template_id'	=> 'toggle_container',
							'templates_include'	=> array(
													$this->popup_key( 'content_togles' ),
													$this->popup_key( 'content_behaviour' )
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
													$this->popup_key( 'styling_toggles' ),
													$this->popup_key( 'styling_colors' )
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
							'name'	=> __( 'Add/Edit Toggles', 'avia_framework' ),
							'desc'	=> __( 'Here you can add, remove and edit the toggles you want to display.', 'avia_framework' ),
							'type'	=> 'modal_group',
							'id'	=> 'content',
							'modal_title'	=> __('Edit Form Element', 'avia_framework' ),
							'std'	=> array(
											array( 'title' => __( 'Toggle 1', 'avia_framework' ), 'tags' => '' ),
											array( 'title' => __( 'Toggle 2', 'avia_framework' ), 'tags' => '' ),
										),
							'subelements'	=> $this->create_modal()
						),
				
						array(
							'name' 	=> __( 'Use as FAQ Page (SEO improvement)', 'avia_framework' ),
							'desc' 	=> __( 'Select if content is used as FAQ and add schema.org markup to support Google Search. You must enable theme option &quot;Automated Schema.org HTML Markup&quot; (SEO tab). For valid structured HTML only one FAQ section allowed per page - you can activate &quot;Sorting&quot; and group questions if needed.', 'avia_framework' ),
							'id' 	=> 'faq_markup',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype'	=> array( 
												__( 'No markup needed', 'avia_framework' )	=> '', 
												__( 'Add FAQ markup', 'avia_framework' )	=> 'faq_markup'
											)
						)
				
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Toggles', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_togles' ), $template );
			
			
			$c = array(
						array(
							'name' 	=> __( 'Initial Open', 'avia_framework' ),
							'desc' 	=> __( 'Enter the Number of the Accordion Item that should be open initially. Set to Zero if all should be close on page load', 'avia_framework' ),
							'id' 	=> 'initial',
							'std' 	=> '0',
							'type' 	=> 'input'
						),

						array(
							'name' 	=> __( 'Behavior', 'avia_framework' ),
							'desc' 	=> __( 'Should only one toggle be active at a time and the others be hidden or can multiple toggles be open at the same time?', 'avia_framework' ),
							'id' 	=> 'mode',
							'type' 	=> 'select',
							'std' 	=> 'accordion',
							'subtype'	=> array( 
												__( 'Only one toggle open at a time (Accordion Mode)', 'avia_framework' )	=> 'accordion', 
												__( 'Multiple toggles open allowed (Toggle Mode)', 'avia_framework' )		=> 'toggle'
											)
						),

						array(
							'name' 	=> __( 'Sorting', 'avia_framework' ),
							'desc' 	=> __( 'Display the toggle sorting menu? (You also need to add a number of tags to each toggle to make sorting possible)', 'avia_framework' ),
							'id' 	=> 'sort',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype'	=> array( 
												__( 'No Sorting', 'avia_framework' )		=> '', 
												__( 'Sorting Active', 'avia_framework' )	=> 'true'
											)
						)
				
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Behaviour', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_behaviour' ), $template );
			
			/**
			 * Styling Tab
			 * ===========
			 */
			
			$c = array(
						array(
							'name' 	=> __( 'Styling', 'avia_framework' ),
							'desc' 	=> __( 'Select the styling of the toggles', 'avia_framework' ),
							'id' 	=> 'styling',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype'	=> array(
												__( 'Default', 'avia_framework' )	=> '',
												__( 'Minimal', 'avia_framework' )	=> 'av-minimal-toggle',
												__( 'Elegant', 'avia_framework' )	=> 'av-elegant-toggle'
											)
						),
				
					
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Toggles Styling', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_toggles' ), $template );
			
			$c = array(
						array(
							'name' 	=> __( 'Colors', 'avia_framework' ),
							'desc' 	=> __( 'Either use the themes default colors or apply some custom ones', 'avia_framework' ),
							'id' 	=> 'colors',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype'	=> array( 
												__( 'Default', 'avia_framework' )				=> '',
												__( 'Define Custom Colors', 'avia_framework' )	=> 'custom'
											),
						),
			
						array(	
							'name' 	=> __( 'Custom Font Color', 'avia_framework' ),
							'desc' 	=> __( 'Select a custom font color. Leave empty to use the default', 'avia_framework' ),
							'id' 	=> 'font_color',
							'type' 	=> 'colorpicker',
							'std' 	=> '',
							'rgba' 	=> true,
							'required'	=> array( 'colors', 'equals', 'custom' ),
							'container_class'	=> 'av_third av_third_first'
						),	
					
						array(	
							'name' 	=> __( 'Custom Background Color', 'avia_framework' ),
							'desc' 	=> __( 'Select a custom background color. Leave empty to use the default', 'avia_framework' ),
							'id' 	=> 'background_color',
							'type' 	=> 'colorpicker',
							'std' 	=> '',
							'rgba' 	=> true,
							'required'	=> array( 'colors', 'equals', 'custom' ),
							'container_class'	=> 'av_third',	
						),

						array(
							'name' 	=> __( 'Custom Border Color', 'avia_framework' ),
							'desc' 	=> __( 'Select a custom border color. Leave empty to use the default', 'avia_framework' ),
							'id' 	=> 'border_color',
							'type' 	=> 'colorpicker',
							'std' 	=> '',
							'rgba' 	=> true,
							'required'	=> array( 'colors', 'equals', 'custom' ),
							'container_class'	=> 'av_third',
						),
						
						array(
							'name' 	=> __( 'Current Toggle Appearance', 'avia_framework' ),
							'desc' 	=> __( 'Highlight title bar of open toggles', 'avia_framework' ),
							'id' 	=> 'colors_current',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype'	=> array(
												__( 'Default', 'avia_framework' )				=> '',
												__( 'Define Custom Colors', 'avia_framework' )	=> 'custom'
											),
						),

						array(
							'name' 	=> __( 'Custom Font Color Current Toggle', 'avia_framework' ),
							'desc' 	=> __( 'Select a custom font color for the current active toggle. Leave empty to use the default', 'avia_framework' ),
							'id' 	=> 'font_color_current',
							'type' 	=> 'colorpicker',
							'std' 	=> '',
							'rgba' 	=> true,
							'required'	=> array( 'colors_current', 'equals', 'custom' )
						),

						array(
							'name' 	=> __( 'Background Current Toggle', 'avia_framework' ),
							'desc' 	=> __( 'Select the type of background for the current active toggle title bar.', 'avia_framework' ),
							'id' 	=> 'background_current',
							'type' 	=> 'select',
							'std' 	=> '',
							'required'	=> array( 'colors_current', 'equals', 'custom' ),
							'subtype'	=> array(
												__( 'Default', 'avia_framework' )				=> '',
												__( 'Background Color', 'avia_framework' )		=> 'bg_color',
												__( 'Background Gradient', 'avia_framework' )	=> 'bg_gradient',
											)
						),
				
						array(
							'name' 	=> __( 'Title Bar Custom Background Color', 'avia_framework' ),
							'desc' 	=> __( 'Select a custom background color. Leave empty for default color', 'avia_framework' ),
							'id' 	=> 'background_color_current',
							'type' 	=> 'colorpicker',
							'rgba' 	=> true,
							'std' 	=> '',
							'required'	=> array( 'background_current', 'equals', 'bg_color' ),
						),

						array(
							'name' 	=> __( 'Background Gradient Color 1', 'avia_framework' ),
							'desc' 	=> __( 'Select the first color for the gradient.', 'avia_framework' ),
							'id' 	=> 'background_gradient_current_color1',
							'type' 	=> 'colorpicker',
							'rgba' 	=> true,
							'std' 	=> '',
							'container_class' => 'av_third av_third_first',
							'required'	=> array( 'background_current', 'equals', 'bg_gradient' ),
						),
				
						array(
							'name' 	=> __( 'Background Gradient Color 2', 'avia_framework' ),
							'desc' 	=> __( 'Select the second color for the gradient.', 'avia_framework' ),
							'id' 	=> 'background_gradient_current_color2',
							'type' 	=> 'colorpicker',
							'rgba' 	=> true,
							'std' 	=> '',
							'container_class' => 'av_third',
							'required'	=> array( 'background_current', 'equals', 'bg_gradient' )
						),

						array(
							'name' 	=> __( 'Background Gradient Direction', 'avia_framework' ),
							'desc' 	=> __( 'Define the gradient direction', 'avia_framework' ),
							'id' 	=> 'background_gradient_current_direction',
							'type' 	=> 'select',
							'std' 	=> 'vertical',
							'container_class' => 'av_third',
							'required'	=> array( 'background_current', 'equals', 'bg_gradient' ),
							'subtype'	=> array(
												__( 'Vertical', 'avia_framework' )		=> 'vertical',
												__( 'Horizontal', 'avia_framework' )	=> 'horizontal',
												__( 'Radial', 'avia_framework' )		=> 'radial',
												__( 'Diagonal Top Left to Bottom Right', 'avia_framework' )	=> 'diagonal_tb',
												__( 'Diagonal Bottom Left to Top Right', 'avia_framework' )	=> 'diagonal_bt',
											)
						),
				
						array(
							'name' 	=> __( 'Hover Toggle Appearance', 'avia_framework' ),
							'desc' 	=> __( 'Appearance of toggles on mouse hover', 'avia_framework' ),
							'id' 	=> 'hover_colors',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype'	=> array(
												__( 'Default', 'avia_framework' )				=> '',
												__( 'Define Custom Colors', 'avia_framework' )	=> 'custom'
											),
						),

						array(
							'name' 	=> __( 'Custom Hover Background Color', 'avia_framework' ),
							'desc' 	=> __( 'Select a custom hover background color. Leave empty to use the default', 'avia_framework' ),
							'id' 	=> 'hover_background_color',
							'type' 	=> 'colorpicker',
							'std' 	=> '',
							'rgba' 	=> true,
							'container_class' => 'av_third av_half_first',
							'required'	=> array( 'hover_colors', 'equals', 'custom')
						),

						array(
							'name' 	=> __( 'Custom Hover Font Color', 'avia_framework' ),
							'desc' 	=> __( 'Select a custom hover font color. Leave empty to use the default', 'avia_framework' ),
							'id' 	=> 'hover_font_color',
							'type' 	=> 'colorpicker',
							'std' 	=> '',
							'rgba' 	=> true,
							'container_class' => 'av_third',
							'required'	=> array( 'hover_colors', 'equals', 'custom' )
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
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_colors' ), $template );
			
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
							'template_id'	=> $this->popup_key( 'modal_content_toggle' )
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
													$this->popup_key( 'modal_advanced_developer' )
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
							'name' 	=> __( 'Toggle Title', 'avia_framework' ),
							'desc' 	=> __( 'Enter the toggle title here (Better keep it short)', 'avia_framework' ),
							'id' 	=> 'title',
							'std' 	=> 'Toggle Title',
							'type' 	=> 'input'
						),

						array(
							'name' 	=> __( 'Toggle Content', 'avia_framework' ),
							'desc' 	=> __( 'Enter some content here', 'avia_framework' ),
							'id' 	=> 'content',
							'type' 	=> 'tiny_mce',
							'std' 	=> __( 'Toggle Content goes here', 'avia_framework' ),
                        ),

						array(
							'name' 	=> __( 'Toggle Sorting Tags', 'avia_framework' ),
							'desc' 	=> __( 'Enter any number of comma separated tags here. If sorting is active the user can filter the visible toggles with the help of these tags', 'avia_framework' ),
							'id' 	=> 'tags',
							'std' 	=> '',
							'type' 	=> 'input'
						),
						
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_content_toggle' ), $c );
			
			$setting_id = Avia_Builder()->get_developer_settings( 'custom_id' );
			$class = in_array( $setting_id, array( 'deactivate', 'hide' ) ) ? 'avia-hidden' : '';

			$c = array(
						array(
							'name' 	=> __( 'For Developers: Custom Tab ID','avia_framework' ),
							'desc' 	=> __( 'Insert a custom ID for the element here. Make sure to only use allowed characters (latin characters, underscores, dashes and numbers, no special characters can be used)','avia_framework' ),
							'id' 	=> 'custom_id',
							'type' 	=> 'input',
							'std' 	=> '',
							'container_class'	=> $class,
						)
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Developer Settings', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_advanced_developer' ), $template );
			
		}
		

		/**
		 * Editor Sub Element - this function defines the visual appearance of an element that is displayed within a modal window and on click opens its own modal window
		 * Works in the same way as Editor Element
		 * @param array $params this array holds the default values for $content and $args.
		 * @return $params the return array usually holds an innerHtml key that holds item specific markup.
		 */
		function editor_sub_element( $params )
		{
			$template = $this->update_template( 'title', '{{title}}' );

			$params['innerHtml']  = '';
			$params['innerHtml'] .= "<div class='avia_title_container' {$template}>{$params['args']['title']}</div>";

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
			
			extract( $this->screen_options ); //return $av_font_classes, $av_title_font_classes and $av_display_classes 

			$atts = shortcode_atts( array(
							'initial'			=> '0',
							'mode'				=> 'accordion',
							'sort'				=> '',
							'faq_markup'		=> '',
							'styling'			=> '',
							'colors'			=> '',
							'border_color'		=> '',
							'font_color'		=> '',
							'background_color'	=> '',
							'colors_current'	=> '',
							'font_color_current'		=> '',
							'background_current'		=> '',
							'background_color_current'	=> '',
							'background_gradient_current_color1'	=> '',
							'background_gradient_current_color2'	=> '',
							'background_gradient_current_direction'	=> '',
							'hover_colors'				=> '',
							'hover_background_color'	=> '',
							'hover_font_color'			=> ''

						), $atts, $this->config['shortcode'] );
			
			
			extract( $atts );
			
			
			if( ! is_numeric( $initial ) || $initial < 0 )
			{
				$initial = 0;
			}
			else
			{
				$initial = (int) $initial;
				$nr_toggles = substr_count( $content, '[av_toggle ' );
				
				if( $initial > $nr_toggles )
				{
					$initial = $nr_toggles;
				}
			}
			

			$output = '';
			$addClass = '';
			if( $mode == 'accordion' ) 
			{
				$addClass = 'toggle_close_all ';
			}

			// custom title bar styling
			$current_colors = '';

			if( $atts['colors_current'] == 'custom' )
			{

				if( $atts['font_color_current'] !== '' ) 
				{
					$current_colors .= AviaHelper::style_string( $atts, 'font_color_current', 'color' );
					$current_colors .= AviaHelper::style_string( $atts, 'font_color_current', 'border-color' );
					$addClass .= ' hasCurrentStyle';
				}

				if( $atts['background_current'] == 'bg_color' ) 
				{
					$current_colors .= AviaHelper::style_string( $atts, 'background_color_current','background-color' );
				}
				else if( $atts['background_current'] == 'bg_gradient' )
				{
					$gradient_settings = array(
												$atts['background_gradient_current_direction'],
												$atts['background_gradient_current_color1'],
												$atts['background_gradient_current_color2']
											);
					
					$atts['gradient_string'] = AviaHelper::css_background_string( array(), $gradient_settings );
					$atts['gradient_fallback'] = $atts['background_gradient_current_color1'];
					$current_colors .= AviaHelper::style_string( $atts, 'gradient_fallback', 'background-color' );
					$current_colors .= AviaHelper::style_string( $atts, 'gradient_string', 'background' );
				}

			}

			$current_colors_attr = '';
			if( $current_colors ) 
			{
				$current_colors_attr = "data-currentstyle='{$current_colors}'";
			}

			$markup = '';
			if( ! empty( $atts['faq_markup'] ) )
			{
				$markup = avia_markup_helper( array( 'context' => 'faq_section', 'echo' => false ) );
			}

			$output  = '<div ' . $markup . ' ' . $meta['custom_el_id'] . ' class="togglecontainer ' . $av_display_classes . ' ' . $styling . ' ' . $addClass . $meta['el_class'] . '" ' . $current_colors_attr . '>';

			avia_sc_toggle::$counter = 1;
			avia_sc_toggle::$initial = $initial;
			avia_sc_toggle::$tags = array();
			avia_sc_toggle::$atts = $atts;

			$content  = ShortcodeHelper::avia_remove_autop( $content, true );
			$sortlist = ! empty( $sort ) ? $this->sort_list( $atts ) : '';

			$output .= $sortlist . $content . '</div>';

			return $output;
		}


		/**
		 * Shortcode handler
		 * 
		 * @param array $atts
		 * @param string $content
		 * @param string $shortcodename
		 * @return string
		 */
		public function av_toggle( $atts, $content = '', $shortcodename = '' )
		{
			/**
			 * Fixes a problem when 3-rd party plugins call nested shortcodes without executing main shortcode  (like YOAST in wpseo-filter-shortcodes)
			 */
			if( empty( $this->screen_options ) )
			{
				return '';
			}
	
			$toggle_atts = shortcode_atts( array(
									'title'			=> '', 
									'tags'			=> '', 
									'custom_id'		=> '', 
									'custom_markup'	=> ''
				
							), $atts, 'av_toggle' );
			
			$output = $titleClass = $contentClass = '';
			$toggle_init_open_style = '';
			
			if( is_numeric( avia_sc_toggle::$initial ) && avia_sc_toggle::$counter == avia_sc_toggle::$initial )
			{
				$titleClass = 'activeTitle';
				$contentClass = 'active_tc';
				$toggle_init_open_style = "style='display:block;'";
			}

			if( empty( $toggle_atts['title'] ) )
			{
				$toggle_atts['title'] = avia_sc_toggle::$counter;
			}

			$setting_id = Avia_Builder()->get_developer_settings( 'custom_id' );
			if( empty( $toggle_atts['custom_id'] ) || in_array( $setting_id, array( 'deactivate' ) ) )
			{
				$toggle_atts['custom_id'] = 'toggle-id-' . avia_sc_toggle::$toggle_id++;
			}
			else
			{
				$toggle_atts['custom_id'] = AviaHelper::save_string( $toggle_atts['custom_id'], '-' );
			}
            
			//custom colors
			$colors = $inherit = $icon_color = '';
			if( ! empty( avia_sc_toggle::$atts['colors'] ) && avia_sc_toggle::$atts['colors'] == 'custom' )
			{
				if( ! empty( avia_sc_toggle::$atts['background_color'] ) )
				{
					$colors = 'background-color: ' . avia_sc_toggle::$atts['background_color'] . '; ';
				}
	            
				if( ! empty(avia_sc_toggle::$atts['font_color'] ) )
				{
					$colors .= 'color: ' . avia_sc_toggle::$atts['font_color'] . '; ';
					$icon_color = "style='border-color:" . avia_sc_toggle::$atts['font_color'] . ";'";
					$inherit .= ' av-inherit-font-color ';
					$titleClass .= ' hasCustomColor';
				}
	            
				if( ! empty( avia_sc_toggle::$atts['border_color'] ) )
				{
					$colors .= 'border-color: ' . avia_sc_toggle::$atts['border_color'] . '; ';
					$inherit .= ' av-inherit-border-color ';
				}
			}

			if( ! empty( $colors ) )
			{
				$colors = "style='{$colors}'";
			}

			// hover styling
			$hover_styling = '';
			if( ! empty( avia_sc_toggle::$atts['hover_colors'] ) && avia_sc_toggle::$atts['hover_colors'] == 'custom' )
			{
				if( ! empty( avia_sc_toggle::$atts['hover_background_color'] ) )
				{
					$hover_styling .= 'background-color: ' . avia_sc_toggle::$atts['hover_background_color'] . '; ';
				}

				if( ! empty( avia_sc_toggle::$atts['hover_font_color'] ) ) 
				{
					$hover_styling .= 'color: ' . avia_sc_toggle::$atts['hover_font_color'] . '; ';
				}
			}

			$hover_styling_markup = ! empty( $hover_styling ) ? "data-hoverstyle='{$hover_styling}'" : '';

			$markup_answer = '';

			if( '' == avia_sc_toggle::$atts['faq_markup'] )
			{
				$markup_tab = avia_markup_helper( array( 'context' => 'entry', 'echo' => false, 'custom_markup' => $toggle_atts['custom_markup'] ) );
				$markup_title = avia_markup_helper( array( 'context' => 'entry_title', 'echo' => false, 'custom_markup' => $toggle_atts['custom_markup'] ) );
				$markup_text = avia_markup_helper( array( 'context' => 'entry_content', 'echo' => false, 'custom_markup' => $toggle_atts['custom_markup'] ) );
			}
			else
			{
				$markup_tab = avia_markup_helper( array( 'context' => 'faq_question_container', 'echo' => false, 'custom_markup' => $toggle_atts['custom_markup'] ) );
				$markup_title = avia_markup_helper( array( 'context' => 'faq_question_title', 'echo' => false, 'custom_markup' => $toggle_atts['custom_markup'] ) );
				$markup_answer = avia_markup_helper( array( 'context' => 'faq_question_answer', 'echo' => false, 'custom_markup' => $toggle_atts['custom_markup'] ) );
				$markup_text = avia_markup_helper( array( 'context' => 'entry_content', 'echo' => false, 'custom_markup' => $toggle_atts['custom_markup'] ) );
			}
			
			$output .= '<section class="av_toggle_section" ' . $markup_tab . ' >';
			$output .= '    <div role="tablist" class="single_toggle" ' . $this->create_tag_string( $toggle_atts['tags'], $toggle_atts ) . '  >';
			$output .= '        <p data-fake-id="#' . $toggle_atts['custom_id'] . '" class="toggler ' . $titleClass . $inherit . '" ' . $markup_title . ' ' . $colors . ' ' . $hover_styling_markup . ' role="tab" tabindex="0" aria-controls="' . $toggle_atts['custom_id'] . '">' . $toggle_atts['title'] . '<span class="toggle_icon" ' . $icon_color . '>';
			$output .= '        <span class="vert_icon"></span><span class="hor_icon"></span></span></p>';
			$output .= '        <div id="' . $toggle_atts['custom_id'] . '" class="toggle_wrap ' . $contentClass . '"  ' . $toggle_init_open_style . ' ' . $markup_answer . '>';
			$output .= '            <div class="toggle_content invers-color ' . $inherit . '" ' . $markup_text . ' ' . $colors . ' >';
			$output .=					ShortcodeHelper::avia_apply_autop( ShortcodeHelper::avia_remove_autop( $content ) );
			$output .= '            </div>';
			$output .= '        </div>';
			$output .= '    </div>';
			$output .= '</section>';

			avia_sc_toggle::$counter ++;

			return $output;
		}

        function create_tag_string( $tags, $toggle_atts )
        {
            $first_item_text = apply_filters( 'avf_toggle_sort_first_label', __( 'All', 'avia_framework' ), $toggle_atts );

            $tag_string = '{' . $first_item_text . '} ';
            if( trim( $tags ) != '' )
            {
                $tags = explode( ',', $tags );

                foreach( $tags as $tag )
                {
                    $tag = esc_html( trim( $tag ) );
                    if( ! empty( $tag ) )
                    {
                        $tag_string .= '{' . $tag . '} ';
                        avia_sc_toggle::$tags[ $tag ] = true;
                    }
                }
            }

            $tag_string = 'data-tags="' . $tag_string . '"';
            return $tag_string;
        }



        function sort_list( $toggle_atts )
        {
            $output = '';
            $first = 'activeFilter';
			
            if( ! empty( avia_sc_toggle::$tags ) )
            {
                ksort( avia_sc_toggle::$tags );
                $first_item_text = apply_filters( 'avf_toggle_sort_first_label', __( 'All', 'avia_framework' ), $toggle_atts );
                $start = array( $first_item_text => true );
                avia_sc_toggle::$tags = $start + avia_sc_toggle::$tags;
				
				$sep = apply_filters( 'avf_toggle_sort_seperator', '/', $toggle_atts );
				
                foreach( avia_sc_toggle::$tags as $key => $value )
                {
                    $output .= '<a href="#" data-tag="{' . $key . '}" class="' . $first . '">' . $key . '</a>';
                    $output .= "<span class='tag-seperator'>{$sep}</span>";
                    $first = '';
                }
            }

            if( ! empty( $output ) ) 
            { 
	            $output = "<div class='taglist'>{$output}</div>";
	        }
			
            return $output;
        }

    }
}
