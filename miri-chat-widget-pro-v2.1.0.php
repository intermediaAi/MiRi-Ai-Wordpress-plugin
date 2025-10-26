<?php
/**
 * Plugin Name: Miri Chat Widget Pro
 * Plugin URI: https://intermedia.co.il
 * Description: וידג'ט צ'אט מתקדם עם AI - גרסה מסחרית עם התאמה אישית מלאה
 * Version: 2.1.0
 * Author: Intermedia
 * Author URI: https://intermedia.co.il
 * Text Domain: miri-chat-pro
 * Domain Path: /languages
 * License: Commercial
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class Miri_Chat_Widget_Pro {
    
    private $option_name = 'miri_chat_pro_settings';
    private $version = '2.1.0';
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_ajax_miri_upload_logo', array($this, 'handle_logo_upload'));
        add_action('wp_ajax_miri_test_webhook', array($this, 'handle_test_webhook'));
        add_action('wp_ajax_miri_reset_settings', array($this, 'handle_reset_settings'));
        add_action('wp_footer', array($this, 'inject_chat_widget'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('init', array($this, 'load_textdomain'));
    }
    
    /**
     * Load text domain for translations
     */
    public function load_textdomain() {
        load_plugin_textdomain('miri-chat-pro', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    /**
     * Get default settings
     */
    public function get_defaults() {
        return array(
            // Basic
            'enabled' => true,
            'webhook_url' => 'https://bot.intermedia.co.il/webhook/dc518a4b-4656-440b-90e4-3b583c90ef5c/chat',

            // Appearance
            'skin' => 'cosmic',
            'title' => 'שלום! אני מירי 🤖',
            'subtitle' => 'זמינה עכשיו',
            'welcome_msg_1' => 'היי! 👋',
            'welcome_msg_2' => 'כאן <b>מירי</b> — איך אפשר לעזור היום?',
            'placeholder' => 'כתוב/כתבי הודעה...',
            'button_text' => 'שלח',
            
            // Colors
            'color_primary' => '#7C3AED',
            'color_secondary' => '#06B6D4',
            'color_bg' => '#0B1220',
            
            // Position & Size
            'button_position' => 'bottom-right',
            'button_size' => '64',
            'chat_width' => '420',
            'chat_height' => '600',
            
            // Logo
            'custom_logo' => '',
            'show_logo' => true,
            
            // Advanced
            'show_on_pages' => 'all',
            'excluded_pages' => '',
            'mobile_enabled' => true,
            'sound_enabled' => true,
            'typing_indicator' => true,
            'max_history_messages' => '50',
        );
    }
    
    /**
     * Add settings page
     */
    public function add_settings_page() {
        add_menu_page(
            __('Miri Chat Pro', 'miri-chat-pro'),
            __('Miri Chat Pro', 'miri-chat-pro'),
            'manage_options',
            'miri-chat-pro',
            array($this, 'render_settings_page'),
            'dashicons-format-chat',
            30
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting(
            'miri_chat_pro_options',
            $this->option_name,
            array(
                'sanitize_callback' => array($this, 'sanitize_settings')
            )
        );
    }
    
    /**
     * Sanitize settings
     */
    public function sanitize_settings($input) {
        $sanitized = array();
        
        $sanitized['enabled'] = !empty($input['enabled']);
        $sanitized['webhook_url'] = esc_url_raw($input['webhook_url']);
        $sanitized['title'] = sanitize_text_field($input['title']);
        $sanitized['subtitle'] = sanitize_text_field($input['subtitle']);
        $sanitized['welcome_msg_1'] = sanitize_text_field($input['welcome_msg_1']);
        $sanitized['welcome_msg_2'] = wp_kses_post($input['welcome_msg_2']);
        $sanitized['placeholder'] = sanitize_text_field($input['placeholder']);
        $sanitized['button_text'] = sanitize_text_field($input['button_text']);

        $sanitized['skin'] = in_array($input['skin'] ?? '', array('cosmic', 'whatsapp', 'light', 'business'))
            ? $input['skin']
            : 'cosmic';

        $sanitized['color_primary'] = sanitize_hex_color($input['color_primary']);
        $sanitized['color_secondary'] = sanitize_hex_color($input['color_secondary']);
        $sanitized['color_bg'] = sanitize_hex_color($input['color_bg']);

        $sanitized['button_position'] = in_array($input['button_position'], array('bottom-right', 'bottom-left', 'top-right', 'top-left')) 
            ? $input['button_position'] : 'bottom-right';
        $sanitized['button_size'] = absint($input['button_size']);
        $sanitized['chat_width'] = absint($input['chat_width']);
        $sanitized['chat_height'] = absint($input['chat_height']);
        
        $sanitized['custom_logo'] = esc_url_raw($input['custom_logo']);
        $sanitized['show_logo'] = !empty($input['show_logo']);
        
        $sanitized['show_on_pages'] = in_array($input['show_on_pages'], array('all', 'exclude', 'homepage')) 
            ? $input['show_on_pages'] : 'all';
        $sanitized['excluded_pages'] = sanitize_text_field($input['excluded_pages']);
        $sanitized['mobile_enabled'] = !empty($input['mobile_enabled']);
        $sanitized['sound_enabled'] = !empty($input['sound_enabled']);
        $sanitized['typing_indicator'] = !empty($input['typing_indicator']);
        $sanitized['max_history_messages'] = absint($input['max_history_messages']);

        return $sanitized;
    }

    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        if (is_admin()) {
            return;
        }

        $settings = wp_parse_args(get_option($this->option_name, array()), $this->get_defaults());

        if (!$this->should_display_widget($settings)) {
            return;
        }

        wp_enqueue_script(
            'miri-chat-widget',
            plugins_url('miri-chat-widget-v2.1.0.js', __FILE__),
            array(),
            $this->version,
            true
        );

        $config = array(
            'webhook' => esc_url_raw($settings['webhook_url']),
            'welcome1' => wp_kses_post($settings['welcome_msg_1']),
            'welcome2' => wp_kses_post($settings['welcome_msg_2']),
            'placeholder' => sanitize_text_field($settings['placeholder']),
            'buttonText' => sanitize_text_field($settings['button_text']),
            'title' => sanitize_text_field($settings['title']),
            'subtitle' => sanitize_text_field($settings['subtitle']),
            'typingIndicator' => !empty($settings['typing_indicator']),
            'soundEnabled' => !empty($settings['sound_enabled']),
            'maxHistoryMessages' => max(1, absint($settings['max_history_messages'])),
        );

        wp_add_inline_script(
            'miri-chat-widget',
            'window.miriChatConfig = ' . wp_json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . ';',
            'before'
        );
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        if ($hook !== 'toplevel_page_miri-chat-pro') {
            return;
        }
        
        wp_enqueue_media();
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        
        wp_enqueue_script(
            'miri-admin-js',
            plugins_url('assets/admin.js', __FILE__),
            array('jquery', 'wp-color-picker'),
            $this->version,
            true
        );
        
        wp_localize_script('miri-admin-js', 'miriAdmin', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('miri_admin_nonce'),
            'strings' => array(
                'confirmReset' => __('האם אתה בטוח? פעולה זו תמחק את כל ההגדרות המותאמות אישית!', 'miri-chat-pro'),
                'resetSuccess' => __('ההגדרות אופסו! לחץ על "שמור" כדי להחיל את השינויים.', 'miri-chat-pro'),
                'testingWebhook' => __('בודק חיבור...', 'miri-chat-pro'),
                'webhookSuccess' => __('החיבור תקין! ✓', 'miri-chat-pro'),
                'webhookError' => __('שגיאה בחיבור. בדוק את כתובת ה-Webhook.', 'miri-chat-pro'),
            )
        ));
    }
    
    /**
     * Handle logo upload via AJAX
     */
    public function handle_logo_upload() {
        check_ajax_referer('miri_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('אין לך הרשאות מספיקות', 'miri-chat-pro'));
        }
        
        if (empty($_FILES['logo'])) {
            wp_send_json_error(__('לא הועלה קובץ', 'miri-chat-pro'));
        }
        
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        
        $file = $_FILES['logo'];
        $upload_overrides = array('test_form' => false);
        $movefile = wp_handle_upload($file, $upload_overrides);
        
        if ($movefile && !isset($movefile['error'])) {
            wp_send_json_success(array(
                'url' => $movefile['url'],
                'message' => __('הלוגו הועלה בהצלחה', 'miri-chat-pro')
            ));
        } else {
            wp_send_json_error($movefile['error']);
        }
    }
    
    /**
     * Test webhook connection
     */
    public function handle_test_webhook() {
        check_ajax_referer('miri_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('אין לך הרשאות מספיקות', 'miri-chat-pro'));
        }
        
        $webhook_url = isset($_POST['webhook_url']) ? esc_url_raw($_POST['webhook_url']) : '';
        
        if (empty($webhook_url)) {
            wp_send_json_error(__('כתובת Webhook חסרה', 'miri-chat-pro'));
        }
        
        $response = wp_remote_post($webhook_url, array(
            'timeout' => 10,
            'headers' => array('Content-Type' => 'application/json'),
            'body' => json_encode(array(
                'sessionId' => 'test_' . time(),
                'chatInput' => 'test connection',
                'metadata' => array('source' => 'wordpress-test')
            ))
        ));
        
        if (is_wp_error($response)) {
            wp_send_json_error($response->get_error_message());
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        
        if ($status_code >= 200 && $status_code < 300) {
            wp_send_json_success(__('החיבור תקין!', 'miri-chat-pro'));
        } else {
            wp_send_json_error(__('שגיאה בחיבור - קוד: ', 'miri-chat-pro') . $status_code);
        }
    }
    
    /**
     * Reset settings to defaults
     */
    public function handle_reset_settings() {
        check_ajax_referer('miri_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('אין לך הרשאות מספיקות', 'miri-chat-pro'));
        }
        
        delete_option($this->option_name);
        wp_send_json_success(__('ההגדרות אופסו בהצלחה', 'miri-chat-pro'));
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        $settings = wp_parse_args(get_option($this->option_name, array()), $this->get_defaults());

        // Save notice
        if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
            echo '<div class="notice notice-success is-dismissible"><p>' . __('ההגדרות נשמרו בהצלחה!', 'miri-chat-pro') . '</p></div>';
        }

        include(plugin_dir_path(__FILE__) . 'admin/settings-page.php');
    }

    /**
     * Get custom CSS for selected skin
     */
    private function get_skin_css($settings) {
        $skin = $settings['skin'];

        switch ($skin) {
            case 'whatsapp':
                return "
:root{
  --primary:#075e54;
  --primary-light:#128c7e;
  --bg:#e5ddd5;
  --msg-bot:#ffffff;
  --msg-user:#dcf8c6;
  --text:#000000;
  --text-light:#667781;
  --border:#d1d7db;
}
.miri-launcher{background:var(--primary-light)!important}
.miri-wrap{font-family:system-ui,-apple-system,sans-serif}
.miri-card{background:#e5ddd5!important;background-image:url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAwIiBoZWlnaHQ9IjQwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxwYXRoIGQ9Ik0wIDBoNDAwdjQwMEgweiIgZmlsbD0iI0U1RERENSIvPjwvZz48L3N2Zz4=')!important;backdrop-filter:none!important;opacity:1!important}
.miri-header{background:var(--primary)!important;border-bottom:none!important;padding:10px 14px 8px!important;gap:10px!important}
.miri-hgroup{padding-left:8px!important;padding-right:0!important}
.miri-avatar{margin-left:4px!important}
.miri-feed{background:#e5ddd5!important}
.msg .bubble{box-shadow:0 1px 0.5px rgba(0,0,0,.13)!important}
.msg.me .bubble{background:var(--msg-user)!important;color:#000!important}
.msg:not(.me) .bubble{background:var(--msg-bot)!important;color:#000!important;border:none!important}
canvas#miri-net,
.miri-bubble{display:none!important}
.miri-input{background:#f0f2f5!important;border-top:none!important}
.miri-text{background:#ffffff!important;color:#000!important;border-color:#d1d7db!important}
.miri-send{background:var(--primary-light)!important}
";

            case 'light':
                return "
:root{
  --primary:#3b82f6;
  --bg:#ffffff;
  --bg-secondary:#f9fafb;
  --msg-bot:#f3f4f6;
  --msg-user:#3b82f6;
  --text:#111827;
  --text-light:#6b7280;
  --border:#e5e7eb;
}
.miri-launcher{background:var(--primary)!important;border-radius:50%!important;box-shadow:0 4px 12px rgba(59,130,246,0.3)!important}
.miri-card{background:rgba(255,255,255,0.98)!important;border:1px solid var(--border)!important;backdrop-filter:blur(20px)!important}
.miri-header{background:#ffffff!important;border-bottom:1px solid var(--border)!important;color:var(--text)!important;text-shadow:none!important;padding:10px 14px 8px!important;gap:10px!important}
.miri-hgroup{padding-left:8px!important;padding-right:0!important}
.miri-avatar{margin-left:4px!important}
.miri-title{color:var(--text)!important}
.miri-sub{color:var(--text-light)!important}
.miri-online{background:#10b981!important}
.miri-feed{background:var(--bg-secondary)!important}
.msg.me .bubble{background:var(--msg-user)!important;color:#ffffff!important;border:none!important}
.msg:not(.me) .bubble{background:var(--msg-bot)!important;color:var(--text)!important;border:1px solid var(--border)!important}
canvas#miri-net,
.miri-bubble,
.miri-shimmer{display:none!important}
.miri-input{background:#ffffff!important;border-top:1px solid var(--border)!important}
.miri-text{background:var(--bg-secondary)!important;color:var(--text)!important;border-color:var(--border)!important}
.miri-text::placeholder{color:var(--text-light)!important}
.miri-send{background:var(--primary)!important}
.ts{color:var(--text-light)!important}
";

            case 'business':
                return "
:root{
  --primary:#1e40af;
  --primary-light:#3b82f6;
  --bg:#f8fafc;
  --msg-bot:#e0e7ff;
  --msg-user:#3b82f6;
  --text:#1e293b;
  --text-light:#64748b;
  --border:#cbd5e1;
}
.miri-launcher{background:linear-gradient(135deg,var(--primary),var(--primary-light))!important}
.miri-card{background:rgba(255,255,255,0.98)!important;border:1px solid var(--border)!important;backdrop-filter:blur(20px)!important}
.miri-header{background:var(--primary)!important;border-bottom:1px solid rgba(255,255,255,0.1)!important;padding:10px 14px 8px!important;gap:10px!important}
.miri-hgroup{padding-left:8px!important;padding-right:0!important}
.miri-avatar{margin-left:4px!important}
.miri-feed{background:var(--bg)!important}
.msg.me .bubble{background:var(--msg-user)!important;color:#ffffff!important;border:none!important}
.msg:not(.me) .bubble{background:var(--msg-bot)!important;color:var(--text)!important;border:none!important}
canvas#miri-net,
.miri-bubble{display:none!important}
.miri-input{background:#ffffff!important;border-top:1px solid var(--border)!important}
.miri-text{background:var(--bg)!important;color:var(--text)!important;border-color:var(--border)!important}
.miri-text::placeholder{color:var(--text-light)!important}
.miri-send{background:var(--primary)!important}
";

            case 'cosmic':
            default:
                return "
/* Cosmic skin - הטקסט צריך להיות לא צמוד */
.miri-hgroup{padding-left:8px!important;padding-right:8px!important}
";
        }
    }
    
    /**
     * Check if widget should be displayed
     */
    private function should_display_widget($settings) {
        // Check if enabled
        if (!$settings['enabled']) {
            return false;
        }
        
        // Check mobile
        if (!$settings['mobile_enabled'] && wp_is_mobile()) {
            return false;
        }
        
        // Check page display rules
        $show_on = $settings['show_on_pages'];
        
        if ($show_on === 'homepage' && !is_front_page() && !is_home()) {
            return false;
        }
        
        if ($show_on === 'exclude' && !empty($settings['excluded_pages'])) {
            $excluded = array_map('trim', explode(',', $settings['excluded_pages']));
            $current_id = get_the_ID();
            if (in_array($current_id, $excluded)) {
                return false;
            }
        }
        
        return apply_filters('miri_should_display_widget', true, $settings);
    }
    
    /**
     * Inject chat widget into footer
     */
    public function inject_chat_widget() {
        $settings = wp_parse_args(get_option($this->option_name, array()), $this->get_defaults());
        
        if (!$this->should_display_widget($settings)) {
            return;
        }
        
        // Prepare variables
        $webhook_url = esc_js($settings['webhook_url']);
        $position = $settings['button_position'];
        $button_size = intval($settings['button_size']);
        $chat_width = intval($settings['chat_width']);
        $chat_height = intval($settings['chat_height']);
        
        // Position CSS
        $position_styles = $this->get_position_styles($position, $button_size);
        
        // Logo HTML
        $logo_html = $this->get_logo_html($settings);
        
        include(plugin_dir_path(__FILE__) . 'templates/widget.php');
    }
    
    /**
     * Get position styles based on button position
     */
    private function get_position_styles($position, $button_size) {
        $styles = array();
        
        switch ($position) {
            case 'bottom-right':
                $styles['button'] = 'right:22px;bottom:22px;';
                $styles['chat'] = 'right:22px;bottom:' . ($button_size + 30) . 'px;';
                break;
            case 'bottom-left':
                $styles['button'] = 'left:22px;bottom:22px;';
                $styles['chat'] = 'left:22px;bottom:' . ($button_size + 30) . 'px;';
                break;
            case 'top-right':
                $styles['button'] = 'right:22px;top:22px;';
                $styles['chat'] = 'right:22px;top:' . ($button_size + 30) . 'px;';
                break;
            case 'top-left':
                $styles['button'] = 'left:22px;top:22px;';
                $styles['chat'] = 'left:22px;top:' . ($button_size + 30) . 'px;';
                break;
        }
        
        return $styles;
    }
    
    /**
     * Get logo HTML
     */
    private function get_logo_html($settings) {
        if (!$settings['show_logo']) {
            return '';
        }
        
        if (!empty($settings['custom_logo'])) {
            return '<img src="' . esc_url($settings['custom_logo']) . '" alt="Logo" style="display:block;width:60%;height:60%;object-fit:contain;">';
        }
        
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="display:block;width:28px;height:28px"><path d="M12 2 L14.8 9.2 22 12 14.8 14.8 12 22 9.2 14.8 2 12 9.2 9.2 Z"/></svg>';
    }
}

// Initialize the plugin
function miri_chat_widget_pro_init() {
    return new Miri_Chat_Widget_Pro();
}
add_action('plugins_loaded', 'miri_chat_widget_pro_init');

// Activation hook
register_activation_hook(__FILE__, function() {
    // Set default options on activation
    $default_settings = (new Miri_Chat_Widget_Pro())->get_defaults();
    add_option('miri_chat_pro_settings', $default_settings);
    
    // Clear any caches
    wp_cache_flush();
});

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    // Clean up if needed
    wp_cache_flush();
});
