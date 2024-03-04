<?php
/**
 * Button Row
 *
 * Displays a set of buttons with links
 * Each button can be styled individually
 * 
 *
 * @author tinabillinger
 * @since 4.3
 */



if ( ! class_exists( 'avia_sc_buttonrow' ) ) 
{
    class avia_sc_buttonrow extends aviaShortcodeTemplate
    {
		
		/**
		 *
		 * @since 4.5.5
		 * @var array 
		 */
		protected $screen_options;
		
		/**
		 *
		 * @since 4.5.5
		 * @var array 
		 */
		protected $alignment;
		
		/**
		 *
		 * @since 4.5.5
		 * @var array 
		 */
		protected $spacing;
		
		/**
		 *
		 * @since 4.5.5
		 * @var array 
		 */
		protected $spacing_unit;
		
		/**
		 * 
		 * @since 4.5.5
		 * @param AviaBuilder $builder
		 */
		public function __construct( $builder ) 
		{
			$this->screen_options = array();
			$this->alignment = '';
            $this->spacing = '';
            $this->spacing_unit = '';
			
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
        public function shortcode_insert_button()
        {
			$this->config['version']		= '1.0';
            $this->config['self_closing']	= 'no';

            $this->config['name']			= __( 'Button Row', 'avia_framework' );
            $this->config['tab']			= __( 'Content Elements', 'avia_framework' );
            $this->config['icon']			= AviaBuilder::$path['imagesURL'] . 'sc-buttonrow.png';
            $this->config['order']			= 84;
            $this->config['target']			= 'avia-target-insert';
            $this->config['shortcode']		= 'av_buttonrow';
            $this->config['shortcode_nested'] = array( 'av_buttonrow_item' );
            $this->config['tooltip']		= __( 'Displays multiple buttons beside each other', 'avia_framework' );
            $this->config['preview']		= true;
			$this->config['disabling_allowed'] = true;
			$this->config['id_name']		= 'id';
			$this->config['id_show']		= 'yes';
			$this->config['alb_desc_id']	= 'alb_description';
        }

        public function extra_assets()
        {
            //load css
			wp_enqueue_style( 'avia-module-button', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/buttons/buttons.css', array( 'avia-layout' ), false );
            wp_enqueue_style( 'avia-module-buttonrow', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/buttonrow/buttonrow.css', array( 'avia-layout' ), false );
        }

        /**
         * Popup Elements
         *
         * If this function is defined in a child class the element automatically gets an edit button, that, when pressed
         * opens a modal window that allows to edit the element properties
         *
         * @return void
         */
        public function popup_elements()
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
								'template_id'	=> $this->popup_key( 'content_buttonrow' )
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
								'template_id'	=> $this->popup_key( 'styling_appearance' )
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
			
			$this->register_modal_group_templates();
			
			/**
			 * Content Tab
			 * ===========
			 */
			
			$c = array(
						array(
							'name'	=> __( 'Add/Edit Buttons', 'avia_framework' ),
							'desc'	=> __( 'Here you can add, remove and edit buttons.', 'avia_framework' ),
							'type'	=> 'modal_group',
							'id'	=> 'content',
							'modal_title'	=> __( 'Edit Button', 'avia_framework' ),
							'std'	=> array(
											array( 'label' => __( 'Click me', 'avia_framework' ), 'icon' => '4' ),
											array( 'label' => __( 'Call to Action', 'avia_framework' ), 'icon' => '5' ),
											array( 'label' => __( 'Click me', 'avia_framework' ), 'icon' => '6' ),
										),
							'subelements' => $this->create_modal()
						),
				
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_buttonrow' ), $c );
			
			
			/**
			 * Styling Tab
			 * ===========
			 */
			
			$c = array(
						array(	
							'name' 	=> __( 'Align Buttons', 'avia_framework' ),
							'desc' 	=> __( 'Choose the alignment of your buttons here', 'avia_framework' ),
							'id' 	=> 'alignment',
							'type' 	=> 'select',
							'std' 	=> 'center',
							'subtype'	=> array(
												__( 'Align Left', 'avia_framework' )	=> 'left',
												__( 'Align Center', 'avia_framework' )	=> 'center',
												__( 'Align Right', 'avia_framework' )	=> 'right',
											)
						),
				
						array(
							'name'	=> __( 'Space between buttons', 'avia_framework' ),
							'desc'	=> __( 'Define the space between the buttons. Leave blank for default space. Make sure you enter a valid positive number.', 'avia_framework' ),
							'id'	=> 'button_spacing',
							'container_class' => 'av_half',
							'type'	=> 'input',
							'std'	=> '5'
						),

						array(
							'name'	=> __( 'Unit', 'avia_framework' ),
							'desc'	=> __( 'Unit for the spacing', 'avia_framework' ),
							'id'	=> 'button_spacing_unit',
							'container_class' => 'av_half',
							'type'	=> 'select',
							'std'	=> 'px',
							'subtype'	=> array(
												__( 'px', 'avia_framework' )	=> 'px',
												__( '%', 'avia_framework' )		=> '%',
												__( 'em', 'avia_framework' )	=> 'em',
												__( 'rem', 'avia_framework' )	=> 'rem',
											)
						)
				
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_appearance' ), $c );
			
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
													$this->popup_key( 'modal_content_button' ),
													$this->popup_key( 'modal_advanced_link' )
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
													$this->popup_key( 'modal_styling_appearance' ),
													$this->popup_key( 'modal_styling_colors' )
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
							'name' 	=> __( 'Button Label', 'avia_framework' ),
							'desc' 	=> __( 'This is the text that appears on your button.', 'avia_framework' ),
							'id' 	=> 'label',
							'type' 	=> 'input',
							'std' => __( 'Click me', 'avia_framework' )
						),
				
						array(	
							'name' 	=> __( 'Button Icon', 'avia_framework' ),
							'desc' 	=> __( 'Should an icon be displayed at the left side of the button', 'avia_framework' ),
							'id' 	=> 'icon_select',
							'type' 	=> 'select',
							'std' 	=> 'yes',
							'subtype'	=> array(
												__( 'No Icon', 'avia_framework' )							=> 'no',
												__( 'Yes, display Icon to the left', 'avia_framework' )		=> 'yes' ,	
												__( 'Yes, display Icon to the right', 'avia_framework' )	=> 'yes-right-icon',
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
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_content_button' ), $template );
			
			$c = array(
						array(	
							'type'			=> 'template',
							'template_id'	=> 'linkpicker_toggle',
							'name'			=> __( 'Button Link?', 'avia_framework' ),
							'desc'			=> __( 'Where should your button link to?', 'avia_framework' ),
							'subtypes'		=> array( 'manually', 'single', 'taxonomy' ),
							'target_id'		=> 'link_target',
							'no_toggle'		=> true
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
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_advanced_link' ), $template );
			
			
			/**
			 * Styling Tab
			 * ===========
			 */
			
			$c = array(
						array(	
							'name' 	=> __( 'Button Size', 'avia_framework' ),
							'desc' 	=> __( 'Choose the size of your button here', 'avia_framework' ),
							'id' 	=> 'size',
							'type' 	=> 'select',
							'std' 	=> 'small',
							'subtype'	=> array(
												__( 'Small', 'avia_framework' )		=> 'small',
												__( 'Medium', 'avia_framework' )	=> 'medium',
												__( 'Large', 'avia_framework' )		=> 'large',
												__( 'X Large', 'avia_framework' )	=> 'x-large',
											)
						),
							
						array(	
							'name' 	=> __( 'Button Label display', 'avia_framework' ),
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
						)
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Appearance', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_styling_appearance' ), $template );
			
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
							'required'	=> array( 'color', 'equals', 'custom' )
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
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'modal_styling_colors' ), $template );

		}
				

        /**
         * Editor Sub Element - this function defines the visual appearance of an element that is displayed within a modal window and on click opens its own modal window
         * Works in the same way as Editor Element
         * @param array $params this array holds the default values for $content and $args.
         * @return $params the return array usually holds an innerHtml key that holds item specific markup.
         */
        public function editor_sub_element( $params )
        {
			/**
			 * Fix a bug in 4.7 and 4.7.1 renaming option id (no longer backwards comp.) - can be removed in a future version again
			 */
			if( isset( $params['args']['linktarget'] ) )
			{
				$params['args']['link_target'] = $params['args']['linktarget'];
			}
			
            $template = $this->update_template( 'label', __( 'Button', 'avia_framework' ) . ': {{label}}' );

            extract( av_backend_icon( $params ) ); // creates $font and $display_char if the icon was passed as param 'icon' and the font as 'font'

            $params['innerHtml'] = '';
            $params['innerHtml'] .= "<div class='avia_title_container'>";
            $params['innerHtml'] .=		'<span ' . $this->class_by_arguments( 'font', $font ) . '>';
            $params['innerHtml'] .=			"<span data-update_with='icon_fakeArg' class='avia_tab_icon'>{$display_char}</span>";
            $params['innerHtml'] .=		'</span>';
            $params['innerHtml'] .= "<span {$template} >" . __( 'Button', 'avia_framework' ) . ": {$params['args']['label']}</span></div>";

            return $params;

        }
		
		/**
		 * 
		 * @since 4.5.5
		 * @param string $shortcode
		 * @return boolean
		 */
		public function is_nested_self_closing( $shortcode ) 
		{
			if( in_array( $shortcode, $this->config['shortcode_nested'] ) )
			{
				return true;
			}
				
			return false;
		}

		/**
         * Frontend Shortcode Handler
         *
         * @param array $atts array of attributes
         * @param string $content text within enclosing form of shortcode element
         * @param string $shortcodename the shortcode found, when == callback name
         * @return string $output returns the modified html string
         */
        public function shortcode_handler( $atts, $content = '', $shortcodename = '', $meta = '' )
        {
	        
            $this->screen_options = AviaHelper::av_mobile_sizes( $atts );
			
			$this->alignment = '';
            $this->spacing = '';
            $this->spacing_unit = '';

            extract( $this->screen_options );	//return $av_font_classes, $av_title_font_classes and $av_display_classes

            extract( shortcode_atts( array(
							'alignment'				=> 'center',
							'button_spacing'		=> '5',
							'button_spacing_unit'	=> 'px'
						), $atts, $this->config['shortcode'] ) );

            $this->alignment = $alignment;
            $this->spacing = is_numeric( $button_spacing ) && $button_spacing > 0 ? $button_spacing : '';
            $this->spacing_unit = $button_spacing_unit;

            $output = '';
			$output .=	"<div {$meta['custom_el_id']} class='avia-buttonrow-wrap avia-buttonrow-{$this->alignment} {$av_display_classes} {$meta['el_class']}'>";
            $output .=		ShortcodeHelper::avia_remove_autop( $content, true );
            $output .=	'</div>';

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
        function av_buttonrow_item( $atts, $content = '', $shortcodename = '' )
        {
			/**
			 * Fixes a problem when 3-rd party plugins call nested shortcodes without executing main shortcode  (like YOAST in wpseo-filter-shortcodes)
			 */
			if( empty( $this->screen_options ) )
			{
				return '';
			}
			
			/**
			 * Fix a bug in 4.7 and 4.7.1 renaming option id (no longer backwards comp.) - can be removed in a future version again
			 */
			if( isset( $atts['linktarget'] ) )
			{
				$atts['link_target'] = $atts['linktarget'];
			}
			
            extract( $this->screen_options ); //return $av_font_classes, $av_title_font_classes and $av_display_classes
			
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
//										'btn_color_font_hover'	=> '#ffffff',
//										'btn_custom_font_hover'	=> '#ffffff'
				
									), $atts, 'av_buttonrow_item' );

            
			$style = '';
			$style_hover = '';
			$background_hover = '';
			$spacing = $this->spacing;
            $spacing_unit = $this->spacing_unit;
			
			$display_char = av_icon( $atts['icon'], $atts['font'] );
            $extraClass = $atts['icon_hover'] ? 'av-icon-on-hover' : '';

            if( $atts['icon_select'] == 'yes' ) 
			{
				$atts['icon_select'] = 'yes-left-icon';
			}

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

            if( ! empty( $spacing ) )
			{
                $atts['margin-bottom'] = $spacing . $spacing_unit;
                $atts['margin-left'] = $spacing . $spacing_unit;
                $atts['margin-right'] = $spacing . $spacing_unit;

                $style .= AviaHelper::style_string( $atts, 'margin-bottom' );

                if( $this->alignment == 'left' ) 
				{
                    $style .= AviaHelper::style_string( $atts, 'margin-right', 'margin-right', '' );
                }

                if( $this->alignment == 'right' ) 
				{
                    $style .= AviaHelper::style_string( $atts, 'margin-left' );
                }

                if( $this->alignment == 'center' ) 
				{
                    $spacingval = round( $spacing / 2 );
                    $atts['margin-left'] = $spacingval;
                    $atts['margin-right'] = $spacingval;
					
                    $style .= AviaHelper::style_string( $atts, 'margin-left', 'margin-left', $spacing_unit );
                    $style .= AviaHelper::style_string( $atts, 'margin-right', 'margin-right', $spacing_unit );
                }
            }

            $style = AviaHelper::style_string( $style );

			$blank = AviaHelper::get_link_target( $atts['link_target'] );
            $link = trim( AviaHelper::get_url( $atts['link'] ) );
            $link = ( in_array( $link, array( 'http://', 'https://', 'manually' ) ) ) ? '' : $link;
			
			$title_attr = ! empty( $atts['title_attr'] ) && empty( $atts['label_display'] ) ? 'title="' . esc_attr( $atts['title_attr'] ) . '"' : '';
			
			$data = '';
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
					
            $content_html = '';
            if( 'yes-left-icon' == $atts['icon_select'] ) 
			{
				$content_html .= "<span class='avia_button_icon avia_button_icon_left ' {$display_char}></span>";
			}
			
            $content_html .= "<span class='avia_iconbox_title' >" . $atts['label'] . "</span>";
			
            if( 'yes-right-icon' == $atts['icon_select'] ) 
			{
				$content_html .= "<span class='avia_button_icon avia_button_icon_right' {$display_char}></span>";
			}

            $output = '';
            $output .=	"<a href='{$link}' {$data} class='avia-button {$extraClass} " . $this->class_by_arguments( 'icon_select, size', $atts, true ) . "' {$blank} {$style} {$title_attr}>";
            $output .=		$content_html;
			$output .=		$background_hover;
            $output .=	'</a>';

            return $output;

        }
    }
}
