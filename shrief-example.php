<?php 

/**
 * @package ShriefPlugin
 * 
 * Plugin Name: Shrief Example 1
 */




 class ShriefPlugin{

    function __construct(){

        add_action('init',[$this, 'menu_page_init']);
        add_action('init',array($this, 'change_background'));
        add_action('admin_enqueue_scripts',[$this, 'shrief_load_admin_script']);
        add_action('admin_init',[$this, 'handle_sortable']);
        add_action('admin_init',[$this, 'handle_register_settings']);
        add_action('admin_init',[$this,'handle_ajax']);
        // add_action('admin_init',[$this,'get_posts']);
        // add_action('admin_init',[$this,'get_number_of_posts']);
        
        add_action( 'customize_register', [$this,'shrief_customize'] );
    }

    public function shrief_customize($wp_customize){

        $wp_customize->add_section('shrief_color_picker',array(
            'title'=>'Color Picker'
        ));
        $wp_customize->add_setting('shrief_cp_settings',array(
            'default'=> '#ffff',
            'transport'=>'postMessage'
        ));
        $wp_customize->add_control('shrief_cp_settings',array(
            'label'=>'Color Picker',
            'section'=>'shrief_color_picker',
            'type'=>'color'
        ));

        // $wp_customize->selective_refresh->add_partial('shrief_cp_settings', array(
        //     'selector'=>'#site-header',
        //     'container_inclusive'=>false,
        //     'render_callback'=> function(){
        //         echo '<p>abcdef</p>';
        //     },
        //     'fallback_refresh'=>true
        // ));
        add_action( 'customize_preview_init', array($this,'mytheme_customizer_preview_scripts') );
        
    }
    function mytheme_customizer_preview_scripts() {
        wp_enqueue_script( 'mytheme-customizer-preview', trailingslashit( plugin_dir_url(__FILE__) ) . 'assets/js/shrief-test-app.js', array( 'customize-preview', 'jquery' ) );
     }
    public function change_background(){
        add_action('wp_footer',function(){
            $bgcolor = get_theme_mod('shrief_cp_settings');
            echo '<style>
                #site-header{
                    background-color: ' . $bgcolor .
                ' !important ;}
            </style>';
        });
    }



    public function test(){
        var_dump(get_post_meta(28,'_tax_status')) ;
    }
    public static function get_posts(){
        $args = array(  
            'post_type' => 'shrief_note',
            'orderby' => 'meta_value_num'
        );
        $loop = new WP_Query( $args ); 
        return $loop->posts;
    }
    public static function get_posts_with_meta_query(){
        $args = array(  
            'post_type' => 'shrief_note',
            'orderby' => 'meta_value_num',
            'meta_query' => array(
                array(
                    'key'       => '_note_position',
                    'compare'   => '>',
                    'type'      => 'NUMERIC',
                    'order' => 'DESC'
                ),
            ),
        );
        $loop = new WP_Query( $args ); 
        return $loop->posts;
    }
    public static function get_number_of_posts(){
        $args = array(  
            'post_type' => 'shrief_note'
        );
        $loop = new WP_Query( $args ); 
        return count($loop->posts);
    }
    public function menu_page_init(){
        add_action('admin_menu',function(){
            add_menu_page('Note Minder','Note Minder','manage_options','shrief-note-minder',[$this,'shrief_note_minder_call_back'],'',3);
        });
        
    }

    public function shrief_load_admin_script(){

        wp_enqueue_style('shrief-css-script',plugin_dir_url(__FILE__) . 'assets/css/shrief-example-style.css',[]);
        wp_enqueue_script('shrief-js-script',plugin_dir_url(__FILE__) . 'assets/js/shrief-app.js',['jquery']);
        wp_localize_script( 'shrief-js-script', 'thienobj',
        array( 
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'data_var_1' => 'value 1',
            'data_var_2' => 'value 2',
            'nonce' => wp_create_nonce('thien')
        )
    );
        
    }

    public function handle_register_settings(){
        register_setting('shrief-note-minder-group','shrief_note_title');
        register_setting('shrief-note-minder-group','shrief_note_content');
    }
    public function handle_sortable(){
        add_action('admin_head',function(){
            ?>
            <script>
                jQuery( function() {
                    jQuery( "#sortable" ).sortable();
                } );
            </script>
            <?php
        });
    }


    public function handle_ajax(){
        add_action('wp_ajax_shrief_insert_post',function(){
            global $wpdb;
            check_ajax_referer('thien','nonce',true);
            $post_id =  wp_insert_post([
                'post_title' => $_POST['note_title'],
                'post_content' => $_POST['note_content'],
                'post_type' => 'shrief_note'
            ]);
            $number_posts = ShriefPlugin::get_number_of_posts();
            $meta_id = add_post_meta($post_id, '_note_position',$number_posts);
            $post_title = get_post($post_id)->post_title ;
            $meta_value = get_post_meta($post_id,'_note_position');
            echo '{ "post_id" : "'.$post_id.'", "post_title" : "'.$post_title.'" , "position" : "' . $meta_value[0] .  '" }';
            wp_die();
        });
        add_action('wp_ajax_shrief_save_post',function(){
            global $wpdb;
            $post_id =  wp_update_post([
                'ID' => $_POST['post_id'],
                'post_title' => $_POST['note_title'],
                'post_content' => $_POST['note_content'],
            ]);
            
            echo '{ "post_id" : "'.$_POST['post_id'].'", "post_title" : "'.$_POST['note_title'].'" }';
            wp_die();
        });

        add_action('wp_ajax_shrief_delete_post', function(){
            global $wpdb;
            $post_id = $_POST['post_id'];
            delete_post_meta($post_id,'_note_position');
            wp_delete_post($post_id);
            wp_die();
        });

        add_action('wp_ajax_shrief_sort_change',function(){
            global $wpdb;
            $posts = ShriefPlugin::get_posts();
            $index = 0;
            $data = $_POST['data'];
            echo count($data);
            for ($i=0; $i <count($data) ; $i++) { 
                update_post_meta($data[$i],'_note_position',count($data)-$i);
            }
            wp_die();
        });
        add_action('wp_ajax_shrief_edit',function(){
            global $wpdb;
            $post = get_post($_POST['data']);
            echo '{ "title" : "'.$post->post_title.'", "content" : "'.$post->post_content.'", "post_id" : "' .$post->ID . '"}';
            wp_die();
        });


        add_action('wp_ajax_shshd',function(){
            wp_die();
        });


    }

    public function shrief_note_minder_call_back(){
        require_once(plugin_dir_path(__FILE__) . 'templates/shrief-note-minder-temp.php');
    }
 }


$shriefPlugin = new ShriefPlugin();