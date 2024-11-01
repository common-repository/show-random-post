<?php
 
class showrandompost_widget extends WP_Widget {
 
    function __construct(){
        // Constructor del Widget
        $widget_ops = array('classname' => 'showrandompost_widget', 'description' => "Show Random Post" );
        parent::__construct('showrandompost_widget', "Show random Post", $widget_ops);
    }
 
    function widget($args,$instance){
        // Contenido del Widget que se mostrará en la Sidebar
        extract($args);
        $id = preg_replace("/[^0-9]/","",$args["widget_id"]);
        echo $before_widget; 
        $title =  $instance["showrandompost_title"];
        $description = $instance["showrandompost_descr"]; 
        $tiempo = $instance["showrandompost_time"]; 
        $categories = $instance["showrandompost_categories"];
        $categories = json_encode($categories);
        $cat64 = base64_encode($categories);
        $ids = get_all_published_ids($categories);
        $i = array_rand($ids, 1);
        $postid = $ids[$i];
            if (isset($title))
                echo "<h3 class=\"widget-title\">".$title."</h3>";
            if (isset($description))
                echo "<p id=\"widget-desc\">".$description."</p>";
        echo "<div id=\"display-post-".$id."\" class=\"display-post\" data-timeexec=\"".$tiempo."\" data-postid=\"" . $postid . "\" data-widgetid=\"".$id."\" data-catg=\"" . $cat64 . "\">";
        echo "<a href=\"";
        echo post_permalink($postid)."\">";
        if(has_post_thumbnail($postid)) {
        	echo get_the_post_thumbnail($postid, 'full');
        } else {
            echo "<img src=\"".plugins_url('../img/noimage.png',__FILE__)."\" alt=\"noimage\" id=\"noimage\">";
        }
        echo "<h4 id=\"widget-title-".$id."\" class=\"widget-post-title\">".apply_filters('the_title', get_post_field('post_title', $postid))."</h4>";
        echo "<div id=\"widget-content-".$id."\" class=\"widget-content\">".get_the_excerpt_byid($postid)."</div>";
        echo "</a></div>";
        echo $after_widget;
    }
 
    function update($new_instance, $old_instance){
        // Función de guardado de opciones  
        $instance = $old_instance;
        $instance["showrandompost_title"] = strip_tags($new_instance["showrandompost_title"]);
        $instance["showrandompost_descr"] = strip_tags($new_instance["showrandompost_descr"]);
        $instance['showrandompost_time'] = $new_instance['showrandompost_time'];
        $instance['showrandompost_categories'] = $new_instance['showrandompost_categories'];
        // Repetimos esto para tantos campos como tengamos en el formulario.
        return $instance;      
    }
 
    function form($instance){
        // Formulario de opciones del Widget, que aparece cuando añadimos el Widget a una Sidebar
            $defaults = array( 'showrandompost_categories' => array() );
            $instance = wp_parse_args( (array) $instance, $defaults );    
     ?>
        <p>
            <label for="<?php echo $this->get_field_id('showrandompost_title'); ?>"><?php _e('Title','show_random_post'); ?></label>
            <input type="text" id="<?php echo $this->get_field_id('showrandompost_title'); ?>" name="<?php echo $this->get_field_name('showrandompost_title'); ?>" <?php if (isset($instance["showrandompost_title"])) { ?> value="<?php echo $title = $instance["showrandompost_title"]; ?>" <?php } ?>>
        </p>
         <p>
            <label for="<?php echo $this->get_field_id('showrandompost_descr'); ?>"><?php _e('Description','show_random_post'); ?></label>
            <input type="text" id="<?php echo $this->get_field_id('showrandompost_descr'); ?>" name="<?php echo $this->get_field_name('showrandompost_descr'); ?>" <?php if (isset($instance["showrandompost_descr"])) { ?> value="<?php echo $title = $instance["showrandompost_descr"]; ?>" <?php } ?>>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('showrandompost_categories'); ?>"><?php _e('Category','show_random_post'); ?></label>
                <?php $walker = new Walker_Category_Checklist_Widget(
                  $this->get_field_name('showrandompost_categories'), $this->get_field_id('showrandompost_categories')
                );
                echo '<ul class="categorychecklist">';
                wp_category_checklist( 0, 0, $instance['showrandompost_categories'], FALSE, $walker, FALSE);
                echo '</ul>'; ?>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('showrandompost_time'); ?>"><?php _e('Exec Time','show_random_post'); ?></label>
                <select id="<?php echo $this->get_field_id('showrandompost_time'); ?>" name="<?php echo $this->get_field_name('showrandompost_time'); ?>">
                    <option value="10" <?php if ((isset($instance["showrandompost_time"])) && $instance["showrandompost_time"]=="10") { echo "selected='selected'"; } ?>><?php _e('10 seconds','show_random_post'); ?></option>
                    <option value="30" <?php if ((isset($instance["showrandompost_time"])) && $instance["showrandompost_time"]=="30") { echo "selected='selected'"; } ?>><?php _e('30 seconds','show_random_post'); ?></option>
                    <option value="60" <?php if ((isset($instance["showrandompost_time"])) && $instance["showrandompost_time"]=="60") { echo "selected='selected'"; } ?>><?php _e('1 minute','show_random_post'); ?></option>
                    <option value="120" <?php if ((isset($instance["showrandompost_time"])) && $instance["showrandompost_time"]=="120") { echo "selected='selected'"; } ?>><?php _e('2 minutes','show_random_post'); ?></option>
                </select>
                    </p>
        <?php
    }    
} 
 
// This is required to be sure Walker_Category_Checklist class is available
require_once ABSPATH . 'wp-admin/includes/template.php';
/**
 * Custom walker to print category checkboxes for widget forms
 */
class Walker_Category_Checklist_Widget extends Walker_Category_Checklist {

  private $name;
  private $id;

  function __construct( $name = '', $id = '' ){
      $this->name = $name;
      $this->id = $id;
  }

  function start_el( &$output, $cat, $depth = 0, $args = array(), $id = 0 ) {
    extract($args);
    if ( empty($taxonomy) ) $taxonomy = 'category';
    $class = in_array( $cat->term_id, $popular_cats ) ? ' class="popular-category"' : '';
    $id = $this->id . '-' . $cat->term_id;
    $checked = checked( in_array( $cat->term_id, $selected_cats ), true, false );
    $output .= "\n<li id='{$taxonomy}-{$cat->term_id}'$class>" 
      . '<label class="selectit"><input value="' 
      . $cat->term_id . '" type="checkbox" name="' . $this->name 
      . '[]" id="in-'. $id . '"' . $checked 
      . disabled( empty( $args['disabled'] ), false, false ) . ' /> ' 
      . esc_html( apply_filters('the_category', $cat->name )) 
      . '</label>';
    }
}

?>