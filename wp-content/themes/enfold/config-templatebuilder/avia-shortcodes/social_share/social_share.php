<?php
/**
 * Social Share Buttons
 * 
 * Shortcode creates one or more social share buttons
 */
if ( ! defined( 'ABSPATH' ) ) {  exit;  }    // Exit if accessed directly


if ( ! class_exists( 'avia_sc_social_share' ) )
{
	class avia_sc_social_share extends aviaShortcodeTemplate
	{
		/**
		 * Create the config array for the shortcode button
		 */
		function shortcode_insert_button()
		{

			$this->config['version']		= '1.0';
			$this->config['self_closing']	= 'yes';

			$this->config['name']			= __( 'Social Share Buttons', 'avia_framework' );
			$this->config['tab']			= __( 'Content Elements', 'avia_framework' );
			$this->config['icon']			= AviaBuilder::$path['imagesURL'] . 'sc-social.png';
			$this->config['order']			= 7;
			$this->config['target']			= 'avia-target-insert';
			$this->config['shortcode'] 		= 'av_social_share';
			$this->config['tooltip'] 	    = __( 'Creates one or more social share buttons ', 'avia_framework' );
			$this->config['preview'] 		= true;
//			$this->config['disabling_allowed'] 	= true;		//	also needed in single pages
			$this->config['id_name']		= 'id';
			$this->config['id_show']		= 'yes';
			$this->config['alb_desc_id']	= 'alb_description';
		}


		function extra_assets()
		{
			//load css
			wp_enqueue_style( 'avia-module-social', AviaBuilder::$path['pluginUrlRoot'] . 'avia-shortcodes/social_share/social_share.css', array( 'avia-layout' ), false );

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
							'template_id'	=> $this->popup_key( 'content_icons' )
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
							'template_id'	=> $this->popup_key( 'styling_general' )
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
			
			$desc  = __( 'Which Social Buttons do you want to display? Defaults are set in ', 'avia_framework' );
			$desc .= '<a target="_blank" href="' . admin_url( 'admin.php?page=avia#goto_blog' ) . '">' . __( 'Blog Layout', 'avia_framework' ) . '</a>';
									
			$c = array(
						array(
							'name'  => __( 'Small title', 'avia_framework' ),
							'desc'  => __( 'A small title above the buttons.', 'avia_framework' ),
							'id'    => 'title',
							'type' 	=> 'input',
							'std' 	=> __( 'Share this entry', 'avia_framework' )
						),
					
						array(
							'name' 	=> __( 'Social Buttons', 'avia_framework' ),
							'desc' 	=> $desc,
							'id' 	=> 'buttons',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype'	=> array( 
												__( 'Use Defaults that are also used for your blog', 'avia_framework' )	=> '',
												__( 'Use a custom set', 'avia_framework' )		=> 'custom'),
						),
										
						array(	
							'name' 	=> __( 'Facebook link', 'avia_framework' ),
							'desc' 	=> __( 'Check to display', 'avia_framework' ),
							'id' 	=> 'share_facebook',
							'std' 	=> '',
							'container_class' => 'av_third av_third_first',
							'required'	=> array( 'buttons', 'equals', 'custom' ),
							'type'	=> 'checkbox'
						),

						array(	
							'name' 	=> __( 'Twitter link', 'avia_framework' ),
							'desc' 	=> __( 'Check to display', 'avia_framework' ),
							'id' 	=> 'share_twitter',
							'std' 	=> '',
							'container_class' => 'av_third ',
							'required'	=> array( 'buttons', 'equals', 'custom' ),
							'type' 	=> 'checkbox'
						),

						array(	
							'name' 	=> __( 'WhatsApp link', 'avia_framework' ),
							'desc' 	=> __( 'Check to display', 'avia_framework' ),
							'id' 	=> 'share_whatsapp',
							'std' 	=> '',
							'container_class' => 'av_third ',
							'required'	=> array( 'buttons', 'equals', 'custom' ),
							'type' 	=> 'checkbox'
						),

						array(	
							'name' 	=> __( 'Pinterest link', 'avia_framework' ),
							'desc' 	=> __( 'Check to display', 'avia_framework' ),
							'id' 	=> 'share_pinterest',
							'std' 	=> '',
							'container_class' => 'av_third ',
							'required'	=> array( 'buttons', 'equals', 'custom' ),
							'type' 	=> 'checkbox'
						),

						array(	
							'name' 	=> __( 'Reddit link', 'avia_framework' ),
							'desc' 	=> __( 'Check to display', 'avia_framework' ),
							'id' 	=> 'share_reddit',
							'std' 	=> '',
							'container_class' => 'av_third ',
							'required'	=> array( 'buttons', 'equals', 'custom' ),
							'type' 	=> 'checkbox'
						),

						array(	
							'name' 	=> __( 'LinkedIn link', 'avia_framework' ),
							'desc' 	=> __( 'Check to display', 'avia_framework' ),
							'id' 	=> 'share_linkedin',
							'std' 	=> '',
							'container_class' => 'av_third ',
							'required'	=> array( 'buttons', 'equals', 'custom' ),
							'type' 	=> 'checkbox'
						),

						array(	
							'name' 	=> __( 'Tumblr link', 'avia_framework' ),
							'desc' 	=> __( 'Check to display', 'avia_framework' ),
							'id' 	=> 'share_tumblr',
							'std' 	=> '',
							'container_class' => 'av_third ',
							'required'	=> array( 'buttons', 'equals', 'custom' ),
							'type' 	=> 'checkbox'
						),

						array(	
							'name' 	=> __( 'VK link', 'avia_framework' ),
							'desc' 	=> __( 'Check to display', 'avia_framework' ),
							'id' 	=> 'share_vk',
							'std' 	=> '',
							'container_class' => 'av_third ',
							'required'	=> array( 'buttons', 'equals', 'custom' ),
							'type' 	=> 'checkbox'
						),

						array(	
							'name' 	=> __( 'Email link', 'avia_framework' ),
							'desc' 	=> __( 'Check to display', 'avia_framework' ),
							'id' 	=> 'share_mail',
							'std' 	=> '',
							'container_class' => 'av_third ',
							'required'	=> array( 'buttons', 'equals', 'custom' ),
							'type' 	=> 'checkbox'
						),
						
						array(	
							'name' 	=> __( 'Yelp', 'avia_framework' ),
							'desc' 	=> __( 'Check to display', 'avia_framework' ),
							'id' 	=> 'share_yelp',
							'std' 	=> '',
							'container_class' => 'av_third ',
							'required'	=> array( 'buttons', 'equals', 'custom' ),
							'type' 	=> 'checkbox'
						),
				
						array(
							'name'  => __( 'Yelp Link', 'avia_framework' ),
							'desc'  => __( 'Enter the link to Yelp for this button.', 'avia_framework' ),
							'id'    => 'yelp_link',
							'type' 	=> 'input',
							'std' 	=> 'https://www.yelp.com',
							'required'	=> array( 'share_yelp', 'not', '' ),
						)
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'content_icons' ), $c );
			
			/**
			 * Content Tab
			 * ===========
			 */
			
			$c = array(
						array(
							'name' 	=> __( 'Style', 'avia_framework' ),
							'desc' 	=> __( 'How to display the social sharing bar?', 'avia_framework' ),
							'id' 	=> 'style',
							'type' 	=> 'select',
							'std' 	=> '',
							'subtype'	=> array( 
												__( 'Default with border', 'avia_framework' )	=> '',
												__( 'Minimal', 'avia_framework' )				=> 'minimal'),
						),
					
				);
			
			AviaPopupTemplates()->register_dynamic_template( $this->popup_key( 'styling_general' ), $c );
			
		}

		/**
		 * Frontend Shortcode Handler
		 *
		 * @param array $atts array of attributes
		 * @param string $content text within enclosing form of shortcode element
		 * @param string $shortcodename the shortcode found, when == callback name
		 * @param array $meta
		 * @return string $output returns the modified html string
		 */
		function shortcode_handler( $atts, $content = '', $shortcodename = '', $meta = '' )
		{
			extract( AviaHelper::av_mobile_sizes( $atts ) ); //return $av_font_classes, $av_title_font_classes and $av_display_classes 

			$atts = shortcode_atts( array( 
						'buttons'			=> '',
						'share_facebook'	=> '',
						'share_twitter'		=> '',
						'share_whatsapp'	=> '',
						'share_vk'			=> '',
						'share_tumblr'		=> '',
						'share_linkedin'	=> '',
						'share_pinterest'	=> '',
						'share_mail'		=> '',
						'share_reddit'		=> '',
						'share_yelp'		=> '',
						'yelp_link'			=> '',
						'title'				=> '',
						'style'				=> ''
					), $atts, $this->config['shortcode'] );

			extract( $atts );

			$custom_class 	= ! empty( $meta['custom_class'] ) ? $meta['custom_class'] : '';
			$custom_class  .= $meta['el_class'];
			if( $style == 'minimal' ) 
			{
				$custom_class .= ' av-social-sharing-box-minimal';
			}
				
			$output = '';
			$args = array();
			$options = false;
			$echo = false;

			if( $buttons == 'custom' )
			{
				foreach( $atts as &$att )
				{
					if( empty( $att ) ) 
					{
						$att = 'disabled';
					}
				}
				unset( $att );
				$options = $atts;
			}

			$output .= "<div {$meta['custom_el_id']} class='av-social-sharing-box {$custom_class} {$av_display_classes}'>";
			$output .=		avia_social_share_links( $args, $options, $title, $echo );
			$output .= '</div>';

			return $output;
		}

	}
}
