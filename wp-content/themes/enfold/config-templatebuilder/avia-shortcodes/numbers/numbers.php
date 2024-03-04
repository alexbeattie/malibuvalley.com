<?php
/**
 * Animated Numbers
 * 
 * Display Numbers that count from 0 to the number you entered
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if ( ! class_exists( 'avia_sc_animated_numbers' ) ) 
{
	
	class avia_sc_animated_numbers extends aviaShortcodeTemplate
	{
		/**
		 * Create the config array for the shortcode button
		 */
		function shortcode_insert_button()
		{
			$this->config['version']		= '1.0';
			$this->config['self_closing']	= 'no';

			$this->config['name']			= __( 'Animated Numbers', 'avia_framework' );
			$this->config['tab']			= __( 'Content Elements', 'avia_framework' );
			$this->config['icon']			= AviaBuilder::$path['imagesURL'] . 'sc-numbers.png';
			$this->config['order']			= 15;
			$this->config['target']			= 'avia-target-insert';
			$this->config['shortcode']		= 'av_animated_numbers';
			$this->config['tooltip']		= __( 'Display an animated Number with subtitle', 'avia_framework' );
			$this->config['preview']		= true;
			$this->config['disabling_allowed'] = true;
			$this->config['id_name']		= 'id';
			$this->config['id_show']		= 'yes';
		}


		function extra_assets()
		{
			//load css
			wp_enqueue_style( 'avia-module-numbers', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/numbers/numbers.css', array( 'avia-layout' ), false );

			//load js
			wp_enqueue_script( 'avia-module-numbers', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/numbers/numbers.js', array( 'avia-shortcodes' ), false, true );
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
								'template_id'	=> $this->popup_key( 'content_number' )
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
													$this->popup_key( 'styling_appearance' ),
													$this->popup_key( 'styling_fonts' ),
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
								'template_id'	=> $this->popup_key( 'advanced_animation' )
							),
				
						array(	
								'type'			=> 'template',
								'template_id'	=> $this->popup_key( 'advanced_link' )
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
							'name' 	=> __( 'Number', 'avia_framework' ),
							'desc' 	=> __( 'Add a number here. It will be animated. You can also add non numerical characters. Valid examples: 24/7, 50.45, 99.9$, 90&percnt;, 35k, 200mm etc. Leading 0 will be kept, separated numbers will be animated individually.', 'avia_framework' ),
							'id' 	=> 'number',
							'type' 	=> 'input',
							'std' 	=> __( '100', 'avia_framework' )
						),
				
						array(	
							'name' 	=> __( 'Start animation value', 'avia_framework' ),
							'desc' 	=> __( 'Add a number here to start the animation. Leave blank to start from 0. Only use numerical characters for a valid integer number.', 'avia_framework' ),
							'id' 	=> 'start_from',
							'type' 	=> 'input',
							'std' 	=> ''
						),
				
						array(	
							'name' 	=> __( 'Description', 'avia_framework' ),
							'desc' 	=> __( 'Add some content to be displayed below the number', 'avia_framework' ),
							'id' 	=> 'content',
							'type' 	=> 'textarea',
							'std' 	=> __( 'Add your own text', 'avia_framework' )
						),
					
						array(	
							'name' 	=> __( 'Icon', 'avia_framework' ),
							'desc' 	=> __( 'Add an icon to the element?', 'avia_framework' ),
							'id' 	=> 'icon_select',
							'type' 	=> 'select',
							'std' 	=> 'no',
							'subtype'	=> array(
												__( 'No Icon', 'avia_framework' )	=> 'no',
												__( 'Yes, display an icon in front of number', 'avia_framework' )	=> 'av-icon-before',	
												__( 'Yes, display an icon after the number', 'avia_framework' )		=> 'av-icon-after'
											)
						),	
					
						array(	
							'name' 	=> __( 'Icon', 'avia_framework' ),
							'desc' 	=> __( 'Select an icon for the element here', 'avia_framework' ),
							'id' 	=> 'icon',
							'type' 	=> 'iconfont',
							'std' 	=> '',
							'required'	=> array( 'icon_select', 'not', 'no' )
						),

				
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_number' ), $c );
			
			/**
			 * Styling Tab
			 * ===========
			 */
			
			$c = array(
						array(	
							'name' 	=> __( 'Format Number', 'avia_framework' ),
							'desc' 	=> __( 'Select the thousands separator', 'avia_framework' ),
							'id' 	=> 'number_format',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype' => array(
												__( 'No thousands seperator',  'avia_framework' )	=> '',
												__( '123.350',  'avia_framework' )					=> '.',	
												__( '123,350',  'avia_framework' )					=> ',',
												__( '123 350',  'avia_framework' )					=> ' '
											)
						),	
				
						array(
							'name' 	=> __( 'Display Circle', 'avia_framework' ),
							'desc' 	=> __( 'Do you want to display a circle around the animated number?', 'avia_framework' ),
							'id' 	=> 'circle',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype'	=> array(
												__( 'No', 'avia_framework' )	=> '',
												__( 'Yes', 'avia_framework' )	=> 'yes'
											)
						),

						array(
							'name' => __( 'Display Circle', 'avia_framework' ),
							'desc' => __( 'The circle may overlap other elements, add spacing around the Animated Number element to prevent that.', 'avia_framework' ),
							'type' => 'heading',
							'required' 	=> array( 'circle', 'not', '' ),
						),
                    
						array(
							'name' 	=> __( 'Circle Appearance', 'avia_framework' ),
							'desc' 	=> __( 'Define the appearance of the circle here', 'avia_framework' ),
							'id' 	=> 'circle_custom',
							'type' 	=> 'select',
							'std' 	=> '',
							'required'	=> array( 'circle', 'not', '' ),
							'subtype'	=> array(
												__( 'Default', 'avia_framework' )	=> '',
												__( 'Custom', 'avia_framework' )	=> 'custom'
											)
						),

						array(
							'name' 	=> __( 'Circle Border Color', 'avia_framework' ),
							'desc' 	=> __( 'Select a custom border color. Leave empty to use the default', 'avia_framework' ),
							'id' 	=> 'circle_border_color',
							'type' 	=> 'colorpicker',
							'rgba'  => true,
							'container_class' => 'av_half av_half_first',
							'required'	=> array( 'circle_custom', 'not', '' ),
							'std' 	=> '',
						),

						array(
							'name' 	=> __( 'Circle Backgound Color', 'avia_framework' ),
							'desc' 	=> __( 'Select a custom background color. Leave empty to use the default', 'avia_framework' ),
							'id' 	=> 'circle_bg_color',
							'container_class' => 'av_half',
							'rgba'  => true,
							'type' 	=> 'colorpicker',
							'required'	=> array( 'circle_custom', 'not', '' ),
							'std' 	=> '',
						),

						array(
							'name' 	=> __( 'Border Width', 'avia_framework' ),
							'desc' 	=> __( 'Select a custom border width for the circle', 'avia_framework' ),
							'id' 	=> 'circle_border_width',
							'type' 	=> 'select',
							'std' 	=> '',
							'container_class' => 'av_half av_half_first',
							'required' 	=> array( 'circle_custom', 'not', '' ),
							'subtype'	=> AviaHtmlHelper::number_array( 1, 30, 1, array( __( 'Default Width', 'avia_framework' ) => '' ), 'px'  ),
						),

						array(
							'name' 	=> __( 'Circle Size', 'avia_framework' ),
							'desc' 	=> __( 'Define the size of the circle', 'avia_framework' ),
							'id' 	=> 'circle_size',
							'type' 	=> 'select',
							'std' 	=> '',
							'container_class' => 'av_half',
							'required'	=> array( 'circle_custom', 'not', '' ),
							'subtype'	=> AviaHtmlHelper::number_array( 50, 120, 10, array( __( 'Default Size', 'avia_framework' ) => '' ), '%' ),
                    ),

				
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Appearance', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_appearance' ), $template );
			
			$c = array(
						array(
							'name'			=> __( 'Number Font Sizes', 'avia_framework' ),
							'desc'			=> __( 'Select a custom font size for the numbers.', 'avia_framework' ),
							'type'			=> 'template',
							'template_id'	=> 'font_sizes_icon_switcher',
							'subtype'		=> array(
												'default'	=> AviaHtmlHelper::number_array( 16, 100, 1, array( __( 'Use Default', 'avia_framework' ) => '' ), 'px' ),
//												'medium'	=> AviaHtmlHelper::number_array( 10, 60, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
//												'small'		=> AviaHtmlHelper::number_array( 10, 60, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
//												'mini'		=> AviaHtmlHelper::number_array( 10, 60, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' )
											),
							'id_sizes'		=> array(
												'default'	=> 'font_size',
//												'medium'	=> 'av-medium-font-size-title',
//												'small'		=> 'av-small-font-size-title',
//												'mini'		=> 'av-mini-font-size-title'
											)
						),
				
						array(
							'name'			=> __( 'Description Text Font Size', 'avia_framework' ),
							'desc'			=> __( 'Select a custom font size for the text.', 'avia_framework' ),
							'type'			=> 'template',
							'template_id'	=> 'font_sizes_icon_switcher',
							'subtype'		=> array(
												'default'	=> AviaHtmlHelper::number_array( 10, 40, 1, array( __( 'Use Default', 'avia_framework' ) => '' ), 'px' ),
//												'medium'	=> AviaHtmlHelper::number_array( 10, 60, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
//												'small'		=> AviaHtmlHelper::number_array( 10, 60, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
//												'mini'		=> AviaHtmlHelper::number_array( 10, 60, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' )
											),
							'id_sizes'		=> array(
												'default'	=> 'font_size_description',
//												'medium'	=> 'av-medium-font-size',
//												'small'		=> 'av-small-font-size',
//												'mini'		=> 'av-mini-font-size'
											)
						)
				
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Fonts', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_fonts' ), $template );
			
			
			$c = array(
						array(
							'name' 	=> __( 'Font color?', 'avia_framework' ),
							'desc' 	=> __( 'You can use the default font colors and styles or use a custom font color for the element (in case you use a background image for example)', 'avia_framework' ),
							'id' 	=> 'color',
							'type' 	=> 'select',
							'std'	=> '',
							'subtype'	=> array(
												__( 'Default', 'avia_framework' )	=> '',
												__( 'Light', 'avia_framework' )		=> 'font-light',
												__( 'Dark', 'avia_framework' )		=> 'font-dark',
												__( 'Custom', 'avia_framework' )	=> 'font-custom'
											),
						),
                    
						array(	
							'name' 	=> __( 'Custom Color', 'avia_framework' ),
							'desc' 	=> __( 'Select a custom color for your text here', 'avia_framework' ),
							'id' 	=> 'custom_color',
							'type' 	=> 'colorpicker',
							'std' 	=> '#444444',
							'required'	=> array( 'color', 'equals', 'font-custom' )
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
			
			
			/**
			 * Advanced Tab
			 * ============
			 */
			
			$c = array(
						array(	
							'name' 	=> __( 'Animation Duration', 'avia_framework' ),
							'desc' 	=> __( 'For large numbers higher values allow to slow down the animation from 0 to the given value. For smaller numbers minimum speed depends on the refresh cycle of the client screen.', 'avia_framework' ),
							'id' 	=> 'timer',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype' => AviaHtmlHelper::number_array( 1, 600, 1, array( 'Default (3)' => '' ) ),
						),
				
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
							'type'			=> 'template',
							'template_id'	=> 'linkpicker_toggle',
							'name'			=> __( 'Apply link?', 'avia_framework' ),
							'desc'			=> __( 'Do you want to apply a link to the element?', 'avia_framework' ),
							'subtypes'		=> array( 'no', 'manually', 'single', 'taxonomy' ),
						)
				
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'advanced_link' ), $c );
			
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
			extract( av_backend_icon( $params ) ); // creates $font and $display_char if the icon was passed as param 'icon' and the font as 'font' 

			$char = '';
			$char .= '<span ' . $this->class_by_arguments( 'font', $font ) . '>';
			$char .=	"<span data-update_with='icon_fakeArg' class='avia_big_numbers_icon'>{$display_char}</span>";
			$char .= '</span>';

			$inner  = "<div class='avia_iconbox avia_big_numbers avia_textblock avia_textblock_style avia_center_text'>";
			$inner .=		'<div ' . $this->class_by_arguments( 'icon_select', $params['args'] ) . '>';
			$inner .=			"<h2><span class='avia_big_numbers_icon_before'>{$char}</span><span data-update_with='number'>" . html_entity_decode( $params['args']['number'] ) . "</span><span class='avia_big_numbers_icon_after'>{$char}</span></h2>";
			$inner .=			"<div class='' data-update_with='content'>" . stripslashes( wpautop( trim( html_entity_decode( $params['content'] ) ) ) ) . '</div>';
			$inner .=		'</div>';
			$inner .= '</div>';

			$params['innerHtml'] = $inner;
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

			$atts = shortcode_atts( array(	
						'number' 		=> '100', 
						'start_from'	=> '',
						'number_format'	=> '',
						'timer'			=> '',			//	defaults to 3 - set in numbers.js
						'icon' 			=> '1', 
						'position' 		=> 'left', 
						'link' 			=> '', 
						'linktarget'	=> '', 
						'color' 		=> '', 
						'custom_color'	=> '', 
						'icon_select'	=> '', 
						'icon' 			=> 'no',
						'font'			=> '',
						'font_size'		=> '',
						'font_size_description'	=>'',
						'circle'		=> '',
						'circle_custom'	=> '',
						'circle_border_color'	=> '',
						'circle_bg_color'		=> '',
						'circle_border_width'	=> '',
						'circle_size'	=> ''

					), $atts, $this->config['shortcode'] );

			
			$atts['start_from'] = ! empty( $atts['start_from'] ) && is_numeric( $atts['start_from'] ) ? (int) $atts['start_from'] : 0;
			$atts['timer'] = ! empty( $atts['timer'] ) ? (int) $atts['timer'] * 1000 : 3000;
			
			extract( $atts );
	
			$tags = array( 'div', 'div' );
			$style = '';
			$font_style = '';
			$font_style2= '';
			$linktarget = AviaHelper::get_link_target( $linktarget );
			$link = AviaHelper::get_url( $link );
			$display_char = $before = $after = '';

			if( ! empty( $link ) )
			{
				$tags[0] = "a href='{$link}' title='' {$linktarget}";
				$tags[1] = 'a';
			}

			if( $color == 'font-custom' )
			{
				$style = "style='color:{$custom_color};'";
			}

			if( $font_size )
			{
				$font_style = "style='font-size:{$font_size}px;'";
			}
                
			if( $font_size_description )
			{
				$font_style2 = "style='font-size:{$font_size_description}px;'";
			}


			if( $icon_select !== 'no' )
			{
				$char 		  = av_icon( $icon, $font );
				$display_char = "<span class='avia-animated-number-icon {$icon_select}-number av-icon-char' {$char}></span>";
				if( $icon_select == 'av-icon-before' ) 
				{
					$before = $display_char;
				}
				if( $icon_select == 'av-icon-after' )  
				{
					$after  = $display_char;
				}
			}


			// add circle around animated number
			$circle_markup = '';
			if( $circle !== '' ) 
			{
				$circle_style_string = '';
				$circle_size_string = '';

				if( $circle_custom == 'custom' )
				{
					if( $circle_border_color !== '' )
					{
						$circle_style_string .= AviaHelper::style_string( $atts, 'circle_border_color', 'border-color' );
					}
					if( $circle_bg_color !== '' )
					{
						$circle_style_string .= AviaHelper::style_string( $atts, 'circle_bg_color', 'background-color' );
					}
					if( $circle_border_width !== '' )
					{
						$circle_style_string .= AviaHelper::style_string( $atts, 'circle_border_width', 'border-width', 'px' );
					}
					if( $circle_size !== '' )
					{
						$circle_size_string .= AviaHelper::style_string( $atts, 'circle_size', 'width','%' );
					}
				}

				$circle_style_string = AviaHelper::style_string( $circle_style_string );
				$circle_size_string = AviaHelper::style_string( $circle_size_string );

				$circle_markup = "<span class='avia-animated-number-circle' {$circle_size_string}><span class='avia-animated-number-circle-inner' {$circle_style_string}></span></span>";
			}


			// add blockquotes to the content
			$output  = '<' . $tags[0] . ' ' . $meta['custom_el_id'] . ' class="avia-animated-number av-force-default-color ' . $av_display_classes . ' avia-color-' . $color . ' ' . $meta['el_class'] . ' avia_animate_when_visible" ' . $style . ' data-timer="' . $timer . '">';
			$output .= $circle_markup;


			$output .= 		'<strong class="heading avia-animated-number-title" ' . $font_style . '>';
			$output .= 		$before . $this->extract_numbers( $number, $number_format, $atts ) . $after;
			$output .= 		'</strong>';
			$output .= 		"<div class='avia-animated-number-content' {$font_style2}>";
			$output .= 		wpautop( ShortcodeHelper::avia_remove_autop( $content ) );
			$output .= 	'</div></' . $tags[1] . '>';

			return $output;
		}

		/**
		 * Split string into animatable numbers and fixed string
		 * 
		 * @since < 4.0
		 * @param string $number
		 * @param string $number_format
		 * @param array $atts
		 * @return string
		 */
		protected function extract_numbers( $number, $number_format, &$atts )
		{
			$number = strip_tags( apply_filters( 'avf_big_number', $number ) );
			
			/**
			 * @used_by				currently unused
			 * @since 4.5.6
			 * @return string
			 */
			$number_format = apply_filters( 'avf_animated_numbers_separator', $number_format, $number, $atts );

			$replace = '<span class="avia-single-number __av-single-number" data-number_format="' . $number_format . '" data-number="$1" data-start_from="' . $atts['start_from'] . '">$1</span>';

			$number = preg_replace( '!(\D+)!', '<span class="avia-no-number">$1</span>', $number );

			/**
			 * In frontend we have to render unformatted to allow js work properly
			 */
			if( version_compare( phpversion(), '7.0', '<' ) || ! is_admin() )
			{
				$number = preg_replace( '!(\d+)!', $replace, $number );
			}
			else
			{
				$number = preg_replace_callback( 
									'!(\d+)!', 
									function ( $match ) use ( $number_format )
									{
										switch( $number_format )
										{
											case '.':
												$number = number_format( $match[0], 0, ',', $number_format );
												break;
											case ',':
												$number = number_format( $match[0], 0, '.', $number_format );
												break;
											case ' ':
												$number = number_format( $match[0], 0, ',', $number_format );
												break;
											default:
												$number = $match[0];
										}
										return $number;
									}, 
									$number );
			}
				
				return $number;
		}
			
	}
}
