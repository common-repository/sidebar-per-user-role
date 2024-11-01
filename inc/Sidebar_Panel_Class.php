<?php
/**
 * user_role_sidebar_panel adds a select dropdown of sidebars to simplepanel class
 */
if (!class_exists('user_role_sidebar_panel')){
	class user_role_sidebar_panel extends SimplePanel{
		function  _setting_sidebars($args) {
			$std = isset($args['std'])? $args['std'] : '';
		    $name = esc_attr( $args['name'] );
		    $value = esc_attr( $this->get_value($args['id'],$std));
			$items =  $GLOBALS['wp_registered_sidebars'];
			echo "<select name='$name'>";
			foreach($items as $s) {
				if (!in_array($s['id'], $args['exclude'])){
					$v = $s['id'];
					$l = $s['name'];
					$selected = ($value == ucwords($v)) ? 'selected="selected"' : '';
					echo "<option value='".ucwords($v)."' $selected>".ucwords($l)."</option>";
				}
			}
			echo "</select>";
		}
	}//end class
}//end if