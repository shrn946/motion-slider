<?php
/*
Plugin Name: Motion Slider
Description: A motion image slider with two styles via shortcode.
Version: 1.0
Author: WP Design Lab
*/

if (!defined('ABSPATH')) exit;

// ✅ Register assets (styles + both scripts)
function motion_slider_register_assets() {
    if (is_admin()) return;

    $plugin_url = plugin_dir_url(__FILE__);

    wp_register_style('motion-slider-style', $plugin_url . 'css/base.css');

    wp_register_script('motion-slider-charming', $plugin_url . 'js/charming.min.js', [], false, true);
    wp_register_script('motion-slider-imagesloaded', $plugin_url . 'js/imagesloaded.pkgd.min.js', [], false, true);
    wp_register_script('motion-slider-tweenmax', $plugin_url . 'js/TweenMax.min.js', [], false, true);

    wp_register_script('motion-slider-demo', $plugin_url . 'js/demo.js', ['motion-slider-charming', 'motion-slider-imagesloaded', 'motion-slider-tweenmax'], false, true);
    wp_register_script('motion-slider-demo2', $plugin_url . 'js/demo2.js', ['motion-slider-charming', 'motion-slider-imagesloaded', 'motion-slider-tweenmax'], false, true);
}
add_action('wp_enqueue_scripts', 'motion_slider_register_assets');

// ✅ Common slider HTML output
function motion_slider_render_html() {
    $slides = get_option('motion_slider_slides', []);
    if (empty($slides)) return '';

    ob_start(); ?>
    <div class="loading demo-1">
        <main>
            <div class="deco deco--bg"></div>
            <div class="deco deco--shape deco--shape-hor deco--shape-1"></div>
            <div class="deco deco--shape deco--shape-hor deco--shape-2"></div>
            <div class="slideshow">
                <?php foreach ($slides as $index => $slide): ?>
                    <div class="slide">
                        <div class="slide__image<?php echo $index > 0 ? ' slide__image--hidden' : ''; ?>"
                             style="background-image:url('<?php echo esc_url($slide['img']); ?>')"></div>
                        <h2 class="slide__title<?php echo $index > 0 ? ' slide__title--hidden' : ''; ?>">
                            <?php echo esc_html($slide['title']); ?>
                        </h2>
                    </div>
                <?php endforeach; ?>
                <nav class="nav">
                    <button class="nav__button nav__button--previous">&#10094;</button>
                    <button class="nav__button nav__button--next">&#10095;</button>
                </nav>
            </div>
        </main>
    </div>
    <?php return ob_get_clean();
}

// ✅ Shortcode for demo.js
function motion_slider_demo1_shortcode() {
    if (is_admin()) return '';

    wp_enqueue_style('motion-slider-style');
    wp_enqueue_script('motion-slider-demo');

    return motion_slider_render_html();
}
add_shortcode('motion_slider_1', 'motion_slider_demo1_shortcode');


// ✅ Shortcode for demo2.js
function motion_slider_demo2_shortcode() {
    if (is_admin()) return '';

    wp_enqueue_style('motion-slider-style');
    wp_enqueue_script('motion-slider-demo2');

    return motion_slider_render_html();
}
add_shortcode('motion_slider_2', 'motion_slider_demo2_shortcode');
// ✅ Admin Menu
function motion_slider_menu() {
    add_menu_page('Motion Slider', 'Motion Slider', 'manage_options', 'motion_slider', 'motion_slider_admin_page');
}
add_action('admin_menu', 'motion_slider_menu');

// ✅ Admin Scripts and Styles
function motion_slider_admin_scripts($hook) {
    if ($hook !== 'toplevel_page_motion_slider') return;

    $plugin_url = plugin_dir_url(__FILE__);
    wp_enqueue_media();
    wp_enqueue_script('motion-slider-admin', $plugin_url . 'js/admin.js', ['jquery', 'jquery-ui-sortable'], false, true);
    wp_localize_script('motion-slider-admin', 'motion_slider_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('motion_slider_nonce')
    ]);
}
add_action('admin_enqueue_scripts', 'motion_slider_admin_scripts');

// ✅ Admin Page HTML
function motion_slider_admin_page() {
    $slides = get_option('motion_slider_slides', []);
    ?>
    <div class="wrap">
        <h1>Motion Slider Settings</h1>
        <ul id="motion-slider-list">
            <?php foreach ($slides as $index => $slide): ?>
                <li class="motion-slide-item" data-index="<?php echo $index; ?>">
                    <img src="<?php echo esc_url($slide['img']); ?>" width="100">
                    <input type="text" class="motion-title" value="<?php echo esc_attr($slide['title']); ?>" placeholder="Enter title">
                    <button class="remove-slide">Remove</button>
                </li>
            <?php endforeach; ?>
        </ul>
        <button id="add-slide">Add Slide</button>
        <button id="save-slides" class="button button-primary">Save Slides</button>
        
        <h2>Motion Slider Plugin Instructions</h2>

<p>The <strong>Motion Slider Plugin</strong> allows you to display animated sliders on your WordPress site using simple shortcodes. Follow the steps below to use the plugin effectively:</p>

<h3>How to Use</h3>

<ol>
  <li>Install and activate the plugin from your WordPress admin dashboard.</li>
  <li>Navigate to the <strong>Motion Slider</strong> menu in the WordPress admin sidebar.</li>
  <li>Upload images and configure your slider settings as needed.</li>
</ol>

<h3>Available Shortcodes</h3>

<ul>
  <li>
    <code>[motion_slider_1]</code><br>
    <em>This shortcode displays the first slider type.</em>
  </li>
  <li>
    <code>[motion_slider_2]</code><br>
    <em>This shortcode displays the second slider type.</em>
  </li>
</ul>


<h3>Notes</h3>
<ul>
  <li>You can use these shortcodes anywhere on your site: in posts, pages, or widgets.</li>
  <li>Each slider type may have different animations or layout styles. Test both to choose the one that fits your design.</li>
</ul>

<p>For any issues or customizations, please refer to the plugin documentation or contact the developer.</p>


        <style>
          /* Container */
.wrap {
  max-width: 900px;
  margin-top: 30px;
  background: #fff;
  padding: 25px;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

/* Heading */
.wrap h1 {
  margin-bottom: 25px;
  font-size: 24px;
  color: #333;
}

/* Slide List */
#motion-slider-list {
  list-style: none;
  margin: 0;
  padding: 0;
}

.motion-slide-item {
  display: flex;
  align-items: center;
  background: #f9f9f9;
  border: 1px solid #ddd;
  margin-bottom: 10px;
  padding: 10px 15px;
  border-radius: 5px;
  gap: 15px;
  cursor: move;
}

.motion-slide-item img {
  width: 90px;
  height: 60px;
  object-fit: cover;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.motion-slide-item input.motion-title {
  flex: 1;
  padding: 8px 10px;
  font-size: 14px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

/* Remove Button */
.motion-slide-item .remove-slide {
  background-color: #e74c3c;
  color: #fff;
  border: none;
  padding: 6px 10px;
  border-radius: 4px;
  font-size: 13px;
  cursor: pointer;
}

.motion-slide-item .remove-slide:hover {
  background-color: #c0392b;
}

/* Add + Save buttons */
#add-slide,
#save-slides {
  margin-top: 20px;
  margin-right: 10px;
  padding: 8px 14px;
  font-size: 14px;
  border-radius: 4px;
  cursor: pointer;
}

#add-slide {
  background-color: #0073aa;
  border: none;
  color: #fff;
}

#add-slide:hover {
  background-color: #005177;
}

#save-slides {
  background-color: #28a745;
  border: none;
  color: #fff;
}

#save-slides:hover {
  background-color: #218838;
}

        </style>
    </div>
    <?php
}

//  AJAX Save Slides
add_action('wp_ajax_save_motion_slider', function () {
    check_ajax_referer('motion_slider_nonce', 'nonce');
    $slides = isset($_POST['slides']) ? $_POST['slides'] : [];
    update_option('motion_slider_slides', $slides);
    wp_send_json_success();
});
