<?php
if (!defined('ABSPATH')) {
    exit;
}

?>
<div class="wrap miri-admin-wrap" dir="rtl">
    <h1>
        <span class="dashicons dashicons-format-chat" style="font-size:32px;color:#7C3AED;"></span>
        Miri Chat Widget Pro
        <span class="miri-version">גרסה <?php echo esc_html($this->version); ?></span>
    </h1>

    <form method="post" action="options.php" id="miri-settings-form" data-option="<?php echo esc_attr($this->option_name); ?>">
        <?php settings_fields('miri_chat_pro_options'); ?>

        <div class="miri-admin-container">
            <aside class="miri-admin-sidebar">
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
                        טקסטים ותוכן
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

                <div class="miri-sidebar-card">
                    <h3>כלים מהירים</h3>
                    <p>בדוק חיבור או אפס להגדרות ברירת המחדל.</p>
                    <button type="button" class="button button-secondary" id="miri-test-webhook">🔌 בדיקת חיבור</button>
                    <div id="miri-webhook-status" class="miri-status"></div>
                    <button type="button" class="button button-link-delete" id="miri-reset-settings">↩️ אפס הגדרות</button>
                </div>
            </aside>

            <section class="miri-admin-content">
                <div class="miri-tab-content active" data-content="basic">
                    <h2>⚙️ הגדרות בסיסיות</h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">סטטוס הצ'אט</th>
                            <td>
                                <label class="miri-switch">
                                    <input type="checkbox" name="<?php echo esc_attr($this->option_name); ?>[enabled]" value="1" <?php checked($settings['enabled'], true); ?>>
                                    <span class="miri-switch-slider"></span>
                                </label>
                                <span class="description">הפעל או כבה את הווידג'ט באתר</span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">כתובת Webhook</th>
                            <td>
                                <div class="miri-field-group">
                                    <input type="url" class="regular-text" id="miri-webhook-url" name="<?php echo esc_attr($this->option_name); ?>[webhook_url]" value="<?php echo esc_attr($settings['webhook_url']); ?>" placeholder="https://...">
                                </div>
                                <p class="description">קישור לשרת ה-AI שלך לקבלת הודעות</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">הצג במובייל</th>
                            <td>
                                <label class="miri-switch">
                                    <input type="checkbox" name="<?php echo esc_attr($this->option_name); ?>[mobile_enabled]" value="1" <?php checked($settings['mobile_enabled'], true); ?>>
                                    <span class="miri-switch-slider"></span>
                                </label>
                                <span class="description">כאשר כבוי – הווידג'ט יוסתר במכשירים ניידים</span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">צליל התראה</th>
                            <td>
                                <label class="miri-switch">
                                    <input type="checkbox" name="<?php echo esc_attr($this->option_name); ?>[sound_enabled]" value="1" <?php checked($settings['sound_enabled'], true); ?>>
                                    <span class="miri-switch-slider"></span>
                                </label>
                                <span class="description">נגן צליל כאשר מתקבלת תשובה חדשה</span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">אינדיקציית הקלדה</th>
                            <td>
                                <label class="miri-switch">
                                    <input type="checkbox" name="<?php echo esc_attr($this->option_name); ?>[typing_indicator]" value="1" <?php checked($settings['typing_indicator'], true); ?>>
                                    <span class="miri-switch-slider"></span>
                                </label>
                                <span class="description">הצג אנימציית "מירי מקלידה" בזמן עיבוד</span>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="miri-tab-content" data-content="design">
                    <h2>🎨 עיצוב וצבעים</h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">בחר סקין</th>
                            <td>
                                <div class="skin-selector">
                                    <?php
                                    $skins = array(
                                        'cosmic' => array(
                                            'label' => '✨ Cosmic',
                                            'description' => 'עיצוב כהה עם גרדיאנטים דינמיים'
                                        ),
                                        'whatsapp' => array(
                                            'label' => '💚 WhatsApp',
                                            'description' => 'מראה מוכר בסגנון וואטסאפ'
                                        ),
                                        'light' => array(
                                            'label' => '🤍 Modern Light',
                                            'description' => 'עיצוב בהיר ועסקי'
                                        ),
                                        'business' => array(
                                            'label' => '💼 Business',
                                            'description' => 'כחול עמוק וקווי עיצוב נקיים'
                                        ),
                                    );
                                    foreach ($skins as $skin => $data) :
                                        $is_active = $settings['skin'] === $skin;
                                        ?>
                                        <label class="skin-option <?php echo $is_active ? 'active' : ''; ?>">
                                            <input type="radio" name="<?php echo esc_attr($this->option_name); ?>[skin]" value="<?php echo esc_attr($skin); ?>" <?php checked($is_active); ?>>
                                            <span class="skin-name"><?php echo esc_html($data['label']); ?></span>
                                            <span class="skin-desc"><?php echo esc_html($data['description']); ?></span>
                                            <span class="skin-badge">בחר</span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                                <p class="description">ניתן להתאים צבעים כאשר בוחרים סקין "Cosmic" או "Business"</p>
                            </td>
                        </tr>
                        <tr class="color-settings" <?php echo in_array($settings['skin'], array('cosmic', 'business')) ? '' : 'style="display:none;"'; ?>>
                            <th scope="row">צבעים מותאמים</th>
                            <td>
                                <div class="miri-color-grid">
                                    <label>
                                        <span>צבע ראשי</span>
                                        <input type="text" class="miri-color-picker" name="<?php echo esc_attr($this->option_name); ?>[color_primary]" value="<?php echo esc_attr($settings['color_primary']); ?>">
                                    </label>
                                    <label>
                                        <span>צבע משני</span>
                                        <input type="text" class="miri-color-picker" name="<?php echo esc_attr($this->option_name); ?>[color_secondary]" value="<?php echo esc_attr($settings['color_secondary']); ?>">
                                    </label>
                                    <label>
                                        <span>רקע חלון</span>
                                        <input type="text" class="miri-color-picker" name="<?php echo esc_attr($this->option_name); ?>[color_bg]" value="<?php echo esc_attr($settings['color_bg']); ?>">
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">לוגו מותאם</th>
                            <td>
                                <div class="miri-logo-upload">
                                    <?php if (!empty($settings['custom_logo'])) : ?>
                                        <img src="<?php echo esc_url($settings['custom_logo']); ?>" alt="לוגו" class="miri-logo-preview">
                                    <?php endif; ?>
                                    <input type="url" id="miri-logo-url" name="<?php echo esc_attr($this->option_name); ?>[custom_logo]" value="<?php echo esc_attr($settings['custom_logo']); ?>" class="regular-text" placeholder="https://">
                                    <button type="button" class="button" id="miri-upload-logo">בחר לוגו</button>
                                    <?php if (!empty($settings['custom_logo'])) : ?>
                                        <button type="button" class="button" id="miri-remove-logo">הסר לוגו</button>
                                    <?php endif; ?>
                                </div>
                                <label class="miri-switch inline">
                                    <input type="checkbox" name="<?php echo esc_attr($this->option_name); ?>[show_logo]" value="1" <?php checked($settings['show_logo'], true); ?>>
                                    <span class="miri-switch-slider"></span>
                                    <span class="miri-switch-label">הצג את הלוגו בחלון</span>
                                </label>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="miri-tab-content" data-content="texts">
                    <h2>📝 טקסטים והודעות</h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">כותרת ראשית</th>
                            <td>
                                <input type="text" class="regular-text" name="<?php echo esc_attr($this->option_name); ?>[title]" value="<?php echo esc_attr($settings['title']); ?>">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">כותרת משנה</th>
                            <td>
                                <input type="text" class="regular-text" name="<?php echo esc_attr($this->option_name); ?>[subtitle]" value="<?php echo esc_attr($settings['subtitle']); ?>">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">הודעת פתיחה 1</th>
                            <td>
                                <input type="text" class="regular-text" name="<?php echo esc_attr($this->option_name); ?>[welcome_msg_1]" value="<?php echo esc_attr($settings['welcome_msg_1']); ?>">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">הודעת פתיחה 2</th>
                            <td>
                                <textarea class="large-text" rows="2" name="<?php echo esc_attr($this->option_name); ?>[welcome_msg_2]"><?php echo esc_textarea($settings['welcome_msg_2']); ?></textarea>
                                <p class="description">ניתן להשתמש בתגיות HTML בסיסיות להדגשה</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">טקסט תיבת ההקלדה</th>
                            <td>
                                <input type="text" class="regular-text" name="<?php echo esc_attr($this->option_name); ?>[placeholder]" value="<?php echo esc_attr($settings['placeholder']); ?>">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">טקסט כפתור שליחה</th>
                            <td>
                                <input type="text" class="regular-text" name="<?php echo esc_attr($this->option_name); ?>[button_text]" value="<?php echo esc_attr($settings['button_text']); ?>">
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="miri-tab-content" data-content="position">
                    <h2>📐 מיקום וגודל</h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">מיקום הכפתור</th>
                            <td>
                                <select name="<?php echo esc_attr($this->option_name); ?>[button_position]">
                                    <option value="bottom-right" <?php selected($settings['button_position'], 'bottom-right'); ?>>למטה מימין</option>
                                    <option value="bottom-left" <?php selected($settings['button_position'], 'bottom-left'); ?>>למטה משמאל</option>
                                    <option value="top-right" <?php selected($settings['button_position'], 'top-right'); ?>>למעלה מימין</option>
                                    <option value="top-left" <?php selected($settings['button_position'], 'top-left'); ?>>למעלה משמאל</option>
                                </select>
                                <p class="description">בחר היכן יוצג כפתור ההפעלה של מירי</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">גודל הכפתור</th>
                            <td>
                                <div class="miri-range-field">
                                    <input type="range" id="button-size-range" name="<?php echo esc_attr($this->option_name); ?>[button_size]" value="<?php echo esc_attr($settings['button_size']); ?>" min="48" max="80" step="2">
                                    <output for="button-size-range" id="button-size-output"><?php echo intval($settings['button_size']); ?>px</output>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">רוחב חלון</th>
                            <td>
                                <div class="miri-range-field">
                                    <input type="range" id="chat-width-range" name="<?php echo esc_attr($this->option_name); ?>[chat_width]" value="<?php echo esc_attr($settings['chat_width']); ?>" min="320" max="520" step="10">
                                    <output for="chat-width-range" id="chat-width-output"><?php echo intval($settings['chat_width']); ?>px</output>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">גובה חלון</th>
                            <td>
                                <div class="miri-range-field">
                                    <input type="range" id="chat-height-range" name="<?php echo esc_attr($this->option_name); ?>[chat_height]" value="<?php echo esc_attr($settings['chat_height']); ?>" min="420" max="720" step="10">
                                    <output for="chat-height-range" id="chat-height-output"><?php echo intval($settings['chat_height']); ?>px</output>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="miri-tab-content" data-content="advanced">
                    <h2>🔧 הגדרות מתקדמות</h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row">הצג בעמודים</th>
                            <td>
                                <select name="<?php echo esc_attr($this->option_name); ?>[show_on_pages]" id="miri-show-on">
                                    <option value="all" <?php selected($settings['show_on_pages'], 'all'); ?>>כל העמודים</option>
                                    <option value="exclude" <?php selected($settings['show_on_pages'], 'exclude'); ?>>כל העמודים חוץ מ...</option>
                                    <option value="homepage" <?php selected($settings['show_on_pages'], 'homepage'); ?>>דף הבית בלבד</option>
                                </select>
                            </td>
                        </tr>
                        <tr class="excluded-pages-row" <?php echo $settings['show_on_pages'] === 'exclude' ? '' : 'style="display:none;"'; ?>>
                            <th scope="row">עמודים לא לכלול</th>
                            <td>
                                <textarea class="large-text" rows="3" name="<?php echo esc_attr($this->option_name); ?>[excluded_pages]" placeholder="לדוגמה: 12,45,128"><?php echo esc_textarea($settings['excluded_pages']); ?></textarea>
                                <p class="description">הזינו מזהי עמודים (ID) מופרדים בפסיקים</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">מספר הודעות היסטוריה</th>
                            <td>
                                <input type="number" min="10" max="200" step="5" name="<?php echo esc_attr($this->option_name); ?>[max_history_messages]" value="<?php echo esc_attr($settings['max_history_messages']); ?>">
                                <p class="description">כמה הודעות אחרונות נשמרות בדפדפן (מומלץ 50)</p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="miri-save-section">
                    <?php submit_button('💾 שמור את כל השינויים', 'primary large'); ?>
                </div>
            </section>
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
.miri-version {
    font-size: 14px;
    color: #666;
    font-weight: normal;
}
.miri-admin-container {
    display: flex;
    gap: 20px;
    align-items: flex-start;
}
.miri-admin-sidebar {
    width: 250px;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    gap: 20px;
}
.miri-tabs {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
}
.miri-tab {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    padding: 16px 20px;
    border: none;
    background: #fff;
    text-align: right;
    cursor: pointer;
    transition: all 0.2s;
    border-bottom: 1px solid #f0f0f0;
    font-size: 15px;
}
.miri-tab:last-child {
    border-bottom: none;
}
.miri-tab.active {
    background: linear-gradient(135deg, #7C3AED, #4F46E5);
    color: #fff;
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.3);
}
.miri-tab:not(.active):hover {
    background: #f9fafb;
}
.miri-admin-content {
    flex: 1;
    background: #fff;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.1);
    border: 1px solid rgba(148,163,184,0.2);
}
.miri-tab-content {
    display: none;
    animation: fadeIn 0.2s ease;
}
.miri-tab-content.active {
    display: block;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}
.miri-switch {
    position: relative;
    display: inline-flex;
    width: 54px;
    height: 28px;
}
.miri-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.miri-switch-slider {
    position: absolute;
    cursor: pointer;
    inset: 0;
    background: #d1d5db;
    transition: .2s;
    border-radius: 999px;
}
.miri-switch-slider:before {
    position: absolute;
    content: "";
    height: 22px;
    width: 22px;
    left: 3px;
    bottom: 3px;
    background: #fff;
    transition: .2s;
    border-radius: 50%;
    box-shadow: 0 2px 6px rgba(15,23,42,0.2);
}
.miri-switch input:checked + .miri-switch-slider {
    background: linear-gradient(135deg, #7C3AED, #06B6D4);
}
.miri-switch input:checked + .miri-switch-slider:before {
    transform: translateX(26px);
}
.miri-switch.inline {
    margin-top: 12px;
    align-items: center;
    gap: 10px;
}
.miri-switch-label {
    margin-right: 12px;
    font-weight: 500;
}
.miri-field-group {
    display: flex;
    gap: 10px;
    align-items: center;
}
.skin-selector {
    display: grid;
    gap: 12px;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
}
.skin-option {
    position: relative;
    padding: 18px;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
    display: flex;
    flex-direction: column;
    gap: 8px;
    background: #f8fafc;
}
.skin-option input {
    position: absolute;
    opacity: 0;
}
.skin-option .skin-name {
    font-weight: 700;
    font-size: 15px;
    color: #1e293b;
}
.skin-option .skin-desc {
    font-size: 13px;
    color: #64748b;
}
.skin-option .skin-badge {
    margin-top: auto;
    align-self: flex-start;
    padding: 4px 10px;
    border-radius: 999px;
    background: rgba(124,58,237,0.12);
    color: #5b21b6;
    font-size: 12px;
    font-weight: 600;
}
.skin-option.active {
    transform: translateY(-2px);
    box-shadow: 0 12px 30px rgba(79,70,229,0.15);
    border-color: rgba(79,70,229,0.6);
}
.skin-option.active .skin-badge {
    background: linear-gradient(135deg, #7C3AED, #06B6D4);
    color: #fff;
}
.miri-color-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 16px;
    align-items: flex-end;
}
.miri-color-grid label {
    display: flex;
    flex-direction: column;
    gap: 6px;
    font-weight: 500;
    color: #1e293b;
}
.miri-logo-upload {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
}
.miri-logo-preview {
    max-height: 60px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(15,23,42,0.15);
}
.miri-range-field {
    display: flex;
    align-items: center;
    gap: 12px;
}
.miri-range-field output {
    min-width: 60px;
    text-align: center;
    font-weight: 600;
}
.miri-save-section {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
}
.miri-sidebar-card {
    background: #fff;
    border: 1px solid rgba(148,163,184,0.25);
    border-radius: 12px;
    padding: 18px;
    box-shadow: 0 12px 24px rgba(15,23,42,0.08);
    display: flex;
    flex-direction: column;
    gap: 12px;
}
.miri-status {
    font-size: 13px;
    min-height: 20px;
    color: #1f2937;
}
.miri-status.success {
    color: #16a34a;
}
.miri-status.error {
    color: #dc2626;
}
@media (max-width: 1024px) {
    .miri-admin-container {
        flex-direction: column;
    }
    .miri-admin-sidebar {
        width: 100%;
        flex-direction: row;
        flex-wrap: wrap;
    }
    .miri-tabs {
        flex: 1;
    }
    .miri-admin-content {
        width: 100%;
    }
}
</style>
