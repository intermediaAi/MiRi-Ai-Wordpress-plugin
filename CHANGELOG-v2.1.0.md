# Changelog - Miri Chat Widget Pro

## [2.1.0] - 2025-10-25

### 🐛 תיקוני באגים קריטיים
- **[FIXED]** תיקון Ajax handlers חסרים - `handle_logo_upload`, `handle_test_webhook`, `handle_reset_settings`
- **[FIXED]** תיקון ניהול Session ID - סנכרון מלא בין לקוח לשרת
- **[FIXED]** תיקון פונקציית `extractBotResponse` - תמיכה בפורמטים מרובים של תגובות
- **[FIXED]** תיקון בעיות RTL - יישור נכון בעברית
- **[FIXED]** תיקון העלאת לוגו - עובד 100% כעת
- **[FIXED]** תיקון nonces חסרים - אבטחה משופרת
- **[FIXED]** תיקון זליגת זיכרון באפקטים ויזואליים
- **[FIXED]** תיקון auto-resize של textarea
- **[FIXED]** תיקון scrolling בהיסטוריה ארוכה

### ✨ תכונות חדשות
- **[NEW]** אינדיקטור הקלדה (typing indicator)
- **[NEW]** התראות קוליות למסרים חדשים
- **[NEW]** בדיקת חיבור Webhook מהממשק
- **[NEW]** API חיצוני לשליטה בצ'אט (`window.MiriChat`)
- **[NEW]** מנגנון reconnect אוטומטי בכשלון
- **[NEW]** שמירת timestamp למסרים
- **[NEW]** הגדרת גובה חלון הצ'אט
- **[NEW]** הגדרת מספר מסרים מקסימלי בהיסטוריה
- **[NEW]** תמיכה בקיצורי מקלדת (ESC לסגירה, Enter לשליחה)

### 🚀 שיפורים
- **[IMPROVED]** ביצועים - קוד מאופטם ומהיר יותר
- **[IMPROVED]** אבטחה - sanitization ו-escaping מלאים
- **[IMPROVED]** טיפול בשגיאות - הודעות ברורות יותר
- **[IMPROVED]** תצוגה במובייל - התאמה מושלמת
- **[IMPROVED]** אפקטים ויזואליים - חלקים יותר
- **[IMPROVED]** ניהול זיכרון - localStorage אופטימלי
- **[IMPROVED]** נגישות - ARIA labels ו-semantic HTML
- **[IMPROVED]** תיעוד קוד - comments מפורטים
- **[IMPROVED]** ארכיטקטורה - קוד מודולרי ונקי

### 🔧 שינויים טכניים
- **[CHANGED]** מבנה קבצים - הפרדה ל-PHP, JS, CSS נפרדים
- **[CHANGED]** שמות משתנים - קונבנציות אחידות
- **[CHANGED]** פורמט localStorage - גרסה 2 עם metadata
- **[CHANGED]** Session management - UUID במקום timestamp
- **[CHANGED]** Event listeners - delegation pattern
- **[CHANGED]** Animation frame - cleanup נכון

### 📚 תיעוד
- **[DOCS]** README מפורט עם דוגמאות שימוש
- **[DOCS]** הוראות התקנה צעד אחר צעד
- **[DOCS]** מדריך פתרון תקלות
- **[DOCS]** דוגמאות API
- **[DOCS]** Comments בקוד

### 🔒 אבטחה
- **[SECURITY]** WordPress nonces לכל Ajax requests
- **[SECURITY]** Sanitization של כל inputs
- **[SECURITY]** Escaping של כל outputs
- **[SECURITY]** הגנה מפני XSS
- **[SECURITY]** הגנה מפני CSRF
- **[SECURITY]** בדיקת הרשאות משתמש
- **[SECURITY]** Strip scripts מהיסטוריה

---

## [2.0.0] - הגרסה המקורית

### ✨ תכונות ראשוניות
- ממשק ניהול WordPress מלא
- וידג'ט צ'אט עם עיצוב מודרני
- התאמה אישית של צבעים
- התאמה אישית של טקסטים
- העלאת לוגו מותאם אישית
- מיקום וגודל מתכווננים
- תמיכה במובייל
- שמירת היסטוריית שיחה
- אפקטים ויזואליים (neural network)
- חיבור ל-Webhook
- RTL support

### 🐛 בעיות ידועות (תוקנו ב-2.1.0)
- Ajax handlers לא מוגדרים במלואם
- Session ID לא סונכרן נכון
- בעיות ב-extractBotResponse
- העלאת לוגו לא עובדת
- חסרים nonces
- בעיות RTL מסוימות
- זליגת זיכרון באפקטים

---

## השוואה בין גרסאות

| תכונה | v2.0.0 | v2.1.0 |
|-------|--------|--------|
| Ajax Handlers | ❌ חלקי | ✅ מלא |
| Session Management | ⚠️ בסיסי | ✅ מתקדם |
| טיפול בתגובות | ⚠️ מוגבל | ✅ גמיש |
| אבטחה | ⚠️ חלקית | ✅ מלאה |
| אינדיקטור הקלדה | ❌ | ✅ |
| התראות קוליות | ❌ | ✅ |
| API חיצוני | ❌ | ✅ |
| Reconnect אוטומטי | ❌ | ✅ |
| תיעוד | ⚠️ מינימלי | ✅ מפורט |
| גודל קובץ | ~45KB | ~50KB |

---

## תוכניות לעתיד

### [2.2.0] - מתוכנן לדצמבר 2025
- [ ] העלאת קבצים
- [ ] הודעות קוליות  
- [ ] שיתוף מסך
- [ ] אימוג'ים ו-GIFs
- [ ] תבניות תגובות מהירות
- [ ] דירוג שיחות

### [2.3.0] - מתוכנן לינואר 2026
- [ ] מצב לילה (Dark Mode)
- [ ] תרגום לאנגלית
- [ ] תרגום לערבית
- [ ] תמות עיצוב מובנות
- [ ] Widget customizer live
- [ ] A/B testing built-in

### [2.4.0] - מתוכנן לפברואר 2026
- [ ] דוחות סטטיסטיקה
- [ ] ניתוח שיחות
- [ ] אינטגרציה עם Google Analytics
- [ ] ייצוא שיחות ל-CSV
- [ ] ניהול רב-משתמשים
- [ ] API webhooks ליציאה

### [3.0.0] - מתוכנן למרץ 2026
- [ ] מצב Multi-agent
- [ ] תורים וטרייאג'
- [ ] אינטגרציה עם CRM
- [ ] Chatbot builder ויזואלי
- [ ] Machine learning insights
- [ ] Mobile apps (iOS/Android)

---

## הערות שדרוג

### שדרוג מ-2.0.0 ל-2.1.0

**לפני השדרוג:**
1. גבה את קובץ הפלאגין הנוכחי
2. גבה את ההגדרות (ייצא מהממשק)
3. רשום את כתובת ה-Webhook

**תהליך השדרוג:**
1. השבת את הפלאגין הישן
2. מחק את הפלאגין הישן
3. העלה את הגרסה החדשה
4. הפעל את הפלאגין
5. בדוק שההגדרות נשמרו (אם לא - ייבא מהגיבוי)

**אחרי השדרוג:**
1. נקה את זיכרון המטמון של WordPress
2. נקה את זיכרון המטמון של הדפדפן (Ctrl+F5)
3. בדוק שהצ'אט עובד תקין
4. בדוק חיבור ל-Webhook
5. נסה לשלוח מסר בדיקה

**שימו לב:**
- ההיסטוריה שנשמרה ב-localStorage תימחק (גרסת פורמט שונה)
- משתמשים יקבלו Session ID חדש
- לא נדרשת הגדרה מחדש מלאה

---

## Bug Reports & Feature Requests

מצאת באג או יש רעיון לתכונה חדשה?

📧 **Email:** support@intermedia.co.il
🌐 **Website:** https://intermedia.co.il
📝 **נושא:** [Miri Chat Widget Pro] Bug/Feature

**בדיווח על באג, אנא כלול:**
1. גרסת הפלאגין
2. גרסת WordPress
3. דפדפן וגרסה
4. תיאור הבעיה
5. צילומי מסך (אם רלוונטי)
6. שגיאות מהקונסול (אם יש)

---

## תודות

**Contributors:**
- Development Team @ Intermedia
- QA Testers
- Beta Users

**Special Thanks:**
- WordPress Community
- n8n Community  
- Open Source Contributors

---

© 2025 Intermedia. All rights reserved.
