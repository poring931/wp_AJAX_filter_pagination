<?php

/**
 * StrangeBrain Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package StrangeBrain_Theme
 */

/**
 * DATA BASE BACKUPER
 */

define("BACKUP_DIR", '../sql');
define("BACKUP_FILE", 'backup.sql');
define("TABLES", '*');
define("CHARSET", 'utf8');
define("GZIP_BACKUP_FILE", false);
define("DISABLE_FOREIGN_KEY_CHECKS", true);
define("BATCH_SIZE", 1000);
require_once(get_template_directory() . '/vendors/backupbd/myphp-backup.php');

//do backup
add_action('wp_ajax_backup_action', 'backup_action_callback');
function backup_action_callback()
{
    if (class_exists('Backup_Database')) {
        set_time_limit(600);
        $backupDatabase = new Backup_Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, CHARSET);
        $result = $backupDatabase->backupTables(TABLES, BACKUP_DIR) ? 'OK' : 'KO';
    }

    wp_die();
}

add_action('wp_ajax_gallery_load_more', 'gallery_load_more');
add_action('wp_ajax_nopriv_gallery_load_more', 'gallery_load_more');
function gallery_load_more()
{
    $current_page = (int) $_POST['current_page'];
    $max_pages = (int) $_POST['max_pages'];
    $perage = (int) $_POST['perage'];

    ob_start();
    get_template_part(
        'partials/galleries/galleries',
        'all-list',
        [
            'current_page' => $current_page,
            'max_pages' => $max_pages,
            'perage' => $perage
        ]
    );

    wp_send_json_success(ob_get_clean());
    wp_die();
}
// CPT Archive page SEO Title
add_filter('aioseop_title', 'dd_rewrite_custom_titles');

function dd_rewrite_custom_titles($title)
{
    if (in_category(36) && !is_category(25) && !in_category(25) &&  is_single()) :
    else :
        if (is_post_type_archive()) {
            $post_type = get_post_type_object(get_post_type());

            if ($post_type->labels->archive_seo_title) {
                $title = $post_type->labels->archive_seo_title;
            }
        }

        return $title;
    endif;
}

// CPT Archive page meta Description
add_filter('aioseop_description', 'filter_aioseop_description');

function filter_aioseop_description($description)
{
    if (is_post_type_archive()) {
        $post_type = get_post_type_object(get_post_type());

        if ($post_type->labels->archive_meta_desc) {
            $description = $post_type->labels->archive_meta_desc;
        }
    }
    return $description;
}
//add backup button
function add_backup_button($wp_admin_bar)
{
    if (!current_user_can('administrator')) return;

    $args = array(
        'id' => 'backupDB',
        'title' => 'Бекап базы',
        'href' => '#',
        'meta' => array(
            'class' => 'js-do-backupb backupbtn'
        )
    );
    $wp_admin_bar->add_node($args);
}

add_action('admin_bar_menu', 'add_backup_button', 50);

if (!function_exists('strangebrain_theme_setup')) :
    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which
     * runs before the init hook. The init hook is too late for some features, such
     * as indicating support for post thumbnails.
     */
    function strangebrain_theme_setup()
    {
        /*
         * Make theme available for translation.
         * Translations can be filed in the /languages/ directory.
         * If you're building a theme based on StrangeBrain Theme, use a find and replace
         * to change 'strangebrain-theme' to the name of your theme in all the template files.
         */
        load_theme_textdomain('strangebrain-theme', get_template_directory() . '/languages');

        // Add default posts and comments RSS feed links to head.
        add_theme_support('automatic-feed-links');

        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        /* add_theme_support('title-tag');

         /*
          * Enable support for Post Thumbnails on posts and pages.
          *
          * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
          */
        add_theme_support('post-thumbnails');

        // This theme uses wp_nav_menu() in one location.
        register_nav_menus(array(

            // меню в шапке
            'head_menu_pages' => esc_html__('Меню в шапке (страницы)', 'strangebrain-theme'),
            'head_menu_catalog' => esc_html__('Меню в шапке (каталог)', 'strangebrain-theme'),
            'head_sidebar_pages' => esc_html__('Меню в левом блоке (страницы)', 'strangebrain-theme'),
            'head_sidebar_catalog' => esc_html__('Меню в левом блоке (каталог)', 'strangebrain-theme'),

            // меню в подвале
            'footer_menu_window' => esc_html__('Меню в подвале (Окна)', 'strangebrain-theme'),
            'footer_menu_doors' => esc_html__('Меню в подвале (Двери)', 'strangebrain-theme'),
            'footer_menu_balconies' => esc_html__('Меню в подвале (Балконы и лоджии)', 'strangebrain-theme'),
            'footer_menu_verandas' => esc_html__('Меню в подвале (Веранды и беседки)', 'strangebrain-theme'),
            'footer_menu_services' => esc_html__('Меню в подвале (Услуги)', 'strangebrain-theme'),
            'footer_menu_other1' => esc_html__('Меню в подвале (Разное 1)', 'strangebrain-theme'),

            'footer_menu_about' => esc_html__('Меню в подвале (О компании)', 'strangebrain-theme'),
            'footer_menu_publication' => esc_html__('Меню в подвале (Публикации)', 'strangebrain-theme'),
            'footer_menu_other2' => esc_html__('Меню в подвале (Разное 2)', 'strangebrain-theme'),
        ));

        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support('html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ));

        // Set up the WordPress core custom background feature.
        add_theme_support('custom-background', apply_filters('strangebrain_theme_custom_background_args', array(
            'default-color' => 'ffffff',
            'default-image' => '',
        )));

        // Add theme support for selective refresh for widgets.
        add_theme_support('customize-selective-refresh-widgets');
    }
endif;
add_action('after_setup_theme', 'strangebrain_theme_setup');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function strangebrain_theme_content_width()
{
    $GLOBALS['content_width'] = apply_filters('strangebrain_theme_content_width', 640);
}

add_action('after_setup_theme', 'strangebrain_theme_content_width', 0);

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function strangebrain_theme_widgets_init()
{
    register_sidebar(array(
        'name' => esc_html__('Sidebar', 'strangebrain-theme'),
        'id' => 'sidebar-1',
        'description' => esc_html__('Add widgets here.', 'strangebrain-theme'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));
}

add_action('widgets_init', 'strangebrain_theme_widgets_init');

/**
 * Enqueue scripts and styles.
 */
function strangebrain_theme_scripts()
{
    wp_enqueue_script('strangebrain-theme-navigation', get_template_directory_uri() . '/js/core/navigation.js', array(), '20151215', true);
    wp_enqueue_script('strangebrain-theme-skip-link-focus-fix', get_template_directory_uri() . '/js/core/skip-link-focus-fix.js', array(), '20151215', true);

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }

    wp_enqueue_script('jquery');
    wp_localize_script('jquery', 'sb_vars', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));

    $manifest = json_decode(file_get_contents('dist/assets.json', true));
    wp_enqueue_script('theme-js', get_template_directory_uri() . "/dist" . $manifest->app->js, ['jquery'], 1.0, true);
    wp_enqueue_script('mapjs', get_template_directory_uri() . '/_src/js/map.js', array('jquery'), '1.1', true);
    wp_enqueue_script('ajaxGalleryLoadjs', get_template_directory_uri() . '/_src/js/ajax-load-more.js', array('jquery'), '', true);
    wp_enqueue_style('theme-main', get_template_directory_uri() . '/dist/styles.css?v=2.1');
}

add_action('wp_enqueue_scripts', 'strangebrain_theme_scripts');

/**
 * Enqueue admin scripts and styles.
 */
function strangebrain_admin_theme_scripts()
{
    if (current_user_can('administrator')) {
        wp_enqueue_script('jquery');
        wp_localize_script('jquery', 'sb_vars', array(
            'ajaxurl' => admin_url('admin-ajax.php')
        ));

        $manifest = json_decode(file_get_contents('dist/assets.json', true));
        wp_enqueue_script('adminmainjs-loadmore', get_template_directory_uri() . "/dist" . $manifest->admin->js, array('jquery'), 1.0, true);
    }
}

add_action('admin_enqueue_scripts', 'strangebrain_admin_theme_scripts');

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';


/**
 * Add menu icon with ACF
 */
add_filter('wp_nav_menu_objects', 'my_wp_nav_menu_objects', 10, 2);

function my_wp_nav_menu_objects($items, $args)
{
    foreach ($items as &$item) {
        $icon = get_field('menuicon', $item);

        if ($icon) {
            $item->title = '<img class="menuicon" src="' . $icon . '"/>' . $item->title;
        }
    }

    return $items;
}


/**
 * Add menu logo with ACF
 */
add_filter('wp_nav_menu_items', 'my_wp_nav_menu_items', 10, 2);

function my_wp_nav_menu_items($items, $args)
{
    $menu = wp_get_nav_menu_object($args->menu);
    $logo = get_field('menulogo', $menu);

    if ($logo) {
        $html_logo = '<li class="menu-item-logo"><a href="' . home_url() . '"><img src="' . $logo['url'] . '" alt="' . $logo['alt'] . '" /></a></li>';
        $items = $html_logo . $items;
    }

    return $items;
}

/*
	Phone Cleaner
*/
function get_phone_link($str)
{
    if (!$str) return;

    $phone_link = "";
    $phone_link .= "tel:+";
    $phone_link .= preg_replace('/[^0-9]/', '', $str);

    return $phone_link;
}

/**
 * ACF Options page
 */

if (function_exists('acf_add_options_page')) {

    acf_add_options_page(array(
        'page_title' => 'Настройки темы',
        'menu_title' => 'НАСТРОЙКИ ТЕМЫ',
        'menu_slug' => 'theme-general-settings',
        'capability' => 'edit_posts',
        'redirect' => false
    ));

    acf_add_options_sub_page(array(
        'page_title' => 'Сертификаты',
        'menu_title' => 'Сертификаты',
        'parent_slug' => 'theme-general-settings',
        'menu_slug' => 'theme-general-certificates',
        'capability' => 'edit_posts',
    ));

    acf_add_options_sub_page(array(
        'page_title' => 'Сертификаты с баннером',
        'menu_title' => 'Сертификаты с баннером',
        'parent_slug' => 'theme-general-settings',
        'menu_slug' => 'theme-general-certificates-banner',
        'capability' => 'edit_posts',
    ));

    acf_add_options_sub_page(array(
        'page_title' => 'Обратная связь',
        'menu_title' => 'Обратная связь',
        'parent_slug' => 'theme-general-settings',
        'menu_slug' => 'theme-general-feedback',
        'capability' => 'edit_posts',
    ));

    acf_add_options_sub_page(array(
        'page_title' => 'О компании(блок)',
        'menu_title' => 'О компании(блок)',
        'parent_slug' => 'theme-general-settings',
        'menu_slug' => 'theme-general-about',
        'capability' => 'edit_posts',
    ));

    acf_add_options_sub_page(array(
        'page_title' => 'Преимущества(блок)',
        'menu_title' => 'Преимущества(блок)',
        'parent_slug' => 'theme-general-settings',
        'menu_slug' => 'theme-general-advantages',
        'capability' => 'edit_posts',
    ));

    acf_add_options_sub_page(array(
        'page_title' => 'Галерея(блок)',
        'menu_title' => 'Галерея(блок)',
        'parent_slug' => 'theme-general-settings',
        'menu_slug' => 'theme-general-gallery',
        'capability' => 'edit_posts',
    ));

    acf_add_options_sub_page(array(
        'page_title' => 'Отзывы(блок)',
        'menu_title' => 'Отзывы(блок)',
        'parent_slug' => 'theme-general-settings',
        'menu_slug' => 'theme-general-reviews',
        'capability' => 'edit_posts',
    ));

    acf_add_options_sub_page(array(
        'page_title' => 'Контакты',
        'menu_title' => 'Контакты',
        'parent_slug' => 'theme-general-settings',
        'menu_slug' => 'theme-general-map',
        'capability' => 'edit_posts',
    ));

    acf_add_options_sub_page(array(
        'page_title' => 'Преимущества с кнопкой (блок)',
        'menu_title' => 'Преимущества с кнопкой (блок)',
        'parent_slug' => 'theme-general-settings',
        'menu_slug' => 'theme-general-advantages-2',
        'capability' => 'edit_posts',
    ));

    acf_add_options_sub_page(array(
        'page_title' => '5 этапов работы (блок)',
        'menu_title' => '5 этапов работы (блок)',
        'parent_slug' => 'theme-general-settings',
        'menu_slug' => 'theme-general-stage-work',
        'capability' => 'edit_posts',
    ));

    acf_add_options_sub_page(array(
        'page_title' => 'Слайдер каталог окон Rehau (блок)',
        'menu_title' => 'Слайдер каталог окон Rehau (блок)',
        'parent_slug' => 'theme-general-settings',
        'menu_slug' => 'theme-general-slider-win-rehau',
        'capability' => 'edit_posts',
    ));

    acf_add_options_sub_page(array(
        'page_title' => 'Кредит (блок)',
        'menu_title' => 'Кредит (блок)',
        'parent_slug' => 'theme-general-settings',
        'menu_slug' => 'theme-general-credit',
        'capability' => 'edit_posts',
    ));

    acf_add_options_sub_page(array(
        'page_title' => 'Характеристики окон (таблица)',
        'menu_title' => 'Характеристики окон (таблица)',
        'parent_slug' => 'theme-general-settings',
        'menu_slug' => 'theme-general-table',
        'capability' => 'edit_posts',
    ));

    acf_add_options_sub_page(array(
        'page_title' => 'Расчёт стоимости online',
        'menu_title' => 'Расчёт стоимости online',
        'parent_slug' => 'theme-general-settings',
        'menu_slug' => 'theme-general-calc-win',
        'capability' => 'edit_posts',
    ));

    acf_add_options_sub_page(array(
        'page_title' => 'Как пользоваться калькулятором',
        'menu_title' => 'Как пользоваться калькулятором',
        'parent_slug' => 'theme-general-settings',
        'menu_slug' => 'theme-general-calc',
        'capability' => 'edit_posts',
    ));

    acf_add_options_sub_page(array(
        'page_title' => 'Цены на остекление балконов и лоджий',
        'menu_title' => 'Цены на остекление балконов и лоджий',
        'parent_slug' => 'theme-general-settings',
        'menu_slug' => 'theme-general-balcony-tabs',
        'capability' => 'edit_posts',
    ));

    acf_add_options_sub_page(array(
        'page_title' => 'Цены на отделку балкона',
        'menu_title' => 'Цены на отделку балкона',
        'parent_slug' => 'theme-general-settings',
        'menu_slug' => 'theme-general-balcony-price',
        'capability' => 'edit_posts',
    ));
}

// удалить, когда в продакшен пойдёт, убирает верхнюю панель админки
//add_filter('show_admin_bar', '__return_false');


// ключ для google карт
function my_acf_init()
{
    acf_update_setting('google_api_key', 'AIzaSyCpSnGXoNHqTza5zucdWNkvas6ow0Yj0Bw'); // Ваш ключ Google API
}

add_action('acf/init', 'my_acf_init');

// шаблоны страниц
add_filter('template_include', 'my_template');
function my_template($template)
{
    if (is_page('О компании')) {
        if ($new_template = locate_template(array('templates/about.php')))
            return $new_template;
    }

    if (is_page('Отзывы')) {
        if ($new_template = locate_template(array('templates/reviews.php')))
            return $new_template;
    }

    if (is_page('Аксессуары')) {
        if ($new_template = locate_template(array('templates/accessories.php')))
            return $new_template;
    }

    if (is_page('Публикации')) {
        if ($new_template = locate_template(array('templates/publications.php')))
            return $new_template;
    }

    if (is_page('Сертификаты')) {
        if ($new_template = locate_template(array('templates/certificates.php')))
            return $new_template;
    }

    if (is_page('Контакты')) {
        if ($new_template = locate_template(array('templates/contacts.php')))
            return $new_template;
    }

    if (is_page('Жалюзи')) {
        if ($new_template = locate_template(array('templates/jalousie.php')))
            return $new_template;
    }

    if (is_page('verandas')) {
        if ($new_template = locate_template(array('templates/verandas.php')))
            return $new_template;
    }

    if (is_page('balconies')) {
        if ($new_template = locate_template(array('templates/balconies.php')))
            return $new_template;
    }

    if (is_page('glazing')) {
        if ($new_template = locate_template(array('templates/glazing.php')))
            return $new_template;
    }
    if (is_page('okna-po-tipu-pomeshhenija')) {
        if ($new_template = locate_template(array('templates/okna-po-tipu-pomeshcheniya.php')))
            return $new_template;
    }


    if (is_page('room_glazing')) {
        if ($new_template = locate_template(array('templates/room_glazing.php')))
            return $new_template;
    }

    if (is_page('expierence')) {
        if ($new_template = locate_template(array('templates/expierence.php')))
            return $new_template;
    }

    if (is_page('doors')) {
        if ($new_template = locate_template(array('templates/doors.php')))
            return $new_template;
    }

    if (is_page('windows-rehau')) {
        if ($new_template = locate_template(array('templates/windows-rehau.php')))
            return $new_template;
    }

    if (is_page('calculator')) {
        if ($new_template = locate_template(array('templates/calculator.php')))
            return $new_template;
    }

    if (is_page('calculator-form')) {
        if ($new_template = locate_template(array('templates/calculator-form.php')))
            return $new_template;
    }

    if (is_page('services')) {
        if ($new_template = locate_template(array('templates/services.php')))
            return $new_template;
    }

    if (is_front_page()) {
        if ($new_template = locate_template(array('templates/home.php')))
            return $new_template;
    }

    // if (is_single('balcony-decoration')) {
    //     if ($new_template = locate_template(array('templates/balcony-decoration.php')))
    //         return $new_template;
    // }

    if (is_single('balcony-decorationcopy')) {
        if ($new_template = locate_template(array('templates/balcony-decorationcopy.php')))
            return $new_template;
    }
    if (is_single('balcony-decoration')) {
        if ($new_template = locate_template(array('templates/balcony-decorationcopy.php')))
            return $new_template;
    }
    if (is_single('otdelka-panelyami-pvh-copy')) {
        if ($new_template = locate_template(array('templates/otdelka-panelyami-pvh.php')))
            return $new_template;
    }
    if (is_single('otdelka-panelyami-pvh')) {
        if ($new_template = locate_template(array('templates/otdelka-panelyami-pvh.php')))
            return $new_template;
    }
    if (is_single('vnutrennyaya-otdelkacopy')) {
        if ($new_template = locate_template(array('templates/vnutrennyaya-otdelka.php')))
            return $new_template;
    }
    // if (is_single('teploe-osteklenie-besedok')) {
    //     if ($new_template = locate_template(array('templates/teploe-osteklenie-besedok.php')))
    //         return $new_template;
    // }


    if ((is_category(36)) || strpos($_SERVER['REQUEST_URI'], 'katalog-okon-rehau/page/')) { //категория популярных размеров - отдельная статья
        if ($new_template = locate_template(array('category-36.php')))
            return $new_template;
    }

    if (in_category(25) && !is_category(25) &&  !is_single()) { //категория популярных размеров (подраздел размеров)
        if ($new_template = locate_template(array('templates/populjarnye-razmery-okon.php')))
            return $new_template;
    }
    if (in_category(36) && !is_category(25) && !in_category(25) &&  !is_single()) { //категория популярных размеров (подраздел размеров)
        if ($new_template = locate_template(array('templates/katalog-razmery-okon.php')))
            return $new_template;
    }
    if (in_category(36) && !in_category(25) && !is_category(25) &&  is_single()) { //категория популярных размеров (подраздел размеров)
        if ($new_template = locate_template(array('templates/katalog-razmery-okon-single.php')))
            return $new_template;
    }
    if (in_category(25) && !is_category(25) && is_single()) { //категория популярных размеров - отдельная статья
        if ($new_template = locate_template(array('templates/populjarnye-razmery-okon-single.php')))
            return $new_template;
    }
    // if (is_page('testsss')) { //категория популярных размеров - отдельная статья
    //       if ($new_template = locate_template(array('category-25.php')))
    //         return $new_template;
    // }




    if (is_single('vnutrennyaya-otdelka')) {
        if ($new_template = locate_template(array('templates/vnutrennyaya-otdelka.php')))
            return $new_template;
    }
    if (is_single(array('kladka-penoblokov', 'vinos-balkona', 'uteplenie-balkonov-lodzhiy', 'elektrika', 'vneshnyaya-otdelka', 'vozvedenie-kryshi', 'otdelka-gipsokartonom'))) {
        if ($new_template = locate_template(array('templates/balconies-single_new.php')))
            return $new_template;
    }

    if (is_page('galleries')) {
        if ($new_template = locate_template(array('templates/galleries.php')))
            return $new_template;
    }

    if (is_page('prices')) {
        if ($new_template = locate_template(array('templates/prices.php')))
            return $new_template;
    }

    if (is_page('houses-list')) {
        if ($new_template = locate_template(array('templates/houses.php')))
            return $new_template;
    }


    /* шаблоны для кастомных типов: новости, статьи, выставочные центры
    все страницы которые там находятся
    будут подходить под этот шаблоны */

    global $post;
    if (!$post) return get_stylesheet_directory() . '/404.php';

    if ($post->post_type == 'news' || $post->post_type == 'articles') {
        return get_stylesheet_directory() . '/templates/news-single.php';
    }

    if ($post->post_type == 'stocks') {
        return get_stylesheet_directory() . '/templates/stock-single.php';
    }

    if ($post->post_type == 'exhibition_centre') {
        return get_stylesheet_directory() . '/templates/exhibition-centre.php';
    }

    if ($post->post_type == 'jalousie') {
        return get_stylesheet_directory() . '/templates/jalousie-single.php';
    }

    if ($post->post_type == 'room_glazing') {
        return get_stylesheet_directory() . '/templates/room_glazing-single.php';
    }

    if ($post->post_type == 'reviews') {
        return get_stylesheet_directory() . '/templates/reviews-single.php';
    }

    if ($post->post_type == 'balconies') {
        return get_stylesheet_directory() . '/templates/balconies-single.php';
    }

    if ($post->post_type == 'doors') {
        return get_stylesheet_directory() . '/templates/doors-single.php';
    }

    if ($post->post_type == 'balcony_decoration') {
        return get_stylesheet_directory() . '/templates/balcony-decoration-single.php';
    }

    if ($post->post_type == 'gallery') {
        return get_stylesheet_directory() . '/templates/galleries-single.php';
    }

    if ($post->post_type == 'windows') {
        return get_stylesheet_directory() . '/templates/windows-rehau-single.php';
    }

    if ($post->post_type == 'accessories') {
        return get_stylesheet_directory() . '/templates/accessories-single.php';
    }

    if ($post->post_type == 'services') {
        return get_stylesheet_directory() . '/templates/accessories-single.php';
    }


    if ($post->post_type == 'houses') {
        return get_stylesheet_directory() . '/templates/houses-single.php';
    }

    if ($post->post_type == 'windowlist') {
        return get_stylesheet_directory() . '/templates/window-list-single.php';
    }
    if ($post->post_type == 'verandas' && $template != get_stylesheet_directory() . '/post-besedki-cat.php' && $template != get_stylesheet_directory() . '/post-besedki-single.php') {
        return get_stylesheet_directory() . '/templates/verandas-single.php';
    }
    if ($post->post_type == 'glazings') {
        return get_stylesheet_directory() . '/templates/glazing-single.php';
    }
    if ($post->post_type == 'expierence') {
        return get_stylesheet_directory() . '/templates/expierence-single.php';
    }

    return $template;
}

// пагинация
/* Удаляем H2 из пагинации */
add_filter('navigation_markup_template', 'my_navigation_template', 10, 2);
function my_navigation_template($template, $class)
{
    return '
 <nav class="%1$s" role="navigation">
  <div class="nav-links">%3$s</div>
 </nav>
 ';
}

// длина контента в списках
add_filter('excerpt_length', function () {
    return 20;
});

add_filter('excerpt_more', function ($more) {
    return '...';
});

// AJAX загрузка постов
add_action('wp_ajax_loadmore', 'load_posts_callback');
add_action('wp_ajax_nopriv_loadmore', 'load_posts_callback');

function load_posts_callback()
{
    $args = unserialize(stripslashes($_POST['query']));
    $args['paged'] = $_POST['page'] + 1; // следующая страница
    $args['post_status'] = 'publish';
    $currPostID = intval($_POST['postID']);

    wp_die();
}

add_action('wp_ajax_get_calculator_data', 'get_calculator_data_callback');
add_action('wp_ajax_nopriv_get_calculator_data', 'get_calculator_data_callback');

function get_calculator_data_callback()
{
    $rangeDiscount = get_field('calc_dcount_range_gr', 'option');
    $specialDiscounts = get_field('calc_discounts', 'option');
    $tabPrice = get_field('calc_price_gr', 'option');

    $output = array(
        'tabPrice' => $tabPrice,
        'rangeDiscount' => $rangeDiscount,
        'specialDiscounts' => $specialDiscounts,
    );

    echo json_encode($output);

    wp_die();
}

// AJAX загрузка галереи

function load_more_posts()
{
    $next_page = $_POST['current_page'] + 1;
    $query = new WP_Query([
        'post_type' => 'gallery',
        'gallery_sections' => ['gallery-window', 'gallery-cottages', 'gallery-balconies'],
        'posts_per_page' => 2,
        'paged' => $next_page
    ]);
    if ($query->have_posts()) :

        ob_start();

        while ($query->have_posts()) : $query->the_post();

            get_template_part('partials/galleries/galleries', 'list-item');

        endwhile;

        wp_send_json_success(ob_get_clean());

    else :

        wp_send_json_error('No more posts!');

    endif;
    wp_die();
}

add_action('wp_ajax_load_more_posts', 'load_more_posts');
add_action('wp_ajax_nopriv_load_more_posts', 'load_more_posts');

// отзывы
function wd_post_title_acf_name($field)
{
    if (is_singular('vendors')) { // if on the vendor page
        $field['label'] = 'Vendor Name';
    } else {
        $field['label'] = 'Ваше имя';
    }
    return $field;
}
add_filter('acf/load_field/name=_post_title', 'wd_post_title_acf_name');

// AMMO intergation ->
add_action('wpcf7_before_send_mail', 'amo_handler_amopoint');

function amo_handler_amopoint($form)
{
    $submission = WPCF7_Submission::get_instance();
    $data = $submission->get_posted_data();

    $url = 'https://amopoint-dev.ru/site_integrations/createLead?hash=OGQ5Yjk5NTFhMzFhNTNmM2M0MjMyMzliNGRkMzQwMWI';

    $allData['data'] = array(
        'site' => 'strangebrain',
        'cookie' => $_COOKIE,
        'server' => $_SERVER,
        'form_data' => $data
    );

    $result = file_get_contents($url, false, stream_context_create(array(
        'http' => array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => http_build_query($allData)
        )
    )));
}

add_action('init', 'save_utm_to_cookie');
function save_utm_to_cookie()
{
    $day = 30;
    $date = time() + 3600 * 24 * $day;
    if (isset($_GET["utm_source"])) setcookie("utm_source", $_GET["utm_source"], $date, "/");
    if (isset($_GET["utm_medium"])) setcookie("utm_medium", $_GET["utm_medium"], $date, "/");
    if (isset($_GET["utm_campaign"])) setcookie("utm_campaign", $_GET["utm_campaign"], $date, "/");
    if (isset($_GET["utm_content"])) setcookie("utm_content", $_GET["utm_content"], $date, "/");
    if (isset($_GET["utm_term"])) setcookie("utm_term", $_GET["utm_term"], $date, "/");
}
// <- AMMO intergation

remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);

// REMOVE EMOJI ICONS
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

// удаляем стр. вложения медиа КАРТИНКИ
add_action('template_redirect', 'template_redirect_attachment');
function template_redirect_attachment()
{
    global $post;
    // Перенаправляем на основную запись:
    if (is_attachment()) {
        wp_redirect(home_url('/404/'));
    }
}

add_action('template_redirect', function () {
    global $wp_query;
    $invalid = array('PAGEN_1', 'pagen_1');
    foreach ($_GET as $key => $value) {
        if (in_array($key, $invalid)) {
            $wp_query->set_404();
            status_header(404);
        }
    }
});


remove_action('wp_head', 'rsd_link');

function remove_pingback_header($headers)
{
    unset($headers['X-Pingback']);
    return $headers;
}
add_filter('wp_headers', 'remove_pingback_header');

function link_rel_buffer_callback($buffer)
{
    $buffer = preg_replace('/(<link.*?rel=("|\')pingback("|\').*?href=("|\')(.*?)("|\')(.*?)?\/?>|<link.*?href=("|\')(.*?)("|\').*?rel=("|\')pingback("|\')(.*?)?\/?>)/i', '', $buffer);
    return $buffer;
}
function link_rel_buffer_start()
{
    ob_start("link_rel_buffer_callback");
}
function link_rel_buffer_end()
{
    ob_flush();
}
add_action('template_redirect', 'link_rel_buffer_start', -1);
add_action('get_header', 'link_rel_buffer_start');
add_action('wp_head', 'link_rel_buffer_end', 999);





add_action('wp_ajax_myfilter', 'true_filter_function');
add_action('wp_ajax_nopriv_myfilter', 'true_filter_function');

function true_filter_function()
{


    if (
        isset($_POST['products_width'])
        && $_POST['products_width'] != 'Не выбрано'
        && ($_POST['products_height'] == 'Не выбрано' || $_POST['products_height'] == '')

    ) {
        $width = array(
            'key' => 'osnovnoj_razmer',
            'value' => '^' . $_POST['products_width'],
            'compare' => 'REGEXP'
        );
    } else {
        $width = array();
    }

    if (
        isset($_POST['products_height'])
        && $_POST['products_height'] != 'Не выбрано'
        && ($_POST['products_width'] == 'Не выбрано' || $_POST['products_width'] == '')
    ) {
        $height = array(
            'key' => 'osnovnoj_razmer',
            'value' => ' х ' . $_POST['products_height'],
            'compare' => 'LIKE'
        );
    } else {
        $height = array();
    }

    if (
        isset($_POST['products_height'])
        && $_POST['products_height'] != 'Не выбрано'
        && isset($_POST['products_width'])
        && $_POST['products_width'] != 'Не выбрано'
    ) {
        $width_height = array(
            'key' => 'osnovnoj_razmer',
            'value' => $_POST['products_width'] . ' х ' . $_POST['products_height'],
            'compare' => 'LIKE'
        );
    } else {
        $width_height = array();
    }

    if (isset($_POST['products_profil']) && $_POST['products_profil'] != 'Не выбрано') {
        $products_profil = explode(',', $_POST['products_profil']);
    } else {
        $products_profil = [];
    }

    if ($_POST['cena_min'] == '') {
        $price = 0;
    } else {
        $price = $_POST['cena_min'];
    }



    if ($_POST['page_num'] > 1) {
        $paged = $_POST['page_num'];
    } else {
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    }
    $args = array(
        'numberposts' => -1,
        'suppress_filters' => true,
        // 'orderby' => 'title', // сортировка по дате у нас будет в любом случае (но вы можете изменить/доработать это)
        'order'    => 'ASC', // ASC или DESC
        'meta_key' => 'jalousie_list_price',
        'orderby' => 'meta_value_num',
        'posts_per_page' => 12,
        'category' => 36,
        'category__not_in' => 25,
        'post__in'  => $products_profil,
        'paged' => $paged
    );
    $args['meta_query'] = array(
        'relation' => 'AND',
        array(
            'key' => 'jalousie_list_price',
            'value' => $price,
            'type' => 'numeric',
            'compare' => '>='
        ),
        $width,
        $height,
        $width_height,
    );




    //  var_dump($products_profil);
    $args_getPost['meta_query'] = array(
        'relation' => 'AND',
        array(
            'key' => 'jalousie_list_price',
            'value' => $price,
            'type' => 'numeric',
            'compare' => '>='
        ),
        $width,
        $height,
        $width_height,
    );
    $args_getPost = array(
        'numberposts' => -1,
        'suppress_filters' => true,
        'orderby' => 'title', // сортировка по дате у нас будет в любом случае (но вы можете изменить/доработать это)
        'order'    => 'ASC', // ASC или DESC
        'category' => 36,
        'category__not_in' => 25,
        'post__in'  => $products_profil,
    );
    //  var_dump($products_profil);
    $args_getPost['meta_query'] = array(
        'relation' => 'AND',
        array(
            'key' => 'jalousie_list_price',
            'value' => $price,
            'type' => 'numeric',
            'compare' => '>='
        ),
        $width,
        $height,
        $width_height,
    );
    // echo '<pre>';
    // var_dump($args);
    // echo '<br>';echo '<br>';
    // var_dump($_POST['products_width']);
    // var_dump($width);
    // echo '<br>';
    // var_dump($_POST['products_height']);
    // var_dump($height);
    // echo '<br>';
    // var_dump($width_height);
    // echo '<br>';
    // echo '<br>';
    // var_dump($_POST['products_profil']);
    // var_dump($products_profil);
    // echo '</pre>';   

    $product_list = get_posts($args_getPost);
    foreach ($product_list as $product) {
        if (get_field('osnovnoj_razmer', $product->ID)) {
            $razmerArr[] = get_field('osnovnoj_razmer', $product->ID);
        }
        $namesArr[] = $product->post_title;
        $namesArrIds[$product->post_title][] = $product->ID;
        //    var_dump($product->post_title);
    }
    foreach (array_unique($razmerArr) as $razmer) {
        $razmerWidth[] = preg_replace("/\s+/", "", explode('х', $razmer)[0]);
        $razmerHeight[] = preg_replace("/\s+/", "", explode('х', $razmer)[1]);
    }

    $namesArr = array_unique($namesArr);
    $razmerWidth = array_unique($razmerWidth);
    $razmerHeight = array_unique($razmerHeight);

    sort($razmerWidth);
    sort($razmerHeight);
    sort($namesArr); ?>
    <script>
        var products_width = <?= json_encode($razmerWidth); ?>;
        var products_height = <?= json_encode($razmerHeight); ?>;
        var products_profil = <?= json_encode($namesArr); ?>;
    </script>

    <?

    global $wp_query;
    $save_wpq = $wp_query;
    $wp_query = new WP_Query($args);

    if ($wp_query->have_posts()) {

        while ($wp_query->have_posts()) : $wp_query->the_post(); ?>

            <div class="other_size__tab_content__item" data-itemId="<?= get_the_title(); ?>">
                <? if (get_field('form-faktor') != '') : ?>
                    <span class="form-factor"><?= get_field('form-faktor'); ?>
                        <? if (get_field('form-faktor') != 'Балконный блок') {
                            echo 'окно';
                        }
                        ?>
                    </span>
                <? else : ?>
                    <span class="form-factor">
                        <? if (get_field('product_form_factor') != '') {
                            echo get_field('product_form_factor');
                        }
                        ?>
                    </span>
                <? endif; ?>
                <? if (get_field('form-faktor') == 'Одностворчатое') {
                    $margin = 'style="margin-left:auto;margin-right:auto;"';
                } else {
                    $margin = '';
                }
                ?>
                <a <?= $margin; ?> href="<?php echo the_permalink(); ?>" class="other_size__tab_content__item-img">

                    <? if (get_field('product_window_img')) : ?>

                        <img <? //if (get_field('product_window_img')=='https://www.oknastars.ru/wp-content/uploads/1_window.png') echo 'width="149"';
                                ?> <? if (get_field('product_window_img') == 'https://www.oknastars.ru/wp-content/uploads/1_window.png') echo 'width="116"'; ?> <? if (get_field('product_window_img') == 'https://www.oknastars.ru/wp-content/uploads/window-price-2.jpg') echo 'width="272"'; ?> <? if (get_field('product_window_img') == 'https://www.oknastars.ru/wp-content/uploads/window-price-1.jpg') echo 'width="186"'; ?> decoding="async" loading="lazy" src="<?= get_field('product_window_img'); ?>" alt="<?= get_the_title(); ?>" title=" REHAU <? $strings = preg_split('/ /', get_the_title());
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        echo implode(' ', array_slice($strings, 0, 2)); ?>">
                    <? endif; ?>
                </a>
                <a href="<?php echo the_permalink(); ?>" class="other_size__tab_content__item-name">
                    <!-- <? the_title(); ?> -->
                    REHAU
                    <?
                    $strings = preg_split('/ /', get_the_title());
                    echo implode(' ', array_slice($strings, 0, 2));
                    ?>
                </a>
                <div class="other_size__tab_content__item-size">
                    <? if (get_field('osnovnoj_razmer')) : ?>
                        <? echo str_replace('х', '<span>х</span>', get_field('osnovnoj_razmer')); ?>
                    <? endif; ?>
                </div>
                <div class="other_size__tab_content__item-price">
                    <? if (get_field('jalousie_list_price')) : ?>
                        от
                        <strong>
                            <?= number_format(get_field('jalousie_list_price'), 0, '.', ' ') ?>
                        </strong>

                        ₽
                    <? endif; ?>

                </div>
                <div class="size_tabs_item__content__product__href">
                    <a href="<?php echo the_permalink(); ?>" class="btn-link-text">
                        <span class="btn-link-text__title">Подробнее</span>
                        <span class="btn-link-text__icon"></span>
                    </a>
                </div>



            </div>

    <? endwhile;



        $pagenum = (int) $_GET['page'];
        if (!$pagenum) {
            $pagenum = (get_query_var('paged')) ? get_query_var('paged') : 1;
        }

        $result =  paginate_links(array(
            'base'         => @add_query_arg('page', '%#%'),
            'format'       => '%#%',
            'current'   => $pagenum,
            'mid_size'  => 1,
            'end_size' => 1,
            'total'   => $wp_query->max_num_pages,
            'prev_text' => '<',
            'next_text' => '>'
        ));
        $result = str_replace('/wp-admin/admin-ajax.php', '', $result);
        echo ($result) ? "<div class='wp-pagenavi'>" . $result . "</div>" : '';


        wp_reset_postdata();
        wp_reset_query();
        $wp_query = $save_wpq;
    } else {
        echo 'Ничего не найдено';
    }

    die();
}





add_action('wp_ajax_myfilter_single_pages', 'true_filter_function2');
add_action('wp_ajax_nopriv_myfilter_single_pages', 'true_filter_function2');

function true_filter_function2()
{


    if (
        isset($_POST['products_width'])
        && $_POST['products_width'] != 'Не выбрано'
        && ($_POST['products_height'] == 'Не выбрано' || $_POST['products_height'] == '')

    ) {
        $width = array(
            'key' => 'osnovnoj_razmer',
            'value' => '^' . $_POST['products_width'],
            'compare' => 'REGEXP'
        );
    } else {
        $width = array();
    }

    if (
        isset($_POST['products_height'])
        && $_POST['products_height'] != 'Не выбрано'
        && ($_POST['products_width'] == 'Не выбрано' || $_POST['products_width'] == '')
    ) {
        $height = array(
            'key' => 'osnovnoj_razmer',
            'value' => ' х ' . $_POST['products_height'],
            'compare' => 'LIKE'
        );
    } else {
        $height = array();
    }

    if (
        isset($_POST['products_height'])
        && $_POST['products_height'] != 'Не выбрано'
        && isset($_POST['products_width'])
        && $_POST['products_width'] != 'Не выбрано'
    ) {
        $width_height = array(
            'key' => 'osnovnoj_razmer',
            'value' => $_POST['products_width'] . ' х ' . $_POST['products_height'],
            'compare' => 'LIKE'
        );
    } else {
        $width_height = array();
    }

    if (isset($_POST['products_profil']) && $_POST['products_profil'] != 'Не выбрано') {
        $products_profil = explode(',', $_POST['products_profil']);
    } else {
        $products_profil = [];
    }

    if ($_POST['cena_min'] == '') {
        $price = 0;
    } else {
        $price = $_POST['cena_min'];
    }


    if ($_POST['page_num'] > 1) {
        $paged = $_POST['page_num'];
    } else {
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    }
    $args = array(
        'numberposts' => -1,
        'suppress_filters' => true,
        // 'orderby' => 'title', // сортировка по дате у нас будет в любом случае (но вы можете изменить/доработать это)
        'order'    => 'ASC', // ASC или DESC
        'meta_key' => 'jalousie_list_price',
        'orderby' => 'meta_value_num',
        'posts_per_page' => 12,
        'category' => 36,
        'category__not_in' => 25,
        'post__in'  => $products_profil,
        'paged' => $paged,
        'meta_query' => array(
            array(
                'key' => 'product_form_factor',
                'value' => $_POST['count_stvorok'],
                'compare' => 'LIKE'
            )
        )
    );
    $args['meta_query'] = array(
        'relation' => 'AND',
        array(
            'key' => 'jalousie_list_price',
            'value' => $price,
            'type' => 'numeric',
            'compare' => '>='
        ),
        $width,
        $height,
        $width_height,
        array(
            array(
                'key' => 'product_form_factor',
                'value' => $_POST['count_stvorok'],
                'compare' => 'LIKE'
            )
        )
    );




    //  var_dump($products_profil);
    $args_getPost['meta_query'] = array(
        'relation' => 'AND',
        array(
            'key' => 'jalousie_list_price',
            'value' => $price,
            'type' => 'numeric',
            'compare' => '>='
        ),
        $width,
        $height,
        $width_height,
    );
    $args_getPost = array(
        'numberposts' => -1,
        'suppress_filters' => true,
        'orderby' => 'title', // сортировка по дате у нас будет в любом случае (но вы можете изменить/доработать это)
        'order'    => 'ASC', // ASC или DESC
        'category' => 36,
        'category__not_in' => 25,
        'post__in'  => $products_profil,
    );
    //  var_dump($products_profil);
    $args_getPost['meta_query'] = array(
        'relation' => 'AND',
        array(
            'key' => 'jalousie_list_price',
            'value' => $price,
            'type' => 'numeric',
            'compare' => '>='
        ),
        $width,
        $height,
        $width_height,
    );
    // echo '<pre>';
    // var_dump($args);
    // echo '<br>';echo '<br>';
    // var_dump($_POST['products_width']);
    // var_dump($width);
    // echo '<br>';
    // var_dump($_POST['products_height']);
    // var_dump($height);
    // echo '<br>';
    // var_dump($width_height);
    // echo '<br>';
    // echo '<br>';
    // var_dump($_POST['products_profil']);
    // var_dump($products_profil);
    // echo '</pre>';   

    $product_list = get_posts($args_getPost);
    foreach ($product_list as $product) {
        if (get_field('osnovnoj_razmer', $product->ID)) {
            $razmerArr[] = get_field('osnovnoj_razmer', $product->ID);
        }
        $namesArr[] = $product->post_title;
        $namesArrIds[$product->post_title][] = $product->ID;
        //    var_dump($product->post_title);
    }
    foreach (array_unique($razmerArr) as $razmer) {
        $razmerWidth[] = preg_replace("/\s+/", "", explode('х', $razmer)[0]);
        $razmerHeight[] = preg_replace("/\s+/", "", explode('х', $razmer)[1]);
    }

    $namesArr = array_unique($namesArr);
    $razmerWidth = array_unique($razmerWidth);
    $razmerHeight = array_unique($razmerHeight);

    sort($razmerWidth);
    sort($razmerHeight);
    sort($namesArr); ?>
    <script>
        var products_width = <?= json_encode($razmerWidth); ?>;
        var products_height = <?= json_encode($razmerHeight); ?>;
        var products_profil = <?= json_encode($namesArr); ?>;
    </script>

    <?

    global $wp_query;
    $save_wpq = $wp_query;
    $wp_query = new WP_Query($args);

    if ($wp_query->have_posts()) {

        while ($wp_query->have_posts()) : $wp_query->the_post(); ?>

            <div class="other_size__tab_content__item" data-itemId="<?= get_the_title(); ?>">
                <? if (get_field('form-faktor') != '') : ?>
                    <span class="form-factor"><?= get_field('form-faktor'); ?>
                        <? if (get_field('form-faktor') != 'Балконный блок') {
                            echo 'окно';
                        }
                        ?>
                    </span>
                <? else : ?>
                    <span class="form-factor">
                        <? if (get_field('product_form_factor') != '') {
                            echo get_field('product_form_factor');
                        }
                        ?>
                    </span>
                <? endif; ?>
                <? if (get_field('form-faktor') == 'Одностворчатое') {
                    $margin = 'style="margin-left:auto;margin-right:auto;"';
                } else {
                    $margin = '';
                }
                ?>
                <a <?= $margin; ?> href="<?php echo the_permalink(); ?>" class="other_size__tab_content__item-img">

                    <? if (get_field('product_window_img')) : ?>

                        <img <? //if (get_field('product_window_img')=='https://www.oknastars.ru/wp-content/uploads/1_window.png') echo 'width="149"';
                                ?> <? if (get_field('product_window_img') == 'https://www.oknastars.ru/wp-content/uploads/1_window.png') echo 'width="116"'; ?> <? if (get_field('product_window_img') == 'https://www.oknastars.ru/wp-content/uploads/window-price-2.jpg') echo 'width="272"'; ?> <? if (get_field('product_window_img') == 'https://www.oknastars.ru/wp-content/uploads/window-price-1.jpg') echo 'width="186"'; ?> decoding="async" loading="lazy" src="<?= get_field('product_window_img'); ?>" alt="<?= get_the_title(); ?>" title=" REHAU <? $strings = preg_split('/ /', get_the_title());
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        echo implode(' ', array_slice($strings, 0, 2)); ?>">
                    <? endif; ?>
                </a>
                <a href="<?php echo the_permalink(); ?>" class="other_size__tab_content__item-name">
                    <!-- <? the_title(); ?> -->
                    REHAU
                    <?
                    $strings = preg_split('/ /', get_the_title());
                    echo implode(' ', array_slice($strings, 0, 2));
                    ?>
                </a>
                <div class="other_size__tab_content__item-size">
                    <? if (get_field('osnovnoj_razmer')) : ?>
                        <? echo str_replace('х', '<span>х</span>', get_field('osnovnoj_razmer')); ?>
                    <? endif; ?>
                </div>
                <div class="other_size__tab_content__item-price">
                    <? if (get_field('jalousie_list_price')) : ?>
                        от
                        <strong>
                            <?= number_format(get_field('jalousie_list_price'), 0, '.', ' ') ?>
                        </strong>

                        ₽
                    <? endif; ?>

                </div>
                <div class="size_tabs_item__content__product__href">
                    <a href="<?php echo the_permalink(); ?>" class="btn-link-text">
                        <span class="btn-link-text__title">Подробнее</span>
                        <span class="btn-link-text__icon"></span>
                    </a>
                </div>



            </div>

<? endwhile;



        $pagenum = (int) $_GET['page'];
        if (!$pagenum) {
            $pagenum = (get_query_var('paged')) ? get_query_var('paged') : 1;
        }

        $result =  paginate_links(array(
            'base'         => @add_query_arg('page', '%#%'),
            'format'       => '%#%',
            'current'   => $pagenum,
            'mid_size'  => 1,
            'end_size' => 1,
            'total'   => $wp_query->max_num_pages,
            'prev_text' => '<',
            'next_text' => '>'
        ));
        $result = str_replace('/wp-admin/admin-ajax.php', '', $result);
        echo ($result) ? "<div class='wp-pagenavi ajax_wp'>" . $result . "</div>" : '';


        wp_reset_postdata();
        wp_reset_query();
        $wp_query = $save_wpq;
    } else {
        echo 'Ничего не найдено';
    }

    die();
}


// УДАЛЕНИЕ ВСЕХ ПОСТОВ
// $params = array(
// 	'posts_per_page' => -1, // все посты
// 	'post_type'	=> 'post',
//      'category' => 36,
//         'category__not_in' => 25,
// );
// $q = new WP_Query( $params );
// if( $q->have_posts() ) : // если посты по заданным параметрам найдены
// 	while( $q->have_posts() ) : $q->the_post();
// 		wp_delete_post( $q->post->ID, true ); // второй параметр функции true означает, что пост будут удаляться, минуя корзину
// 	endwhile;
// endif;
// wp_reset_postdata();
if (strpos($_SERVER['REQUEST_URI'], '/page/') == true){
   function remove_robots_meta( $data ) {
   $data='noindex,follow';
   return $data;
   }
   add_filter( 'aioseop_robots_meta', 'remove_robots_meta' );
}
