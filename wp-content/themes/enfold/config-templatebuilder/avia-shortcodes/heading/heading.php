<?php
/**
 * Special Heading
 * 
 * Creates a special Heading
 */
 
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) { die( '-1' ); }



if ( ! class_exists( 'avia_sc_heading' ) ) 
{
	class avia_sc_heading extends aviaShortcodeTemplate
	{
			
		/**
		 * Create the config array for the shortcode button
		 */
		function shortcode_insert_button()
		{
			$this->config['version']		= '1.0';
			$this->config['self_closing']	= 'no';

			$this->config['name']		= __( 'Special Heading', 'avia_framework' );
			$this->config['tab']		= __( 'Content Elements', 'avia_framework' );
			$this->config['icon']		= AviaBuilder::$path['imagesURL'] . 'sc-heading.png';
			$this->config['order']		= 93;
			$this->config['target']		= 'avia-target-insert';
			$this->config['shortcode'] 	= 'av_heading';
			$this->config['modal_data'] = array('modal_class' => 'mediumscreen');
			$this->config['tooltip'] 	= __( 'Creates a special Heading', 'avia_framework' );
			$this->config['preview'] 	= true;
			$this->config['disabling_allowed'] = true;
			$this->config['id_name']	= 'id';
			$this->config['id_show']	= 'yes';
		}

		function extra_assets()
		{
			//load css
			wp_enqueue_style( 'avia-module-heading', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/heading/heading.css', array( 'avia-layout' ), false );
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
							'template_id'	=> $this->popup_key( 'content_heading' )
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
													$this->popup_key( 'styling_fonts' ),
													$this->popup_key( 'styling_colors' ),
													$this->popup_key( 'styling_spacing' )
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
							'name' 	=> __( 'Heading Text', 'avia_framework' ),
							'id' 	=> 'heading',
							'container_class' => 'avia-element-fullwidth',
							'std' 	=> __( 'Hello', 'avia_framework' ),
							'type' 	=> 'input'
						),
				
						array(	
							'name' 	=> __( 'Heading Type', 'avia_framework' ),
							'desc' 	=> __( 'Select which kind of heading you want to display.', 'avia_framework' ),
							'id' 	=> 'tag',
							'type' 	=> 'select',
							'std' 	=> 'h3',
							'subtype'	=> array( 'H1'=>'h1', 'H2'=>'h2', 'H3'=>'h3', 'H4'=>'h4', 'H5'=>'h5', 'H6'=>'h6' )
						), 
				
						array(	
							'name' 	=> __( 'Heading Style', 'avia_framework' ),
							'desc' 	=> __( 'Select a heading style', 'avia_framework' ),
							'id' 	=> 'style',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype'	=> array( 
												__( 'Default Style', 'avia_framework' )										=> '',  
												__( 'Heading Style Modern (left)', 'avia_framework' )						=> 'blockquote modern-quote' , 
												__( 'Heading Style Modern (centered)', 'avia_framework' )					=> 'blockquote modern-quote modern-centered',
												__( 'Heading Style Modern (right)', 'avia_framework' )						=> 'blockquote modern-quote modern-right',
												__( 'Heading Style Classic (left, italic)', 'avia_framework' )				=> 'blockquote classic-quote classic-quote-left',
												__( 'Heading Style Classic (centered, italic)', 'avia_framework' )			=> 'blockquote classic-quote',
												__( 'Heading Style Classic (right, italic)', 'avia_framework' )				=> 'blockquote classic-quote classic-quote-right',
												__( 'Heading Style Elegant (centered, optional icon)', 'avia_framework' )	=> 'blockquote elegant-quote elegant-centered'
											)
						),   
				
						array(	
							'name' 	=> __( 'Subheading', 'avia_framework' ),
							'desc' 	=> __( 'Add an extra descriptive subheading above or below the actual heading', 'avia_framework' ),
							'id' 	=> 'subheading_active',
							'type' 	=> 'select',
							'std' 	=> '',
				            'required'	=> array( 'style', 'not', '' ),
							'subtype'	=> array( 
												__( 'No Subheading', 'avia_framework' )				=> '',  
												__( 'Display subheading above', 'avia_framework' )	=> 'subheading_above',  
												__( 'Display subheading below', 'avia_framework' )	=> 'subheading_below'
											),
							),  							  
							  
						array(
							'name' 	=> __( 'Subheading Text','avia_framework' ),
							'desc' 	=> __( 'Add your subheading here','avia_framework' ),
							'id' 	=> 'content',
							'type' 	=> 'textarea',
							'required'	=> array( 'subheading_active', 'not', '' ),
							'std' 	=> ''
						),   
				
						array(	
							'name' 	=> __( 'Icon', 'avia_framework' ),
							'desc' 	=> __( 'Select to show an additional icon above headline', 'avia_framework' ),
							'id' 	=> 'show_icon',
							'type' 	=> 'select',
							'std' 	=> '',
							'required' => array( 'style', 'equals', 'blockquote elegant-quote elegant-centered' ),
							'subtype'	=> array( 
												__( 'No Icon', 'avia_framework' )		=>'',  
												__( 'Display Icon', 'avia_framework' )	=>'custom_icon' , 
											)
						),   

						array(
							'name' 	=> __( 'Icon','avia_framework' ),
							'desc' 	=> __( 'Select an icon to display above the headline', 'avia_framework' ),
							'id' 	=> 'icon',
							'type' 	=> 'iconfont',
							'std' 	=> '',
							'required'	=> array( 'show_icon', 'equals', 'custom_icon' ),
						)
				
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_heading' ), $c );
			
			/**
			 * Styling Tab
			 * ===========
			 */
			
			$font_size_array = array( 
						__( 'Default Size', 'avia_framework' ) => '',
						__( 'Flexible font size (adjusts to screen width)' , 'avia_framework' )	=> AviaHtmlHelper::number_array( 3, 7, 0.5, array(), 'vw', '', 'vw' ),
						__( 'Fixed font size' , 'avia_framework' )								=> AviaHtmlHelper::number_array( 11, 150, 1, array(), 'px', '', '' )
					);

				
			
			$c = array(
						array(
							'name'			=> __( 'Heading Font Sizes', 'avia_framework' ),
							'desc'			=> __( 'Select a custom font size for the heading.', 'avia_framework' ),
							'type'			=> 'template',
							'template_id'	=> 'font_sizes_icon_switcher',
							'required'		=> array( 'style', 'not', '' ),
							'subtype'		=> array(
												'default'	=> $font_size_array,
												'medium'	=> AviaHtmlHelper::number_array( 10, 120, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
												'small'		=> AviaHtmlHelper::number_array( 10, 120, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
												'mini'		=> AviaHtmlHelper::number_array( 10, 120, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' )
											),
							'id_sizes'		=> array(
												'default'	=> 'size',
												'medium'	=> 'av-medium-font-size-title',
												'small'		=> 'av-small-font-size-title',
												'mini'		=> 'av-mini-font-size-title'
											)
						),
				
						array(
							'name'			=> __( 'Subheading Font Sizes', 'avia_framework' ),
							'desc'			=> __( 'Select a custom font size for the subheading.', 'avia_framework' ),
							'type'			=> 'template',
							'template_id'	=> 'font_sizes_icon_switcher',
							'required'		=> array( 'subheading_active', 'not', '' ),
							'subtype'		=> array(
												'default'	=> AviaHtmlHelper::number_array( 10, 40, 1, array( __( 'Use Default', 'avia_framework' ) => '' ), 'px' ),
												'medium'	=> AviaHtmlHelper::number_array( 10, 40, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
												'small'		=> AviaHtmlHelper::number_array( 10, 40, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
												'mini'		=> AviaHtmlHelper::number_array( 10, 40, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' )
											),
							'id_sizes'		=> array(
												'default'	=> 'subheading_size',
												'medium'	=> 'av-medium-font-size',
												'small'		=> 'av-small-font-size',
												'mini'		=> 'av-mini-font-size'
											)
						),
				
						array(
							'name'			=> __( 'Icon Font Size', 'avia_framework' ),
							'desc'			=> __( 'Select a custom font size for the icon', 'avia_framework' ),
							'type'			=> 'template',
							'template_id'	=> 'font_sizes_icon_switcher',
							'required'		=> array( 'show_icon', 'equals', 'custom_icon' ),
							'subtype'		=> array(
												'default'	=> AviaHtmlHelper::number_array( 10, 120, 1, array( __( 'Use Default', 'avia_framework' ) => '' ), 'px' ),
												'medium'	=> AviaHtmlHelper::number_array( 10, 120, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
												'small'		=> AviaHtmlHelper::number_array( 10, 120, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
												'mini'		=> AviaHtmlHelper::number_array( 10, 120, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' )
											),
							'id_sizes'		=> array(
												'default'	=> 'icon_size',
												'medium'	=> 'av-medium-font-size-1',
												'small'		=> 'av-small-font-size-1',
												'mini'		=> 'av-mini-font-size-1'
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
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_fonts' ), $template );
			
			$c = array(
						array(	
							'name' 	=> __( 'Heading Color', 'avia_framework' ),
							'desc' 	=> __( 'Select a heading color', 'avia_framework' ),
							'id' 	=> 'color',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype'	=> array( 
												__( 'Default Color', 'avia_framework' )	=> '', 
												__( 'Meta Color', 'avia_framework' )	=> 'meta-heading', 
												__( 'Custom Color', 'avia_framework' )	=> 'custom-color-heading'
											)
							), 
					
						array(	
							'name' 	=> __( 'Custom Font Color', 'avia_framework' ),
							'desc' 	=> __( 'Select a custom font color for your Heading here', 'avia_framework' ),
							'id' 	=> 'custom_font',
							'type' 	=> 'colorpicker',
							'std' 	=> '',
							'required' => array( 'color', 'equals', 'custom-color-heading' )
						),
				
						array(	
							'name' 	=> __( 'Custom Icon Color', 'avia_framework' ),
							'desc' 	=> __( 'Select a custom icon color for your Heading here', 'avia_framework' ),
							'id' 	=> 'icon_color',
							'type' 	=> 'colorpicker',
							'std' 	=> '',
							'required' => array( 'color', 'equals', 'custom-color-heading' )
						)
						
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
			
			
			$c = array(
						array(	
							'name' 	=> __( 'Margin', 'avia_framework' ),
							'desc' 	=> __( 'Set the distance from the content to other elements here. Leave empty for default value. Both pixel and &percnt; based values are accepted. eg: 30px, 5&percnt; ', 'avia_framework' ),
							'id' 	=> 'margin',
							'type' 	=> 'multi_input',
							'std' 	=> ',,,,', 
							'sync' 	=> true,
							'multi' => array(	
											'top'		=> __( 'Margin-Top', 'avia_framework' ), 
											'right'		=> __( 'Margin-Right', 'avia_framework' ), 
											'bottom'	=> __( 'Margin-Bottom', 'avia_framework' ),
											'left'		=> __( 'Margin-Left', 'avia_framework' ), 
										)
						),
						
						array(	
							'name' 	=> __( 'Padding Bottom', 'avia_framework' ),
							'desc' 	=> __( 'Bottom Padding in pixel', 'avia_framework' ),
							'id' 	=> 'padding',
							'type' 	=> 'select',
							'subtype'	=> AviaHtmlHelper::number_array( 0, 120, 1 ),
							'std'	=> '10'
						), 
				
						array(	
							'name' 	=> __( 'Icon Padding Bottom', 'avia_framework' ),
							'desc' 	=> __( 'Icon bottom padding in pixel', 'avia_framework' ),
							'id' 	=> 'icon_padding',
							'type' 	=> 'select',
							'std'	=> '10',
							'subtype'	=> AviaHtmlHelper::number_array( 0, 120, 1 ),
							'required'	=> array( 'show_icon', 'equals', 'custom_icon' ),
						)
						
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Spacing', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_spacing' ), $template );
			
			/**
			 * Advanced Tab
			 * ===========
			 */
			
			$c = array(
						array(	
							'type'			=> 'template',
							'template_id'	=> 'linkpicker_toggle',
							'name'			=> __( 'Header Text Link?', 'avia_framework' ),
							'desc'			=> __( 'Do you want to apply a link to the header text?', 'avia_framework' ),
							'subtypes'		=> array( 'no', 'manually', 'single', 'taxonomy' ),
							'target_id'		=> 'link_target'
						),
						
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
			/**
			 * Fix a bug in 4.7 and 4.7.1 renaming option id (no longer backwards comp.) - can be removed in a future version again
			 */
			if( isset( $params['args']['linktarget'] ) )
			{
				$params['args']['link_target'] = $params['args']['linktarget'];
			}
			
			extract( av_backend_icon( $params ) ); // creates $font and $display_char if the icon was passed as param 'icon' and the font as 'font' 
			
			$params['args'] = shortcode_atts( array(
									'heading'					=> '',
									'tag'						=> 'h3', 
									'link'						=> '',
									'link_target'				=> '',
									'style'						=> '',
									'size'						=> '',
									'subheading_active'			=> '', 
									'subheading_size'			=> '', 
									'margin'					=> '',
									'padding'					=> '5', 
									'icon_padding'				=> '10',
									'color'						=> '', 
									'custom_font'				=> '', 
									'icon_color'				=> '', 
									'show_icon'					=> '',
									'icon'						=> '',
									'font'						=> '',
									'icon_size'					=> '',
									'custom_class'				=> '', 
									'id'						=> '',
									'admin_preview_bg'			=> '',
									'av-desktop-hide'			=> '',
									'av-medium-hide'			=> '',
									'av-small-hide'				=> '',
									'av-mini-hide'				=> '',
									'av-medium-font-size-title'	=> '',
									'av-small-font-size-title'	=> '',
									'av-mini-font-size-title'	=> '',
									'av-medium-font-size'		=> '',
									'av-small-font-size'		=> '',
									'av-mini-font-size'			=> '',
									'av-medium-font-size-1'		=> '',
									'av-small-font-size-1'		=> '',
									'av-mini-font-size-1'		=> ''
				
						), $params['args'], $this->config['shortcode'] );
			
			
//			$templateNAME  	= $this->update_template( 'name', '{{name}}' );

			$content = stripslashes( wpautop( trim( html_entity_decode( $params['content'] ) ) ) );

			$params['class'] = '';
			$params['innerHtml']  = "<div class='avia_textblock avia_textblock_style avia-special-heading' >";

			$params['innerHtml'] .= 	'<div ' . $this->class_by_arguments( 'tag, style, color, subheading_active, show_icon', $params['args'] ) . '>';
			$params['innerHtml'] .= 		"<div class='av-subheading-top av-subheading' data-update_with='content'>{$content}</div>";
			$params['innerHtml'] .=			'<span class="avia-heading-icon">';
			$params['innerHtml'] .=				'<span ' . $this->class_by_arguments( 'font', $font ) . '>';
			$params['innerHtml'] .=					"<span data-update_with='icon_fakeArg' class='avia_icon_char'>{$display_char}</span>";
			$params['innerHtml'] .=				'</span>';
			$params['innerHtml'] .=			'</span>';
			$params['innerHtml'] .= 		"<div data-update_with='heading'>";
			$params['innerHtml'] .=				stripslashes( trim( htmlspecialchars_decode( $params['args']['heading'] ) ) );
			$params['innerHtml'] .= 		'</div>';
			$params['innerHtml'] .= 		"<div class='av-subheading-bottom av-subheading' data-update_with='content'>{$content}</div>";
			$params['innerHtml'] .= 	'</div>';
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
			/**
			 * Fix a bug in 4.7 and 4.7.1 renaming option id (no longer backwards comp.) - can be removed in a future version again
			 */
			if( isset( $atts['linktarget'] ) )
			{
				$atts['link_target'] = $atts['linktarget'];
			}
			
			extract( AviaHelper::av_mobile_sizes( $atts ) ); //return $av_font_classes, $av_title_font_classes and $av_display_classes 

			$atts = shortcode_atts( array(
							'heading'			=> '',
							'tag'				=> 'h3', 
							'link_apply'		=> null,		//	backwards comp. < version 1.0
							'link'				=> '',
							'link_target'		=> '',
							'style'				=> '',
							'show_icon'			=> '',
							'icon'				=> '',
							'font'				=> '',
							'icon_size'			=> '',
							'icon_padding'		=> 10,
							'icon_color'		=> '',
							'size'				=> '',
							'subheading_active' => '', 
							'subheading_size'	=> '', 
							'margin'			=> '',
							'padding'			=> '5', 
							'color'				=> '', 
							'custom_font'		=> '', 
				
					), $atts, $this->config['shortcode'] );
			
			//	backwards comp. < version 1.0
			if( ! is_null( $atts['link_apply'] ) )
			{
				if( empty( $atts['link_apply'] ) )
				{
					$atts['link'] = '';
					$atts['link_target'] = '';
				}
			}
			
			$atts['link'] = trim( $atts['link'] );
			if( ( 'manually,http://' == $atts['link'] ) || ( 'manually,https://' == $atts['link'] ) )
			{
				$atts['link'] = '';
				$atts['link_target'] = '';
			}

			extract( $atts );
				
			$output  = '';
			$styling = '';
			$subheading = '';
			$border_styling = '';
			$before = $after = '';
			$class = $meta['el_class'];
			$subheading_extra = '';
			$link_before = '';
			$link_after = '';
			$subheading_size = empty( $subheading_size ) ? '15' : $subheading_size;
			$icon_size = empty( $icon_size ) ? '25' : $icon_size;

			/*margin calc*/
			$margin_calc = AviaHelper::multi_value_result( $margin , 'margin' );

			if( $heading )
			{
				// add seo markup
				$markup = avia_markup_helper( array( 'context' => 'entry_title', 'echo' => false, 'custom_markup' => $meta['custom_markup'] ) );

				// filter heading for & symbol and convert them					
				$heading = apply_filters( 'avia_ampersand', wptexturize( $heading ) );

				//if the heading contains a strong tag make apply a custom class that makes the rest of the font appear smaller for a better effect
				if( strpos( $heading, '<strong>' ) !== false ) 
				{
					$class .= ' av-thin-font';
				}

				//apply the padding bottom styling
				$styling .= "padding-bottom:{$padding}px; {$margin_calc['complement']}";

				// if the color is a custom hex value add the styling for both border and font
				if( $color == 'custom-color-heading' && $custom_font )  
				{
					$styling .= "color:{$custom_font};";
					$border_styling = "style='border-color:{$custom_font}'";
					$subheading_extra = 'av_custom_color';
				}
	        		
				// if a custom font size is set apply it to the container and also apply the inherit class so the actual heading uses the size
				if( ! empty( $style ) && ! empty( $size ) ) 
				{ 
					if( is_numeric( $size ) ) 
					{
						$size .= 'px';
					}

					$styling .= "font-size:{$size};"; 
					$class .= ' av-inherit-size';
				}

				//finish up the styling string
				if( ! empty( $styling ) ) 
				{
					$styling = "style='{$styling}'";
				}

				//check if we need to apply a link
				if( ! empty( $link ) )
				{
					$class .= ' av-linked-heading';

					$link_before .= '<a href="' . AviaHelper::get_url( $link ) . '"' . AviaHelper::get_link_target( $link_target ) . '>';
					$link_after = '</a>';
				}
				
				// special markup for 'elegant' style
				if( $style == 'blockquote elegant-quote elegant-centered' )
				{
					$output_before = "";

					if( $show_icon == 'custom_icon' && $icon !== '' )
					{
						$display_char = av_icon( $icon, $font );
						$icon_styling = "";
						
						if( is_numeric( $icon_size ) )
						{
							$icon_styling .= AviaHelper::style_string( $atts, 'icon_size', 'font-size','px' );
						}
						
						if( $icon_color !== '' )
						{
							$icon_styling .= AviaHelper::style_string( $atts, 'icon_color', 'color' );
						}
						if( is_numeric( $icon_padding ) )
						{
							$icon_styling .= AviaHelper::style_string( $atts, 'icon_padding', 'padding-bottom','px' );
						}

						$icon_styling = ( $icon_styling !== "" ) ? AviaHelper::style_string( $icon_styling ) : "";

						$icon_markup = "<span class='heading-char avia-font-{$font} {$av_font_classes_1}' {$icon_styling} {$display_char}></span>";
						$output_before = $icon_markup;
					}

					$output_before .= '<span class="heading-wrap">';
					$output_after = '</span>';

					$heading = $output_before . $heading . $output_after;
				}
	        		
				//check if we got a subheading
				if( ! empty( $style ) && ! empty( $subheading_active ) && ! empty( $content ) )
				{

					$content = "<div class ='av-subheading av-{$subheading_active} {$subheading_extra} {$av_font_classes}' style='font-size:{$subheading_size}px;'>" . ShortcodeHelper::avia_apply_autop( ShortcodeHelper::avia_remove_autop( $content ) ) . '</div>';

					if( $subheading_active == 'subheading_above' )
					{
						$before = $content;
					}
					else
					{
						$after = $content;
					}
				}

				//html markup
				$output .= "<div {$meta['custom_el_id']} {$styling} class='av-special-heading av-special-heading-{$tag} {$color} {$style} {$class} {$av_display_classes}'>";
				$output .= 		$before;
				$output .= 		"<{$tag} class='av-special-heading-tag {$av_title_font_classes}' $markup >{$link_before}{$heading}{$link_after}</{$tag}>";
				$output .= 		$after;
				$output .= 		"<div class='special-heading-border'><div class='special-heading-inner-border' {$border_styling}></div></div>";
				$output .= '</div>';
			}

			return $output;
		}
	}
}
