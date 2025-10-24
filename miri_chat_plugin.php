<?php
/**
 * Plugin Name: Miri Chat Widget Pro
 * Plugin URI: https://intermedia.co.il
 * Description: וידג'ט צ'אט מתקדם עם AI - גרסה מסחרית עם התאמה אישית מלאה
 * Version: 2.0.9
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
    private $version = '2.0.9';
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('wp_footer', array($this, 'inject_chat_widget'));
    }
    
    /**
     * Get default settings
     */
    private function get_defaults() {
        return array(
            // Basic
            'enabled' => true,
            'webhook_url' => 'https://bot.intermedia.co.il/webhook/dc518a4b-4656-440b-90e4-3b583c90ef5c/chat',
            
            // Appearance
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
            
            // Logo
            'custom_logo' => '',
            'show_logo' => true,
            
            // Advanced
            'show_on_pages' => 'all',
            'excluded_pages' => '',
            'mobile_enabled' => true,
        );
    }
    
    /**
     * Add settings page
     */
    public function add_settings_page() {
        add_menu_page(
            'Miri Chat Pro',
            'Miri Chat Pro',
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
        register_setting('miri_chat_pro_options', $this->option_name);
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
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        $settings = wp_parse_args(get_option($this->option_name, array()), $this->get_defaults());
        ?>
        <div class="wrap miri-admin-wrap" dir="rtl">
            <h1>
                <span class="dashicons dashicons-format-chat" style="font-size:32px;color:#7C3AED;"></span>
                Miri Chat Widget Pro
                <span style="font-size:14px;color:#666;font-weight:normal;">גרסה <?php echo $this->version; ?></span>
            </h1>
            
            <form method="post" action="options.php" id="miri-settings-form">
                <?php settings_fields('miri_chat_pro_options'); ?>
                
                <div class="miri-admin-container">
                    <!-- Sidebar Tabs -->
                    <div class="miri-admin-sidebar">
                        <div class="miri-tabs">
                            <button type="button" class="miri-tab active" data-tab="basic">
                                <span class="dashicons dashicons-admin-generic"></span>
                                הגדרות בסיסיות
                            </button>
                            <button type="button" class="miri-tab" data-tab="design">
                                <span class="dashicons dashicons-art"></span>
                                עיצוב וצבעים
                            </button>
                            <button type="button" class="miri-tab" data-tab="texts">
                                <span class="dashicons dashicons-edit"></span>
                                טקסטים ותכנים
                            </button>
                            <button type="button" class="miri-tab" data-tab="position">
                                <span class="dashicons dashicons-move"></span>
                                מיקום וגודל
                            </button>
                            <button type="button" class="miri-tab" data-tab="advanced">
                                <span class="dashicons dashicons-admin-tools"></span>
                                מתקדם
                            </button>
                        </div>
                    </div>
                    
                    <!-- Main Content -->
                    <div class="miri-admin-content">
                        
                        <!-- Basic Settings -->
                        <div class="miri-tab-content active" data-content="basic">
                            <h2>⚙️ הגדרות בסיסיות</h2>
                            
                            <table class="form-table">
                                <tr>
                                    <th>סטטוס הצ'אט</th>
                                    <td>
                                        <label class="miri-switch">
                                            <input type="checkbox" name="<?php echo $this->option_name; ?>[enabled]" value="1" <?php checked($settings['enabled'], true); ?>>
                                            <span class="miri-slider"></span>
                                        </label>
                                        <p class="description">הפעל או השבת את וידג'ט הצ'אט באתר</p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th>כתובת Webhook</th>
                                    <td>
                                        <input type="url" 
                                               name="<?php echo $this->option_name; ?>[webhook_url]" 
                                               value="<?php echo esc_attr($settings['webhook_url']); ?>"
                                               class="regular-text"
                                               dir="ltr"
                                               style="text-align:left;"
                                               required>
                                        <p class="description">כתובת ה-API של בוט הצ'אט</p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th>תצוגה במובייל</th>
                                    <td>
                                        <label class="miri-switch">
                                            <input type="checkbox" name="<?php echo $this->option_name; ?>[mobile_enabled]" value="1" <?php checked($settings['mobile_enabled'], true); ?>>
                                            <span class="miri-slider"></span>
                                        </label>
                                        <p class="description">הצג את הצ'אט גם במכשירים ניידים</p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <!-- Design Settings -->
                        <div class="miri-tab-content" data-content="design">
                            <h2>🎨 עיצוב וצבעים</h2>
                            
                            <table class="form-table">
                                <tr>
                                    <th>צבע ראשי</th>
                                    <td>
                                        <input type="text" 
                                               name="<?php echo $this->option_name; ?>[color_primary]" 
                                               value="<?php echo esc_attr($settings['color_primary']); ?>"
                                               class="miri-color-picker">
                                        <p class="description">צבע הגרדיאנט העיקרי (ברירת מחדל: #7C3AED)</p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th>צבע משני</th>
                                    <td>
                                        <input type="text" 
                                               name="<?php echo $this->option_name; ?>[color_secondary]" 
                                               value="<?php echo esc_attr($settings['color_secondary']); ?>"
                                               class="miri-color-picker">
                                        <p class="description">צבע הגרדיאנט המשני (ברירת מחדל: #06B6D4)</p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th>צבע רקע</th>
                                    <td>
                                        <input type="text" 
                                               name="<?php echo $this->option_name; ?>[color_bg]" 
                                               value="<?php echo esc_attr($settings['color_bg']); ?>"
                                               class="miri-color-picker">
                                        <p class="description">צבע רקע החלון (ברירת מחדל: #0B1220)</p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th>לוגו מותאם אישית</th>
                                    <td>
                                        <div class="miri-logo-upload">
                                            <?php if (!empty($settings['custom_logo'])): ?>
                                                <img src="<?php echo esc_url($settings['custom_logo']); ?>" class="miri-logo-preview" style="max-width:80px;display:block;margin-bottom:10px;">
                                            <?php endif; ?>
                                            <input type="hidden" name="<?php echo $this->option_name; ?>[custom_logo]" value="<?php echo esc_attr($settings['custom_logo']); ?>" id="miri-logo-url">
                                            <button type="button" class="button" id="miri-upload-logo">העלה לוגו</button>
                                            <?php if (!empty($settings['custom_logo'])): ?>
                                                <button type="button" class="button" id="miri-remove-logo">הסר לוגו</button>
                                            <?php endif; ?>
                                        </div>
                                        <p class="description">העלה לוגו משלך להחליף את הסמל הדיפולטיבי (מומלץ: 100x100 פיקסלים)</p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th>הצג לוגו/סמל</th>
                                    <td>
                                        <label class="miri-switch">
                                            <input type="checkbox" name="<?php echo $this->option_name; ?>[show_logo]" value="1" <?php checked($settings['show_logo'], true); ?>>
                                            <span class="miri-slider"></span>
                                        </label>
                                        <p class="description">הצג או הסתר את הלוגו/סמל בכפתור ובכותרת</p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <!-- Text Settings -->
                        <div class="miri-tab-content" data-content="texts">
                            <h2>📝 טקסטים ותכנים</h2>
                            
                            <table class="form-table">
                                <tr>
                                    <th>כותרת ראשית</th>
                                    <td>
                                        <input type="text" 
                                               name="<?php echo $this->option_name; ?>[title]" 
                                               value="<?php echo esc_attr($settings['title']); ?>"
                                               class="regular-text">
                                        <p class="description">הכותרת שתופיע בראש חלון הצ'אט</p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th>כותרת משנה</th>
                                    <td>
                                        <input type="text" 
                                               name="<?php echo $this->option_name; ?>[subtitle]" 
                                               value="<?php echo esc_attr($settings['subtitle']); ?>"
                                               class="regular-text">
                                        <p class="description">סטטוס או הודעה קצרה מתחת לכותרת</p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th>הודעת פתיחה 1</th>
                                    <td>
                                        <input type="text" 
                                               name="<?php echo $this->option_name; ?>[welcome_msg_1]" 
                                               value="<?php echo esc_attr($settings['welcome_msg_1']); ?>"
                                               class="regular-text">
                                        <p class="description">ההודעה הראשונה שמופיעה כשפותחים את הצ'אט</p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th>הודעת פתיחה 2</th>
                                    <td>
                                        <input type="text" 
                                               name="<?php echo $this->option_name; ?>[welcome_msg_2]" 
                                               value="<?php echo esc_attr($settings['welcome_msg_2']); ?>"
                                               class="regular-text">
                                        <p class="description">ההודעה השנייה (אפשר להשתמש ב-HTML פשוט כמו &lt;b&gt;)</p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th>טקסט שדה הקלט</th>
                                    <td>
                                        <input type="text" 
                                               name="<?php echo $this->option_name; ?>[placeholder]" 
                                               value="<?php echo esc_attr($settings['placeholder']); ?>"
                                               class="regular-text">
                                        <p class="description">הטקסט שמופיע בתיבת ההקלדה</p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th>טקסט כפתור שליחה</th>
                                    <td>
                                        <input type="text" 
                                               name="<?php echo $this->option_name; ?>[button_text]" 
                                               value="<?php echo esc_attr($settings['button_text']); ?>"
                                               class="regular-text">
                                        <p class="description">הטקסט על כפתור השליחה</p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <!-- Position Settings -->
                        <div class="miri-tab-content" data-content="position">
                            <h2>📍 מיקום וגודל</h2>
                            
                            <table class="form-table">
                                <tr>
                                    <th>מיקום הכפתור</th>
                                    <td>
                                        <select name="<?php echo $this->option_name; ?>[button_position]">
                                            <option value="bottom-right" <?php selected($settings['button_position'], 'bottom-right'); ?>>למטה מימין</option>
                                            <option value="bottom-left" <?php selected($settings['button_position'], 'bottom-left'); ?>>למטה משמאל</option>
                                            <option value="top-right" <?php selected($settings['button_position'], 'top-right'); ?>>למעלה מימין</option>
                                            <option value="top-left" <?php selected($settings['button_position'], 'top-left'); ?>>למעלה משמאל</option>
                                        </select>
                                        <p class="description">היכן להציג את כפתור הצ'אט המרחף</p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th>גודל הכפתור</th>
                                    <td>
                                        <input type="range" 
                                               name="<?php echo $this->option_name; ?>[button_size]" 
                                               value="<?php echo esc_attr($settings['button_size']); ?>"
                                               min="48" max="80" step="4"
                                               class="miri-range"
                                               id="button-size-range">
                                        <output id="button-size-output"><?php echo $settings['button_size']; ?>px</output>
                                        <p class="description">גודל כפתור הצ'אט המרחף (48-80 פיקסלים)</p>
                                    </td>
                                </tr>
                                
                                <tr>
                                    <th>רוחב חלון הצ'אט</th>
                                    <td>
                                        <input type="range" 
                                               name="<?php echo $this->option_name; ?>[chat_width]" 
                                               value="<?php echo esc_attr($settings['chat_width']); ?>"
                                               min="320" max="500" step="10"
                                               class="miri-range"
                                               id="chat-width-range">
                                        <output id="chat-width-output"><?php echo $settings['chat_width']; ?>px</output>
                                        <p class="description">רוחב חלון הצ'אט (320-500 פיקסלים)</p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <!-- Advanced Settings -->
                        <div class="miri-tab-content" data-content="advanced">
                            <h2>🔧 הגדרות מתקדמות</h2>
                            
                            <table class="form-table">
                                <tr>
                                    <th>הצג בעמודים</th>
                                    <td>
                                        <select name="<?php echo $this->option_name; ?>[show_on_pages]">
                                            <option value="all" <?php selected($settings['show_on_pages'], 'all'); ?>>כל העמודים</option>
                                            <option value="exclude" <?php selected($settings['show_on_pages'], 'exclude'); ?>>כל העמודים חוץ מ...</option>
                                            <option value="homepage" <?php selected($settings['show_on_pages'], 'homepage'); ?>>דף הבית בלבד</option>
                                        </select>
                                    </td>
                                </tr>
                                
                                <tr class="excluded-pages-row" style="<?php echo $settings['show_on_pages'] === 'exclude' ? '' : 'display:none;'; ?>">
                                    <th>עמודים לא לכלול</th>
                                    <td>
                                        <textarea name="<?php echo $this->option_name; ?>[excluded_pages]" 
                                                  class="large-text" 
                                                  rows="3"
                                                  placeholder="מזהי עמודים מופרדים בפסיקים, לדוגמה: 5,12,34"><?php echo esc_textarea($settings['excluded_pages']); ?></textarea>
                                        <p class="description">הזן מזהי עמודים (IDs) מופרדים בפסיקים</p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        
                        <!-- Save Button -->
                        <div class="miri-save-section">
                            <?php submit_button('💾 שמור את כל השינויים', 'primary large'); ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <style>
        .miri-admin-wrap {
            margin: 20px 20px 20px 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, sans-serif;
        }
        .miri-admin-wrap h1 {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
        }
        .miri-admin-container {
            display: flex;
            gap: 20px;
            margin-top: 20px;
        }
        .miri-admin-sidebar {
            width: 250px;
            flex-shrink: 0;
        }
        .miri-tabs {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .miri-tab {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 15px 20px;
            border: none;
            background: #fff;
            text-align: right;
            cursor: pointer;
            transition: all 0.2s;
            border-bottom: 1px solid #f0f0f0;
            font-size: 14px;
        }
        .miri-tab:last-child {
            border-bottom: none;
        }
        .miri-tab:hover {
            background: #f9f9f9;
        }
        .miri-tab.active {
            background: linear-gradient(135deg, #7C3AED 0%, #06B6D4 100%);
            color: #fff;
        }
        .miri-tab.active .dashicons {
            color: #fff;
        }
        .miri-admin-content {
            flex: 1;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .miri-tab-content {
            display: none;
        }
        .miri-tab-content.active {
            display: block;
            animation: fadeIn 0.3s;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .miri-tab-content h2 {
            margin-top: 0;
            padding-bottom: 15px;
            border-bottom: 2px solid #7C3AED;
            color: #333;
        }
        .form-table th {
            width: 200px;
            font-weight: 600;
            color: #333;
        }
        .form-table td {
            padding: 20px 10px;
        }
        .miri-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }
        .miri-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .miri-slider {
            position: absolute;
            cursor: pointer;
            inset: 0;
            background-color: #ccc;
            transition: 0.3s;
            border-radius: 24px;
        }
        .miri-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            right: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.3s;
            border-radius: 50%;
        }
        input:checked + .miri-slider {
            background: linear-gradient(135deg, #7C3AED 0%, #06B6D4 100%);
        }
        input:checked + .miri-slider:before {
            transform: translateX(-26px);
        }
        .miri-range {
            width: 300px;
            margin-left: 10px;
        }
        output {
            display: inline-block;
            min-width: 50px;
            padding: 4px 8px;
            background: #f0f0f0;
            border-radius: 4px;
            font-weight: 600;
        }
        .miri-save-section {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #f0f0f0;
        }
        .miri-save-section .button-primary {
            background: linear-gradient(135deg, #7C3AED 0%, #06B6D4 100%);
            border: none;
            text-shadow: none;
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
            padding: 12px 30px;
            height: auto;
            font-size: 16px;
        }
        .miri-save-section .button-primary:hover {
            background: linear-gradient(135deg, #6D2ED5 0%, #0596B5 100%);
        }
        </style>
        
        <script>
        (function($) {
            if (!$ || !$.fn) return;
            
            $(document).ready(function() {
                // Tab switching
                $('.miri-tab').on('click', function() {
                    var tab = $(this).data('tab');
                    $('.miri-tab').removeClass('active');
                    $(this).addClass('active');
                    $('.miri-tab-content').removeClass('active');
                    $('.miri-tab-content[data-content="' + tab + '"]').addClass('active');
                });
                
                // Color pickers
                if ($.fn.wpColorPicker) {
                    $('.miri-color-picker').wpColorPicker();
                }
                
                // Range sliders
                $('#button-size-range').on('input', function() {
                    $('#button-size-output').text($(this).val() + 'px');
                });
                $('#chat-width-range').on('input', function() {
                    $('#chat-width-output').text($(this).val() + 'px');
                });
                
                // Show/hide excluded pages
                $('select[name="<?php echo $this->option_name; ?>[show_on_pages]"]').on('change', function() {
                    if ($(this).val() === 'exclude') {
                        $('.excluded-pages-row').show();
                    } else {
                        $('.excluded-pages-row').hide();
                    }
                });
                
                // Logo upload
                $('#miri-upload-logo').on('click', function(e) {
                    e.preventDefault();
                    var mediaUploader = wp.media({
                        title: 'בחר לוגו',
                        button: { text: 'השתמש בתמונה זו' },
                        multiple: false
                    });
                    mediaUploader.on('select', function() {
                        var attachment = mediaUploader.state().get('selection').first().toJSON();
                        $('#miri-logo-url').val(attachment.url);
                        if ($('.miri-logo-preview').length) {
                            $('.miri-logo-preview').attr('src', attachment.url);
                        } else {
                            $('.miri-logo-upload').prepend('<img src="' + attachment.url + '" class="miri-logo-preview" style="max-width:80px;display:block;margin-bottom:10px;">');
                        }
                        if (!$('#miri-remove-logo').length) {
                            $('#miri-upload-logo').after('<button type="button" class="button" id="miri-remove-logo">הסר לוגו</button>');
                        }
                    });
                    mediaUploader.open();
                });
                
                // Remove logo
                $(document).on('click', '#miri-remove-logo', function() {
                    $('#miri-logo-url').val('');
                    $('.miri-logo-preview').remove();
                    $(this).remove();
                });
            });
        })(jQuery);
        </script>
        <?php
    }
    
    /**
     * Check if widget should be displayed
     */
    private function should_display_widget($settings) {
        if (!$settings['enabled']) {
            return false;
        }
        
        if (!$settings['mobile_enabled'] && wp_is_mobile()) {
            return false;
        }
        
        $show_on = $settings['show_on_pages'];
        
        if ($show_on === 'homepage' && !is_front_page()) {
            return false;
        }
        
        if ($show_on === 'exclude' && !empty($settings['excluded_pages'])) {
            $excluded = array_map('trim', explode(',', $settings['excluded_pages']));
            $current_id = get_the_ID();
            if (in_array($current_id, $excluded)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Inject chat widget
     */
    public function inject_chat_widget() {
        $settings = wp_parse_args(get_option($this->option_name, array()), $this->get_defaults());
        
        if (!$this->should_display_widget($settings)) {
            return;
        }
        
        $webhook_url = esc_js($settings['webhook_url']);
        $position = $settings['button_position'];
        $button_size = intval($settings['button_size']);
        $chat_width = intval($settings['chat_width']);
        
        // Position CSS
        $position_css = '';
        switch ($position) {
            case 'bottom-right':
                $position_css = 'right:22px;bottom:22px;';
                $chat_pos_css = 'right:22px;bottom:' . ($button_size + 30) . 'px;';
                break;
            case 'bottom-left':
                $position_css = 'left:22px;bottom:22px;';
                $chat_pos_css = 'left:22px;bottom:' . ($button_size + 30) . 'px;';
                break;
            case 'top-right':
                $position_css = 'right:22px;top:22px;';
                $chat_pos_css = 'right:22px;top:' . ($button_size + 30) . 'px;';
                break;
            case 'top-left':
                $position_css = 'left:22px;top:22px;';
                $chat_pos_css = 'left:22px;top:' . ($button_size + 30) . 'px;';
                break;
        }
        
        ?>
<!-- Miri Chat Widget Pro -->
<style>
:root{
  --accent:<?php echo $settings['color_primary']; ?>;
  --accent-2:<?php echo $settings['color_secondary']; ?>;
  --bg-1:<?php echo $settings['color_bg']; ?>;
  --glass-bg:rgba(255,255,255,.06);
  --glass-border:rgba(255,255,255,.15);
  --text-1:#ffffff;
  --text-2:#d1e0ff;
  --success:#22c55e;
  --danger:#ef4444;
  --shadow:0 10px 30px rgba(0,0,0,.55);
  --radius:20px;
}
*{box-sizing:border-box}

.miri-launcher{position:fixed;<?php echo $position_css; ?>z-index:9999;background:linear-gradient(135deg,var(--accent),var(--accent-2));border:none;width:<?php echo $button_size; ?>px;height:<?php echo $button_size; ?>px;border-radius:20px;color:#fff;box-shadow:var(--shadow);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:transform .2s ease;outline:none}
.miri-launcher:hover{transform:translateY(-2px)}
.miri-pulse{position:absolute;inset:0;border-radius:inherit;box-shadow:0 0 0 0 rgba(124,77,255,.55);animation:pulse 2s infinite}
@keyframes pulse{0%{box-shadow:0 0 0 0 rgba(124,77,255,.55)}70%{box-shadow:0 0 0 16px rgba(124,77,255,0)}100%{box-shadow:0 0 0 0 rgba(124,77,255,0)}}

.miri-wrap{position:fixed;<?php echo $chat_pos_css; ?>z-index:9998;width:min(92vw,<?php echo $chat_width; ?>px);max-height:80vh;display:none}
.miri-wrap.open{display:block;animation:slideUp .35s cubic-bezier(.2,.8,.2,1)}
@keyframes slideUp{from{transform:translateY(12px);opacity:0}to{transform:translateY(0);opacity:1}}

canvas#miri-net{position:absolute;inset:0;width:100%;height:100%;filter:contrast(120%) saturate(120%);opacity:.26}
.miri-bubble{position:absolute;border-radius:50%;background:radial-gradient(120px 120px at 30% 30%, rgba(255,255,255,.22), rgba(255,255,255,.06) 60%, rgba(255,255,255,0) 70%);filter:blur(1px);animation:float 12s ease-in-out infinite}
.miri-bubble:nth-child(1){width:110px;height:110px;left:10%;top:8%;animation-duration:14s}
.miri-bubble:nth-child(2){width:80px;height:80px;right:12%;top:18%}
.miri-bubble:nth-child(3){width:120px;height:120px;right:35%;bottom:10%;animation-duration:16s}
@keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-14px)}}

.miri-card{position:relative;display:flex;flex-direction:column;height:100%;max-height:80vh;border-radius:var(--radius);overflow:hidden;box-shadow:var(--shadow);background:rgba(10,14,30,.78) padding-box;border:1px solid var(--glass-border)}

.miri-header{position:relative;padding:14px 16px 12px;display:flex;align-items:center;gap:12px;flex-direction:row-reverse;justify-content:space-between;background:linear-gradient(135deg,var(--accent),var(--accent-2));backdrop-filter:blur(10px) saturate(160%);border-bottom:1px solid var(--glass-border); color:#fff; text-shadow:0 1px 0 rgba(0,0,0,.35);text-align:right}
.miri-hgroup{display:flex;flex-direction:column;align-items:flex-end}
.miri-avatar{width:36px;height:36px;border-radius:12px;background:linear-gradient(135deg,var(--accent) 10%,var(--accent-2));display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;box-shadow:inset 0 0 0 1px rgba(255,255,255,.2)}
.miri-title{font-weight:700;letter-spacing:.2px}
.miri-sub{font-size:.85rem;color:var(--text-2);display:flex;align-items:center;gap:8px}
.miri-online{width:8px;height:8px;border-radius:999px;background:var(--success);box-shadow:0 0 12px var(--success)}
.miri-shimmer{position:absolute;inset:0;pointer-events:none;background:linear-gradient(75deg, transparent 0%, rgba(255,255,255,.12) 45%, transparent 55%);transform:translateX(-100%);animation:shimmer 4.6s infinite}
@keyframes shimmer{0%{transform:translateX(-130%)}60%{transform:translateX(130%)}100%{transform:translateX(130%)}}

.miri-feed{position:relative;flex:1;overflow:auto;padding:16px 14px;display:flex;flex-direction:column;gap:10px;background:transparent;scrollbar-width:thin;scrollbar-color:rgba(255,255,255,.35) transparent}
.miri-feed::-webkit-scrollbar{width:10px}
.miri-feed::-webkit-scrollbar-track{background:transparent}
.miri-feed::-webkit-scrollbar-thumb{background:linear-gradient(180deg, rgba(255,255,255,.28), rgba(255,255,255,.12));border-radius:10px;border:2px solid transparent;background-clip:padding-box}

.msg{display:flex;gap:10px;align-items:flex-end;opacity:0;transform:translateY(6px);animation:msgIn .28s ease forwards}
@keyframes msgIn{to{opacity:1;transform:translateY(0)}}
.msg .bubble{max-width:78%;padding:12px 14px;border-radius:14px;background:var(--glass-bg);border:1px solid var(--glass-border);backdrop-filter:blur(10px);color:var(--text-1);line-height:1.6;text-align:right}
.msg.me{justify-content:flex-end}
.msg.me .bubble{background:#ffffff;border-color:#e5e7eb;color:#0b1220}
.ts{font-size:.75rem;color:var(--text-2);margin-top:4px;text-align:left}

.miri-input{display:flex;gap:8px;align-items:center;padding:10px;border-top:1px solid var(--glass-border);background:rgba(10,14,30,.6);backdrop-filter:blur(8px)}
.miri-text{flex:1;min-height:42px;max-height:110px;padding:10px 12px;border-radius:12px;border:1px solid var(--glass-border);background:rgba(255,255,255,.08);color:var(--text-1);outline:none;resize:vertical;caret-color:#fff;text-align:right;font-family:inherit}
.miri-text::placeholder{color:rgba(255,255,255,.7)}
.miri-send{border:none;border-radius:12px;height:42px;padding:0 14px;display:inline-flex;align-items:center;gap:8px;cursor:pointer;color:#0b1220;font-weight:700;background:#fff;box-shadow:var(--shadow)}
.miri-send:disabled{opacity:.6;cursor:not-allowed}

.loader{display:inline-grid;gap:4px;grid-auto-flow:column;align-items:center}
.loader span{width:6px;height:6px;border-radius:999px;background:rgba(255,255,255,.9);animation:bounce 1s infinite}
.loader span:nth-child(2){animation-delay:.15s}
.loader span:nth-child(3){animation-delay:.3s}
@keyframes bounce{0%,80%,100%{transform:translateY(0);opacity:.5}40%{transform:translateY(-6px);opacity:1}}

@media (max-width:560px){
  .miri-wrap{left:12px;right:12px;width:auto}
  .miri-card{border-radius:16px}
}
</style>

<button class="miri-launcher" id="miri-launch" aria-label="פתח צ'אט">
  <div class="miri-pulse" aria-hidden="true"></div>
  <?php if ($settings['show_logo']): ?>
    <?php if (!empty($settings['custom_logo'])): ?>
      <img src="<?php echo esc_url($settings['custom_logo']); ?>" alt="Logo" style="width:60%;height:60%;object-fit:contain;">
    <?php else: ?>
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" width="28" height="28">
        <path d="M12 2 L14.8 9.2 22 12 14.8 14.8 12 22 9.2 14.8 2 12 9.2 9.2 Z"/>
      </svg>
    <?php endif; ?>
  <?php endif; ?>
</button>

<div class="miri-wrap" id="miri-wrap" role="dialog" aria-labelledby="miri-heading" aria-modal="true" dir="rtl">
  <div class="miri-card">
    <canvas id="miri-net"></canvas>
    <div class="miri-bubble"></div>
    <div class="miri-bubble"></div>
    <div class="miri-bubble"></div>

    <header class="miri-header">
      <?php if ($settings['show_logo']): ?>
      <div class="miri-avatar" aria-hidden="true">
        <?php if (!empty($settings['custom_logo'])): ?>
          <img src="<?php echo esc_url($settings['custom_logo']); ?>" alt="Logo" style="width:70%;height:70%;object-fit:contain;border-radius:8px;">
        <?php else: ?>
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2 L14.8 9.2 22 12 14.8 14.8 12 22 9.2 14.8 2 12 9.2 9.2 Z"/>
          </svg>
        <?php endif; ?>
      </div>
      <?php endif; ?>
      <div class="miri-hgroup">
        <div class="miri-title" id="miri-heading"><?php echo esc_html($settings['title']); ?></div>
        <div class="miri-sub"><span class="miri-online"></span> <?php echo esc_html($settings['subtitle']); ?></div>
      </div>
      <div class="miri-shimmer"></div>
    </header>

    <main class="miri-feed" id="miri-feed" aria-live="polite"></main>

    <form class="miri-input" id="miri-form">
      <textarea id="miri-text" class="miri-text" placeholder="<?php echo esc_attr($settings['placeholder']); ?>" rows="1"></textarea>
      <button class="miri-send" id="miri-send" type="submit">
        <?php echo esc_html($settings['button_text']); ?>
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <line x1="22" y1="2" x2="11" y2="13"></line>
          <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
        </svg>
      </button>
    </form>
  </div>
</div>

<script>
(function(){
  const webhookUrl = "<?php echo $webhook_url; ?>";
  const wrap = document.getElementById('miri-wrap');
  const launch = document.getElementById('miri-launch');
  const feed = document.getElementById('miri-feed');
  const form = document.getElementById('miri-form');
  const input = document.getElementById('miri-text');
  const sendBtn = document.getElementById('miri-send');

  const sessionKey = 'miri_session_id';
  const historyKey = 'miri_chat_history_v1';
  let sessionId = localStorage.getItem(sessionKey) || crypto.randomUUID();
  localStorage.setItem(sessionKey, sessionId);

  const restoreHistory = () => {
    const raw = localStorage.getItem(historyKey);
    if(!raw) return;
    try{
      const msgs = JSON.parse(raw);
      msgs.forEach(({role, text, ts}) => addMsg(role, text, ts, false));
      scrollToBottom();
    }catch{ /* ignore */ }
  }
  const persistPush = (role, text) => {
    const raw = localStorage.getItem(historyKey);
    const arr = raw ? JSON.parse(raw) : [];
    arr.push({role, text, ts: new Date().toISOString()});
    localStorage.setItem(historyKey, JSON.stringify(arr).slice(0, 20000));
  }

  launch.addEventListener('click', () => {
    const opening = !wrap.classList.contains('open');
    wrap.classList.toggle('open');
    if(opening){
      input.focus();
      if(feed.childElementCount === 0){ welcome(); }
    }
  });

  function addMsg(role, text, ts = new Date().toISOString(), persist=true){
    const row = document.createElement('div');
    row.className = 'msg' + (role === 'user' ? ' me' : '');
    const bubble = document.createElement('div');
    bubble.className = 'bubble';
    bubble.innerHTML = text;
    const meta = document.createElement('div');
    meta.className = 'ts';
    meta.textContent = new Date(ts).toLocaleTimeString('he-IL', {hour:'2-digit', minute:'2-digit', hour12:false});
    row.appendChild(bubble); row.appendChild(meta);
    feed.appendChild(row);
    if(persist) persistPush(role, stripScripts(text));
  }
  function stripScripts(str){ return String(str).replace(/<script[\s\S]*?>[\s\S]*?<\/script>/gi,''); }

  function addLoader(){
    const row = document.createElement('div');
    row.className = 'msg';
    row.dataset.loader = '1';
    const bubble = document.createElement('div');
    bubble.className = 'bubble';
    bubble.innerHTML = '<div class="loader" aria-label="טוען"><span></span><span></span><span></span></div>';
    const meta = document.createElement('div'); meta.className = 'ts'; meta.textContent = '';
    row.appendChild(bubble); row.appendChild(meta); feed.appendChild(row);
    scrollToBottom();
    return row;
  }
  function removeLoader(){
    const el = feed.querySelector('[data-loader="1"]');
    if(el) el.remove();
  }
  function scrollToBottom(){ feed.scrollTo({top:feed.scrollHeight, behavior:'smooth'}); }

  function welcome(){
    addMsg('assistant', "<?php echo esc_js($settings['welcome_msg_1']); ?>");
    addMsg('assistant', "<?php echo esc_js($settings['welcome_msg_2']); ?>");
  }

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const text = input.value.trim();
    if(!text) return;
    
    addMsg('user', escapeHtml(text));
    input.value = '';
    input.style.height = 'auto';
    sendBtn.disabled = true;
    const loader = addLoader();
    
    console.log('📤 Sending to:', webhookUrl);
    console.log('📝 Message:', text);
    
    try{
      const res = await fetch(webhookUrl, {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({
          sessionId,
          chatInput: text,
          metadata: { source: 'miri-chat-widget', url: location.href }
        })
      });
      
      console.log('📥 Response status:', res.status);
      const data = await res.json().catch(()=>({}));
      console.log('📦 Response data:', data);
      
      const botText = resolveBotText(data) || 'קיבלתי!';
      removeLoader();
      addMsg('assistant', botText);
      
    }catch(err){
      console.error('❌ Chat error:', err);
      removeLoader();
      addMsg('assistant', 'מצטערת, יש בעיית חיבור. נסו שוב מאוחר יותר.');
    }finally{ 
      sendBtn.disabled = false; 
      scrollToBottom(); 
    }
  });

  input.addEventListener('input', () => {
    input.style.height = 'auto';
    input.style.height = Math.min(input.scrollHeight, 110) + 'px';
  });
  input.addEventListener('keydown', (e)=>{
    if(e.key === 'Enter' && !e.shiftKey){ e.preventDefault(); form.requestSubmit(); }
  })

  function resolveBotText(data){
    if(!data) return '';
    if(typeof data === 'string') return data;
    if(data.reply) return data.reply;
    if(Array.isArray(data.messages)){
      const last = data.messages.slice().reverse().find(m=>m.role!=='user');
      if(last) return last.text || last.content || '';
    }
    if(data.data && (data.data.text || data.data.answer)) return data.data.text || data.data.answer;
    if(data.output) return data.output;
    return '';
  }
  function escapeHtml(s){
    return s.replace(/[&<>"']/g, (c) => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c]));
  }

  restoreHistory();

  const canvas = document.getElementById('miri-net');
  const ctx = canvas.getContext('2d');
  let dpr = Math.max(1, devicePixelRatio||1);
  let nodes = [];
  function resize(){
    const {clientWidth:w, clientHeight:h} = wrap;
    canvas.width = Math.max(1,w)*dpr; canvas.height = Math.max(1,h)*dpr;
    canvas.style.width = w+'px'; canvas.style.height = h+'px';
    ctx.setTransform(dpr,0,0,dpr,0,0);
    if(nodes.length===0){
      const count = Math.min(60, Math.floor((w*h)/6000));
      nodes = Array.from({length:count}, ()=>({
        x: Math.random()*w,
        y: Math.random()*h,
        vx: (Math.random()-.5)*.6,
        vy: (Math.random()-.5)*.6
      }));
    }
  }
  const dist2 = 130*130;
  function tick(){
    const w = canvas.clientWidth, h = canvas.clientHeight;
    ctx.clearRect(0,0,w,h);
    for(const n of nodes){ n.x += n.vx; n.y += n.vy; if(n.x<0||n.x>w) n.vx*=-1; if(n.y<0||n.y>h) n.vy*=-1; }
    ctx.lineWidth = 1; ctx.strokeStyle = 'rgba(200,220,255,.22)';
    for(let i=0;i<nodes.length;i++){
      const a = nodes[i];
      for(let j=i+1;j<nodes.length;j++){
        const b = nodes[j];
        const dx=a.x-b.x, dy=a.y-b.y; const d=dx*dx+dy*dy;
        if(d<dist2){ const op = 1 - d/dist2; ctx.globalAlpha = op*0.6; ctx.beginPath(); ctx.moveTo(a.x,a.y); ctx.lineTo(b.x,b.y); ctx.stroke(); }
      }
    }
    ctx.globalAlpha = 1;
    for(const n of nodes){ ctx.beginPath(); ctx.arc(n.x,n.y,1.6,0,Math.PI*2); ctx.fillStyle='rgba(255,255,255,.6)'; ctx.fill(); }
    requestAnimationFrame(tick);
  }
  const ro = new ResizeObserver(()=>resize()); ro.observe(wrap);
  resize(); requestAnimationFrame(tick);
})();
</script>
        <?php
    }
}

// Initialize
new Miri_Chat_Widget_Pro();
