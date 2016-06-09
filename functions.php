<?php

$level = error_reporting();
$story_widget_status;
define( "MAILCHIMP_API_KEY","99983ece6b5ad94f7c4f026238381f4d-us6" );

// I'm not sure I like this approach to version management.
define('VERSION', intval( file_get_contents( get_template_directory() . '/version.txt' ) ) );
error_reporting(0);
@ini_set('display_errors', 0);

add_theme_support( 'post-formats' );
add_theme_support( 'post-thumbnails' );
add_theme_support( 'menus' );

add_filter( 'get_twig', 'add_to_twig' );
add_filter( 'timber_context', 'add_to_context' );

add_action( 'wp_enqueue_scripts', 'load_scripts' );
add_action( 'wp_head', 'nocache_headers' );

define( 'THEME_URL', get_template_directory_uri() );


add_filter( 'wpseo_opengraph_title', 'yoast_wpseo_title');

function yoast_wpseo_title( $title ) {

    if( is_single() ) {
        $post = new NationSwellPost();
        return $post->tout_title();
    }

    return $title;
}

function add_to_context( $data ) {
    $data['js_main'] = 'combined' . (WP_DEBUG ? '.min' : '.min') . '.js';
    $data['version'] = VERSION;
	$cdn = 'http://nmelp3rtl8l2tnuwd2blzv3ecu.wpengine.netdna-cdn.com';
    $data['static_dir'] = '/static/' . VERSION;

    /* this is where you can add your own data to Timber's context object */
    $data['menu_main'] = new TimberMenu('menu_main');
    $menu_post = new TimberMenu('menu_main_stories');
    $data['menu_main_menu'] = $menu_post;
    $menu_post_items = $menu_post->items;

    $data['menu_footer'] = new TimberMenu( 'menu_footer' );
    $data['menu_topic'] = new TimberMenu( 'menu_topic' );
    $data['is_editor'] = current_user_can('edit_posts');

    if(!empty($menu_post) && !empty($menu_post_items)) {

        $menu_post_ids = array();
        foreach($menu_post_items as $post) {
            array_push($menu_post_ids, $post->object_id);
        }
        $data['menu_main_stories'] = Timber::get_posts($menu_post_ids, 'NationSwellPost');
    }

    $data['ns_wp_title'] = wp_title('|',false,'right');
    
    
    // Global Site Options
    $options = array(
        'google_api_key',
        'google_client_id',
        'modal_joinus_enabled',
        'flyout_social_enabled',
        'nationswell_mailchimp_daily',
        'nationswell_facebook',
        'nationswell_twitter',
        'nationswell_instagram',
        'nationswell_tumblr',
        'nationswell_google',
        'site_tag_line',
        'facebook_button_expanded_text',
        'twitter_button_expanded_text',
        'twitter_button_bingo_text',
        'facebook_button_bingo_text',
        'take_action_header_text',
        'more_stories_heading_prefix',
        'load_more_button_text',
        'nav_search_placeholder_text',
        'nav_subscribe_placeholder_text',
        'byline_prefix_text',
        'category_prefix_text',
        'take_action_privacy_policy_text',
        'tweet_rep_privacy_policy_text',

        'flyout_header_text',
        'flyout_message_text',
        'flyout_opt_out_button_text',
        'flyout_facebook_opt_out_expiration',

        'facebook_like_url',
        'main_menu_header',

        'take_action_thanks_header',
        'take_action_thanks_text',
        'cta_button_text',
        'take_action_thanks_subscribe_text',
        'share_take_action_button_text',
        'share_take_action_text',

        'modal_joinus_header',
        'modal_joinus_body_text',
        'modal_joinus_opt_out_text',
        'modal_joinus_opt_out_button_text',
        'modal_joinus_subscribe_button_text',
        'modal_joinus_opt_out_expiration',
        'modal_joinus_close_expiration',
        'modal_joinus_subscribe_expiration',

        'password_copy_welcome',

        'facebook_admin'
    );

    foreach( $options as $option ) {
        $data[$option] = get_field( $option, 'option' );
    }

    return $data;
}

function add_to_twig($twig)
{
    /* this is where you can add your own fuctions to twig */
    $twig->addExtension(new Twig_Extension_StringLoader());
    $twig->addFilter('myfoo', new Twig_Filter_Function('myfoo'));

    return $twig;
}

function load_scripts()
{
    wp_enqueue_script('jquery');
}

// Custom Taxonomies

function setup_series_query($query) {
    // not an admin page and it is the main query
    if (is_tax('series') && $query->is_main_query()){
        // show 50 posts on series pages
        $query->set('posts_per_page', 50);
    }
}

add_action('pre_get_posts', 'setup_series_query');

include_once('lib/taxonomies/series.php');

function register_acf_fields()
{
    include_once('lib/fields/attachment.php');
    include_once('lib/fields/taxonomy.php');
    include_once('lib/fields/home_page.php');
    include_once('lib/fields/mug_shot.php');
    include_once('lib/fields/social_accounts.php');
    include_once('lib/fields/social_sharing.php');
    include_once('lib/fields/dek.php');
    include_once('lib/fields/content_type.php');
    include_once('lib/fields/hero.php');
    include_once('lib/fields/story_content.php');
    include_once('lib/fields/story_tease_display.php');
    include_once('lib/fields/call_to_action.php');
    include_once('lib/fields/call_to_action_link.php');
    include_once('lib/fields/ns_series_link.php');
    include_once('lib/fields/tout_options.php');
    include_once('lib/fields/ns_series.php');

    include_once('lib/fields/mailing_lists.php');
    include_once('lib/fields/modal_options.php');
    include_once('lib/fields/display_options.php');
    include_once('lib/fields/flyout_options.php');
    include_once('lib/fields/social_links.php');
    include_once('lib/fields/site_text.php');
    include_once('lib/fields/story_list.php');
    include_once('lib/fields/widget_popular.php');
    include_once('lib/fields/daily_newsletter_posts.php');
    include_once('lib/fields/editors_picks_posts.php');
    include_once('lib/fields/bi_contributors_posts.php');
    include_once('lib/fields/facebook_admin.php');
    include_once('lib/fields/council_contact.php');
    include_once('lib/fields/council_event.php');
    include_once('lib/fields/options/change_org.php');
    include_once('lib/fields/options/google.php');
    include_once('lib/fields/options/rally.php');
    include_once('lib/fields/options/nsc-portal.php');

    include_once('lib/fields/story_sponsorship.php');
}

add_action('acf/include_fields', 'register_acf_fields');

include_once('lib/classes/ChangeOrgApi.php');
include_once('lib/classes/ChangeOrgPetition.php');
include_once('lib/classes/RallyApi.php');
include_once('lib/classes/CallToAction.php');
include_once('lib/classes/NationSwellVideo.php');
include_once('lib/classes/NationSwellPost.php');
include_once('lib/classes/NationSwellSeries.php');
include_once('lib/classes/MailChimpFeed.php');
include_once('lib/classes/EditorsPicksFeed.php');
include_once('lib/classes/BIContributorsFeed.php');
include_once('lib/classes/NSCEvent.php');



// Shortcodes
include_once('lib/shortcodes/placeholder.php');

// Configure Menus
include_once('lib/menu/menu.php');

// Custom Widgets
include_once('lib/widgets/widgets.php');
include_once('lib/widgets/widget-joinus.php');
include_once('lib/widgets/widget-subscribe.php');
include_once('lib/widgets/widget-story.php');
include_once('lib/widgets/widget-popular.php');
include_once('lib/widgets/widget-stories.php');
include_once('lib/widgets/widget-contact.php');
include_once('lib/widgets/widget-text.php');
include_once('lib/widgets/widget-voting.php');
//partners widget added 6/9/2016 @cloudred
include_once('lib/widgets/widget-partners.php');

// Plugin Activation
//include_once('lib/tgm-plugin-activation/tgm-config.php');

// Custom Post Types
include_once('lib/custom_post_types/call_to_action.php');
include_once('lib/custom_post_types/story_list.php');
include_once('lib/custom_post_types/daily_newsletter.php');
include_once('lib/custom_post_types/editors_picks.php');
include_once('lib/custom_post_types/bi_contributors.php');
include_once('lib/custom_post_types/series.php');
include_once('lib/custom_post_types/nsc_contact.php');
include_once('lib/custom_post_types/nsc_event.php');

// Remove the SEO MetaBox from Custom Post Types
function prefix_remove_wp_seo_meta_box() {
    remove_meta_box( 'wpseo_meta', 'ns_story_list', 'normal' );
    remove_meta_box( 'wpseo_meta', 'ns_call_to_action', 'normal' );
    remove_meta_box( 'wpseo_meta', 'guest-author', 'normal' );
    remove_meta_box( 'wpseo_meta', 'ns_daily_newsletter', 'normal' );
    remove_meta_box( 'wpseo_meta', 'ns_editors_picks', 'normal' );
    remove_meta_box( 'wpseo_meta', 'ns_bi_contributors', 'normal' );
    remove_meta_box( 'wpseo_meta', 'nsccontact', 'normal' );
    remove_meta_box( 'wpseo_meta', 'nscevent', 'normal' );
}
add_action( 'add_meta_boxes', 'prefix_remove_wp_seo_meta_box', 100000 );

// For 4.3.0 <= PHP <= 5.4.0
if (!function_exists('http_response_code'))
{
    function http_response_code($new_code = NULL)
    {
        static $code = 200;
        if($new_code !== NULL)
        {
            header('X-PHP-Response-Code: '. $new_code, true, $new_code);
            if(!headers_sent())
            {
                $code = $new_code;
            }
        }
        return $code;
    }
}

function getAttribute($attrib, $tag){
    //get attribute from html tag
    $re = '/' . preg_quote($attrib) . '=([\'"])?((?(1).+?|[^\s>]+))(?(1)\1)/is';
    if (preg_match($re, $tag, $match)) {
        return urldecode($match[2]);
    }
    return false;
}

function truncate($input, $maxWords, $maxChars, $link)
{
    $words = preg_split('/\s+/', $input);
    $words = array_slice($words, 0, $maxWords);
    $words = array_reverse($words);

    $chars = 0;
    $truncated = array();

    while(count($words) > 0)
    {
        $fragment = trim(array_pop($words));
        $chars += strlen($fragment);

        if($chars > $maxChars) break;

        $truncated[] = $fragment;
    }

    $result = implode($truncated, ' ');

    return $result . (($input == $result) ? '' : ((isset($link)) ? '<a href="'. $link .'">&hellip;</a>' : '&hellip;'));
}

// Change.org
global $change_org_api;

function init_change_org_api() {

    if(function_exists('get_field')){

        global $change_org_api;

        $change_org_api_key = get_field('change_api_key', 'option');
        $change_org_secret = get_field('change_secret', 'option');
        $email = get_field('change_email', 'option');

        if(!empty($change_org_api_key) && !empty($change_org_secret) && !empty($email)) {
            $source = 'nationswell.com';
            $description = get_field('change_description', 'option');

            $change_org_api = new ChangeOrgApi($change_org_api_key, $change_org_secret, $email, $source, $description);

            $frequency = get_field('change_frequency', 'option');
            if(!empty($frequency) || $frequency > 0) {
                $change_org_api->set_frequency($frequency);
            }
        }

        if(isset($change_org_api)) {
            function is_petition($cta_id) {
                return get_post_meta($cta_id, 'type', true) === 'petition';
            }

            function save_petition_data($cta_id) {
                global $change_org_api;



                if ($_POST['post_type'] === 'ns_call_to_action' && is_petition($cta_id)) {
                    $petition = new ChangeOrgPetition($cta_id);
                    $change_org_api->fetch($petition);
                }
            }

            add_action( 'acf/save_post', 'save_petition_data', 20);

            function copy_from_post($keys) {
                $result = array();
                foreach($keys as $key) {
                    if(isset($_POST[$key])) {
                        $result[$key] = $_REQUEST[$key];
                    }
                }
                return $result;
            }

            function handle_sign_petition() {
                global $change_org_api;
                define( "NEWSLETTER_ID","8eaa257d1b" );

                $cta_id = (int)$_REQUEST['cta_id'];

                if(is_petition($cta_id)) {
                    $petition = new ChangeOrgPetition($cta_id);

                    $signer = copy_from_post(array('email','first_name', 'last_name', 'city', 'postal_code', 'country_code', 'reason'));
                    try {
                        ns_mailchimp_subscribe( NEWSLETTER_ID, $signer['email'], true );
                    } catch (Mailchimp_Error $e) {
                        //do nothing and fail silently
                    }
                    $response = $change_org_api->sign($petition, $signer);

                    http_response_code($response['response_code']);
                    unset($response['response_code']);

                    wp_send_json($response);
                }
                else {
                    http_response_code(404);
                }
                die();
            }

            add_action('wp_ajax_sign_petition', 'handle_sign_petition');
            add_action('wp_ajax_nopriv_sign_petition', 'handle_sign_petition');
        }
    }
}

add_action('init', 'init_change_org_api');

// Change.org
global $rally_api;

function init_rally_api()
{

    global $rally_api;

    if (function_exists('get_field')) {

        $drive = get_field('rally_drive', 'option');
        $auth_token = get_field('rally_auth_token', 'option');
        if (!empty($drive) && !empty($auth_token)) {
            $rally_api = new RallyApi($drive, $auth_token);

            $frequency = get_field('rally_frequency', 'option');
            if (!empty($frequency) || $frequency > 0) {
                $rally_api->set_frequency($frequency);
            }
        }
    }

}

add_action('init', 'init_rally_api');

// Automatically Create the Inital Pages for the Site and set the Homepage to be the Front Page
if (isset($_GET['activated']) && is_admin()){
    add_action('init', 'create_initial_pages');
}

function create_initial_pages() {
    $pages = array(
        'home' => 'Home',
        'about' => 'About',
        'contact' => 'Contact',
        'advertise' => 'Advertise',
        'termsofservice' => 'Terms of Service',
        'privacy' => 'Privacy Policy',
        'jobs' => 'Jobs',
    );

    foreach($pages as $key => $value) {
        $id = get_page_by_title($value);
        $page = array(
            'post_type'   => 'page',
            'post_title'  => $value,
            'post_name'   => $key,
            'post_status' => 'publish',
            'post_author' => 1,
            'post_parent' => ''
        );

        if (!isset($id)) {

            $post_id = wp_insert_post($page);

            update_post_meta($post_id, '_wp_page_template', 'page-static.php');

            if($value == 'Home') {
                update_option( 'show_on_front', 'page' );
                update_option( 'page_on_front', $post_id);
            }

        }

    };
}

add_action('generate_rewrite_rules', 'ns_add_rewrites');

function ns_add_rewrites($content) {
    global $wp_rewrite;
    $ns_new_non_wp_rules = array(
        'static/\d+/(css|img|fonts|js)/(.*)'          => 'wp-content/themes/nationswell/$1/$2',
    );

    $wp_rewrite->non_wp_rules = array_merge($wp_rewrite->non_wp_rules, $ns_new_non_wp_rules);

    return $content;
}

add_filter('gtc_list_item', 'gtc_add_tracking_list_item');
function gtc_add_tracking_list_item ($html)
{

    if(preg_match('/href="([^"]+)">([^<]+)/', $html, $matches))
    {
        $url = $matches[1];
        $title = $matches[2];
        return '<li><a href="' . $url . '" data-track=\'{}\'>' . $title . '</a></li>';
    }

    return $html;
}

add_filter('gtc_list_output', 'gtc_modify_list_output');

function gtc_modify_list_output($html) {
    return preg_replace('/<ol>/', '<ol data-module=\'{"name": "sidebar:popular"}\'>', $html, 1);
}


 add_action( 'wp_ajax_subscribe_action', 'subscribe_callback' );
 add_action( 'wp_ajax_nopriv_subscribe_action', 'subscribe_callback' );

function subscribe_callback() {
    
    $email = sanitize_email( $_POST['EMAIL'] );
    $listID = sanitize_text_field( $_POST['listid'] );	
    define( "NEWSLETTER_ID","8eaa257d1b" );
	try
	{
		if ( !empty( $listID ))
	    {
	    	try
	    	{
	    		ns_mailchimp_subscribe( NEWSLETTER_ID, $email, true );
	    		$response = ns_mailchimp_subscribe( $listID, $email, false );
	    	}
	    	catch (Mailchimp_List_AlreadySubscribed $e)
			{
	        	$response = ns_mailchimp_subscribe( $listID, $email, false );
                setcookie("subscribed","yes",time()+3600*24*999,"/");
			}
	    }
	    else {
	    	$response = ns_mailchimp_subscribe( NEWSLETTER_ID, $email, false );
	    }
	}
	catch (Mailchimp_List_AlreadySubscribed $e)
	{
		$response = array (
			"status" => "error",
			"message" => "You are already subscribed."
		);
        setcookie("subscribed","yes",time()+3600*24*999,"/");
	}
	catch (Mailchimp_Email_NotExists $e)
	{
		$response = array (
			"status" => "error",
			"message" => "That e-mail address does not exist."
		);
	}
	catch (Mailchimp_ValidationError $e)
	{
		$response = array (
			"status" => "error",
			"message" => "Please enter a valid e-mail address."
		);
	}
	catch (Mailchimp_Error $e)
	{
		$response = array (
			"status" => "error",
			"message" => "Please enter a valid e-mail address and retry."
		);
	}
    wp_send_json( $response);
    exit();
}

function ns_mailchimp_subscribe( $list, $emailaddr, $welcome ) {
    include_once 'Mailchimp.php';

    $params = array(
  	"id" => $list, 
   	"email" => array( 'email' => $emailaddr ), 
 	"merge_vars" => array(), 
    "email_type" => 'html', 
    "double_optin" => false,  
    "replace_interests" => false, 
    "send_welcome" => $welcome
    );
    
    $mail_chimp = new Mailchimp( MAILCHIMP_API_KEY );          			
    return $mail_chimp->call( 'lists/subscribe', $params );
}

add_action( 'wp_ajax_support_action', 'ns_supportmsg_callback' );
add_action( 'wp_ajax_nopriv_support_action', 'ns_supportmsg_callback' );

function ns_supportmsg_callback() {
    define('APP_URL', "https://script.google.com/macros/s/AKfycbz91ZsJLH_zCCWz6z-Do6rDwTAe5VyYUwHwnyYNm2WGHdTXrDo/exec");
    define('URL_PARAMS', "?sheet=" . urlencode(sanitize_text_field( $_POST['wsname'] )) . "&first=" . urlencode(sanitize_text_field( $_POST['first_name'] )) . "&last=" .
        urlencode(sanitize_text_field( $_POST['last_name'] )) . "&email=" . sanitize_email( $_POST['email'] ) . "&message=" . urlencode(sanitize_text_field( $_POST['message'] )));
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => APP_URL . URL_PARAMS
    ));

    $ss_response = curl_exec($curl);
    if ($ss_response)
    {
        curl_close($curl);
        exit();
    } else {
        curl_close($curl);
        exit();
    }
}

function google_analytics_tracking_code(){

    ?>

    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-45799105-1', 'nationswell.com');
        ga('require', 'displayfeatures');

        // Optimizely Universal Analytics Integration code
        window.optimizely = window.optimizely || [];
        window.optimizely.push(['activateUniversalAnalytics']);
        ga('send', 'pageview');

    </script>

<?php
}

add_action( 'wp_head', 'google_analytics_tracking_code' );


function pubexchange_widget()	{
	?>
		<script>(function(d, s, id) {
		var js, pjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = "http://cdn.pubexchange.com/modules/partner/nation_swell";
		pjs.parentNode.insertBefore(js, pjs);
		}(document, 'script', 'pubexchange-jssdk'));</script>
	<?php
}

add_action( 'wp_footer','pubexchange_widget' );

function ns_tynt() {
	?>
	<!-- BEGIN Tynt Script -->
	<script type="text/javascript">
	if(document.location.protocol=='http:'){
	 var Tynt=Tynt||[];Tynt.push('cE3vDq33er44VJacwqm_6r');
	 (function(){var s=document.createElement('script');s.async="async";s.type="text/javascript";s.src='http://tcr.tynt.com/ti.js';var h=document.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);})();
	}
	</script>
	<!-- END Tynt Script -->
	<?php
}
add_action( 'wp_head', 'ns_tynt' );

function ns_get_joinus_cookie() {

    if ( isset($_COOKIE["subscribed"] )) {
        $GLOBALS["story_widget_status"] = "disabled";
    } else {
        $GLOBALS["story_widget_status"] = "enabled";
    }
}
add_action('init', 'ns_get_joinus_cookie');

function ns_newsletter_shortcode() {

    if ($GLOBALS["story_widget_status"] == "disabled") {
        return;
    } else {

        return "<div class=\"story-signup\" data-module='{\"name\": \"story:subscribe\"}'>
        <header>
            <div class=\"indicator indicator-story-signup icon icon_envelope\"></div>
        </header>
        <p>" . get_field( 'embedded_subscribe_copy', 'option' ) . "</p>
            <form action=\"\" method=\"post\" name=\"mc-embedded-subscribe-form\" class=\"mc-form validate\" target=\"_blank\" novalidate>
                <div class=\"mc-field-group cf\">
                    <div class=\"form-fields\">
                        <input type=\"email\" value=\"\" placeholder=\"Enter your email address\" name=\"EMAIL\" class=\"required email text-input\" id=\"mc-email\">
                        <input type=\"hidden\" name=\"listid\" value=\"\">
                        <label for=\"mc-email\" class=\"mc-email-status\"></label>
                        <input type=\"submit\" value=\"" . get_field( 'embedded_subscribe_button_text','option' ) . "\" name=\"subscribe\" id=\"mc-embedded-subscribe\" class=\"btn btn--solid\" data-modal-disable=\"" . get_field( 'modal_joinus_subscribe_expiration','option' ) . "\">
                    </div>
                </div>
            </form>
        </div>";
    }
}
add_shortcode( 'newsletter', 'ns_newsletter_shortcode' );

function voting_shortcode() {
  return "" . do_shortcode('[gravityform id="3" title="false" description="false" ajax="true"]') ."";
}
add_shortcode( 'voting_widget', 'voting_shortcode' );

class GW_Submission_Limit {
    var $_args;
	var $_notification_event;
	private static $forms_with_individual_settings = array();
    function __construct($args) {
	    // make sure we're running the required minimum version of Gravity Forms
	    if( ! property_exists( 'GFCommon', 'version' ) || ! version_compare( GFCommon::$version, '1.8', '>=' ) )
		    return;
        $this->_args = wp_parse_args( $args, array(
            'form_id' => false,
            'limit' => 1,
            'limit_by' => 'ip', // 'ip', 'user_id', 'role', 'embed_url', 'field_value'
            'time_period' => 60 * 60 * 24, // integer in seconds or 'day', 'month', 'year' to limit to current day, month, or year respectively
            'limit_message' => __( 'Sorry, you have reached the submission limit for this form.' ),
	        'apply_limit_per_form' => true,
	        'enable_notifications' => false
        ) );
        if( ! is_array( $this->_args['limit_by'] ) ) {
            $this->_args['limit_by'] = array( $this->_args['limit_by'] );
        }
	    if( $this->_args['form_id'] ) {
		    self::$forms_with_individual_settings[] = $this->_args['form_id'];
	    }
        add_action( 'init', array( $this, 'init' ) );
    }
	function init() {
		add_filter( 'gform_pre_render', array( $this, 'pre_render' ) );
		add_filter( 'gform_validation', array( $this, 'validate' ) );
		if( $this->_args['enable_notifications'] ) {
			$this->enable_notifications();
			add_action( 'gform_after_submission', array( $this, 'maybe_send_limit_reached_notifications' ), 10, 2 );
		}
	}
    function pre_render( $form ) {
        if( ! $this->is_applicable_form( $form ) || ! $this->is_limit_reached( $form['id'] ) ) {
	        return $form;
        }
        
        ob_start();
        if ($form['id'] == 4) {
	        require_once( get_template_directory() . '/allstar_voted-widget.php' );
        } else {
	        require_once( get_template_directory() . '/allstar_voted.php' );
	      }
        $message = ob_get_clean();
        $submission_info = rgar( GFFormDisplay::$submission, $form['id'] );
        // if no submission, hide form
        // if submission and not valid, hide form
        // unless 'field_value' limiter is applied
        if( ( ! $submission_info || ! rgar( $submission_info, 'is_valid' ) ) && ! $this->is_limited_by_field_value() ) {
	        add_filter( 'gform_get_form_filter_' . $form['id'], create_function( '', 'return \'' . $message . '\';' ) );
        }
        return $form;
    }
    function validate( $validation_result ) {
        if( ! $this->is_applicable_form( $validation_result['form'] ) || ! $this->is_limit_reached( $validation_result['form']['id'] ) ) {
            return $validation_result;
        }
        $validation_result['is_valid'] = false;
        if( $this->is_limited_by_field_value() ) {
	        $field_ids = array_map( 'intval', $this->get_limit_field_ids() );
            foreach( $validation_result['form']['fields'] as &$field ) {
                if( in_array( $field['id'], $field_ids ) ) {
                    $field['failed_validation'] = true;
                    $field['validation_message'] = do_shortcode( $this->_args['limit_message'] );
                }
            }
        }
        return $validation_result;
    }
    public function is_limit_reached($form_id) {
        global $wpdb;
        $where = array();
        $join = array();
	    $where[] = 'l.status = "active"';
        foreach( $this->_args['limit_by'] as $limiter ) {
            switch( $limiter ) {
                case 'role': // user ID is required when limiting by role
                case 'user_id':
                    $where[] = $wpdb->prepare( 'l.created_by = %s', get_current_user_id() );
                    break;
                case 'embed_url':
                    $where[] = $wpdb->prepare( 'l.source_url = %s', GFFormsModel::get_current_page_url());
                    break;
                case 'field_value':
                    $values = $this->get_limit_field_values( $form_id, $this->get_limit_field_ids() );
                    // if there is no value submitted for any of our fields, limit is never reached
                    if( empty( $values ) ) {
                         return false;
                    }
					foreach( $values as $field_id => $value ) {
						$table_slug = sprintf( 'ld%s', str_replace( '.', '_', $field_id ) );
						$join[]     = "INNER JOIN {$wpdb->prefix}rg_lead_detail {$table_slug} ON {$table_slug}.lead_id = l.id";
						//$where[]    = $wpdb->prepare( "CAST( {$table_slug}.field_number as unsigned ) = %f AND {$table_slug}.value = %s", $field_id, $value );
						$where[]    = $wpdb->prepare( "\n( ( {$table_slug}.field_number BETWEEN %s AND %s ) AND {$table_slug}.value = %s )", doubleval( $field_id ) - 0.001, doubleval( $field_id ) + 0.001, $value );
					}
                    break;
                default:
                    $where[] = $wpdb->prepare( 'ip = %s', GFFormsModel::get_ip() );
            }
        }
	    if( $this->_args['apply_limit_per_form'] ) {
		    $where[] = $wpdb->prepare( 'l.form_id = %d', $form_id );
	    }
        $time_period = $this->_args['time_period'];
        $time_period_sql = false;
        if( $time_period === false ) {
            // no time period
        } else if( intval( $time_period ) > 0 ) {
            $time_period_sql = $wpdb->prepare( 'date_created BETWEEN DATE_SUB(utc_timestamp(), INTERVAL %d SECOND) AND utc_timestamp()', $this->_args['time_period'] );
        } else {
            switch( $time_period ) {
                case 'per_day':
                case 'day':
                    $time_period_sql = 'DATE( date_created ) = DATE( utc_timestamp() )';
                break;
                case 'per_month':
                case 'month':
                    $time_period_sql = 'MONTH( date_created ) = MONTH( utc_timestamp() )';
                break;
                case 'per_year':
                case 'year':
                    $time_period_sql = 'YEAR( date_created ) = YEAR( utc_timestamp() )';
                break;
            }
        }
        if( $time_period_sql ) {
            $where[] = $time_period_sql;
        }
        $where = implode( ' AND ', $where );
        $join = implode( "\n", $join );
        $sql = "SELECT count( l.id )
                FROM {$wpdb->prefix}rg_lead l
                $join
                WHERE $where";
        $entry_count = $wpdb->get_var( $sql );
        return $entry_count >= $this->get_limit();
    }
    public function is_limited_by_field_value() {
        return in_array( 'field_value', $this->_args['limit_by'] );
    }
    public function get_limit_field_ids() {
	    $limit = $this->_args['limit'];
	    if( is_array( $limit ) ) {
		    $field_ids = array( call_user_func( 'array_shift', array_keys( $this->_args['limit'] ) ) );
	    } else {
		    $field_ids = $this->_args['fields'];
	    }
        return $field_ids;
    }
    public function get_limit_field_values( $form_id, $field_ids ) {
	    $form   = GFAPI::get_form( $form_id );
	    $values = array();
	    foreach( $field_ids as $field_id ) {
		    $field      = GFFormsModel::get_field( $form, $field_id );
		    $input_name = 'input_' . str_replace( '.', '_', $field_id );
		    $value      = GFFormsModel::prepare_value( $form, $field, rgpost( $input_name ), $input_name, null );
		    if( ! rgblank( $value ) ) {
			    $values[ $field_id ] = $value;
		    }
	    }
        return $values;
    }
    public function get_limit() {
        $limit = $this->_args['limit'];
        if( $this->is_limited_by_field_value() ) {
            $limit = is_array( $limit ) ? array_shift( $limit ) : intval( $limit );
        } else if( in_array( 'role', $this->_args['limit_by'] ) ) {
            $limit = rgar( $limit, $this->get_user_role() );
        }
        return intval( $limit );
    }
    public function get_user_role() {
        $user = wp_get_current_user();
        $role = reset( $user->roles );
        return $role;
    }
	public function enable_notifications() {
		if( ! class_exists( 'GW_Notification_Event' ) ) {
			_doing_it_wrong( 'GW_Inventory::$enable_notifications', __( 'Inventory notifications require the \'GW_Notification_Event\' class.' ), '1.0' );
		} else {
			$event_slug = implode( array_filter( array( "gw_submission_limit_limit_reached", $this->_args['form_id'] ) ) );
			$event_name = GFForms::get_page() == 'notification_edit' ? __( 'Submission limit reached' ) : __( 'Event name is only populated on Notification Edit view; saves a DB call to get the form on every ' );
			$this->_notification_event = new GW_Notification_Event( array(
				'form_id'    => $this->_args['form_id'],
				'event_name' => $event_name,
				'event_slug' => $event_slug
				//'trigger'    => array( $this, 'notification_event_listener' )
			) );
		}
	}
	public function maybe_send_limit_reached_notifications( $entry, $form ) {
		if( $this->is_applicable_form( $form ) && $this->is_limit_reached( $form['id'] ) ) {
			$this->send_limit_reached_notifications( $form, $entry );
		}
	}
	public function send_limit_reached_notifications( $form, $entry ) {
		$this->_notification_event->send_notifications( $this->_notification_event->get_event_slug(), $form, $entry, true );
	}
	function is_applicable_form( $form ) {
		$form_id          = isset( $form['id'] ) ? $form['id'] : $form;
		$is_global_form   = empty( $this->_args['form_id'] ) && ! in_array( $form_id, self::$forms_with_individual_settings );
		$is_specific_form = $form_id == $this->_args['form_id'];
		return $is_global_form || $is_specific_form;
	}
}
class GWSubmissionLimit extends GW_Submission_Limit { }

new GW_Submission_Limit( array(
    'form_id' => 3,
    'limit_by' => 'ip',
    'limit' => 1,
    'time_period' => 'per_day'
) );

new GW_Submission_Limit( array(
    'form_id' => 4,
    'limit_by' => 'ip',
    'limit' => 1,
    'time_period' => 'per_day'
) );


add_filter( 'gform_confirmation', 'custom_confirmation', 10, 4 );
function custom_confirmation( $confirmation, $form, $entry, $ajax ) {
    if( $form['id'] == '3' ) {
        ob_start();
        require_once( get_template_directory() . '/allstar_voted.php' );
        $confirmation = ob_get_clean();
    } 
    if( $form['id'] == '4' ) {
        ob_start();
        require_once( get_template_directory() . '/allstar_voted-widget.php' );
        $confirmation = ob_get_clean();
    } 
    return $confirmation;
}

add_action( 'show_user_profile', 'ns_extra_email_field' );
add_action( 'edit_user_profile', 'ns_extra_email_field' );

function ns_extra_email_field( $user ) { ?>

    <h3>E-mail To Display</h3>

    <table class="form-table">

        <tr>
            <th><label for="email-display">E-mail Address</label></th>

            <td>
                <input type="text" name="email_display" id="email_display" value="<?php echo esc_attr( get_the_author_meta( 'email_display', $user->ID ) ); ?>" class="regular-text" /><br />
                <span class="description">Please enter your E-mail to display to visitors.</span>
            </td>
        </tr>

    </table>
<?php
}

add_action( 'personal_options_update', 'ns_save_extra_email_field' );
add_action( 'edit_user_profile_update', 'ns_save_extra_email_field' );

function ns_save_extra_email_field( $user_id ) {

    if ( !current_user_can( 'edit_user', $user_id ) )
        return false;

    update_usermeta( $user_id, 'email_display', $_POST['email_display'] );
}


add_action( 'wp_ajax_initialize_nscdirectory', 'ns_nscdirectory_callback' );
add_action( 'wp_ajax_nopriv_initialize_nscdirectory', 'ns_nscdirectory_callback' );

function ns_nscdirectory_callback() {

    $contactData = get_transient( 'nsc_portal_contact_data' );

    if ( false === ( $contactData ) ) {
        // It wasn't there, so regenerate the data and save the transient
        $query = array(
            'posts_per_page' => -1,
            'post_type' => 'nsccontact',
            'orderby' => 'meta_value',
            'order' => 'ASC',
            'meta_key' => 'nsc_surname',
        );
        $contactPosts = get_posts( $query );
        $contactData = array();
        foreach($contactPosts as $contact) {
            $contactData[] = array (
                "name" => $contact->post_title,
                "surname" => get_field("nsc_surname", $contact),
                "bio" => get_field("nsc_bio", $contact),
                "image" => get_field("nsc_image", $contact),
                "interests" => get_field("nsc_interests", $contact),
                "email" => get_field("nsc_email", $contact),
                "linkedin" => get_field("nsc_linkedin", $contact),
                "phone" => get_field("nsc_phone", $contact),
                "twitter" => get_field("nsc_twitter", $contact)
            );
        }
        set_transient( 'nsc_portal_contact_data', $contactData, 12 * HOUR_IN_SECONDS );
    }

    wp_send_json( $contactData );
    exit();
}

add_action( 'wp_ajax_initialize_nscevents', 'ns_nscevents_callback' );
add_action( 'wp_ajax_nopriv_initialize_nscevents', 'ns_nscevents_callback' );

function ns_nscevents_callback() {
    $events = array();
    $events["upcoming"] = NSCEvent::getUpcomingEvents();
    $events["past"] = NSCEvent::getPastEvents();
    wp_send_json( $events );
    exit();
}


add_action( 'wp_ajax_initialize_nscupdates', 'ns_nscupdates_callback' );
add_action( 'wp_ajax_nopriv_initialize_nscupdates', 'ns_nscupdates_callback' );

function ns_nscupdates_callback() {
    $updatesData = array();
    $updatesPosts = get_posts( array(
                'numberposts' => -1,
                'category_name' => 'nsc-updates'
    ));

    foreach ($updatesPosts as $update) {
        $updatesData[] = array(
            "title" => $update->post_title,
            'dek'   => get_field("dek", $update),
            'url'   => get_permalink($update),
            'thumbnail_url' => wp_get_attachment_url( get_post_thumbnail_id($update->ID) )
        );
    }
    error_log(print_r($updatesData, TRUE));
    wp_send_json( $updatesData );
    exit();

}

function clear_portal_transient($post_id, $post) {

    if ($post->post_type != 'nsccontact') {
        return;
    }
    delete_transient('nsc_portal_contact_data');
}

add_action('save_post', 'clear_portal_transient', 1, 2);
	
//Customize login screen
function custom_login() { 
echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('template_directory').'/custom-login/custom-login.css" />'; 
}
add_action('login_head', 'custom_login');

add_image_size( 'member-thumb', 380, 380, true );
add_image_size( 'event-thumb', 400, 260, true );
add_image_size( 'quote-thumb', 650, 450, true );

function get_latest_stylesheet() {
	$stylesheet = "style.css";

	foreach (glob(get_stylesheet_directory() . "/style_*.css") as $filename) {
		$filename = basename($filename);
		if ($filename > $stylesheet)
			$stylesheet = $filename;
	}

	return dirname(get_stylesheet_uri()) . "/" . $stylesheet;
}