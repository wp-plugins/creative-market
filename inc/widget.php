<?php

 class Creative_Market_Widget extends WP_Widget {

	var $widget_cssclass;
	var $widget_description;
	var $widget_idbase;
	var $widget_name;

 	function __construct() {

		$this->widget_cssclass = 'widget_creativemarket';
		$this->widget_description = __( 'Display a Creative Market Item', 'creativemarket-widget' );
		$this->widget_idbase = 'widget_creativemarket';
		$this->widget_name = __('Creative Market Widget', 'creativemarket-widget' );

		$widget_ops = array( 'classname' => $this->widget_cssclass, 'description' => $this->widget_description );

		parent::__construct('creativemarket_widget', $this->widget_name, $widget_ops);

		add_action( 'save_post', array(&$this, 'flush_widget_cache') );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );
	}

	function widget($args, $instance) {

		$cache = wp_cache_get('widget_creativemarket', 'widget');

		if ( !is_array($cache) ) $cache = array();

		if ( isset($cache[$args['widget_id']]) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract($args);

		$title = apply_filters('widget_title', empty($instance['title']) ? __('Creative Market', 'creativemarket-widget') : $instance['title'], $instance, $this->id_base);

		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;

		$url = $instance['url'];
		$regex = '/^(http(?:s?):\/\/(?:www\.)?creativemarket\.com\/([a-zA-Z0-9-_]+)\/(\s*\d+)-([a-zA-z0-9-_]+))/';
		preg_match($regex,$url,$m);
		$cid = $m[3];

		$product_div = sprintf('<div id="creativemarket-product%s"></div>', $cid);
		$product_code = sprintf('<script>var __creativemarket__ = {width:195,height:190,productID:%s,u:""};</script>', $cid);
		$script = '<script type="text/javascript" src="https://d3ui957tjb5bqd.cloudfront.net/js/embed/1/product.js"></script>';
		echo PHP_EOL . $product_div . PHP_EOL . $product_code . PHP_EOL . $script . PHP_EOL;

		echo $after_widget;

		$content = ob_get_clean();
		if ( isset( $args['widget_id'] ) ) $cache[$args['widget_id']] = $content;
		echo $content;
		wp_cache_set('widget_creativemarket', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['url'] = $new_instance['url'];
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_creativemarket']) ) delete_option('widget_creativemarket');

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('widget_creativemarket', 'widget');
	}

	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$url = isset($instance['url']) ? esc_attr($instance['url']) : '';

		?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'creativemarket-widget'); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('title') ); ?>" name="<?php echo esc_attr( $this->get_field_name('title') ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<p><label for="<?php echo $this->get_field_id('url'); ?>"><?php _e('URL:', 'creativemarket-widget'); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id('url') ); ?>" name="<?php echo esc_attr( $this->get_field_name('url') ); ?>" type="text" value="<?php echo esc_attr( $url ); ?>" />
		</p>

		<?php
	}
}
