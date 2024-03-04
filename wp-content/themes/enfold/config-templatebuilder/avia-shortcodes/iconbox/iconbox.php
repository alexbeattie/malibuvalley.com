<?php
/**
 * Icon Box
 * 
 * Shortcode which creates a content block with icon to the left or above
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if ( ! class_exists( 'avia_sc_icon_box' ) )
{
	class avia_sc_icon_box extends aviaShortcodeTemplate
	{
		/**
		 * Create the config array for the shortcode button
		 */
		function shortcode_insert_button()
		{
			$this->config['version']		= '1.0';
			$this->config['self_closing']	= 'no';

			$this->config['name']			= __('Icon Box', 'avia_framework' );
			$this->config['tab']			= __('Content Elements', 'avia_framework' );
			$this->config['icon']			= AviaBuilder::$path['imagesURL'] . 'sc-icon_box.png';
			$this->config['order']			= 90;
			$this->config['target']			= 'avia-target-insert';
			$this->config['shortcode'] 		= 'av_icon_box';
			$this->config['tooltip'] 	    = __('Creates a content block with icon to the left or above', 'avia_framework' );
			$this->config['preview']		= 1;
			$this->config['disabling_allowed'] = true;
			$this->config['id_name']		= 'id';
			$this->config['id_show']		= 'yes';
		}

		function extra_assets()
		{
			wp_enqueue_style( 'avia-module-icon', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/icon/icon.css', array( 'avia-layout' ), false );
			wp_enqueue_style( 'avia-module-iconbox', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/iconbox/iconbox.css', array( 'avia-layout' ), false );
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
							'template_id'	=> $this->popup_key( 'content_iconbox' )
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
													$this->popup_key( 'styling_colors' ),
													$this->popup_key( 'styling_font_sizes' ),
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
								'template_id'	=> $this->popup_key( 'advanced_heading' ),
								'nodescription' => true
							),
				
						array(
								'type'			=> 'template',
								'template_id'	=> $this->popup_key( 'advanced_link' ),
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
							'name' 	=> __( 'IconBox Icon', 'avia_framework' ),
							'desc' 	=> __( 'Select an IconBox Icon below', 'avia_framework' ),
							'id' 	=> 'icon',
							'type' 	=> 'iconfont',
							'std' 	=> '1'
						),
				
						array(
							'name' 	=> __( 'Title', 'avia_framework' ),
							'desc' 	=> __( 'Add an IconBox title here', 'avia_framework' ),
							'id' 	=> 'title',
							'type' 	=> 'input',
							'std' 	=> __( 'IconBox Title', 'avia_framework' )
						),
				
						array(
							'name' 	=> __( 'Content', 'avia_framework' ),
							'desc' 	=> __('Add some content for this IconBox', 'avia_framework' ),
							'id' 	=> 'content',
							'type' 	=> 'tiny_mce',
							'std' 	=> __( 'Click to add your own text here', 'avia_framework' )
						)
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_iconbox' ), $c );
			
			/**
			 * Styling Tab
			 * ============
			 */
			
			$c = array(
						array(
							'name' 	=> __( 'IconBox Styling', 'avia_framework' ),
							'desc' 	=> __( 'Defines the styling of the iconbox and the position of the icon', 'avia_framework' ),
							'id' 	=> 'position',
							'type' 	=> 'select',
							'std' 	=> 'left',
							'subtype'	=> array( 
												__( 'Display small icon at the left side of the title', 'avia_framework' )			=> 'left',
												__( 'Display icon at the left side of the whole content block', 'avia_framework' )	=> 'left_content',
												__( 'Display icon at the right side of the whole content block', 'avia_framework' )	=> 'right_content',
												__( 'Display icon above the title', 'avia_framework' )								=> 'top'
											)
						),
	
						array(
							'name' 	=> __( 'Icon display', 'avia_framework' ),
							'desc' 	=> __( 'Select how to display the icon beside your content', 'avia_framework' ),
							'id' 	=> 'icon_style',
							'type' 	=> 'select',
							'std' 	=> '',
							'required'	=> array( 'position', 'contains','content' ),
							'subtype'	=> array( 
												__( 'Small with border', 'avia_framework' )		=> '',
												__( 'Big without border', 'avia_framework' )	=> 'av-icon-style-no-border',
											)
						),
					
						array(
							'name' 	=> __( 'Content block', 'avia_framework' ),
							'desc' 	=> __( 'Select if the iconbox should receive a border around the content', 'avia_framework' ),
							'id' 	=> 'boxed',
							'type' 	=> 'select',
							'std' 	=> '',
							'required'	=> array( 'position', 'equals', 'top' ),
							'subtype'	=> array( 
												__( 'Boxed content block with borders', 'avia_framework' )	=> '',
												__( 'No box arround content', 'avia_framework' )			=> 'av-no-box',
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
												__( 'Default', 'avia_framework' )				=> '',
												__( 'Define Custom Colors', 'avia_framework' )	=> 'custom'),
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
							'required' => array( 'font_color', 'equals', 'custom' )
						),
				
						array(
							'name' 	=> __( 'Icon Colors', 'avia_framework' ),
							'desc' 	=> __( 'Either use the themes default colors or apply some custom ones', 'avia_framework' ),
							'id' 	=> 'color',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype'	=> array( 
												__( 'Default', 'avia_framework' )				=> '',
												__( 'Define Custom Colors', 'avia_framework' )	=> 'custom'),						
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
							'name' 	=> __( 'Custom Font Color', 'avia_framework' ),
							'desc' 	=> __( 'Select a custom font color. Leave empty to use the default', 'avia_framework' ),
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
								'title'			=> __( 'Colors', 'avia_framework' ),
								'content'		=> $c 
							),
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_colors' ), $template );
			
			$c = array(
						array(
							'name'			=> __( 'Title Font Sizes', 'avia_framework' ),
							'desc'			=> __( 'Select a custom font size for the title. Using non default values might need CSS styling.', 'avia_framework' ),
							'type'			=> 'template',
							'template_id'	=> 'font_sizes_icon_switcher',
							'subtype'		=> array(
												'default'	=> AviaHtmlHelper::number_array( 10, 50, 1, array( __( 'Use Default', 'avia_framework' ) => '' ), 'px' ),
												'medium'	=> AviaHtmlHelper::number_array( 10, 40, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
												'small'		=> AviaHtmlHelper::number_array( 10, 40, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
												'mini'		=> AviaHtmlHelper::number_array( 10, 40, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' )
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
												'medium'	=> AviaHtmlHelper::number_array( 10, 40, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
												'small'		=> AviaHtmlHelper::number_array( 10, 40, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
												'mini'		=> AviaHtmlHelper::number_array( 10, 40, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' )
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
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_font_sizes' ), $template );
		
			
			/**
			 * Advanced Tab
			 * ============
			 */
			
			$c = array(
						array(	
							'type'				=> 'template',
							'template_id'		=> 'heading_tag',
							'theme_default'		=> 'h3',
							'context'			=> __CLASS__
						),
				
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Heading', 'avia_framework' ),
								'content'		=> $c 
							),
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'advanced_heading' ), $template );
			
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
							'type' 	=> 'select',
							'std' 	=> '',
							 'required'	=> array( 'link', 'not', '' ),
							'subtype'	=> array(
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
								'title'			=> __( 'Link Settings', 'avia_framework' ),
								'content'		=> $c 
							),
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'advanced_link' ), $template );
			
			
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
			extract(av_backend_icon($params)); // creates $font and $display_char if the icon was passed as param 'icon' and the font as 'font' 

			$inner  = "<div class='avia_iconbox avia_textblock avia_textblock_style'>";
			$inner .=		'<div ' . $this->class_by_arguments('position' ,$params['args']) . '>';
			$inner .=			'<span ' . $this->class_by_arguments( 'font', $font ) . '>';
			$inner .=				"<span data-update_with='icon_fakeArg' class='avia_iconbox_icon'>{$display_char}</span>";
			$inner .=			"</span>";
			$inner .=			"<div class='avia_iconbox_content_wrap'>";
			$inner .=				"<h4 class='avia_iconbox_title' data-update_with='title'>" . html_entity_decode( $params['args']['title'] ) . '</h4>';
			$inner .=				"<div class='avia_iconbox_content' data-update_with='content'>" . stripslashes( wpautop( trim( html_entity_decode( $params['content'] ) ) ) ) . '</div>';
			$inner .=			'</div>';
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

			$meta = aviaShortcodeTemplate::set_frontend_developer_heading_tag( $atts, $meta );

			$atts = shortcode_atts( array(
						'title'			=> 'Title', 
						'icon'			=> 'ue800', 
						'position'		=> 'left', 
						'link'			=> '', 
						'linktarget'	=> '', 
						'linkelement'	=> '', 
						'font'			=> '', 
						'boxed'			=> '',
						'color'			=> '', 
						'custom_bg'		=> '', 
						'custom_border'	=> '', 
						'custom_font'	=> '',
						'font_color'	=> '',
						'custom_title'	=> '',
						'icon_style'	=> '',
						'custom_content'		=> '',
						'custom_title_size'		=> '',
						'custom_content_size'	=> ''
				), $atts, $this->config['shortcode'] );



			extract( $atts );

			$display_char = av_icon( $icon, $font );
			$display_char_wrapper = array();

			if( $position == 'top' && empty( $boxed ) ) 
			{
				$position .= ' main_color';
			}
			
			if( $position != 'top' ) 
			{
				$boxed = '';
			}

			$link = AviaHelper::get_url( $link );
			$blank = AviaHelper::get_link_target( $linktarget );

			if( ! empty( $link ) )
			{
				$linktitle = $title;

				switch( $linkelement )
				{
					case 'both':
						if( $title ) 
						{
							$title = "<a href='{$link}' title='" . esc_attr( $linktitle ) . "' $blank>$linktitle</a>";
						}
						
						$display_char_wrapper['start'] = "a href='{$link}' title='"  . esc_attr($linktitle) . "' {$blank}";
						$display_char_wrapper['end'] = 'a';
						break;
					case 'only_icon':
						$display_char_wrapper['start'] = "a href='{$link}' title='" . esc_attr($linktitle) . "' {$blank}";
						$display_char_wrapper['end'] = 'a';
						break;
					default:
						if( $title ) 
						{
							$title = "<a href='{$link}' title='" . esc_attr( $linktitle ) . "' {$blank} >$linktitle</a>";
						}
						
						$display_char_wrapper['start'] = 'div';
						$display_char_wrapper['end'] = 'div';
						break;
				}
			}


			if( empty( $display_char_wrapper ) )
			{
				$display_char_wrapper['start'] = 'div';
				$display_char_wrapper['end'] = 'div';
			}

			$icon_html_styling = '';
			$title_styling = '';
			$content_styling = '';
			$content_class = '';

			if( $color == 'custom' )
			{
				$icon_html_styling .= ! empty( $custom_bg ) ? "background-color:{$custom_bg}; " : '';
				$icon_html_styling .= ! empty( $custom_border ) ? "border:1px solid {$custom_border}; " : '';
				$icon_html_styling .= ! empty( $custom_font ) ? "color:{$custom_font}; " : '';
			}

			if( $font_color == 'custom' )
			{
				$title_styling .= ! empty( $custom_title ) ? "color:{$custom_title}; " : '';
				$content_styling .= ! empty( $custom_content ) ? "color:{$custom_content}; " : '';

				if( ! empty( $content_styling ) )
				{
					$content_class	 = 'av_inherit_color';
				}
			}
			
			if( ! empty( $custom_title_size ) )
			{
				$title_styling .= "font-size:{$custom_title_size}px; ";
			}
			
			if( ! empty( $custom_content_size ) )
			{
				$content_styling .= "font-size:{$custom_content_size}px; ";
			}
			
			if( ! empty( $title_styling ) )
			{
				$title_styling = " style='{$title_styling}'" ;
			}
			
			if( ! empty( $icon_html_styling ) )
			{
				$icon_html_styling = " style='{$icon_html_styling}'" ;
			}
			
			if( ! empty( $content_styling ) )
			{
				$content_styling = " style='{$content_styling}'" ;
			}
			
			$meta['el_class'] .= ' ' . $icon_style;
				
			$icon_html = '<' . $display_char_wrapper['start'] . ' class="iconbox_icon heading-color" ' . $display_char . ' ' . $icon_html_styling . ' ></' . $display_char_wrapper['end'] . '>';

			// add blockquotes to the content
			$markup = avia_markup_helper( array( 'context' => 'entry', 'echo' => false, 'custom_markup' => $meta['custom_markup'] ) );
			$output  = '<article ' . $meta['custom_el_id'] . ' class="iconbox iconbox_' . $position . ' ' . $boxed . ' ' . $av_display_classes . ' ' . $meta['el_class'] . '" ' . $markup . '>';

			if( $position == 'left_content' || $position == 'right_content' )
			{
				$output .= $icon_html; 
				$icon_html = '';
			}

			$output .= 		'<div class="iconbox_content">';
			$output .= 			'<header class="entry-content-header">';
			$output .= 			$icon_html;

			$markup = avia_markup_helper( array( 'context' => 'entry_title', 'echo' => false, 'custom_markup' => $meta['custom_markup'] ) );


			$default_heading = ! empty( $meta['heading_tag'] ) ? $meta['heading_tag'] : 'h3';
			$args = array(
						'heading'		=> $default_heading,
						'extra_class'	=> $meta['heading_class']
					);

			$extra_args = array( $this, $atts, $content, 'title' );

			/**
			 * @since 4.5.5
			 * @return array
			 */
			$args = apply_filters( 'avf_customize_heading_settings', $args, __CLASS__, $extra_args );

			$heading = ! empty( $args['heading'] ) ? $args['heading'] : $default_heading;
			$css = ! empty( $args['extra_class'] ) ? $args['extra_class'] : $meta['heading_class'];

			if( $title ) 
			{
				$output .= 			"<{$heading} class='iconbox_content_title {$css} {$av_title_font_classes}' {$markup} {$title_styling}>{$title}</{$heading}>";
			}
			$output .= 			'</header>';

			$markup = avia_markup_helper( array( 'context' => 'entry_content', 'echo' => false, 'custom_markup' => $meta['custom_markup'] ) );
			
			$output .= 			"<div class='iconbox_content_container {$content_class} {$av_font_classes}' {$markup} {$content_styling}>";
			$output .=              ShortcodeHelper::avia_apply_autop( ShortcodeHelper::avia_remove_autop( $content ) );
			$output .= 			'</div>';

			$output .= 			'</div>';
			$output .= 		'<footer class="entry-footer"></footer>';
			$output .= '</article>';

			return $output;
		}

	}
}
