<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

namespace IO;

if (!class_exists('\IO\IO_Slider')) {
    class IO_Slider {
        
        protected static $instance = null;
        protected $plugin_url = null;
            
        public static function get_instance() {
            if (null === self::$instance) self::$instance = new self;
            return self::$instance;
        }

        public static function init() {

            $class = self::get_instance();

            // Add shortcode [io_slider]
            add_shortcode('io_slider', [$class, 'shortcode_callback']);

            // Add widget if Elementor plugin is active
            if (is_plugin_active( 'elementor/elementor.php' )) add_action( 'elementor/widgets/register', [$class, "register_elementor_widgets"]);

        }

        public function get_plugin_url($path = "") {
            if (null === $this->plugin_url) $this->plugin_url = defined("IO_SLIDER_URL") ? IO_SLIDER_URL : WP_PLUGIN_URL.DIRECTORY_SEPARATOR."io-slider";
            return $this->plugin_url.$path;
        }

        static function get_default_settings() {
            return [
                "template" => false, // HTML template with {@field} tags, or false for default template
    
                "query" => false, // (WP_Query instance|false) Set false to create a new query.
                "post_type" => "post", // (string|array) any, post, page, revision, or custom post type
                "posts_per_page" => "-1", // (int|-1) -1 to return all post
                "offset" => "0", // (int|-1) -1 to return all post
                "orderby" => "menu_order", // (string) "none","ID","author","title","name","type","date","modified","parent","rand","comment_count","relevance","menu_order"
                "order" => "ASC", // (string) "ASC" or "DESC"
                
                "id" => false,
    
                "arrows_position" => "outside", // (string) "outside" or "inside"
                "overflow" => "hidden",
    
                "navigation" => "arrows", // (string) "arrows", "dots", "both"
                "navigation_previous_icon" => "svg",
                "navigation_next_icon" => "svg",
                
                "slidesperview" => "1",
                "slidesperview_md" => "1",
                "slidesperview_lg" => "2",
                "slidesperview_xl" => "2",
                "spacebetween" => "0",
                
                "autoplay" => "false",
                "autoplay_speed" => "5000",
                "pause_on_interaction" => "false",
                "pause_on_hover" => "false",
                "loop" => "true",
                
                "autoheight" => "false",
                "slidetoclickedslide" => "false",
                "centeredslides" => "false",
    
            ];
        }

        static function get_default_template() {
            return '<div class="io-slide io-slide-{@ID}" data-id="{@ID}">
                {@post_thumbnail}
                <h3 class="title">{@post_title}</h3>
                <div class="content">{@post_content}</div>
            </div>';
        }

        public function render($settings = []) {

            $return = '';

            // Get options and settings
            $settings = array_replace_recursive(IO_Slider::get_default_settings(), $settings);
            if (!isset($settings["template"]) || !$settings['template'] || empty($settings['template'])) $settings["template"] = IO_Slider::get_default_template();
    
            // Use WP_Query provided in options or call a new 
            if (is_object($settings['query']) && $settings['query'] instanceof \WP_Query) {
                
                // Use provided WP_Query 
                $the_query = $settings['query'];
    
            } elseif (!empty($settings['post_type'])) {
    
                // Construct a new WP_Query object
                $query_args = [];
    
                $query_args["post_type"] = (isset($settings["post_type"]) && !empty(trim($settings['post_type']))) ? trim($settings["post_type"]) : "post";
                $query_args["posts_per_page"] = intval($settings["posts_per_page"]) ? intval($settings["posts_per_page"]) : -1;
                $query_args["offset"] = intval($settings["offset"]) ? intval($settings["offset"]) : -1;
                $query_args["orderby"] = !empty($settings['orderby']) && in_array($settings['orderby'], ["none","ID","author","title","name","type","date","modified","parent","rand","comment_count","relevance","menu_order"]) ? $settings['orderby'] : "date";
                $query_args["order"] = !empty($settings['order']) && in_array($settings['order'], ["DESC","ASC"]) ? $settings['order'] : "ASC";
    
                $the_query = new \WP_Query($query_args);
                
            } else {
    
                // No query provided or parameters provided.
                return ;
    
            }
            
            if ($the_query && $the_query->have_posts()) {
    
                // Load Swiper script and styles
                wp_enqueue_script('swiper-js', 'https://unpkg.com/swiper/swiper-bundle.min.js', '', '', true);
                wp_enqueue_style('io-slider-css', esc_url($this->get_plugin_url('assets/css/io-slider.css')), false);
    
                // Create an array of slides
                $slides = [];
                if($settings["id"]&&!empty(trim($settings["id"]))) {
                    $id = trim($settings["id"]);
                } else {
                    $id = 'io-slider-'.uniqid();
                }
    
                $return = '<div class="io-slider" id="'.$id.'">';
                
                while ($the_query->have_posts()) {
                    $the_query->the_post();
                    
                    // Do magic tags processing via PODS plugin
                    if(defined('PODS_VERSION') && function_exists('pods_shortcode')) {
                        
                        $temp_view = pods_shortcode([
                            "name" => get_post_type(),
                            "id" => get_the_ID(),
                        ], $settings["template"]);
    
                    } else {
    
                        $image = get_the_post_thumbnail() ? get_the_post_thumbnail() : '';
                        $replacements = [
                            "{@ID}" => get_the_ID(),
                            "{@the_ID}" => get_the_ID(),
                            "{@post_title}" => get_the_title(),
                            "{@title}" => get_the_title(),
                            "{@the_title}" => get_the_title(),
                            "{@permalink}" => get_the_permalink(),
                            "{@post_thumbnail}" => $image,
                            "{@post_content}" => get_the_content(),
                            "{@the_content}" => get_the_content(),
                        ];
                        
                        $temp_view = str_replace(array_keys($replacements),array_values($replacements),$temp_view);
                    }
    
                    $slides[] = '<div class="swiper-slide">'.$temp_view.'</div>';
                }
    
                wp_reset_postdata();
    
                $slides_count = count($slides);
    
                // Return slider
                $return .= '
                <div class="io-swiper '
                    . ($settings["arrows_position"]==="outside" ? " with-space" : "")
                    . ($settings["overflow"]=="hidden"||$settings["overflow"]=="false"||$settings["overflow"]===false ? " overflow-hidden" : "")
                    . '">
                    <div class="swiper-wrapper">
                        '.implode("", $slides).'
                    </div>';
                    
                    if (1 < $slides_count) :
                        
                        if ((in_array($settings['navigation'], ['dots', 'both']))) {
                            $return .= '<div class="swiper-pagination"></div>';
                        }
                        
                        $return .= $this->render_navigation($settings);
    
                    endif;
    
                $return .= '</div>';
            
                // Autoplay
                $pause_on_interaction = $settings['pause_on_interaction']==="yes" || $settings['pause_on_interaction'] === true ? "true" : "false";
                $pause_on_hover = $settings['pause_on_hover']==="yes" || $settings['pause_on_hover'] === true ? "true" : "false";
                if (intval($settings["autoplay"])) {
                    $autoplay = "{
                            delay: ".intval($settings["autoplay"]).",
                            disableOnInteraction: $pause_on_interaction,
                            pauseOnMouseEnter: $pause_on_hover,
                        }";            
                } elseif ($settings["autoplay"]==true || $settings["autoplay"]=="yes" ) {
                    $autoplay = "{
                            delay: ".intval($settings["autoplay_speed"]).",
                            disableOnInteraction: $pause_on_interaction,
                            pauseOnMouseEnter: $pause_on_hover,
                        }";            
                } else {
                    $autoplay = "false";
                }
    
                $loop = $settings["loop"]=="true"||$settings["loop"]=="yes" ? "true" : "false";
                $autoheight = $settings["autoheight"]=="true"||$settings["autoheight"]=="yes" ? "true" : "false";
                $spacebetween = intval($settings["spacebetween"]) ? $settings["spacebetween"] : "0";
    
                $script = '<script type="text/javascript" data-id="'.$id.'">
                window.addEventListener("load", function () {
                    window.io_slider_'.preg_replace("/[^ \w]+/","_",$id).' = new Swiper("#'.$id.' .io-swiper", {
                        autoplay: '.$autoplay.',
                        loop: '.$loop.',
                        autoHeight: '.$autoheight.',
                        centeredslides: '.(trim($settings["centeredslides"])).',
                        slidetoclickedslide: '.(trim($settings["slidetoclickedslide"])).',
                        
                        spacebetween: '.$spacebetween.',                    
                        slidesperview: '.(trim($settings["slidesperview"])).',
                        breakpoints: {
                            768: {
                                slidesperview: '.(trim($settings["slidesperview_md"])).'
                            },
                            1025: {
                                slidesperview: '.(trim($settings["slidesperview_lg"])).'
                            },
                            1200: {
                                slidesperview: '.(trim($settings["slidesperview_xl"])).'
                            }
                        },
                        '. ($settings["navigation"]=="false" ? "" : '
                        navigation: {
                            nextEl: ".io-swiper-button-next",
                            prevEl: ".io-swiper-button-prev",
                        },
                        ').'
                    });
                });
                </script>';
    
                add_action('wp_footer', function() use ($script) { echo $script;});
    
            }
    
            return $return;
        }

        private function render_navigation($settings = []) {
            $show_arrows = (in_array($settings['navigation'], ['arrows', 'both']));
            
            $return = "";
    
            if ($show_arrows) {
                // Left arrow
                // If the left arrow is an Elementor icon:
                if(class_exists("\Elementor\Icons_Manager") && is_array($settings["navigation_previous_icon"])&&isset($settings["navigation_previous_icon"]["value"])) {
                    
                    if (empty($settings["navigation_previous_icon"]['value'])) {
                        $settings["navigation_previous_icon"] = [
                            'library' => 'eicons',
                            'value' => 'eicon-chevron-left',
                        ];
                    }

                    ob_start();
                    ?>
                    <div class="io-swiper-button-prev" tabindex="0" role="button" aria-label="<?php echo esc_html__('Previous', 'elementor'); ?>">
                        <?php \Elementor\Icons_Manager::render_icon($settings["navigation_previous_icon"], ['aria-hidden' => 'true']); ?>
                        <span class="elementor-screen-only"><?php echo esc_html__('Previous', 'elementor'); ?></span>
                    </div>
                    <?php
                    
                    $return .= ob_get_clean();
    
                } else {
                    // If the left arrow is other type:
                    switch ($settings["navigation_previous_icon"]) {
                        case false:
                            break;
    
                        case true:
                        case "svg":
                        case "default":
                            $return .= '
                                <div class="io-swiper-button-prev" tabindex="0" role="button" aria-label="'.esc_html__('Previous', 'elementor').'">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l192 192c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256 246.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-192 192z"/></svg>
                                    <span class="elementor-screen-only">'.esc_html__('Previous', 'elementor').'</span>
                                </div>
                            ';
                            break;
    
                        case "img":
                                $return .= '
                                <div class="io-swiper-button-prev" tabindex="0" role="button" aria-label="'.esc_html__('Previous', 'elementor').'">
                                    <img class="slider-button" src="' . esc_url($this->get_plugin_url('assets/img/button-left.svg')) . '" alt="Previous slide" />
                                    <span class="elementor-screen-only">'.esc_html__('Previous', 'elementor').'</span>
                                </div>
                            ';
                            break;
    
                        default:
                            break;
                    }
                }
    
    
                // If the right arrow is an Elementor icon:
                if(class_exists("\Elementor\Icons_Manager") && is_array($settings["navigation_next_icon"])&&isset($settings["navigation_next_icon"]["value"])) {
    
                    if (empty($settings["navigation_next_icon"]['value'])) {
                        $settings["navigation_next_icon"] = [
                            'library' => 'eicons',
                            'value' => 'eicon-chevron-right',
                        ];
                    }
                    ob_start();
                    ?>
                    <div class="io-swiper-button-next" tabindex="0" role="button" aria-label="<?php echo esc_html__('Next', 'elementor'); ?>">
                        <?php \Elementor\Icons_Manager::render_icon($settings["navigation_next_icon"], ['aria-hidden' => 'true']); ?>
                        <span class="elementor-screen-only"><?php echo esc_html__('Next', 'elementor'); ?></span>
                    </div>
                    <?php
                    
                    $return .= ob_get_clean();
    
                } else {
                    // If the right arrow is other type:
                    switch ($settings["navigation_next_icon"]) {
                        case false:
                            break;
    
                        case true:
                        case "svg":
                        case "default":
                            $return .= '
                                <div class="io-swiper-button-next" tabindex="0" role="button" aria-label="'.esc_html__('Next', 'elementor').'">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z"/></svg>
                                    <span class="elementor-screen-only">'.esc_html__('Next', 'elementor').'</span>
                                </div>
                            ';
                            break;
    
                        case "img":
                            $return .= '
                                <div class="io-swiper-button-next" tabindex="0" role="button" aria-label="'.esc_html__('Next', 'elementor').'">
                                    <img class="slider-button" src="' . esc_url($this->get_plugin_url('assets/img/button-right.svg')) . '" alt="Next slide" />
                                    <span class="elementor-screen-only">'.esc_html__('Next', 'elementor').'</span>
                                </div>
                            ';
                            break;
    
                        default:
                            break;
                    }
                }
            }
    
            return $return;
        }
        
        public function shortcode_callback($atts = [], $slide_template = '') {
            // Get default code for a wordpress shortcode
            $settings = shortcode_atts($this::get_default_settings(), $atts);
            if($slide_template && !empty($slide_template)) $settings["template"] = $slide_template;
            
            ob_start();
            echo $this->render($settings);
            return ob_get_clean();
        }

        
        public function register_elementor_widgets( $widgets_manager ) {
            require_once(__DIR__."/../elementor/io-slider-widget.php");
            $widgets_manager->register( new \IO\Elementor\Slider() );
        }
    }
}