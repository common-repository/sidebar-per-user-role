<?php
/*
Plugin Name: Sidebar Per User Role
Plugin URI: http://en.bainternet.info
Description: This Plugin lets you display a sidebar per user role
Version: 0.3
Author: Bainternet
Author Email: admin@bainternet.info
License:

  Copyright 2013 Bainternet (admin@bainternet.info)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  
*/

if (!class_exists('SidebarPerRole')){
	/**
	* SidebarPerRole
	* @author Ohad Raz <admin@bainternet.info>
	* a class to register and display a sidebar per user role
	*/
	class SidebarPerRole
	{	
		/**
		 * $before_widget
		 * @var string
		 * @access public
		 */
		public $before_widget = '<li id="%1$s" class="widget %2$s">';
		/**
		 * $after_widget 
		 * @var string
		 * @access public
		 */
		public $after_widget  = '</li>';
		/**
		 * $before_title
		 * @var string
		 * @access public
		 */
		public $before_title  = '<h2 class="widgettitle">';
		/**
		 * $after_title 
		 * @var string
		 * @access public
		 */
		public $after_title   = '</h2>';
		/**
		 * $class
		 * @var string
		 * @access public
		 */
		public $class = 'user-sidebar';
		/**
		 * $sidebar_to_replace
		 * @var string
		 * @access public
		 */
		public $sidebar_to_replace = 'guest-sidebar';
		/**
		 * $user_sidebars holds the plugin generated sidebars
		 * @var array
		 * @access public
		 */
		public $user_sidebars = array();
		/**
		 * $option_name holds database option name
		 * @var string
		 * @access public
		 */
		public $option_name = 'sidebarperrole';

		/**
		 * class constructor
		 * @author Ohad Raz <admin@bainternet.info>
		 * @access public
		 * @param array $args array of arguments ex:
		 *  class - CSS class name to assign to the widget HTML (default: user-sidebar).
		 *  before_widget - HTML to place before every widget(default: '<li id="%1$s" class="widget %2$s">')
		 *  after_widget - HTML to place after every widget (default: "</li>\n").
		 *  before_title - HTML to place before every title (default: <h2 class="widgettitle">).
		 *  after_title - HTML to place after every title (default: "</h2>\n").
		 */
		function __construct($args = array()){
			//set defaults
			$this->set_props($args);
			//set hooks
			$this->hooks();
		}

		/**
		 * init 
		 * this function creates the admin panel
		 * @return void
		 */
		function init(){
			if (is_admin()){
				require_once(plugin_dir_path(__FILE__).'inc/Simple_Panel_class.php');
				require_once(plugin_dir_path(__FILE__).'inc/Sidebar_Panel_Class.php');
				$p = new user_role_sidebar_panel(
					array(
						'title'      => __('Sidebar Per Role'),
						'name'       => __('Sidebar Per Role'),
						'capability' => 'manage_options',
						'option'     => $this->option_name,
					)
				);
				//section
				$setting = $p->add_section(array(
					'option_group'      =>  'sidebarperrole-group',
					'sanitize_callback' => null,
					'id'                => 'sidebarperrole-section-id', 
					'title'             => __('Sidebar Per Role settings')
					)
				);
				$p->add_field(array(
					'label'   => 'Disable Guest Sidebar?',
					'std'     => false,
					'type'    => 'checkbox',
					'id'      => 'disable_guest',
					'section' => $setting,
					'desc'    => __('Check to disable the guest sidebar (when using a custom sidebar bellow other then guest sidebar'),
					)
				);
				//sidebars
				$roles = $this->get_editable_roles();
				//add sidebar per role
				foreach ((array)$roles as $key => $r) {
					$this->user_sidebars[] = strtolower(str_replace(' ', '_' ,$key)) .'-sidebar';
				}
				$p->add_field(array(
					'label'   => 'Select Sidebar to Replace based on user role',
					'std'     => $this->sidebar_to_replace,
					'id'      => 'sidebar_to_replace',
					'type'    => 'sidebars',
					'section' => $setting,
					'exclude' => $this->user_sidebars,
					'desc'    => __('Select the Sidebar to replace automatically'),
					)
				);
				$text_inputs = array('class','before_widget','after_widget','before_title','after_title');
				foreach ($text_inputs as $key) {
					$p->add_field(array(
						'label'   => $key,
						'std'     => $this->$key,
						'type'    => 'text',
						'id'      => $key,
						'section' => $setting,
						'desc'    => __('See help tab above for details'),
						)
					);
				}
				$p->add_help_tab(array(
					'id' => 'some-id',
					'title'    => 'Sidebar Per Role',
					'content'  => '<div style="min-height: 350px">
						<h2 style="text-align: center;">'.__('Sidebar Per Role').'</h2>
						<div>
							<p>'  . __('This plugin provides the easiest way to use a sidebar per user role').'.</p>
							<h4>' .__('Usage').'</h4>
							<p>'  .__('Simple select the sidebar you wish to replace based on the user role from the list below and place the widgets you want for each role in the matching sidebar, and you are done.').'</p>
							<p>'  .__('By default the plugin adds a guest sidebar which can be removed by checking the box below, but if you want to use it just place this sinppet in your theme (where you actually want it to show up)').'</p>
							<pre style="background-color: #EFEFEF;">&lt;?php dynamic_sidebar( "guest-sidebar" );  ?&gt;</pre>
							<h4>' .__('Styling').'</h4>
							<p>' .__('By default the plugin will pickup the theme\'s css for styling but when using the guest sidebar you can set the stylink as you want:').'</p>
							<ul>
								<li><strong>before_widget</strong> - '.__('HTML to place before every widget(default: \'&lt;li id=&quot;%1$s&quot; class=&quot;widget %2$s&quot;&gt;\''). '</li>
		 						<li><strong>after_widget</strong> - '.__('HTML to place after every widget (default: "&lt;/li&gt;\n").').'</li>
		 						<li><strong>before_title</strong> - '.__('HTML to place before every title (default: &lt;h2 class=&quot;widgettitle&quot;&gt;)').'</li>
		 						<li><strong>after_title</strong> - '.__('HTML to place after every title (default: "&lt;/h2&gt;\n").').'</li>
		 					</ul>
		 					<p>' .__('If you like my wrok then please ') .'<a class="button button-primary" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=K4MMGF5X3TM5L" target="_blank">' . __('Donate') . '</a>
						</div>
					</div>
					'
					)
				);
			}
		}

		/**
		 * set_props sets default and user defined properties
		 * @author Ohad Raz <admin@bainternet.info>
		 * @access public
		 * @param array   $args       user defined properties
		 * @param boolean $properties optional array od specific properties to set
		 */
		function set_props($args = array(), $properties = false){
			if (!is_array($properties))
				$properties = array_keys(get_object_vars($this));

			foreach ($properties as $key ) {
			  $this->$key = (isset($args[$key]) ? $args[$key] : $this->$key);
			}
		}

		/**
		 * hooks function to hook all needed actions and filters
		 * @author Ohad Raz <admin@bainternet.info>
		 * @access public
		 * @return void
		 */
		function hooks(){
			//register sidebars
			add_action( 'widgets_init',array($this, 'register_sidebars'),100);

			//replace sidebars
			add_action('wp_head',array($this,'replace_sidebars'));

			//plugin row meta
			add_filter( 'plugin_row_meta', array($this,'_my_plugin_links'), 10, 2 );

			//initiate
			add_action('init',array($this,'init'));
		}

		public function getOptions(){
			return get_option($this->option_name,array(
				'sidebar_to_replace' => $this->sidebar_to_replace
				)
			);
		}

		/**
		 * register_sidebars function that registers a sidebar per user role and a guest sidebar
		 * @author Ohad Raz <admin@bainternet.info>
		 * @access public
		 * @return void
		 */
		function register_sidebars(){
			$roles = $this->get_editable_roles();
			$std = $this->getOptions();
			$this->set_props($std);
			//add sidebar per role
			foreach ((array)$roles as $key => $r) {
				$args = array(
					'name'          => $r['name'] .__( ' Sidebar' ),
					'id'            => str_replace(' ', '_' ,$key) .'-sidebar',
					'description'   => __('Sidebar For ').$r['name'] .__(' Role Users'),
					'class'         => $this->class . ' '. str_replace(' ', '_' ,$key) .'-sidebar',
					'before_widget' => $this->before_widget,
					'after_widget'  => $this->after_widget,
					'before_title'  => $this->before_title,
					'after_title'   => $this->after_title,
				);
				register_sidebar( $args );
			}
			
			if (!isset($std['disable_guest'])){

				//add guest sidebar
				$args = array(
					'name'          => __( 'Guest Sidebar' ),
					'id'            => 'guest-sidebar',
					'description'   => __('Sidebar For Guests'),
					'class'         => $this->class . ' ' . 'guest-sidebar',
					'before_widget' => $this->before_widget,
					'after_widget'  => $this->after_widget,
					'before_title'  => $this->before_title,
					'after_title'   => $this->after_title,
				);
				register_sidebar( $args );
			}
		}

		/**
		 * get_editable_roles gets an array of defined iser role
		 * @author Ohad Raz <admin@bainternet.info>
		 * @access public
		 * @return array an array of user roles
		 */
		function get_editable_roles() {
		    global $wp_roles;

		    $all_roles = $wp_roles->roles;
		    $editable_roles = apply_filters('editable_roles', $all_roles);

		    return $editable_roles;
		}

		/**
		 * replace_sidebars the magic function which replaces the sidebar based on the current user role
		 * @author Ohad Raz <admin@bainternet.info>
		 * @access public
		 * @return void
		 */
		function replace_sidebars(){
			global $_wp_sidebars_widgets, $post, $wp_registered_sidebars, $wp_registered_widgets;
			//exit early if user is a guest
			if (!is_user_logged_in())
				return;

			$role = $this->get_user_role();
			$sidebar_id = str_replace(' ', '_' ,strtolower($role)) .'-sidebar';
			/*var_dump($sidebar_id);
			var_dump($_wp_sidebars_widgets);
			die();*/
			if ($role && isset($_wp_sidebars_widgets[$sidebar_id]) && count($_wp_sidebars_widgets[$sidebar_id]) >0 ){
				$_wp_sidebars_widgets[$this->sidebar_to_replace] = $_wp_sidebars_widgets[$sidebar_id];
			}
		}

		/**
		 * get_user_role function to get a user role
		 * @author Ohad Raz <admin@bainternet.info>
		 * @access public
		 * @return string role name
		 */
		function get_user_role(){
			global $wp_roles;
			$current_user = wp_get_current_user();
			$roles = $current_user->roles;
			$role = array_shift($roles);
			return isset($wp_roles->role_names[$role]) ? $wp_roles->role_names[$role] : false;
		}

		public function _my_plugin_links($links, $file) {
            $plugin = plugin_basename(__FILE__); 
            if ($file == $plugin) // only for this plugin
                    return array_merge( $links,
                array( '<a href="http://en.bainternet.info/category/plugins">' . __('Other Plugins by Bainternet' ) . '</a>' ),
                array( '<a href="http://wordpress.org/support/plugin/sidebar-per-user-role">' . __('Plugin Support') . '</a>' ),
                array( '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=K4MMGF5X3TM5L" target="_blank">' . __('Donate') . '</a>' )
            );
            return $links;
        }
	}//end class
}//end if
global $sidebars_per_role;
$sidebars_per_role = new SidebarPerRole();