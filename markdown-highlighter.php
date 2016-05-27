<?php
/**
 * Plugin Name: Markdown Highlighter
 * Plugin URI: http://www.appzcoder.com
 * Description: Parse and Highlight The Markdown.
 * Version: 0.1
 * Author: Sohel Amin
 * Author URI: http://www.sohelamin.com
 * License: GPL2
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

class Appzcoder_Markdown_Highlighter {
    /**
     * Instance of this class.
     *
     * @var static
     */
    protected static $instance;

    /**
     * Class constructor
     */
    public function __construct() {
        include dirname( __FILE__ ) . '/parsedown/Parsedown.php';
        include dirname( __FILE__ ) . '/parsedown/ParsedownExtra.php';

        remove_filter( 'the_content', 'wpautop' );
        remove_filter( 'the_excerpt', 'wpautop' );
        add_filter( 'the_content', [ $this, 'parse_content' ], 8, 1 );

        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        add_action( 'wp_footer', [ $this, 'enqueue_footer_js' ] );
    }

    /**
     * Instantiate the class as an object.
     *
     * @return static
     */
    public static function init() {
        if ( null === static::$instance ) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Parse the contant and return as highlight format
     *
     * @param  string $markdown_content (post content)
     *
     * @return string
     */
    public function parse_content( $markdown_content ) {
        $parsedown = new ParsedownExtra();
        $parsedown->setBreaksEnabled( true );

        return $parsedown->text( $markdown_content );
    }

    /**
     * Enqueue the required js & css files
     *
     * @return void
     */
    public function enqueue_scripts() {
        wp_enqueue_style( 'appzcoder-mh-styles', plugins_url( '', __FILE__ ) . '/assets/style.css', false );
        wp_enqueue_script( 'appzcoder-mh-scripts', plugins_url( '', __FILE__ ) . '/assets/highlight.min.js', [], false );
    }

    /**
     * Enqueue js codes into footer
     *
     * @return void
     */
    public function enqueue_footer_js() {
    ?>
        <script>
            jQuery(document).ready(function( $ ) {
                $('pre code').each(function(i, block) {
                    hljs.highlightBlock(block);
                });
            });
        </script>
    <?php
    }
}

Appzcoder_Markdown_Highlighter::init();
