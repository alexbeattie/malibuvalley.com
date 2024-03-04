<?php
/**
 * Button
 * 
 * Displays a colored button that links to any url of your choice
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if ( ! class_exists( 'avia_sc_button' ) ) 
{
	class avia_sc_button extends aviaShortcodeTemplate
	{
		/**
		 * Create the config array for the shortcode button
		 */
		function shortcode_insert_button()
		{
			$this->config['version']		= '1.0';
			$this->config['self_closing']	= 'yes';

			$this->config['name']		= __( 'Button', 'avia_framework' );
			$this->config['tab']		= __( 'Content Elements', 'avia_framework' );
			$this->config['icon']		= AviaBuilder::$path['imagesURL'] . 'sc-button.png';
			$this->config['order']		= 85;
			$this->config['target']		= 'avia-target-insert';
			$this->config['shortcode'] 	= 'av_button';
			$this->config['tooltip'] 	= __( 'Creates a colored button', 'avia_framework' );
			$this->config['tinyMCE']    = array( 'tiny_always' => true );
			$this->config['preview'] 	= true;
			$this->config['id_name']	= 'id';
			$this->config['id_show']	= 'yes';
		}


		function extra_assets()
		{
			//load css
			wp_enqueue_style( 'avia-module-button', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/buttons/buttons.css', array( 'avia-layout' ), false );
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
													$this->popup_key( 'content_button' ),
													$this->popup_key( 'advanced_link' )
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
													$this->popup_key( 'styling_appearance' ),
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
			
			/**
			 * Content Tab
			 * ===========
			 */
			
			$c = array(
						array(
							'name' 	=> __( 'Button Label', 'avia_framework' ),
							'desc' 	=> __( 'This is the text that appears on your button.', 'avia_framework' ),
							'id' 	=> 'label',
							'type' 	=> 'input',
							'std' => __( 'Click me', 'avia_framework' )
						),
				
						array(
							'name' 	=> __( 'Show Button Icon', 'avia_framework' ),
							'desc' 	=> __( 'Should an icon be displayed at the left or right side of the button', 'avia_framework' ),
							'id' 	=> 'icon_select',
							'type' 	=> 'select',
							'std' 	=> 'yes',
							'subtype'	=> array(
												__( 'No Icon', 'avia_framework' )					=> 'no',
												__( 'Display icon to the left', 'avia_framework' )	=> 'yes' ,	
												__( 'Display icon to the right', 'avia_framework' )	=> 'yes-right-icon',
											)
						),
				
						array(	
							'name' 	=> __( 'Button Icon', 'avia_framework' ),
							'desc' 	=> __( 'Select an icon for your Button below', 'avia_framework' ),
							'id' 	=> 'icon',
							'type' 	=> 'iconfont',
							'std' 	=> '',
							'required'	=> array( 'icon_select', 'not_empty_and', 'no' )
							),
				
						array(	
							'name' 	=> __( 'Icon Visibility', 'avia_framework' ),
							'desc' 	=> __( 'Check to only display icon on hover', 'avia_framework' ),
							'id' 	=> 'icon_hover',
							'type' 	=> 'checkbox',
							'std' 	=> '',
							'required'	=> array( 'icon_select', 'not_empty_and', 'no' )
						)
				
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Button', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_button' ), $template );
			
			
			/**
			 * Styling Tab
			 * ===========
			 */
			
			$c = array(
						array(	
							'name' 	=> __( 'Button Size', 'avia_framework' ),
							'desc' 	=> __( 'Choose the size of your button here.', 'avia_framework' ),
							'id' 	=> 'size',
							'type' 	=> 'select',
							'std' 	=> 'small',
							'subtype'	=> array(
												__( 'Small', 'avia_framework' )		=> 'small',
												__( 'Medium', 'avia_framework' )	=> 'medium',
												__( 'Large', 'avia_framework' )		=> 'large',
												__( 'X Large', 'avia_framework' )	=> 'x-large'
											)
						),
							
						array(	
							'name' 	=> __( 'Button Position', 'avia_framework' ),
							'desc' 	=> __( 'Choose the alignment of your button here', 'avia_framework' ),
							'id' 	=> 'position',
							'type' 	=> 'select',
							'std' 	=> 'center',
							'subtype'	=> array(
												__( 'Align Left', 'avia_framework' )	=> 'left',
												__( 'Align Center', 'avia_framework' )	=> 'center',
												__( 'Align Right', 'avia_framework' )	=> 'right',
											),
							'required'	=> array( 'size', 'not', 'fullwidth' )
						),
				
						array(	
							'name' 	=> __( 'Button Label Display', 'avia_framework' ),
							'desc' 	=> __( 'Select how to display the label', 'avia_framework' ),
							'id' 	=> 'label_display',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype'	=> array(
												__( 'Always display', 'avia_framework' )	=> '',	
												__( 'Display on hover', 'avia_framework' )	=> 'av-button-label-on-hover',
											)
						),
					
						array(	
							'name'		=> __( 'Button Title Attribute', 'avia_framework' ),
							'desc'		=> __( 'Add a title attribute for this button.', 'avia_framework' ),
							'id'		=> 'title_attr',
							'type'		=> 'input',
							'required'	=> array( 'label_display', 'equals', '' ),
							'std'		=> ''
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
							'name' 	=> __( 'Button Colors Selection', 'avia_framework' ),
							'desc' 	=> __( 'Select the available options for button colors. Switching to advanced options for already existing buttons you need to set all options (color settings from basic options are ignored).', 'avia_framework' ),
							'id' 	=> 'color_options',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype'	=> array(
												__( 'Basic options only', 'avia_framework' )	=> '',	
												__( 'Advanced options', 'avia_framework' )		=> 'color_options_advanced',
											)
						),
				
						array(	
							'type'			=> 'template',
							'template_id'	=> 'named_colors',
							'custom'		=> true,
							'required'		=> array( 'color_options', 'equals', '' )
						),
				
						array(	
							'name' 	=> __( 'Custom Background Color', 'avia_framework' ),
							'desc' 	=> __( 'Select a custom background color for your button here', 'avia_framework' ),
							'id' 	=> 'custom_bg',
							'type' 	=> 'colorpicker',
							'std' 	=> '#444444',
							'required'	=> array( 'color', 'equals', 'custom' )
						),	
						
						array(
							'name' 	=> __( 'Custom Font Color', 'avia_framework' ),
							'desc' 	=> __( 'Select a custom font color for your button here', 'avia_framework' ),
							'id' 	=> 'custom_font',
							'type' 	=> 'colorpicker',
							'std' 	=> '#ffffff',
							'required'	=> array( 'color', 'equals', 'custom')
						),
				
						array(	
							'type'			=> 'template',
							'template_id'	=> 'button_colors',
							'color_id'		=> 'btn_color',
							'custom_id'		=> 'btn_custom',
							'required'		=> array( 'color_options', 'not', '' )
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
			
			
			
			/**
			 * Advanced Tab
			 * ===========
			 */
			
			$c = array(
						array(	
							'type'			=> 'template',
							'template_id'	=> 'linkpicker_toggle',
							'name'			=> __( 'Button Link?', 'avia_framework' ),
							'desc'			=> __( 'Where should your button link to?', 'avia_framework' ),
							'subtypes'		=> array( 'manually', 'single', 'taxonomy' ),
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

			$inner  = "<div class='avia_button_box avia_hidden_bg_box avia_textblock avia_textblock_style'>";
			$inner .=		'<div ' . $this->class_by_arguments( 'icon_select, color, size, position', $params['args'] ) . '>';
			$inner .=			'<span ' . $this->class_by_arguments( 'font', $font ) . '>';
			$inner .=				"<span data-update_with='icon_fakeArg' class='avia_button_icon avia_button_icon_left'>{$display_char}</span>";
			$inner .=			'</span> ';
			$inner .=			"<span data-update_with='label' class='avia_iconbox_title' >{$params['args']['label']}</span> ";
			$inner .=			'<span ' . $this->class_by_arguments( 'font', $font ) . '>';
			$inner .=				"<span data-update_with='icon_fakeArg' class='avia_button_icon avia_button_icon_right'>{$display_char}</span>";
			$inner .=			'</span>';
			$inner .=		'</div>';
			$inner .= '</div>';

			$params['innerHtml'] = $inner;
			$params['content'] = null;
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
			/**
			 * Fix a bug in 4.7 and 4.7.1 renaming option id (no longer backwards comp.) - can be removed in a future version again
			 */
			if( isset( $atts['linktarget'] ) )
			{
				$atts['link_target'] = $atts['linktarget'];
			}
			
			extract( AviaHelper::av_mobile_sizes( $atts ) ); //return $av_font_classes, $av_title_font_classes and $av_display_classes 

			$atts = shortcode_atts( array(
				
							'label'			=> 'Click me', 
							'link'			=> '', 
							'link_target'	=> '',
							'color'			=> 'theme-color',
							'custom_bg'		=> '#444444',
							'custom_font'	=> '#ffffff',
							'size'			=> 'small',
							'position'		=> 'center',
							'icon_select'	=> 'yes',
							'icon'			=> '', 
							'font'			=> '',
							'icon_hover'	=> '',
							'label_display'	=> '',
							'title_attr'	=> '',
				
							'color_options'			=> '',		//	added 4.7.5.1
							'btn_color_bg'			=> 'theme-color',			
							'btn_color_bg_hover'	=> 'theme-color',
							'btn_color_font'		=> 'custom',
							'btn_custom_bg'			=> '#444444',
							'btn_custom_bg_hover'	=> '#444444',
							'btn_custom_font'		=> '#ffffff',
//							'btn_color_font_hover'	=> '#ffffff',
//							'btn_custom_font_hover'	=> '#ffffff'
							
						), $atts, $this->config['shortcode'] );
			
			
			if( $atts['icon_select'] == 'yes' ) 
			{
				$atts['icon_select'] = 'yes-left-icon';
			}

			$style = '';
			$style_hover = '';
			$data = '';
			$background_hover = '';
			
			$display_char = av_icon( $atts['icon'], $atts['font'] );
			$extraClass = $atts['icon_hover'] ? 'av-icon-on-hover' : '';
			
			if( '' == $atts['color_options'] )
			{
				if( $atts['color'] == 'custom' ) 
				{
					$style .= AviaHelper::style_string( $atts, 'custom_bg', 'background-color' );
					$style .= AviaHelper::style_string( $atts, 'custom_bg', 'border-color' );
					$style .= AviaHelper::style_string( $atts, 'custom_font', 'color' );
				}
				else
				{
					$extraClass .= ' ' . $this->class_by_arguments( 'color', $atts, true );
				}
			}
			else		//	color_options_advanced - added 4.7.5.1
			{
				if( 'custom' == $atts['btn_color_bg'] )
				{
					$style .= AviaHelper::style_string( $atts, 'btn_custom_bg', 'background-color' );
					$style .= AviaHelper::style_string( $atts, 'btn_custom_bg', 'border-color' );
				}
				else 
				{
					$extraClass .= ' avia-color-' . $atts['btn_color_bg'] . ' ';
				}
				
				if( 'custom' == $atts['btn_color_font'] )
				{
					$style .= AviaHelper::style_string( $atts, 'btn_custom_font', 'color' );
				}
				else
				{
					$extraClass .= ' avia-font-color-' . $atts['btn_color_font'];
				}
				
				if( 'custom' == $atts['btn_color_bg_hover'] )
				{
					$style_hover = "style='background-color:{$atts['btn_custom_bg_hover']};'";
				}
				
				$background_hover = "<span class='avia_button_background avia-button avia-color-" . $atts['btn_color_bg_hover'] . "' {$style_hover}></span>";
			}
			
			$style = AviaHelper::style_string( $style );
			
			if( ! empty( $atts['label_display'] ) && $atts['label_display'] == 'av-button-label-on-hover' ) 
			{
				$extraClass .= ' av-button-label-on-hover ';
				$data = 'data-avia-tooltip="' . htmlspecialchars( $atts['label'] ) . '"';
				$atts['label'] = '';
			}

			if( empty( $atts['label'] ) ) 
			{
				$extraClass .= ' av-button-notext ';
			}

			$blank = AviaHelper::get_link_target( $atts['link_target'] );
			$link = AviaHelper::get_url( $atts['link'] );
			$link = ( ( $link == 'http://' ) || ( $link == 'manually' ) ) ? '' : $link;

			$title_attr = ! empty( $atts['title_attr'] ) && empty( $atts['label_display'] ) ? 'title="' . esc_attr( $atts['title_attr'] ) . '"' : '';

			$content_html = '';
			
			if( 'yes-left-icon' == $atts['icon_select'] ) 
			{
				$content_html .= "<span class='avia_button_icon avia_button_icon_left ' {$display_char}></span>";
			}
			
			$content_html .= "<span class='avia_iconbox_title' >{$atts['label']}</span>";
			
			if( 'yes-right-icon' == $atts['icon_select'] ) 
			{
				$content_html .= "<span class='avia_button_icon avia_button_icon_right' {$display_char}></span>";
			}

			$output  = '';
			$output .=	"<a href='{$link}' {$data} class='avia-button {$extraClass} {$av_display_classes} " . $this->class_by_arguments( 'icon_select, size, position', $atts, true ) . "' {$blank} {$style} >";
			$output .=		$content_html;
			$output .=		$background_hover;
			$output .=	'</a>';

			$output =  "<div {$meta['custom_el_id']} class='avia-button-wrap avia-button-{$atts['position']} {$meta['el_class']}' {$title_attr}>{$output}</div>";

			return $output;
		}
			
	}
}
