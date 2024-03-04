<?php
/**
 * Class defines option templates for ALB elements
 * These templates replace an element in the options array.
 * Nested templates are supported.
 * 
 * Basic structure, not all arguments are supported by every template element (example):
 * 
 *			array(	
 *						'type'					=> 'template',
 *						'template_id'			=> 'date_query',
 *						'required'				=> ! isset() | array()     //	used for all elements
 *						'template_required'		=> array( 
 *														0	=> array( 'slide_type', 'is_empty_or', 'entry-based' )
 *													),
 *						'content'				=> ! isset() | array( array of elements - can be templates also )
 *						'templates_include'		=> ! isset() | array( list of needed subtemplates ),
 *						'subtype'				=> mixed					//	allows to change subtype e.g. for select boxes
 *						'args'					=> mixed					//	e.g. shortcode class
 *													
 *					),
 * 
 * Also allows to store HTML code snippets (can be used in editor elements like e.g. 'element streches/fullwidth').
 * 
 * @added_by Günter
 * @since 4.5.7.1
 * @since 4.6.4			supports dynamic added templates
 */

if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly

if( ! class_exists( 'Avia_Popup_Templates' ) )
{
	
	class Avia_Popup_Templates
	{
		
		/**
		 * Holds the instance of this class
		 * 
		 * @since 4.5.7.1
		 * @var Avia_Popup_Templates 
		 */
		static private $_instance = null;
		
		/**
		 * Array of dynamic templates added on the fly
		 *		template_id  =>  array();
		 * 
		 * @since 4.6.4
		 * @var array
		 */
		protected $dynamic_templates;
		
		/**
		 * Array of HTML codesnippets
		 * 
		 * @since 4.6.4
		 * @var array 
		 */
		protected $html_templates;


		/**
		 * Return the instance of this class
		 * 
		 * @since 4.5.7.1
		 * @return Avia_Popup_Templates
		 */
		static public function instance()
		{
			if( is_null( Avia_Popup_Templates::$_instance ) )
			{
				Avia_Popup_Templates::$_instance = new Avia_Popup_Templates();
			}
			
			return Avia_Popup_Templates::$_instance;
		}
		
		/**
		 * @since 4.5.7.1
		 */
		protected function __construct()
		{
			$this->dynamic_templates = array(); 
			$this->html_templates = array();
			
			$this->set_predefined_html_templates();
			
			/**
			 * Allow 3-rd party to register own templates
			 * 
			 * @since 4.6.4
			 * @param Avia_Popup_Templates $this
			 */
			do_action( 'ava_popup_register_dynamic_templates', $this );
		}
		
		/**
		 * @since 4.6.4
		 */
		public function __destruct() 
		{
			unset( $this->dynamic_templates );
			unset( $this->html_templates );
		}
		
		/**
		 * Main entry function:
		 * ====================
		 * 
		 * Replaces predefined templates for easier maintainnance of code
		 * Recursive function. Also supports nested templates.
		 * 
		 * @since 4.5.6.1
		 * @param array $elements
		 * @return array
		 */
		public function replace_templates( array $elements )
		{
			if( empty( $elements ) )
			{
				return $elements;
			}
			
			$start_check = true;
			
			while( $start_check )
			{
				$offset = 0;
				foreach( $elements as $key => $element ) 
				{
					if( isset( $element['subelements'] ) )
					{
						$elements[ $key ]['subelements'] = $this->replace_templates( $element['subelements'] );
					}
					
					if( ! isset( $element['type'] ) || $element['type'] != 'template' )
					{
						$offset++;
						if( $offset >= count( $elements ) )
						{
							$start_check = false;
							break;
						}
						continue;
					}

					$replace = $this->get_template( $element );
					if( false === $replace )
					{
						$offset++;
						if( $offset >= count( $elements ) )
						{
							$start_check = false;
							break;
						}
						continue;
					}

					array_splice( $elements, $offset, 1, $replace );
					break;
				}
			}
			
			return $elements;
		}

		/**
		 * Returns the array elements to replace the template array element.
		 * Dynamic templates override predefined.
		 * 
		 * @since 4.5.6.1
		 * @param array $element
		 * @param boolean
		 * @return array|false
		 */
		protected function get_template( array $element, $parent = false )
		{
			if( ! isset( $element['template_id'] ) )
			{
				return false;
			}
			
			if( array_key_exists( $element['template_id'], $this->dynamic_templates ) )
			{
				if( $parent === false || ! method_exists( $this, $element['template_id'] ) )
				{
					$result = $this->get_dynamic_template( $element );
					return $result;
				}
			}
			
			if( ! method_exists( $this, $element['template_id'] ) )
			{
				return false;
			}
			
			$result = call_user_func_array( array( $this, $element['template_id'] ), array( $element ) );
			return $result;
		}
		
		/**
		 * Returns if a template exists
		 * 
		 * @since 4.6.4
		 * @param string $template_id
		 * @return string|false				false | 'predefined' | 'dynamic' | 'dynamic and fixed'
		 */
		public function template_exists( $template_id )
		{
			$exist = false;
			
			if( array_key_exists( $template_id, $this->dynamic_templates ) )
			{
				$exist = 'dynamic';
			}
			
			if( method_exists( $this, $template_id ) )
			{
				$exist = false === $exist ? 'predefined' : 'dynamic and predefined';
			}
			
			return $exist;
		}

		/**
		 * Add a dynamic template
		 * 
		 * @since 4.6.4
		 * @param string $template_id
		 * @param array $template_data
		 * @param boolean $ignore_debug_notice
		 */
		public function register_dynamic_template( $template_id, array $template_data, $ignore_debug_notice = false )
		{
			if( defined( 'WP_DEBUG' ) && WP_DEBUG && false === $ignore_debug_notice )
			{
				$exist = $this->template_exists( $template_id );
				if( false !== $exist )
				{
					error_log( sprintf( __( 'Already existing template %1$s is overwritten (%2$s). Make sure this is intended.', 'avia_framework' ), $template_id, $exist )  );
				}
			}
			
			$this->dynamic_templates[ $template_id ] = $template_data;
		}
		
		/**
		 * Adds a template to the list of available templates.
		 * 
		 * @since 4.6.4
		 * @param string $template_id
		 * @param mixed $template_data
		 * @param boolean $overwrite
		 * @return boolean
		 */
		public function register_html_template( $template_id, $template_data, $overwrite = false )
		{
			if( array_key_exists( $template_id, $this->html_templates ) && ( false === $overwrite ) )
			{
				return false;
			}
			
			$this->html_templates[ $template_id ] = $template_data;
			return true;
		}
		
		/**
		 * Returns the stored content. If template does not exist '' is returned.
		 * 
		 * @since 4.6.4
		 * @param string $template_id
		 * @return string
		 */
		public function get_html_template( $template_id )
		{
			return isset( $this->html_templates[ $template_id ] ) ? $this->html_templates[ $template_id ] : '';
		}

		/**
		 * Removes a registered dynamic template
		 * 
		 * @since 4.6.4
		 * @param string $template_id
		 * @return boolean
		 */
		public function deregister_dynamic_template( $template_id )
		{
			if( ! isset( $this->dynamic_templates[ $template_id ] ) )
			{
				return false;
			}
			
			unset( $this->dynamic_templates[ $template_id ] );
			return true;
		}

		/**
		 * Return content of template.
		 * 
		 * if 'templates_include'	=> add content of all templates
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array|false
		 */
		protected function get_dynamic_template( array $element )
		{
			$template_content = $this->dynamic_templates[ $element['template_id'] ];
			
			$result = $this->get_templates_to_include( $template_content, $element );
			if( false !== $result )
			{
				return $result;
			}
			
			return $template_content;
		}
		
		/**
		 * Returns all templates to include
		 * 
		 * @since 4.6.4
		 * @param array $template
		 * @param array|null $parent_template
		 * @return array|false
		 */
		protected function get_templates_to_include( array $template, $parent_template = null )
		{
			if( empty( $template['templates_include'] ) )
			{
				return false;
			}
			
			$attr = is_null( $parent_template ) ? $template : $parent_template;
			unset( $attr['template_id'] );
			unset( $attr['templates_include'] );
			
			$result = array();
					
			foreach( $template['templates_include'] as $sub_template ) 
			{
				if( false !== $this->template_exists( $sub_template ) )
				{
					$temp = array(	
									'template_id'   => $sub_template,
								);		
					
					foreach( $attr as $key => $value ) 
					{
						$temp[ $key ] = $value;
					}
					
					$result[] = $temp;
				}
			}
			
			return $result;
		}
		
		/**
		 * Returns a toggle container section.
		 * Content is filled from
		 *		- 'content'
		 *		- 'templates_include'
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array|false
		 */
		protected function toggle_container( array $element )
		{
			$title = ! empty( $element['title'] ) ? $element['title'] : __( 'Click to view content', 'avia_framework' );
			$open = array(
						'type'          => 'toggle_container',
						'nodescription' => true
					);
			
			$close = array(
						'type'          => 'toggle_container_close',
						'nodescription' => true
					);
			
			$content = false;
			if( ! empty( $element['content'] ) )
			{
				$content = $element['content'];
			}
			else if( ! empty( $element['templates_include'] ) )
			{
				$content = $this->get_templates_to_include( $element );
			}
			
			if( empty( $content ) )
			{
				return false;
			}
			
			$result = array_merge( array( $open ), $content, array( $close ) );
			return $result;
		}
		
		/**
		 * Returns a toggle section.
		 * Content is filled from
		 *		- 'content'
		 *		- 'templates_include'
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array|false
		 */
		protected function toggle( array $element )
		{
			$title = ! empty( $element['title'] ) ? $element['title'] : __( 'Click to view content', 'avia_framework' );
			$class = ! empty( $element['container_class'] ) ? $element['container_class'] : '';
			
			$required = ! empty( $element['required'] ) ? $element['required'] : array();
			
			$open = array(
						'type'          => 'toggle',
						'name'          => $title,
						'nodescription' => true,
						'container_class'	=> $class,
						'required'		=> $required
					);
			
			$close = array(
						'type'          => 'toggle_close',
						'nodescription' => true,
					);
			
			$content = false;
			if( ! empty( $element['content'] ) )
			{
				$content = $element['content'];
			}
			else if( ! empty( $element['templates_include'] ) )
			{
				$content = $this->get_templates_to_include( $element );
			}
			
			if( empty( $content ) )
			{
				return false;
			}
			
			$result = array_merge( array( $open ), $content, array( $close ) );
			return $result;
		}
		
		/**
		 * Returns a font sizes icon switcher section.
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array
		 */
		protected function font_sizes_icon_switcher( array $element )
		{
			
			if( isset( $element['subtype'] ) && is_array( $element['subtype'] ) )
			{
				$subtype = $element['subtype'];
			}
			else
			{
				$subtype = array(
							'default'	=> AviaHtmlHelper::number_array( 8, 120, 1, array( __( 'Use Default', 'avia_framework' ) => '' ), 'px' ),
							'medium'	=> AviaHtmlHelper::number_array( 8, 120, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
							'small'		=> AviaHtmlHelper::number_array( 8, 120, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' ),
							'mini'		=> AviaHtmlHelper::number_array( 8, 120, 1, array( __( 'Use Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' )
						);
			}
			
			if( isset( $element['id_sizes'] ) && is_array( $element['id_sizes'] ) )
			{
				$id_sizes = $element['id_sizes'];
			}
			else
			{
				$id_sizes = array(
							'default'	=> 'size',
							'medium'	=> 'av-medium-font-size',
							'small'		=> 'av-small-font-size',
							'mini'		=> 'av-mini-font-size'
						);
			}
			
			if( isset( $element['desc_sizes'] ) && is_array( $element['desc_sizes'] ) )
			{
				$desc_sizes = $element['desc_sizes']; 
			}
			else
			{
				$desc_sizes = array(
							'default'	=> __( 'Font Size (Default)', 'avia_framework' ),
							'medium'	=> __( 'Font Size for medium sized screens (between 768px and 989px - eg: Tablet Landscape)', 'avia_framework' ),
							'small'		=> __( 'Font Size for small screens (between 480px and 767px - eg: Tablet Portrait)', 'avia_framework' ),
							'mini'		=> __( 'Font Size for very small screens (smaller than 479px - eg: Smartphone Portrait)', 'avia_framework' ),
						);
			}
			
			$titles = array(
							'default'	=> __( 'Default', 'avia_framework' ),
							'medium'	=> __( 'Tablet Landscape', 'avia_framework' ),
							'small'		=> __( 'Tablet Portrait', 'avia_framework' ),
							'mini'		=> __( 'Mobile', 'avia_framework' ),
						);
			
			$icons = array(
							'default'	=> 'desktop',
							'medium'	=> 'tablet-landscape',
							'small'		=> 'tablet-portrait',
							'mini'		=> 'mobile'
						);
			
			
			
			$template = array(
							array(
								'type' 	=> 'icon_switcher_container',
								'name'  => ! empty( $element['name'] ) ? $element['name'] : '',
								'desc' 	=> ! empty( $element['desc'] ) ? $element['desc'] : '',
//								'icon'  => __( 'Content', 'avia_framework' ),
								'nodescription' => true,
								'required'	=> isset( $element['required'] ) ? $element['required'] : array()
							),	
						
						);
			
			foreach( $id_sizes as $size => $id ) 
			{
				$template[] = array(
								'type' 	=> 'icon_switcher',
								'name'	=> $titles[ $size ],
								'icon'	=> $icons[ $size ],
								'nodescription' => true
							);
				
				$template[] = array(	
								'name'	=> $desc_sizes[ $size ],
								'desc'	=> __( 'Size of the text in px', 'avia_framework' ),
								'id'	=> $id_sizes[ $size],
								'type'	=> 'select',
								'subtype'	=> $subtype[ $size],
								'std'	=> ''
							);
				
				$template[] = array(
								'type' 	=> 'icon_switcher_close', 
								'nodescription' => true
						);
			}
			
			$template[] = array(
								'type' 	=> 'icon_switcher_container_close', 
								'nodescription' => true
							);
			
			return $template;
		}
		
		/**
		 * Returns a columns count icon switcher section.
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array
		 */
		protected function columns_count_icon_switcher( array $element )
		{
			if( isset( $element['heading'] ) && is_array( $element['heading'] ) )
			{
				$heading = $element['heading'];
			}
			else
			{
				$info  = __( 'Set the column count for this element, based on the device screensize.', 'avia_framework' ) . '<br/><small>';
				$info .= __( 'Please note that changing the default will overwrite any individual &quot;landscape&quot; width settings. Each item will have the same width', 'avia_framework' ) . '</small>';
				
				$heading = array(
								'name' 	=> __( 'Element Columns', 'avia_framework' ),
								'desc' 	=> $info,
								'type' 	=> 'heading',
								'description_class' => 'av-builder-note av-neutral',
							);
			}
			
			if( isset( $element['subtype'] ) && is_array( $element['subtype'] ) )
			{
				$subtype = $element['subtype'];
			}
			else
			{
				$responsive = array(
									__( 'Use Default', 'avia_framework' )	=> '',
									__( '1 Column', 'avia_framework' )		=> '1',
									__( '2 Columns', 'avia_framework' )		=> '2',
									__( '3 Columns', 'avia_framework' )		=> '3',
									__( '4 Columns', 'avia_framework' )		=> '4'
								);
				
				$subtype = array(
							'default'	=> array(
												__( 'Automatic, based on screen width', 'avia_framework' )	=> 'flexible',
												__( '2 Columns', 'avia_framework' )	=> '2',
												__( '3 Columns', 'avia_framework' )	=> '3',
												__( '4 Columns', 'avia_framework' )	=> '4',
												__( '5 Columns', 'avia_framework' )	=> '5',
												__( '6 Columns', 'avia_framework' )	=> '6'
											),
							'medium'	=> $responsive,		
							'small'		=> $responsive,	
							'mini'		=> $responsive
						);
			}
			
			if( isset( $element['std'] ) && is_array( $element['std'] ) )
			{
				$std = $element['std'];
			}
			else
			{
				$std = array(
							'default'	=> 'flexible',
							'medium'	=> '',
							'small'		=> '',
							'mini'		=> ''
						);
			}
			
			if( isset( $element['id_sizes'] ) && is_array( $element['id_sizes'] ) )
			{
				$id_sizes = $element['id_sizes'];
			}
			else
			{
				$id_sizes = array(
							'default'	=> 'columns',
							'medium'	=> 'av-medium-columns',
							'small'		=> 'av-small-columns',
							'mini'		=> 'av-mini-columns'
						);
			}
			
			if( isset( $element['desc_sizes'] ) && is_array( $element['desc_sizes'] ) )
			{
				$desc_sizes = $element['desc_sizes']; 
			}
			else
			{
				$desc_sizes = array(
							'default'	=> __( 'Column count (Default)', 'avia_framework' ),
							'medium'	=> __( 'Column count for medium sized screens (between 768px and 989px - eg: Tablet Landscape)', 'avia_framework' ),
							'small'		=> __( 'Column count for small screens (between 480px and 767px - eg: Tablet Portrait)', 'avia_framework' ),
							'mini'		=> __( 'Column count for very small screens (smaller than 479px - eg: Smartphone Portrait)', 'avia_framework' ),
						);
			}
			
			$titles = array(
							'default'	=> __( 'Default', 'avia_framework' ),
							'medium'	=> __( 'Tablet Landscape', 'avia_framework' ),
							'small'		=> __( 'Tablet Portrait', 'avia_framework' ),
							'mini'		=> __( 'Mobile', 'avia_framework' ),
						);
			
			$icons = array(
							'default'	=> 'desktop',
							'medium'	=> 'tablet-landscape',
							'small'		=> 'tablet-portrait',
							'mini'		=> 'mobile'
						);
			
			$template = array();
							
			if( ! empty( $heading ) )
			{
				$template[] = $heading;
			}
			
			$template[] = array(
								'type' 	=> 'icon_switcher_container',
								'name'  => ! empty( $element['name'] ) ? $element['name'] : '',
								'desc' 	=> ! empty( $element['desc'] ) ? $element['desc'] : '',
//								'icon'  => __( 'Content', 'avia_framework' ),
								'nodescription' => true,
								'required'	=> isset( $element['required'] ) ? $element['required'] : array()
							);
			
			
			foreach( $id_sizes as $size => $id ) 
			{
				$template[] = array(
								'type' 	=> 'icon_switcher',
								'name'	=> $titles[ $size ],
								'icon'	=> $icons[ $size ],
								'nodescription' => true
							);
				
				$template[] = array(	
								'name'	=> $desc_sizes[ $size ],
								'desc'	=> __( 'How many columns do you want to use', 'avia_framework' ),
								'id'	=> $id_sizes[ $size ],
								'type'	=> 'select',
								'subtype'	=> $subtype[ $size ],
								'std'	=> $std[ $size ],
							);
				
				$template[] = array(
								'type' 	=> 'icon_switcher_close', 
								'nodescription' => true
						);
			}
			
			$template[] = array(
								'type' 	=> 'icon_switcher_container_close', 
								'nodescription' => true
							);
			
			return $template;
		}
		
		/**
		 * Returns a screen options toggle section.
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array
		 */
		protected function screen_options_toggle( array $element )
		{

			$screen = $this->screen_options_tab( $element, false );
			
			$template = array(
							array(
								'type'          => 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Responsive', 'avia_framework' ),
								'content'		=> $screen,
								'nodescription'	=> true
							)
						);
			
			return $template;
		}
		
		/**
		 * Returns a screen options toggle for columns.
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array
		 */
		protected function columns_visibility_toggle( array $element )
		{
			$desc  = __( 'Set the visibility for this element, based on the device screensize.', 'avia_framework' ) . '<br><small>';
			$desc .= __( 'In order to prevent breaking the layout it is only possible to change the visibility settings for columns once they take up the full screen width, which means only on mobile devices', 'avia_framework' ) . '</small>';

			$c = array(
						
						array(
							'name' 	=> __( 'Element Visibility', 'avia_framework' ),
							'desc' 	=> $desc,
							'type' 	=> 'heading',
							'description_class' => 'av-builder-note av-neutral',
						),
								
						array(	
							'name' 	=> __( 'Mobile display', 'avia_framework' ),
							'desc' 	=> __( 'Display settings for this element when viewed on smaller screens', 'avia_framework' ),
							'id' 	=> 'mobile_display',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype' => array(	
											__( 'Always display', 'avia_framework' )			=> '',
											__( 'Hide on mobile devices', 'avia_framework' )	=> 'av-hide-on-mobile',
										)
						)
				);
			
			$template = array(
							array(	
								'type'			=> 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Responsive', 'avia_framework' ),
								'content'		=> $c 
							),
					);
			
			return $template;
		}
		
		
		
		/**
		 * Returns a developer options toggle section.
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array
		 */
		protected function developer_options_toggle( array $element )
		{
			$dev = array();
			$shortcode = isset( $element['args']['sc'] ) && $element['args']['sc'] instanceof aviaShortcodeTemplate ? $element['args']['sc'] : null;
			if( is_null( $shortcode ) )
			{
				return $dev;
			}
			
			$nested = isset( $element['args']['nested'] ) ? $element['args']['nested'] : '';
			$visible = $shortcode->get_developer_elements( $dev, $nested );
			if( empty( $dev ) )
			{
				return $dev;
			}
			
			$template = array(
							array(
								'type'          => 'template',
								'template_id'	=> 'toggle',
								'title'			=> __( 'Developer Settings', 'avia_framework' ),
								'content'		=> $dev,
								'nodescription'	=> true,
								'container_class'	=> $visible
							)
						);
			
			return $template;
		}
		
		/**
		 * Element Disabled In Performance Tab Template 
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array
		 */
		protected function element_disabled( array $element )
		{
			$default = __( 'This element is disabled in your theme options. You can enable it in Enfold &raquo; Performance', 'avia_framework' );
			$anchor = ! empty( $element['args']['anchor'] ) ? trim( $element['args']['anchor'] ) : 'goto_performance';
			
			$desc  = ! empty( $element['args']['desc'] ) ? trim( $element['args']['desc'] ) : $default;
			$desc .= '<br/><br/><a target="_blank" href="' . admin_url( 'admin.php?page=avia#' . $anchor ) . '">' . __( 'Enable it here', 'avia_framework' ) . '</a><br/><br/>';
			
			$template = array(
							array(
								'name' 	=> __( 'Element disabled', 'avia_framework' ),
								'desc' 	=> $desc,
								'type' 	=> 'heading',
								'description_class' => 'av-builder-note av-error',
							)
						
				);
			
			return $template;
		}
		
		
		
		/**
		 * Video Template 
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array
		 */
		protected function avia_builder_post_type_option( array $element )
		{
			$desc = __( "Select which post types should be used. Note that your taxonomy will be ignored if you do not select an assign post type. If yo don't select post type all registered post types will be used", 'avia_framework' ); 

			$required = isset( $element['required'] ) && is_array( $element['required'] ) ? $element['required'] : array();
			
			$template = array(
							array(
								'name' 	=> __( 'Select Post Type', 'avia_framework' ),
								'desc' 	=> $desc,
								'id' 	=> 'post_type',
								'type' 	=> 'select',
								'std' 	=> '',
								'multiple'	=> 6,
								'required'	=> $required,
								'subtype'	=> AviaHtmlHelper::get_registered_post_type_array()
							)
				);
			
			return $template;
		}
		
		
		/**
		 * Linkpicker Template 
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array
		 */
		protected function linkpicker_toggle( array $element )
		{
			$id = isset( $element['id'] ) ? $element['id'] : 'link';
			$name = ! empty( $element['name'] ) ? $element['name'] : __( 'Text Link?', 'avia_framework' );
			$desc = ! empty( $element['desc'] ) ? $element['desc'] : __( 'Apply  a link to the text?', 'avia_framework' );
			$std = ! empty( $element['std'] ) ? $element['std'] : '';
			$required = ! empty( $element['required'] ) ? $element['required'] : array();
			$link_required = ! empty( $element['link_required'] ) ? $element['link_required'] : array( $id, 'not', '' );
			$target_id = isset( $element['target_id'] ) ? $element['target_id'] : 'linktarget';
			$target_std = isset( $element['target_std'] ) ? $element['target_std'] : '';
			
			$subtype = array();
			if( isset( $element['subtype'] ) && is_array( $element['subtype'] ) )
			{
				$subtype = $element['subtype'];
			}
			else
			{
				$subtype_keys = ! empty( $element['subtypes'] ) ? $element['subtypes'] : array( 'no', 'manually', 'single', 'taxonomy' );
			
				foreach( $subtype_keys as $key ) 
				{
					switch( $key )
					{
						case 'no':
							$subtype[ __( 'No Link', 'avia_framework' ) ] = '';
							break;
						case 'default':
							$subtype[ __( 'Use Default Link', 'avia_framework' ) ] = 'default';
							break;
						case 'manually':
							$subtype[ __( 'Set Manually', 'avia_framework' ) ] = 'manually';
							break;
						case 'single':
							$subtype[ __( 'Single Entry', 'avia_framework' ) ] = 'single';
							break;
						case 'taxonomy':
							$subtype[ __( 'Taxonomy Overview Page', 'avia_framework' ) ] = 'taxonomy';
							break;
						case 'lightbox':
							$subtype[ __( 'Open in Lightbox', 'avia_framework' ) ] = 'lightbox';
							break;
						default:
							break;
					}
				}
			}
			
			$c = array(
						array(
							'name'		=> $name,
							'desc'		=> $desc,
							'id'		=> $id,
							'type'		=> 'linkpicker',
							'std'		=> $std,
							'fetchTMPL'	=> true,
							'required'	=> $required,
							'subtype'	=> $subtype
						)
				);
			
			if( ! isset( $element['no_target'] ) || true !== $element['no_target'] )
			{
				$c[] = array(
							'name' 	=> __( 'Open in new window', 'avia_framework' ),
							'desc' 	=> __( 'Do you want to open the link in a new window', 'avia_framework' ),
							'id' 	=> $target_id,
							'type' 	=> 'select',
							'std' 	=> $target_std,
							'required'	=> $link_required,
							'subtype'	=> AviaHtmlHelper::linking_options()
						);
			}
			
			if( isset( $element['no_toggle'] ) && true === $element['no_toggle'] )
			{
				$template = $c;
			}
			else
			{
				$template = array(
								array(	
									'type'			=> 'template',
									'template_id'	=> 'toggle',
									'title'			=> __( 'Link Settings', 'avia_framework' ),
									'content'		=> $c 
								),
					);
			}
			
			return $template;
		}
		
		
		/**
		 * Video Template 
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array
		 */
		protected function video( array $element )
		{
			$text = '';
			
			//if self hosted is disabled
			if( avia_get_option( 'disable_mediaelement' ) == 'disable_mediaelement' )
			{
				$text = __( 'Please link to an external video by URL', 'avia_framework' ) . '<br/><br/>' .
						__( 'A list of all supported Video Services can be found on', 'avia_framework' ) .
						" <a target='_blank' href='http://codex.wordpress.org/Embeds#Okay.2C_So_What_Sites_Can_I_Embed_From.3F'>WordPress.org</a>. Youtube videos will display additional info like title, share link, related videos, ...<br/><br/>" .
						__( 'Working examples:', 'avia_framework' ) . '<br/>' .
						'<strong>https://vimeo.com/1084537</strong><br/>' .
						'<strong>https://www.youtube.com/watch?v=G0k3kHtyoqc</strong><br/><br/>'.
						'<strong class="av-builder-note">' . __( 'Using self hosted videos is currently disabled. You can enable it in Enfold &raquo; Performance', 'avia_framework' ) . '</strong><br/>';

			}
			//if youtube/vimeo is disabled
			else if( avia_get_option( 'disable_video' ) == 'disable_video' )
			{
				$text = __( 'Either upload a new video or choose an existing video from your media library', 'avia_framework' ) . '<br/><br/>'.
						__( 'Different Browsers support different file types (mp4, ogv, webm). If you embed an example.mp4 video the video player will automatically check if an example.ogv and example.webm video is available and display those versions in case its possible and necessary','avia_framework' ) . '<br/><br/><strong class="av-builder-note">' .
						__( 'Using external services like Youtube or Vimeo is currently disabled. You can enable it in Enfold &raquo; Performance', 'avia_framework' ) . '</strong><br/>';

			}
			//all video enabled
			else
			{
				$text = __( 'Either upload a new video, choose an existing video from your media library or link to a video by URL', 'avia_framework' ) . '<br/><br/>'.
						__( 'A list of all supported Video Services can be found on', 'avia_framework' ).
						" <a target='_blank' href='http://codex.wordpress.org/Embeds#Okay.2C_So_What_Sites_Can_I_Embed_From.3F'>WordPress.org</a>. YouTube videos will display additional info like title, share link, related videos, ...<br/><br/>".
						__( 'Working examples, in case you want to use an external service:', 'avia_framework' ) . '<br/>'.
						'<strong>https://vimeo.com/1084537</strong><br/>' .
						'<strong>https://www.youtube.com/watch?v=G0k3kHtyoqc</strong><br/><br/>' .
						'<strong>'.__( 'Attention when using self hosted HTML 5 Videos', 'avia_framework' ) . ':</strong><br/>' .
						__( 'Different Browsers support different file types (mp4, ogv, webm). If you embed an example.mp4 video the video player will automatically check if an example.ogv and example.webm video is available and display those versions in case its possible and necessary', 'avia_framework' ) . '<br/>';
			}
			
			
			$template = array();
			$id = ! empty( $element['id'] ) ? $element['id'] :'video';
			$required = ! empty( $element['required'] ) ? $element['required'] : array();
			
			$template[] = array(	
								'name'	=> __( 'Choose Video', 'avia_framework' ),
								'desc'	=> $text,
								'required'	=> $required,
								'id'	=> $id,
								'type'	=> 'video',
								'title'	=> __( 'Select Video', 'avia_framework' ),
								'button'	=> __( 'Use Video', 'avia_framework' ),
								'std'	=> 'https://'
							);
						
			if( ! empty( $element['args']['html_5_urls'] ) )
			{
				$desc = __( 'Either upload a new video, choose an existing video from your media library or link to a video by URL. If you want to make sure that all browser can display your video upload a mp4, an ogv and a webm version of your video.','avia_framework' );

				for( $i = 1; $i <= 2; $i++ )
				{
					$element = $template[0];
					
					$element['id'] = "{$id}_{$i}";
					$element['name'] =  __( 'Choose Another Video (HTML5 Only)', 'avia_framework' );
					$element['desc'] = $desc;
					
					$template[] = $element;
				}
			}
			
			return $template;
		}
		
		/**
		 * Slideshow Video Player Settings Template 
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array
		 */
		protected function slideshow_player( array $element )
		{
			$required = ! empty( $element['required'] ) ? $element['required'] : array();
			
			$template = array(
							array(	
								'name' 	=> __( 'Disable Autoplay', 'avia_framework' ),
								'desc' 	=> __( 'Check if you want to disable video autoplay when this slide shows. Autoplayed videos will be muted by default.', 'avia_framework' ) ,
								'id' 	=> 'video_autoplay',
								'required'	=> $required,
								'std' 	=> '',
								'type' 	=> 'checkbox'
							),
				
							array(	
								'name' 	=> __( 'Hide Video Controls', 'avia_framework' ),
								'desc' 	=> __( 'Check if you want to hide the controls (works for youtube and self hosted videos)', 'avia_framework' ) ,
								'id' 	=> 'video_controls',
								'required'	=> $required,
								'std' 	=> '',
								'type' 	=> 'checkbox'
							),

							array(	
								'name' 	=> __( 'Mute Video Player', 'avia_framework' ),
								'desc' 	=> __( 'Check if you want to mute the video', 'avia_framework' ) ,
								'id' 	=> 'video_mute',
								'required'	=> $required,
								'std' 	=> '',
								'type' 	=> 'checkbox'
							),

							array(	
								'name' 	=> __( 'Loop Video Player', 'avia_framework' ),
								'desc' 	=> __( 'Check if you want to loop the video (instead of showing the next slide the video will play from the beginning again)', 'avia_framework' ) ,
								'id' 	=> 'video_loop',
								'required'	=> $required,
								'std' 	=> '',
								'type' 	=> 'checkbox'
							)
				);
			
			return $template;
		}
		
		/**
		 * Slideshow Fallback Image Template 
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array
		 */
		protected function slideshow_fallback_image( array $element )
		{
			
			$template = array(
			
							array(	
								'name'	=> __( 'Choose a preview/fallback image', 'avia_framework' ),
								'desc'	=> __( 'Either upload a new, or choose an existing image from your media library', 'avia_framework' ) . '<br/><small>' . __( "Video on most mobile devices can't be controlled properly with JavaScript, so you can upload a fallback image which will be displayed instead. This image is also used if lazy loading is active.", 'avia_framework' ) . '</small>',
								'id'	=> 'mobile_image',
								'fetch'	=> 'id',
								'type'	=> 'image',
								'required'	=> array( 'slide_type', 'equals', 'video' ),
								'title'	=> __( 'Choose Image', 'avia_framework' ),
								'button'	=> __( 'Choose Image','avia_framework' ),
								'std'	=> ''
							),
									
							array(	
								'name' 	=> __( 'Mobile Fallback Image Link', 'avia_framework' ),
								'desc' 	=> __( 'You can enter a link to a video on youtube or vimeo that will open in a lightbox when the fallback image is clicked by the user. Links to self hosted videos will be opened in a new browser window on your mobile device or tablet', 'avia_framework' ), 
								'required'	=> array( 'mobile_image', 'not', '' ),
								'id' 	=> 'fallback_link',
								'std' 	=> 'https://',
								'type' 	=> 'input',
							)
					);
			
			return $template;
		}
		
		/**
		 * Slideshow Overlay Template 
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array
		 */
		protected function slideshow_overlay( array $element )
		{
			
			$template = array(
							array(	
								'name' 	=> __( 'Enable Overlay?', 'avia_framework' ),
								'desc' 	=> __( 'Check if you want to display a transparent color and/or pattern overlay above your slideshow image/video', 'avia_framework' ),
								'id' 	=> 'overlay_enable',
								'std' 	=> '',
								'type' 	=> 'checkbox'
							),

							array(
								'name' 	=> __( 'Overlay Opacity', 'avia_framework' ),
								'desc' 	=> __( 'Set the opacity of your overlay: 0.1 is barely visible, 1.0 is opaque ', 'avia_framework' ),
								'id' 	=> 'overlay_opacity',
								'type' 	=> 'select',
								'std' 	=> '0.5',
								'required'	=> array( 'overlay_enable', 'not', '' ),
								'subtype'	=> array(   
													__( '0.1', 'avia_framework' )	=> '0.1',
													__( '0.2', 'avia_framework' )	=> '0.2',
													__( '0.3', 'avia_framework' )	=> '0.3',
													__( '0.4', 'avia_framework'	)	=> '0.4',
													__( '0.5', 'avia_framework' )	=> '0.5',
													__( '0.6', 'avia_framework' )	=> '0.6',
													__( '0.7', 'avia_framework' )	=> '0.7',
													__( '0.8', 'avia_framework' )	=> '0.8',
													__( '0.9', 'avia_framework' )	=> '0.9',
													__( '1.0', 'avia_framework' )	=> '1',
												)
							),

							array(
								'name' 	=> __( 'Overlay Color', 'avia_framework' ),
								'desc' 	=> __( 'Select a custom color for your overlay here. Leave empty if you want no color overlay', 'avia_framework' ),
								'id' 	=> 'overlay_color',
								'type' 	=> 'colorpicker',
								'required'	=> array( 'overlay_enable', 'not', '' ),
								'std' 	=> '',
							),

							array(
								'id'		=> 'overlay_pattern',
								'name'		=> __( 'Background Image', 'avia_framework'),
								'desc'		=> __( 'Select an existing or upload a new background image', 'avia_framework'),
								'type'		=> 'select',
								'required'	=> array( 'overlay_enable', 'not', '' ),
								'subtype'	=> array(
													__( 'No Background Image', 'avia_framework')	=> '',
													__( 'Upload custom image', 'avia_framework')	=> 'custom'
												),
								'std'		=> '',
								'folder'	=> 'images/background-images/',
								'folderlabel'	=> '',
								'group'		=> 'Select predefined pattern',
								'exclude'	=> array( 'fullsize-', 'gradient' )
							),

							array(
								'name'		=> __( 'Custom Pattern', 'avia_framework' ),
								'desc'		=> __( 'Upload your own seamless pattern', 'avia_framework' ),
								'id'		=> 'overlay_custom_pattern',
								'type'		=> 'image',
								'fetch'		=> 'url',
								'secondary_img' => true,
								'required'	=> array( 'overlay_pattern', 'equals', 'custom' ),
								'title'		=> __( 'Insert Pattern', 'avia_framework' ),
								'button'	=> __( 'Insert', 'avia_framework' ),
								'std'		=> ''
							)
				
				);

			return $template;
		}
		
		/**
		 * Slideshow Buttons Link Template 
		 * 
		 * @since 4.6.4
		 * @param array $element
		 * @return array
		 */
		protected function slideshow_button_links( array $element )
		{
			
			$template = array(
				
					array(	
							'name' 	=> __( 'Apply a link or buttons to the slide?', 'avia_framework' ),
							'desc' 	=> __( "You can choose to apply the link to the whole image or to add 'Call to Action Buttons' that get appended to the caption", 'avia_framework' ),
							'id' 	=> 'link_apply',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype'	=> array(
												__( 'No Link for this slide', 'avia_framework' ) => '',
												__( 'Apply Link to Image', 'avia_framework' )	=> 'image',
												__( 'Attach one button', 'avia_framework' )		=> 'button',
												__( 'Attach two buttons', 'avia_framework' )	=> 'button button-two'
											)
					),

					array(	
							'name' 	=> __( 'Image Link?', 'avia_framework' ),
							'desc' 	=> __( 'Where should the Image link to?', 'avia_framework' ),
							'id' 	=> 'link',
							'required'	=> array( 'link_apply', 'equals', 'image' ),
							'type' 	=> 'linkpicker',
							'fetchTMPL'	=> true,
							'subtype'	=> array(	
												__( 'Open Image in Lightbox', 'avia_framework' )	=> 'lightbox',
												__( 'Set Manually', 'avia_framework' )				=> 'manually',
												__( 'Single Entry', 'avia_framework' )				=> 'single',
												__( 'Taxonomy Overview Page', 'avia_framework' )	=> 'taxonomy',
											),
							'std' 	=> ''
					),
							
					array(	
							'name' 	=> __( 'Open Link in new Window?', 'avia_framework' ),
							'desc' 	=> __( 'Select here if you want to open the linked page in a new window', 'avia_framework' ),
							'id' 	=> 'link_target',
							'type' 	=> 'select',
							'std' 	=> '',
							'required'	=> array( 'link', 'not_empty_and', 'lightbox' ),
							'subtype'	=> AviaHtmlHelper::linking_options()
					),   
	
					array(	
							'name' 	=> __( 'Button 1 Label', 'avia_framework' ),
							'desc' 	=> __( 'This is the text that appears on your button.', 'avia_framework' ),
							'id' 	=> 'button_label',
							'type' 	=> 'input',
							'container_class' => 'av_half av_half_first',
							'required'	=> array( 'link_apply', 'contains', 'button' ),
							'std' 	=> 'Click me'
					),	
								            
					array(	
							'type'			=> 'template',
							'template_id'	=> 'named_colors',
							'name'			=> __( 'Button 1 Color', 'avia_framework' ),
							'desc'			=> __( 'Choose a color for your button here', 'avia_framework' ),
							'id'			=> 'button_color',
							'std'			=> 'light',
							'container_class' => 'av_half',
							'required'		=> array( 'link_apply', 'contains', 'button' )
					),
								
					array(	
							'name' 	=> __( 'Button 1 Link?', 'avia_framework' ),
							'desc' 	=> __( 'Where should the Button link to?', 'avia_framework' ),
							'id' 	=> 'link1',
							'container_class' => 'av_half av_half_first',
							'required'	=> array( 'link_apply', 'contains', 'button' ),
							'type' 	=> 'linkpicker',
							'fetchTMPL'	=> true,
							'subtype'	=> array(	
												__( 'Set Manually', 'avia_framework' )	=> 'manually',
												__( 'Single Entry', 'avia_framework' )	=> 'single',
												__( 'Taxonomy Overview Page', 'avia_framework' )	=> 'taxonomy',
											),
							'std' 	=> ''
					),

					array(	
							'name' 	=> __( 'Button 1 Link Target?', 'avia_framework' ),
							'desc' 	=> __( 'Select here if you want to open the linked page in a new window', 'avia_framework' ),
							'id' 	=> 'link_target1',
							'type' 	=> 'select',
							'std' 	=> '',
							'container_class' => 'av_half',
							'required'	=> array( 'link_apply', 'contains', 'button' ),
							'subtype'	=> AviaHtmlHelper::linking_options()
					),   						
								
					array(	
							'name' 	=> __( 'Button 2 Label', 'avia_framework' ),
							'desc' 	=> __( 'This is the text that appears on your second button.', 'avia_framework' ),
							'id' 	=> 'button_label2',
							'type' 	=> 'input',
							'container_class' => 'av_half av_half_first',
							'required'	=> array( 'link_apply', 'contains',' button-two' ),
							'std' 	=> 'Click me'
					),	
								            
					array(	
							'type'			=> 'template',
							'template_id'	=> 'named_colors',
							'name'			=> __( 'Button 2 Color', 'avia_framework' ),
							'desc'			=> __( 'Choose a color for your second button here', 'avia_framework' ),
							'id'			=> 'button_color2',
							'std'			=> 'light',
							'container_class' => 'av_half',
							'required'		=> array( 'link_apply', 'contains', 'button-two' )
					),
						
					array(	
							'name' 	=> __('Button 2 Link?', 'avia_framework' ),
							'desc' 	=> __('Where should the Button link to?', 'avia_framework' ),
							'id' 	=> 'link2',
							'container_class' => 'av_half av_half_first',
							'required'	=> array( 'link_apply', 'contains','button-two' ),
							'type' 	=> 'linkpicker',
							'fetchTMPL'	=> true,
							'subtype'	=> array(	
												__( 'Set Manually', 'avia_framework' ) => 'manually',
												__( 'Single Entry', 'avia_framework' ) => 'single',
												__( 'Taxonomy Overview Page',  'avia_framework' ) => 'taxonomy',
											),
							'std' 	=> ''
					),

					array(	
							'name' 	=> __( 'Button 2 Link Target?', 'avia_framework' ),
							'desc' 	=> __( 'Select here if you want to open the linked page in a new window', 'avia_framework' ),
							'id' 	=> 'link_target2',
							'type' 	=> 'select',
							'std' 	=> '',
							'container_class' => 'av_half',
							'required'=> array( 'link_apply', 'contains', 'button-two' ),
							'subtype' => AviaHtmlHelper::linking_options()
					)
									
				
				);

			return $template;
		}
		
		/**
		 * Button Color Template
		 * 
		 * @since 4.7.5.1
		 * @param array $element
		 * @return array
		 */
		protected function button_colors( array $element )
		{
			$color_id = isset( $element['color_id'] ) ? $element['color_id'] : 'color';
			$custom_id = isset( $element['custom_id'] ) && is_string( $element['custom_id'] ) ? $element['custom_id'] : 'custom';
			$required = isset( $element['required'] ) ? $element['required'] : array();
			
			if( isset( $element['ids'] ) && is_array( $element['ids'] ) )
			{
				$ids = $element['ids'];
			}
			else
			{
				$ids = array(
						'bg'		=> array(
										'color'		=> $color_id . '_bg',
										'custom'	=> 'custom',
										'custom_id'	=> $custom_id . '_bg',
									),
						'bg_hover'	=> array(
										'color'		=> $color_id . '_bg_hover',
										'custom'	=> 'custom',
										'custom_id'	=> $custom_id . '_bg_hover',
									),
						'font'		=> array(
										'color'		=> $color_id . '_font',
										'custom'	=> 'custom',
										'custom_id'	=> $custom_id . '_font',
									),
						'font_hover' => array(
										'color'		=> $color_id . '_font_hover',
										'custom'	=> 'custom',
										'custom_id'	=> $custom_id . '_font_hover',
									),
						);
			}
			
			if( isset( $element['name'] ) && is_array( $element['name'] ) )
			{
				$name = $element['name'];
			}
			else
			{
				$name = array(
							'bg'			=>	__( 'Button Background Color', 'avia_framework' ),
							'bg_hover'		=>	__( 'Button Background Color On Hover', 'avia_framework' ),
							'font'			=>	__( 'Button Font Color', 'avia_framework' ),
							'font_hover'	=>	__( 'Button Font Color On Hover', 'avia_framework' )
						);
			}
			
			if( isset( $element['desc'] ) && is_array( $element['desc'] ) )
			{
				$desc = $element['desc'];
			}
			else
			{
				$desc = array(
							'bg'			=>	__( 'Select background color for your button here', 'avia_framework' ),
							'bg_hover'		=>	__( 'Select background color on hover for your button here', 'avia_framework' ),
							'font'			=>	__( 'Select font color for your button here', 'avia_framework' ),
							'font_hover'	=>	__( 'Select font color on hover for your button here', 'avia_framework' )
						);
			}
			
			if( isset( $element['std'] ) && is_array( $element['std'] ) )
			{
				$std = $element['std'];
			}
			else
			{
				$std = array(
							'bg'				=>	'theme-color',
							'bg_hover'			=>	'theme-color-highlight',
							'font'				=>	'#ffffff',
							'font_hover'		=>	'#ffffff',
							'custom_bg'			=>	'#444444',
							'custom_bg_hover'	=>	'#444444',
							'custom_font'		=>	'#ffffff',
							'custom_font_hover'	=>	'#ffffff'
						);
			}
			
			if( isset( $element['translucent'] ) && is_array( $element['translucent'] ) )
			{
				$translucent = $element['translucent'];
			}
			else
			{
				$translucent = array(
									'bg'			=>	'',
									'bg_hover'		=>	'',
									'font'			=>	array(),
									'font_hover'	=>	array()
								);
			}
			
			$template = array(
				
					array(	
						'type'			=> 'template',
						'template_id'	=> 'named_colors',
						'id'			=> $ids['bg']['color'],
						'name'			=> $name['bg'],
						'desc'			=> $desc['bg'],
						'std'			=> $std['bg'],
						'translucent'	=> $translucent['bg'],
						'custom'		=> $ids['bg']['custom'],
						'required'		=> $required
					),

					array(	
						'name' 	=> $name['bg'],
						'desc' 	=> $desc['bg'],
						'id' 	=> $ids['bg']['custom_id'],
						'type' 	=> 'colorpicker',
						'std' 	=> $std['custom_bg'],
						'required'	=> array( $ids['bg']['color'], 'equals', $ids['bg']['custom'] )
					),
				
					array(	
						'type'			=> 'template',
						'template_id'	=> 'named_colors',
						'id'			=> $ids['bg_hover']['color'],
						'name'			=> $name['bg_hover'],
						'desc'			=> $desc['bg_hover'],
						'std'			=> $std['bg_hover'],
						'translucent'	=> $translucent['bg_hover'],
						'custom'		=> $ids['bg_hover']['custom'],
						'required'		=> $required
					),

					array(	
						'name' 	=> $name['bg_hover'],
						'desc' 	=> $desc['bg_hover'],
						'id' 	=> $ids['bg_hover']['custom_id'],
						'type' 	=> 'colorpicker',
						'std' 	=> $std['custom_bg_hover'],
						'required'	=> array( $ids['bg_hover']['color'], 'equals', $ids['bg_hover']['custom'] )
					),
				
					array(	
						'type'			=> 'template',
						'template_id'	=> 'named_colors',
						'id'			=> $ids['font']['color'],
						'name'			=> $name['font'],
						'desc'			=> $desc['font'],
						'std'			=> $std['font'],
						'translucent'	=> $translucent['font'],
						'custom'		=> $ids['font']['custom'],
						'required'		=> $required
					),
				
					array(	
						'name' 	=> $name['font'],
						'desc' 	=> $desc['font'],
						'id' 	=> $ids['font']['custom_id'],
						'type' 	=> 'colorpicker',
						'std' 	=> $std['font'],
						'required'	=> array( $ids['font']['color'], 'equals', $ids['font']['custom'] )
					),
				
//					array(	
//						'type'			=> 'template',
//						'template_id'	=> 'named_colors',
//						'id'			=> $ids['font_hover']['color'],
//						'name'			=> $name['font_hover'],
//						'desc'			=> $desc['font_hover'],
//						'std'			=> $std['font_hover'],
//						'translucent'	=> $translucent['font_hover'],
//						'custom'		=> $ids['font_hover']['custom'],
//						'required'		=> $required
//					),
//				
//					array(	
//						'name' 	=> $name['font_hover'],
//						'desc' 	=> $desc['font_hover'],
//						'id' 	=> $ids['font_hover']['custom_id'],
//						'type' 	=> 'colorpicker',
//						'std' 	=> $std['font_hover'],
//						'required'	=> array( $ids['font_hover']['color'], 'equals', $ids['font_hover']['custom'] )
//					)
				
				);
			
			return $template;
		}
		
		/**
		 * Named Color Template
		 * 
		 * @since 4.5.6.1
		 * @param array $element
		 * @return array
		 */
		protected function named_colors( array $element )
		{
			$name = isset( $element['name'] ) ? $element['name'] : __( 'Button Color', 'avia_framework' );
			$desc = isset( $element['desc'] ) ? $element['desc'] : __( 'Choose a color for your button here', 'avia_framework' );
			$id = isset( $element['id'] ) ? $element['id'] : 'color';
			$std = isset( $element['std'] ) ? $element['std'] : 'theme-color';
			$required = isset( $element['required'] ) ? $element['required'] : array();
			$container_class  = isset( $element['container_class'] ) ? $element['container_class'] : '';
			$theme_col_key = isset( $element['theme-col-key'] ) ? $element['theme-col-key'] : 'theme-color';
			
			if( isset( $element['translucent'] ) && is_array( $element['translucent'] ) )
			{
				$translucent = $element['translucent'];
			}
			else
			{
				$translucent = array(
									__( 'Light Transparent', 'avia_framework' )	=> 'light',
									__( 'Dark Transparent', 'avia_framework' )	=> 'dark',
								);
			}
			
			$colored = array(
							__( 'Theme Color', 'avia_framework' )			=> $theme_col_key,
							__( 'Theme Color Highlight', 'avia_framework' )	=> 'theme-color-highlight',
							__( 'Theme Color Subtle', 'avia_framework' )	=> 'theme-color-subtle',
							__( 'Blue', 'avia_framework' )		=> 'blue',
							__( 'Red',  'avia_framework' )		=> 'red',
							__( 'Green', 'avia_framework' )		=> 'green',
							__( 'Orange', 'avia_framework' )	=> 'orange',
							__( 'Aqua', 'avia_framework' )		=> 'aqua',
							__( 'Teal', 'avia_framework' )		=> 'teal',
							__( 'Purple', 'avia_framework' )	=> 'purple',
							__( 'Pink', 'avia_framework' )		=> 'pink',
							__( 'Silver', 'avia_framework' )	=> 'silver',
							__( 'Grey', 'avia_framework' )		=> 'grey',
							__( 'Black', 'avia_framework' )		=> 'black',
						);
			
			if( ! empty( $element['no_alternate'] ) )
			{
				array_splice( $colored, 1, 2 );
			}
			
			if( ! empty( $element['custom'] ) )
			{
				$val = true === $element['custom'] ? 'custom' : $element['custom'];
				$colored[ __( 'Custom Color', 'avia_framework' ) ] = $val;
			}
			
			$e = array(
						'name' 	=> $name,
						'desc' 	=> $desc,
						'id' 	=> $id,
						'type' 	=> 'select',
						'std' 	=> $std,
						'container_class' => $container_class,
						'required'	=> $required,
						'subtype'	=> array()		
				);
			
			if( ! empty( $translucent ) )
			{
				$e['subtype'][ __( 'Translucent Buttons', 'avia_framework' ) ] = $translucent;
				$e['subtype'][ __( 'Colored Buttons', 'avia_framework' ) ] = $colored;
			}
			else
			{
				$e['subtype'] = $colored;
			}
			
			$template = array( $e );
			
			return $template;
		}
		
		
		/**
		 * Masonry Captions Template
		 * 
		 * @since 4.5.6.1
		 * @param array $element
		 * @return array
		 */
		protected function masonry_captions( array $element )
		{
			$template = array(
				
					array(
						'name' 	=> __('Element Title and Excerpt', 'avia_framework' ),
						'desc' 	=> __('You can choose if you want to display title and/or excerpt', 'avia_framework' ),
						'id' 	=> 'caption_elements',
						'type' 	=> 'select',
						'std' 	=> 'title excerpt',
						'subtype'	=> array(
											__( 'Display Title and Excerpt', 'avia_framework' )	=> 'title excerpt',
											__( 'Display Title', 'avia_framework' )				=> 'title',
											__( 'Display Excerpt', 'avia_framework' )			=> 'excerpt',
											__( 'Display Neither', 'avia_framework' )			=> 'none',
										)
					),	

					array(
						'name' 	=> __( 'Element Title and Excerpt Styling', 'avia_framework' ),
						'desc' 	=> __( 'You can choose the styling for the title and excerpt here', 'avia_framework' ),
						'id' 	=> 'caption_styling',
						'type' 	=> 'select',
						'std' 	=> 'always',
						'required' => array( 'caption_elements', 'not', 'none' ),
						'subtype' => array(
											__( 'Default display (at the bottom of the elements image)', 'avia_framework' )	=> '',
											__( 'Display as centered overlay (overlays the image)', 'avia_framework' )		=> 'overlay',
										)
					),	



					array(
						'name' 	=> __( 'Element Title and Excerpt display settings', 'avia_framework' ),
						'desc' 	=> __( 'You can choose whether to always display Title and Excerpt or only on hover', 'avia_framework' ),
						'id' 	=> 'caption_display',
						'type' 	=> 'select',
						'std' 	=> 'always',
						'required'	=> array( 'caption_elements', 'not', 'none' ),
						'subtype'	=> array(
											__( 'Always Display', 'avia_framework' )			=> 'always',
											__( 'Display on mouse hover', 'avia_framework' )	=> 'on-hover',
											__( 'Hide on mouse hover', 'avia_framework' )		=> 'on-hover-hide',
										)
					)	
				);
			
			return $template;
		}
		
		
		/**
		 * Background Image Position Template
		 * 
		 * @since 4.5.6.1
		 * @param array $element
		 * @return array
		 */
		protected function background_image_position( array $element )
		{
			$id_pos = isset( $element['args']['id_pos'] ) ? trim(  $element['args']['id_pos'] ) : 'background_position';
			$id_repeat = isset( $element['args']['id_repeat'] ) ? trim(  $element['args']['id_repeat'] ) : 'background_repeat';
			
			$template = array();
				
			$template[] = array(
							'name' 	=> __( 'Background Image Position', 'avia_framework' ),
							'id' 	=> $id_pos,
							'type' 	=> 'select',
							'std' 	=> 'top left',
							'required' => array( 'src', 'not','' ),
							'subtype' => array(   
											__( 'Top Left', 'avia_framework' )       => 'top left',
											__( 'Top Center', 'avia_framework' )     => 'top center',
											__( 'Top Right', 'avia_framework' )      => 'top right',
											__( 'Bottom Left', 'avia_framework' )    => 'bottom left',
											__( 'Bottom Center', 'avia_framework' )  => 'bottom center',
											__( 'Bottom Right', 'avia_framework' )   => 'bottom right',
											__( 'Center Left', 'avia_framework' )    => 'center left',
											__( 'Center Center', 'avia_framework' )  => 'center center',
											__( 'Center Right', 'avia_framework' )   => 'center right'
										)
					);
			
			$sub = array(  
						__( 'No Repeat', 'avia_framework' )          => 'no-repeat',
						__( 'Repeat', 'avia_framework' )             => 'repeat',
						__( 'Tile Horizontally', 'avia_framework' )  => 'repeat-x',
						__( 'Tile Vertically', 'avia_framework' )    => 'repeat-y',
						__( 'Stretch to fit (stretches image to cover the element)', 'avia_framework' )             => 'stretch',
						__( 'Scale to fit (scales image so the whole image is always visible)', 'avia_framework' )	=> 'contain'
					);
			
			if( ! empty( $element['args']['repeat_remove'] ) )
			{
				foreach( $sub as $key => $value ) 
				{
					if( in_array( $value, $element['args']['repeat_remove'] ) )
					{
						unset( $sub[ $key ] );
					}
				}
			}

			$template[] = array(
							'name' 	=> __( 'Background Repeat', 'avia_framework' ),
							'id' 	=> $id_repeat,
							'type' 	=> 'select',
							'std' 	=> 'no-repeat',
							'required' => array( 'src', 'not','' ),
							'subtype' => $sub
				);

			return $template;
		}
		
		
		/**
		 * Date Query Template
		 * 
		 * @since 4.5.6.1
		 * @param array $element
		 * @return array
		 */
		protected function date_query( array $element )
		{
			$template = array(
				
					array(	
							'name' 		=> __( 'Do you want to filter entries by date?', 'avia_framework' ),
							'desc' 		=> __( 'Do you want to display entries within date boundaries only? Can be used e.g. to create archives.', 'avia_framework' ),
							'id' 		=> 'date_filter',
							'type' 		=> 'select',
							'std'		=> '',
							'subtype'	=> array( 
												__( 'Display all entries', 'avia_framework' )		=> '',
												__( 'Filter entries by date', 'avia_framework' )	=> 'date_filter'
											)
						),
					
					array(	
							'name'		=> __( 'Start Date', 'avia_framework' ),
							'desc'		=> __( 'Pick a start date.', 'avia_framework' ),
							'id'		=> 'date_filter_start',
							'type'		=> 'datepicker',
							'required'	=> array( 'date_filter', 'equals', 'date_filter' ),
							'container_class'	=> 'av_third av_third_first',
							'std'		=> '',
							'dp_params'	=> array(
												'dateFormat'        => 'yy/mm/dd',
												'changeMonth'		=> true,
												'changeYear'		=> true,
												'container_class'	=> 'select_dates_30'
											)
						),
					
					array(	
							'name'		=> __( 'End Date', 'avia_framework' ),
							'desc'		=> __( 'Pick the end date. Leave empty to display all entries after the start date.', 'avia_framework' ),
							'id'		=> 'date_filter_end',
							'type'		=> 'datepicker',
							'required'	=> array( 'date_filter', 'equals', 'date_filter' ),
							'container_class'	=> 'av_2_third',
							'std'		=> '',
							'dp_params'	=> array(
												'dateFormat'        => 'yy/mm/dd',
												'changeMonth'		=> true,
												'changeYear'		=> true,
												'container_class'	=> 'select_dates_30'
											)
						),
					
					array(	
							'name'			=> __( 'Date Formt','avia_framework' ),
							'desc'			=> __( 'Define the same date format as used in date picker', 'avia_framework' ),
							'id'			=> 'date_filter_format',
							'container_class'	=> 'avia-hidden',
							'type'			=> 'input',
							'std'			=> 'yy/mm/dd'
						)
									
				);
			
				if( ! empty ( $element['template_required'][0] ) )
				{
					$template[0]['required'] = $element['template_required'][0];
				}
				
			return $template;
		}
		
		/**
		 * Complete Screen Options Tab with several content options
		 * 
		 * @since 4.5.7.1
		 * @param array $element
		 * @param boolean $all				for backwards comp prior 4.6.4
		 * @return array
		 */
		protected function screen_options_tab( array $element, $all = true )
		{
			$template = array();
			
			/**
			 * This is the default template when missing
			 */
			$sub_templates =  array( 'screen_options_visibility' );
			
			if( isset( $element['templates_include'] ) && ! empty( $element['templates_include']  ) )
			{
				$sub_templates = (array) $element['templates_include'];
			}
			
			if(  true === $all )
			{
				$template[] = array(
								'type'          => 'tab',		//	new --->  toggle
								'name'          => __( 'Responsive', 'avia_framework' ),
								'nodescription' => true
							);
			}
			
			foreach( $sub_templates as $sub_template ) 
			{
				if( false !== $this->template_exists( $sub_template ) )
				{
					$temp = array(	
									'type'          => 'template',
									'template_id'   => $sub_template,
								);		
					
					if( isset( $element['subtype'][ $sub_template ] ) && is_array( $element['subtype'][ $sub_template ] ) )
					{
						$temp['subtype'] = $element['subtype'][ $sub_template ];
					}
					
					$template[] = $temp;
				}
			}
								
			if(  true === $all )
			{
				$template[] = array(
								'type'          => 'tab_close',
								'nodescription' => true
							);
			}					
						
			return $template;
		}
		
		
		/**
		 * Simple checkboxes for element visibility
		 * 
		 * @since 4.5.6.1
		 * @param array $element
		 * @return array
		 */
		protected function screen_options_visibility( array $element )
		{
			$template = array(
							
							array(
									'type' 				=> 'heading',
									'name'              => __( 'Element Visibility', 'avia_framework' ),
									'desc'              => __( 'Set the visibility for this element, based on the device screensize.', 'avia_framework' ),
							),
							
							array(	
									'desc'              => __( 'Hide on large screens (wider than 990px - eg: Desktop)', 'avia_framework' ),
									'id'                => 'av-desktop-hide',
									'std'               => '',
									'container_class'   => 'av-multi-checkbox',
									'type'              => 'checkbox'
								),
								
							array(	

									'desc'              => __( 'Hide on medium sized screens (between 768px and 989px - eg: Tablet Landscape)', 'avia_framework' ),
									'id'                => 'av-medium-hide',
									'std'               => '',
									'container_class'   => 'av-multi-checkbox',
									'type'              => 'checkbox'
								),
										
							array(	

									'desc'              => __( 'Hide on small screens (between 480px and 767px - eg: Tablet Portrait)', 'avia_framework' ),
									'id'                => 'av-small-hide',
									'std'               => '',
									'container_class'   => 'av-multi-checkbox',
									'type'              => 'checkbox'
								),
										
							array(	
									
									'desc'              => __( 'Hide on very small screens (smaller than 479px - eg: Smartphone Portrait)', 'avia_framework' ),
									'id'                => 'av-mini-hide',
									'std'               => '',
									'container_class'   => 'av-multi-checkbox',
									'type'              => 'checkbox'
								),
							
				
						);
			
			return $template;
		}
		
		/**
		 * Select boxes for Title Font Sizes
		 * 
		 * @since 4.5.7.1
		 * @param array $element
		 * @return array
		 */
		protected function font_sizes_title( array $element )
		{
			$subtype = AviaHtmlHelper::number_array( 10, 120, 1, array( __( 'Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' );
			
			if( isset( $element['subtype'] ) && is_array( $element['subtype'] ) )
			{
				$subtype = $element['subtype'];
			}
			
			$template = array(
				
							array(	
									'name'		=> __( 'Font Size for medium sized screens (between 768px and 989px - eg: Tablet Landscape)', 'avia_framework' ),
									'id'		=> 'av-medium-font-size-title',
									'type'		=> 'select',
									'subtype'	=> $subtype,
									'std'		=> ''
								),
						            
							array(	
									'name'		=> __( 'Font Size for small screens (between 480px and 767px - eg: Tablet Portrait)', 'avia_framework' ),
									'id'		=> 'av-small-font-size-title',
									'type'		=> 'select',
									'subtype'	=> $subtype,
									'std'		=> ''
								),
						            
							array(	
									'name'		=> __( 'Font Size for very small screens (smaller than 479px - eg: Smartphone Portrait)', 'avia_framework' ),
									'id'		=> 'av-mini-font-size-title',
									'type'		=> 'select',
									'subtype'	=> $subtype,
									'std'		=> ''
								)
				
						);
			
			return $template;
		}
		
		/**
		 * Select boxes for Content Font Sizes
		 * 
		 * @since 4.5.7.1
		 * @param array $element
		 * @return array
		 */
		protected function font_sizes_content( array $element )
		{
			$subtype = AviaHtmlHelper::number_array( 10, 120, 1, array( __( 'Default', 'avia_framework' ) => '', __( 'Hidden', 'avia_framework' ) => 'hidden' ), 'px' );
			
			if( isset( $element['subtype'] ) && is_array( $element['subtype'] ) )
			{
				$subtype = $element['subtype'];
			}
			
			$template = array(
							array(	
									'name'		=> __( 'Font Size for medium sized screens (between 768px and 989px - eg: Tablet Landscape)', 'avia_framework' ),
									'id'		=> 'av-medium-font-size',
									'type'		=> 'select',
									'subtype'	=> $subtype,
									'std'		=> ''
								),
						            
							array(	
									'name'		=> __( 'Font Size for small screens (between 480px and 767px - eg: Tablet Portrait)', 'avia_framework' ),
									'id'		=> 'av-small-font-size',
									'type'		=> 'select',
									'subtype'	=> $subtype,
									'std'		=> ''
								),
						            
							array(	
									'name'		=> __( 'Font Size for very small screens (smaller than 479px - eg: Smartphone Portrait)', 'avia_framework' ),
									'id'		=> 'av-mini-font-size',
									'type'		=> 'select',
									'subtype'	=> $subtype,
									'std'		=> ''
								)				
						);
			
			return $template;
		}

		/**
		 * Select boxes for Heading Font Size
		 * 
		 * @since 4.5.7.1
		 * @param array $element
		 * @return array
		 */
		protected function heading_font_size( array $element )
		{
			$title = array(
							array(
									'name'		=> __( 'Heading Font Size', 'avia_framework' ),
									'desc'		=> __( 'Set the font size for the heading, based on the device screensize.', 'avia_framework' ),
									'type'		=> 'heading',
									'description_class' => 'av-builder-note av-neutral',
								)
							);
			
			$fonts = $this->font_sizes_title( $element );
			$template = array_merge( $title, $fonts );
			
			return $template;
		}
		
		/**
		 * Select boxes for Content Font Size
		 * 
		 * @since 4.5.7.1
		 * @param array $element
		 * @return array
		 */
		protected function content_font_size( array $element )
		{
			$title = array(
							array(
									'name'		=> __( 'Content Font Size', 'avia_framework' ),
									'desc'		=> __( 'Set the font size for the content, based on the device screensize.', 'avia_framework' ),
									'type'		=> 'heading',
									'description_class' => 'av-builder-note av-neutral',
								)
						);
			
			$fonts = $this->font_sizes_content( $element );
			$template = array_merge( $title, $fonts );
			
			return $template;
		}
		
		/**
		 * Select boxes for Subheading Font Size
		 * 
		 * @since 4.5.7.1
		 * @param array $element
		 * @return array
		 */
		protected function subheading_font_size( array $element )
		{
			$template = $this->content_font_size( $element );
			
			$title = array( 
							array(
								'name'		=> __( 'Subheading Font Size', 'avia_framework' ),
								'desc'		=> __( 'Set the font size for the subheading, based on the device screensize.', 'avia_framework' ),
								'type'		=> 'heading',
								'description_class'	=> 'av-builder-note av-neutral',
							)
						);
			
			
			$fonts = $this->font_sizes_content( $element );
			$template = array_merge( $title, $fonts );
			
			return $template;
		}
		
		/**
		 * Select boxes for Number Font Size (countdown)
		 * 
		 * @since 4.5.7.1
		 * @param array $element
		 * @return array
		 */
		protected function number_font_size( array $element )
		{
			$title = array(
							array(
									'name'		=> __( 'Number Font Size', 'avia_framework' ),
									'desc'		=> __( 'Set the font size for the number, based on the device screensize.', 'avia_framework' ),
									'type'		=> 'heading',
									'description_class' => 'av-builder-note av-neutral',
							)
						);
			
			$fonts = $this->font_sizes_title( $element );
			$template = array_merge( $title, $fonts );
			
			return $template;
		}
		
		/**
		 * Select boxes for Text Font Size (countdown)
		 * 
		 * @since 4.5.7.1
		 * @param array $element
		 * @return array
		 */
		protected function text_font_size( array $element )
		{
			$title = array(
							array(
									'name'		=> __( 'Text Font Size', 'avia_framework' ),
									'desc'		=> __( 'Set the font size for the text, based on the device screensize.', 'avia_framework' ),
									'type'		=> 'heading',
									'description_class' => 'av-builder-note av-neutral',
							)
						);
			
			$fonts = $this->font_sizes_content( $element );
			$template = array_merge( $title, $fonts );
			
			return $template;
		}
		
		/**
		 * Select boxes for Columns ( 1 - 4 )
		 * 
		 * @since 4.5.7.1
		 * @param array $element
		 * @return array
		 */
		protected function column_count( array $element )
		{
			$subtype = AviaHtmlHelper::number_array( 1, 4, 1, array( __( 'Default', 'avia_framework' ) => '' ) );
			
			$template = array(
				
							array(	
									'name'		=> __( 'Column count for medium sized screens (between 768px and 989px - eg: Tablet Landscape)', 'avia_framework' ),
									'id'		=> 'av-medium-columns',
									'type'		=> 'select',
									'subtype'	=> $subtype,
									'std'		=> ''
								),
						            
							array(	
									'name'		=> __( 'Column count for small screens (between 480px and 767px - eg: Tablet Portrait)', 'avia_framework' ),
									'id'		=> 'av-small-columns',
									'type'		=> 'select',
									'subtype'	=> $subtype,
									'std'		=> ''
								),
						            
							array(	
									'name'		=> __( 'Column count for very small screens (smaller than 479px - eg: Smartphone Portrait)', 'avia_framework' ),
									'id'		=> 'av-mini-columns',
									'type'		=> 'select',
									'subtype'	=> $subtype,
									'std'		=> ''
								),  	
							  
				);
			
			return $template;
		}
		
		/**
		 * Select box for <h. > tag and inputfield for custom class
		 * 
		 * @since 4.5.7.2
		 * @param array $element
		 * @return array
		 */
		protected function heading_tag( array $element )
		{
			$setting = Avia_Builder()->get_developer_settings( 'heading_tags' );
			$class = in_array( $setting, array( 'deactivate', 'hide' ) ) ? 'avia-hidden' : '';
			
			$allowed = array( 
							__( 'Theme default', 'avia_framework' )	=> '',
							'H1'	=> 'h1', 
							'H2'	=> 'h2', 
							'H3'	=> 'h3', 
							'H4'	=> 'h4', 
							'H5'	=> 'h5', 
							'H6'	=> 'h6',
							'P'		=> 'p',
							'DIV'	=> 'div',
							'SPAN'	=> 'span'
						);
			
			
			$rendered_subtype = isset( $element['subtype'] ) ? $element['subtype'] : $allowed;
			$default = isset( $element['theme_default'] ) ? $element['theme_default'] : array_keys( $rendered_subtype )[0];
			
			/**
			 * Filter possible tags for element
			 * 
			 * @since 4.5.7.2
			 * @param array $rendered_subtype
			 * @param array $element
			 * @return array
			 */
			$subtype = apply_filters( 'avf_alb_element_heading_tags', $rendered_subtype, $element );
			if( ! is_array( $subtype ) || empty( $subtype ) )
			{
				$subtype = $rendered_subtype;
			}
			
			$std = isset( $element['std'] ) ? $element['std'] : '';
			if( ! in_array( $std, $subtype ) )
			{
				$std = ( 1 == count( $subtype ) ) ? array_values( $subtype )[0] : array_values( $subtype )[1];
			}
			
			$template = array();
				
			$templ = array(	
							'name'				=> sprintf( __( 'Heading Tag (Theme Default is &lt;%s&gt;)', 'avia_framework' ), $default ),
							'desc'				=> __( 'Select a heading tag for this element. Enfold only provides CSS for theme default tags, so it might be necessary to add a custom CSS class below and adjust the CSS rules for this element.', 'avia_framework' ),
							'id'				=> 'heading_tag',
							'container_class'	=> $class,
							'type'				=> 'select',
							'subtype'			=> $subtype,
							'std'				=> $std
						);
			
			if( isset( $element['required'] ) && is_array( $element['required'] ) )
			{
				$templ['required'] = $element['required'];
			}
			
			$template[] = $templ;
				
			$templ = array(	
							'name'				=> __( 'Custom CSS Class For Heading Tag', 'avia_framework' ),
							'desc'				=> __( 'Add a custom css class for the heading here. Make sure to only use allowed characters (latin characters, underscores, dashes and numbers).', 'avia_framework' ),
							'id'				=> 'heading_class',
							'container_class'	=> $class,
							'type'				=> 'input',
							'std'				=> ''
						);
			
			if( isset( $element['required'] ) && is_array( $element['required'] ) )
			{
				$templ['required'] = $element['required'];
			}
			
			$template[] = $templ;
			
			return $template;
		}
		
		/**
		 * Lazy Load Template 
		 * 
		 * @since 4.7.6.3
		 * @deprecated 4.7.6.4
		 * @param array $element
		 * @return array
		 */
		protected function lazy_loading( array $element )
		{
			_deprecated_function( 'Avia_Popup_Templates::lazy_loading', '4.7.6.4', 'Avia_Popup_Templates::lazy_loading_toggle' );
			
			$element['no_toggle'] = true;
			
			return $this->lazy_loading_toggle( $element );
		}
		
		/**
		 * Lazy Load Template 
		 * 
		 * @since 4.7.6.4
		 * @param array $element
		 * @return array
		 */
		protected function lazy_loading_toggle( array $element )
		{
			$desc  = __( 'Lazy loading of images using pure HTML is a feature introduced with WP 5.5 as a standard feature to speed up page loading. But it may not be compatible with animations and might break functionality of your page.', 'avia_framework' ) . ' '; 
			$desc .= __( 'Therefore this feature is disabled by default. Please check carefully that everything is working as you expect when you enable this feature for this element.', 'avia_framework' );
					
			$id = isset( $element['id'] ) && ! empty( $element['id'] ) ? $element['id'] : 'lazy_loading';
			$std = isset( $element['std'] ) && in_array( $element['std'] , array( 'disabled', 'enabled' ) ) ? $element['std'] : 'disabled';
			$required = isset( $element['required'] ) && is_array( $element['required'] ) ? $element['required'] : array();
			
			$c = array(
							array(
								'name'		=> __( 'Lazy Loading Of Images', 'avia_framework' ),
								'desc'		=> $desc,
								'id'		=> $id,
								'type'		=> 'select',
								'std'		=> $std,
								'required'	=> $required,
								'subtype'	=> array(
													__( 'Do not use lazy loading', 'avia_framework' )	=> 'disabled',
													__( 'Enable lazy loading', 'avia_framework' )		=> 'enabled'
												)
							)
				);
			
			if( isset( $element['no_toggle'] ) && true === $element['no_toggle'] )
			{
				$template = $c;
			}
			else
			{
				$template = array(
								array(	
									'type'			=> 'template',
									'template_id'	=> 'toggle',
									'title'			=> __( 'Performance', 'avia_framework' ),
									'content'		=> $c 
								),
					);
			}
			
			return $template;
		}
		
		
		
		/**
		 *  Select boxes for WooCommerce Options for non product elements
		 * 
		 * @since 4.5.7.1
		 * @param array $element
		 * @return array
		 */
		protected function wc_options_non_products( array $element )
		{
			$required = array( 'link', 'parent_in_array', implode( ' ', get_object_taxonomies( 'product', 'names' ) ) );
			
			$sort = array( 
							__( 'Use default (defined at Dashboard -&gt; Customize -&gt; WooCommerce)', 'avia_framework' )	=> '',
							__( 'Sort alphabetically', 'avia_framework' )			=> 'title',
							__( 'Sort by most recent', 'avia_framework' )			=> 'date',
							__( 'Sort by price', 'avia_framework' )					=> 'price',
							__( 'Sort by popularity', 'avia_framework' )			=> 'popularity',
							__( 'Sort randomly', 'avia_framework' )					=> 'rand',
							__( 'Sort by menu order and name', 'avia_framework' )	=> 'menu_order',
							__( 'Sort by average rating', 'avia_framework' )		=> 'rating',
							__( 'Sort by relevance', 'avia_framework' )				=> 'relevance',
							__( 'Sort by Product ID', 'avia_framework' )			=> 'id'
						);
			
			/**
			 * @since 4.5.7.1
			 * @param array $sort
			 * @param array $element
			 * @return array
			 */
			$sort = apply_filters( 'avf_alb_wc_options_non_products_sort', $sort, $element );
			
			
			$template = array();
			
			$template[] = array(
								'name'		=> __( 'WooCommerce Out of Stock Product visibility', 'avia_framework' ),
								'desc'		=> __( 'Select the visibility of WooCommerce products. Default setting can be set at Woocommerce -&gt Settings -&gt Products -&gt Inventory -&gt Out of stock visibility', 'avia_framework' ),
								'id'		=> 'wc_prod_visible',
								'type'		=> 'select',
								'std'		=> '',
								'required'	=> $required,
								'subtype'	=> array(
													__( 'Use default WooCommerce Setting (Settings -&gt; Products -&gt; Out of stock visibility)', 'avia_framework' ) => '',
													__( 'Hide products out of stock', 'avia_framework' )	=> 'hide',
													__( 'Show products out of stock', 'avia_framework' )	=> 'show'
												)
							);
			
			$template[] = array(
								'name'		=> __( 'WooCommerce Hidden Products visibility', 'avia_framework' ),
								'desc'		=> __( 'Select the visibility of WooCommerce products depending on catalog visibility. Can be set independently for each product: Edit Product -&gt Publish panel -&gt Catalog visibility', 'avia_framework' ),
								'id'		=> 'wc_prod_hidden',
								'type'		=> 'select',
								'std'		=> 'hide',
								'required'	=> $required,
								'subtype'	=> array(
													__( 'Show all products', 'avia_framework' )			=> '',
													__( 'Hide hidden products', 'avia_framework' )		=> 'hide',
													__( 'Show hidden products only', 'avia_framework' )	=> 'show'
												)
							);
			
			$template[] = array(
								'name'		=> __( 'WooCommerce Featured Products visibility', 'avia_framework' ),
								'desc'		=> __( 'Select the visibility of WooCommerce products depending on checkbox &quot;This is a featured product&quot; in catalog visibility. Can be set independently for each product: Edit Product -&gt Publish panel -&gt Catalog visibility', 'avia_framework' ),
								'id'		=> 'wc_prod_featured',
								'type'		=> 'select',
								'std'		=> '',
								'required'	=> $required,
								'subtype'	=> array(
													__( 'Show all products', 'avia_framework' )				=> '',
													__( 'Hide featured products', 'avia_framework' )		=> 'hide',
													__( 'Show featured products only', 'avia_framework' )	=> 'show'
												)
							);
					
			$template[] = array(
								'name'		=> __( 'WooCommerce Sorting Options', 'avia_framework' ),
								'desc'		=> __( 'Here you can choose how to sort the products. Default setting can be set at Dashboard -&gt; Appearance -&gt; Customize -&gt; WooCommerce -&gt; Product Catalog -&gt; Default Product Sorting', 'avia_framework' ),
								'id'		=> 'prod_order_by',
								'type'		=> 'select',
								'std'		=> '',
								'required'	=> $required,
								'subtype'	=> $sort
							);
				
			$template[] = array(
								'name'		=> __( 'WooCommerce Sorting Order', 'avia_framework' ),
								'desc'		=> __( 'Here you can choose the order of the result products. Default setting can be set at Dashboard -&gt; Appearance -&gt; Customize -&gt; WooCommerce -&gt; Product Catalog -&gt; Default Product Sorting', 'avia_framework' ),
								'id'		=> 'prod_order',
								'type'		=> 'select',
								'std'		=> '',
								'required'	=> $required,
								'subtype'	=> array( 
													__( 'Use default (defined at Dashboard -&gt; Customize -&gt; WooCommerce)', 'avia_framework' ) => '',
													__( 'Ascending', 'avia_framework' )			=> 'ASC',
													__( 'Descending', 'avia_framework' )		=> 'DESC'
												)
							);
			
			return $template;
		}
		
		
		/**
		 *  Select boxes for WooCommerce Options for product elements
		 * 
		 * @since 4.5.7.1
		 * @param array $element
		 * @return array
		 */
		protected function wc_options_products( array $element )
		{
			
			$sort = array( 
							__( 'Use default (defined at Dashboard -&gt; Customize -&gt; WooCommerce)', 'avia_framework' )	=> '0',
							__( 'Sort alphabetically', 'avia_framework' )			=> 'title',
							__( 'Sort by most recent', 'avia_framework' )			=> 'date',
							__( 'Sort by price', 'avia_framework' )					=> 'price',
							__( 'Sort by popularity', 'avia_framework' )			=> 'popularity',
							__( 'Sort randomly', 'avia_framework' )					=> 'rand',
							__( 'Sort by menu order and name', 'avia_framework' )	=> 'menu_order',
							__( 'Sort by average rating', 'avia_framework' )		=> 'rating',
							__( 'Sort by relevance', 'avia_framework' )				=> 'relevance',
							__( 'Sort by Product ID', 'avia_framework' )			=> 'id'
						);
			
			$sort_std = '0';
			
			if( ! empty( $element['sort_dropdown'] ) )
			{
				$sort = array_merge( array( __( 'Let user pick by displaying a dropdown with sort options (default value is defined at Default product sorting)', 'avia_framework' ) => 'dropdown' ), $sort );
				$sort_std = 'dropdown';
			}
			
			/**
			 * @since 4.5.7.1
			 * @param array $sort
			 * @param array $element
			 * @return array
			 */
			$sort = apply_filters( 'avf_alb_wc_options_non_products_sort', $sort, $element );
			
			$template = array();
			
			$template[] = array(
								'name'		=> __( 'WooCommerce Out of Stock Product visibility', 'avia_framework' ),
								'desc'		=> __( 'Select the visibility of WooCommerce products. Default setting can be set at Woocommerce -&gt Settings -&gt Products -&gt Inventory -&gt Out of stock visibility', 'avia_framework' ),
								'id'		=> 'wc_prod_visible',
								'type'		=> 'select',
								'std'		=> '',
								'subtype'	=> array(
													__( 'Use default WooCommerce Setting (Settings -&gt; Products -&gt; Out of stock visibility)', 'avia_framework' ) => '',
													__( 'Hide products out of stock', 'avia_framework' )	=> 'hide',
													__( 'Show products out of stock', 'avia_framework' )	=> 'show'
												)
							);
					
				
			$template[] = array(
								'name'		=> __( 'WooCommerce Hidden Products visibility', 'avia_framework' ),
								'desc'		=> __( 'Select the visibility of WooCommerce products depending on catalog visibility. Can be set independently for each product: Edit Product -&gt Publish panel -&gt Catalog visibility', 'avia_framework' ),
								'id'		=> 'wc_prod_hidden',
								'type'		=> 'select',
								'std'		=> '',
								'subtype'	=> array(
													__( 'Show all products', 'avia_framework' )			=> '',
													__( 'Hide hidden products', 'avia_framework' )		=> 'hide',
													__( 'Show hidden products only', 'avia_framework' )	=> 'show'
												)
							);
				
			$template[] = array(
								'name'		=> __( 'WooCommerce Featured Products visibility', 'avia_framework' ),
								'desc'		=> __( 'Select the visibility of WooCommerce products depending on checkbox &quot;This is a featured product&quot; in catalog visibility. Can be set independently for each product: Edit Product -&gt Publish panel -&gt Catalog visibility', 'avia_framework' ),
								'id'		=> 'wc_prod_featured',
								'type'		=> 'select',
								'std'		=> '',
								'subtype'	=> array(
													__( 'Show all products', 'avia_framework' )				=> '',
													__( 'Hide featured products', 'avia_framework' )		=> 'hide',
													__( 'Show featured products only', 'avia_framework' )	=> 'show'
												)
							);
				
			$template[] = array(
								'name'		=> __( 'WooCommerce Sidebar Filters', 'avia_framework' ),
								'desc'		=> __( 'Allow to filter products for this element using the 3 WooCommerce sidebar filters: Filter Products by Price, Rating, Attribute. These filters are only shown on the selected WooCommerce Shop page (WooCommerce -&gt; Settings -&gt; Products -&gt; General -&gt; Shop Page) or on product category pages. You may also use a custom widget area for the sidebar.', 'avia_framework' ),
								'id'		=> 'wc_prod_additional_filter',
								'type'		=> 'select',
								'std'		=> '',
								'subtype'	=> array(
													__( 'Ignore filters', 'avia_framework' )	=> '',
													__( 'Use filters', 'avia_framework' )		=> 'use_additional_filter'
												)
							);		
			
			$template[] = array(
								'name'		=> __( 'WooCommerce Sorting Options', 'avia_framework' ),
								'desc'		=> __( 'Here you can choose how to sort the products. Default setting can be set at Dashboard -&gt; Appearance -&gt; Customize -&gt; WooCommerce -&gt; Product Catalog -&gt; Default Product Sorting', 'avia_framework' ),
								'id'		=> 'sort',
								'type'		=> 'select',
								'std'		=> $sort_std,
								'subtype'	=> $sort
							);
									
			$template[] = array(
								'name'		=> __( 'WooCommerce Sorting Order', 'avia_framework' ),
								'desc'		=> __( 'Here you can choose the order of the result products. Default setting can be set at Dashboard -&gt; Appearance -&gt; Customize -&gt; WooCommerce -&gt; Product Catalog -&gt; Default Product Sorting', 'avia_framework' ),
								'id'		=> 'prod_order',
								'type'		=> 'select',
								'std'		=> '',
								'subtype'	=> array( 
													__( 'Use default (defined at Dashboard -&gt; Customize -&gt; WooCommerce)', 'avia_framework' ) => '',
													__( 'Ascending', 'avia_framework' )			=> 'ASC',
													__( 'Descending', 'avia_framework' )		=> 'DESC'
												)
							);
			
			return $template;
		}
		
		/**
		 * Adds theme defined html templates for ALB
		 * 
		 * @since 4.6.4
		 */
		protected function set_predefined_html_templates()
		{
			$c  = '';
			
			$c .=	'<div class="avia-flex-element">';
			$c .=		__( 'This element will stretch across the whole screen by default.', 'avia_framework' ) . '<br/>';
			$c .=		__( 'If you put it inside a color section or column it will only take up the available space', 'avia_framework' );
			$c .=		'<div class="avia-flex-element-2nd">' . __( 'Currently:', 'avia_framework' );
			$c .=			'<span class="avia-flex-element-stretched">&laquo; ' . __( 'Stretch fullwidth', 'avia_framework') . ' &raquo;</span>';
			$c .=			'<span class="avia-flex-element-content">| ' . __( 'Adjust to content width', 'avia_framework' ) . ' |</span>';
			$c .=		'</div>';
			$c .=	'</div>';
			
			$this->html_templates['alb_element_fullwidth_stretch'] = $c;
		}
	
	}
	
	/**
	 * Returns the main instance of Avia_Popup_Templates to prevent the need to use globals
	 * 
	 * @since 4.3.2
	 * @return Avia_Popup_Templates
	 */
	function AviaPopupTemplates() 
	{
		return Avia_Popup_Templates::instance();
	}
	
}		//	end Avia_Popup_Templates

