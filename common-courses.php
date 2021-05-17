<?

/*
	Plugin Name: Common - Courses
	Description: WordPress plugin that provides a shortcode to display a table of courses for a given department.
	Version: 1
	Author: Rachel Tran
	License: GPL2
	License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

function courses_plugin_load_scripts() {
    date_default_timezone_set('America/New_York');
    $ver = date("ymd-Gis", filemtime( plugin_dir_path( __FILE__ ) . 'includes/courses.js'));

    wp_enqueue_script('courses_js', plugins_url( 'includes/courses.js', __FILE__ ), array(), $ver);
}
add_action('wp_enqueue_scripts', 'courses_plugin_load_scripts');

include "includes/courses.php";

function courses_handler($atts = []) {
    $attributes = shortcode_atts([
        'dio' => false,
        'start' => false,
        'end' => false,
    ], $atts);

    $dept = DEPT;
    $dio = $atts['dio'];
    $start = $atts['start'];
    $end = $atts['end'];
    
    if ($dio) {
        $dept = $dio;
    }

    if ($start) {
        $start = strtolower($start);
    }

    if ($end) {
        $end = strtolower($start);
    }
    
    if (defined('DEPT') || $dio) {
        ob_start();
        render_courses($dept, $start, $end);
        return ob_get_clean();
    } else {
        echo '<div class="alert alert-danger text-center"><strong>Department ID</strong> could not be found.</div>';
    }
}
add_shortcode('courses', 'courses_handler');

?>