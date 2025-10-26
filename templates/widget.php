<?php
if (!defined('ABSPATH')) {
    exit;
}

$chat_width = intval($settings['chat_width']);
$chat_height = intval($settings['chat_height']);
$button_size = intval($settings['button_size']);
$position_button = isset($position_styles['button']) ? $position_styles['button'] : '';
$position_chat = isset($position_styles['chat']) ? $position_styles['chat'] : '';
$skin_css = $this->get_skin_css($settings);
?>
<!-- Miri Chat Widget Pro -->
<style>
:root {
    --accent: <?php echo esc_html($settings['color_primary']); ?>;
    --accent-2: <?php echo esc_html($settings['color_secondary']); ?>;
    --bg-1: <?php echo esc_html($settings['color_bg']); ?>;
    --glass-bg: rgba(255, 255, 255, .06);
    --glass-border: rgba(255, 255, 255, .15);
    --text-1: #ffffff;
    --text-2: #d1e0ff;
    --success: #22c55e;
    --danger: #ef4444;
    --shadow: 0 10px 30px rgba(0, 0, 0, .55);
    --radius: 20px;
    --chat-height: <?php echo $chat_height; ?>px;
}
* {
    box-sizing: border-box;
}
.miri-launcher {
    position: fixed;
    <?php echo esc_html($position_button); ?>
    z-index: 9999;
    background: linear-gradient(135deg, var(--accent), var(--accent-2));
    border: none;
    width: <?php echo $button_size; ?>px;
    height: <?php echo $button_size; ?>px;
    border-radius: 20px;
    color: #fff;
    box-shadow: var(--shadow);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform .2s ease;
    outline: none;
    padding: 0;
}
.miri-launcher:hover {
    transform: translateY(-2px);
}
.miri-pulse {
    position: absolute;
    inset: 0;
    border-radius: inherit;
    box-shadow: 0 0 0 0 rgba(124, 58, 237, .55);
    animation: miri-pulse 2s infinite;
}
@keyframes miri-pulse {
    0% { box-shadow: 0 0 0 0 rgba(124, 58, 237, .55); }
    70% { box-shadow: 0 0 0 16px rgba(124, 58, 237, 0); }
    100% { box-shadow: 0 0 0 0 rgba(124, 58, 237, 0); }
}
.miri-wrap {
    position: fixed;
    <?php echo esc_html($position_chat); ?>
    z-index: 9998;
    width: min(92vw, <?php echo $chat_width; ?>px);
    max-height: min(80vh, var(--chat-height));
    display: none;
}
.miri-wrap.open {
    display: block;
    animation: miri-slide-up .35s cubic-bezier(.2, .8, .2, 1);
}
@keyframes miri-slide-up {
    from { transform: translateY(12px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
canvas#miri-net {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    filter: contrast(120%) saturate(120%);
    opacity: .26;
}
.miri-bubble {
    position: absolute;
    border-radius: 50%;
    background: radial-gradient(120px 120px at 30% 30%, rgba(255, 255, 255, .22), rgba(255, 255, 255, .06) 60%, rgba(255, 255, 255, 0) 70%);
    filter: blur(1px);
    animation: miri-float 12s ease-in-out infinite;
}
.miri-bubble:nth-child(1) {
    width: 110px;
    height: 110px;
    left: 10%;
    top: 8%;
    animation-duration: 14s;
}
.miri-bubble:nth-child(2) {
    width: 80px;
    height: 80px;
    right: 12%;
    top: 18%;
}
.miri-bubble:nth-child(3) {
    width: 120px;
    height: 120px;
    right: 35%;
    bottom: 10%;
    animation-duration: 16s;
}
@keyframes miri-float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-14px); }
}
.miri-card {
    position: relative;
    display: flex;
    flex-direction: column;
    height: 100%;
    max-height: min(80vh, var(--chat-height));
    border-radius: var(--radius);
    overflow: hidden;
    box-shadow: var(--shadow);
    background: rgba(10, 14, 30, .95) padding-box;
    border: 1px solid var(--glass-border);
    backdrop-filter: blur(20px);
}
.miri-header {
    position: relative;
    padding: 12px 14px 10px;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-direction: row-reverse;
    justify-content: space-between;
    background: linear-gradient(135deg, var(--accent), var(--accent-2));
    backdrop-filter: blur(10px) saturate(160%);
    border-bottom: 1px solid var(--glass-border);
    color: #fff;
    text-shadow: 0 1px 0 rgba(0, 0, 0, .35);
    text-align: right;
}
.miri-hgroup {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    padding-left: 12px;
    padding-right: 12px;
}
.miri-avatar {
    width: 36px;
    height: 36px;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--accent) 10%, var(--accent-2));
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 700;
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .2);
}
.miri-title {
    font-weight: 700;
    letter-spacing: .2px;
}
.miri-sub {
    font-size: .85rem;
    color: var(--text-2);
    display: flex;
    align-items: center;
    gap: 8px;
}
.miri-online {
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: var(--success);
    box-shadow: 0 0 12px var(--success);
}
.miri-shimmer {
    position: absolute;
    inset: 0;
    pointer-events: none;
    background: linear-gradient(75deg, transparent 0%, rgba(255, 255, 255, .12) 45%, transparent 55%);
    transform: translateX(-100%);
    animation: shimmer 4.6s infinite;
}
@keyframes shimmer {
    0% { transform: translateX(-130%); }
    60%, 100% { transform: translateX(130%); }
}
.miri-feed {
    position: relative;
    flex: 1;
    overflow: auto;
    padding: 16px 14px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    background: transparent;
    scrollbar-width: thin;
    scrollbar-color: rgba(255, 255, 255, .35) transparent;
}
.miri-feed::-webkit-scrollbar {
    width: 10px;
}
.miri-feed::-webkit-scrollbar-track {
    background: transparent;
}
.miri-feed::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, rgba(255, 255, 255, .28), rgba(255, 255, 255, .12));
    border-radius: 10px;
    border: 2px solid transparent;
    background-clip: padding-box;
}
.msg {
    display: flex;
    gap: 10px;
    align-items: flex-end;
    opacity: 0;
    transform: translateY(6px);
    animation: msgIn .28s ease forwards;
}
@keyframes msgIn {
    to { opacity: 1; transform: translateY(0); }
}
.msg .bubble {
    max-width: 78%;
    padding: 12px 14px;
    border-radius: 14px;
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    backdrop-filter: blur(10px);
    color: var(--text-1);
    line-height: 1.6;
    text-align: right;
}
.msg.me {
    justify-content: flex-end;
}
.msg.me .bubble {
    background: #ffffff;
    border-color: #e5e7eb;
    color: #0b1220;
}
.ts {
    font-size: .75rem;
    color: var(--text-2);
    margin-top: 4px;
    text-align: left;
}
.miri-input {
    display: flex;
    gap: 8px;
    align-items: center;
    padding: 10px;
    border-top: 1px solid var(--glass-border);
    background: rgba(10, 14, 30, .6);
    backdrop-filter: blur(8px);
}
.miri-text {
    flex: 1;
    min-height: 42px;
    max-height: 110px;
    padding: 10px 12px;
    border-radius: 12px;
    border: 1px solid var(--glass-border);
    background: rgba(255, 255, 255, .08);
    color: var(--text-1);
    outline: none;
    resize: vertical;
    caret-color: #fff;
    text-align: right;
    font-family: inherit;
}
.miri-text::placeholder {
    color: rgba(255, 255, 255, .7);
}
.miri-send {
    border: none;
    border-radius: 12px;
    height: 42px;
    padding: 0 14px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    color: #0b1220;
    font-weight: 700;
    background: #fff;
    box-shadow: var(--shadow);
}
.miri-send:disabled {
    opacity: .6;
    cursor: not-allowed;
}
.loader {
    display: inline-grid;
    gap: 4px;
    grid-auto-flow: column;
    align-items: center;
}
.loader span {
    width: 6px;
    height: 6px;
    border-radius: 999px;
    background: rgba(255, 255, 255, .9);
    animation: bounce 1s infinite;
}
.loader span:nth-child(2) {
    animation-delay: .15s;
}
.loader span:nth-child(3) {
    animation-delay: .3s;
}
@keyframes bounce {
    0%, 80%, 100% { transform: translateY(0); opacity: .5; }
    40% { transform: translateY(-6px); opacity: 1; }
}
@media (max-width: 560px) {
    .miri-wrap {
        left: 12px !important;
        right: 12px !important;
        width: auto;
    }
    .miri-card {
        border-radius: 16px;
    }
}
<?php echo $skin_css; ?>
</style>

<button class="miri-launcher" id="miri-launch" aria-label="פתח צ'אט">
    <div class="miri-pulse" aria-hidden="true"></div>
    <?php if ($settings['show_logo']) : ?>
        <?php if (!empty($settings['custom_logo'])) : ?>
            <img src="<?php echo esc_url($settings['custom_logo']); ?>" alt="Logo" style="width:60%;height:60%;object-fit:contain;">
        <?php else : ?>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" width="28" height="28" aria-hidden="true">
                <path d="M12 2 L14.8 9.2 22 12 14.8 14.8 12 22 9.2 14.8 2 12 9.2 9.2 Z" />
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
            <?php if ($settings['show_logo']) : ?>
            <div class="miri-avatar" aria-hidden="true">
                <?php if (!empty($settings['custom_logo'])) : ?>
                    <img src="<?php echo esc_url($settings['custom_logo']); ?>" alt="Logo" style="width:70%;height:70%;object-fit:contain;border-radius:8px;">
                <?php else : ?>
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2 L14.8 9.2 22 12 14.8 14.8 12 22 9.2 14.8 2 12 9.2 9.2 Z" />
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
