<?php
// إعدادات البوت
define('BOT_TOKEN', '8731473114:AAFFklxLcYm8VVNXfyzRQgz-MCpkUSaQrqE'); // ضع توكن البوت هنا
define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');

// الويب هوك - ضع رابط موقعك هنا
// https://api.telegram.org/bot[TOKEN]/setWebhook?url=https://yourdomain.com/index.php

// قراءة البيانات من تيليجرام
$content = file_get_contents('php://input');
$update = json_decode($content, true);

if (!$update) {
    echo "IDLEB X Bot is Running";
    exit;
}

// معالجة الرسائل
if (isset($update['message'])) {
    $chatId = $update['message']['chat']['id'];
    $message = $update['message']['text'] ?? '';
    $userId = $update['message']['from']['id'] ?? '';
    $username = $update['message']['from']['username'] ?? '';
    
    // أمر بدء
    if ($message == '/start') {
        sendMessage($chatId, welcomeMessage($username));
    }
    // عرض الخدمات
    elseif ($message == '/services' || $message == 'الخدمات' || $message == '📋 الخدمات') {
        sendMessage($chatId, servicesList());
    }
    // قنوات السحب
    elseif ($message == '/channels' || $message == 'قنوات السحب' || $message == '📢 القنوات') {
        sendMessage($chatId, channelsList());
    }
    // الدعم
    elseif ($message == '/support' || $message == 'الدعم' || $message == '🆘 مساعدة') {
        sendMessage($chatId, supportMessage());
    }
    // زر الرجوع
    elseif ($message == '🔙 رجوع' || $message == '🏠 الرئيسية') {
        sendMessage($chatId, mainMenu());
    }
    // معالجة طلبات الخدمات
    elseif (strpos($message, 'رشق مشاهدات انستقرام') !== false) {
        askForLink($chatId, 'instagram_views', 'أدخل رابط منشور انستقرام (Reel أو Post)');
    }
    elseif (strpos($message, 'رشق لايكات انستقرام') !== false) {
        askForLink($chatId, 'instagram_likes', 'أدخل رابط منشور انستقرام');
    }
    elseif (strpos($message, 'رشق حفظ انستقرام') !== false) {
        askForLink($chatId, 'instagram_saves', 'أدخل رابط منشور انستقرام');
    }
    elseif (strpos($message, 'رشق مشاهدات ستوري') !== false) {
        askForUsername($chatId, 'instagram_story', 'أدخل اسم المستخدم (بدون @)');
    }
    elseif (strpos($message, 'رشق لايكات تيك توك') !== false) {
        askForLink($chatId, 'tiktok_likes', 'أدخل رابط فيديو تيك توك');
    }
    elseif (strpos($message, 'رشق مشاهدات تيك توك') !== false) {
        askForLink($chatId, 'tiktok_views', 'أدخل رابط فيديو تيك توك');
    }
    // استقبال الرابط
    elseif (isset($GLOBALS['waiting_for'][$chatId])) {
        $service = $GLOBALS['waiting_for'][$chatId];
        unset($GLOBALS['waiting_for'][$chatId]);
        processService($chatId, $service, $message);
    }
    // قائمة افتراضية
    else {
        sendMessage($chatId, mainMenu());
    }
}

// دالة إرسال رسالة
function sendMessage($chatId, $text, $keyboard = null) {
    $data = [
        'chat_id' => $chatId,
        'text' => $text,
        'parse_mode' => 'HTML'
    ];
    
    if ($keyboard) {
        $data['reply_markup'] = json_encode($keyboard);
    }
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, API_URL . 'sendMessage');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_exec($ch);
    curl_close($ch);
}

// دالة إرسال رسالة مع أزرار
function sendInlineKeyboard($chatId, $text, $buttons) {
    $keyboard = [
        'inline_keyboard' => $buttons
    ];
    
    sendMessage($chatId, $text, $keyboard);
}

// دالة إرسال قائمة عادية
function sendReplyKeyboard($chatId, $text, $buttons) {
    $keyboard = [
        'keyboard' => $buttons,
        'resize_keyboard' => true,
        'one_time_keyboard' => false
    ];
    
    sendMessage($chatId, $text, $keyboard);
}

// رسالة الترحيب
function welcomeMessage($username) {
    return "🎉 <b>مرحباً بك في IDLEB X بوت</b> 🎉
    
✨ <b>البوت الاحترافي لخدمات التواصل الاجتماعي</b> ✨

👤 <b>المطور:</b> @IDLEBX
📊 <b>الخدمات المنفذة:</b> 15,000+
⭐ <b>رضا العملاء:</b> 98%

📌 <b>الخدمات المتوفرة:</b>
• رشق مشاهدات انستقرام
• رشق لايكات انستقرام  
• رشق حفظ (saves) انستقرام
• رشق مشاهدات ستوري انستقرام
• رشق لايكات تيك توك
• رشق مشاهدات تيك توك

👇 <b>اضغط على قائمة الخدمات للبدء</b>";
}

// قائمة الخدمات
function servicesList() {
    return "📋 <b>قائمة الخدمات المتوفرة</b> 📋
    
━━━━━━━━━━━━━━━━━
📸 <b>خدمات انستقرام:</b>
━━━━━━━━━━━━━━━━━
1️⃣ <b>رشق مشاهدات انستقرام</b>
   └ 200 مشاهد | مجاني

2️⃣ <b>رشق لايكات انستقرام</b>
   └ غير محدود | مجاني

3️⃣ <b>رشق حفظ (saves)</b>
   └ 30 حفظ | مجاني

4️⃣ <b>رشق مشاهدات ستوري</b>
   └ غير محدود | مجاني

━━━━━━━━━━━━━━━━━
🎵 <b>خدمات تيك توك:</b>
━━━━━━━━━━━━━━━━━
5️⃣ <b>رشق لايكات تيك توك</b>
   └ 100 لايك | مجاني

6️⃣ <b>رشق مشاهدات تيك توك</b>
   └ غير محدود | مجاني

━━━━━━━━━━━━━━━━━
📌 <b>ملاحظة:</b> جميع الخدمات مجانية تماماً
⚠️ <b>تنبيه:</b> رابط واحد كل 24 ساعة

👇 <b>اختر الخدمة المناسبة:</b>";
}

// قنوات السحب
function channelsList() {
    return "📢 <b>قنوات السحب والخدمات</b> 📢

━━━━━━━━━━━━━━━━━━━
✅ <b>قناة الخدمات:</b>
└ <a href='https://t.me/idlebx3'>@IDLEBX</a>

✅ <b>قناة العروض:</b>  
└ <a href='https://t.me/idlebx2'>@IDLEBX2</a>

━━━━━━━━━━━━━━━━━━━

💡 <b>نصيحة:</b> اشترك في القنوات ليصلك كل جديد";
}

// رسالة الدعم
function supportMessage() {
    return "🆘 <b>مركز المساعدة والدعم</b> 🆘

━━━━━━━━━━━━━━━━━
📞 <b>للتواصل مع المطور:</b>
└ <a href='https://t.me/IDLEBX'>@IDLEBX</a>

⏰ <b>أوقات الدعم:</b>
└ 24 ساعة طوال الأسبوع

❓ <b>الأسئلة الشائعة:</b>
└ كل خدمة تستخدم مرة كل 24 ساعة
└ الرابط يجب أن يكون عام (public)
└ قد تأخذ الخدمة من 5-30 دقيقة

💬 <b>للتبليغ عن مشكلة:</b>
└ تواصل مع المطور مباشرة";
}

// القائمة الرئيسية
function mainMenu() {
    $keyboard = [
        [
            ['text' => '📋 الخدمات'],
            ['text' => '📢 القنوات']
        ],
        [
            ['text' => '🆘 مساعدة'],
            ['text' => '👤 المطور']
        ]
    ];
    
    sendReplyKeyboard(0, "🏠 <b>القائمة الرئيسية</b>\n\nاختر أحد الخيارات:", $keyboard);
    return "";
}

// طلب رابط من المستخدم
function askForLink($chatId, $service, $prompt) {
    $GLOBALS['waiting_for'][$chatId] = $service;
    sendMessage($chatId, "🔗 $prompt\n\nمثال: https://www.instagram.com/p/...");
}

// طلب اسم مستخدم
function askForUsername($chatId, $service, $prompt) {
    $GLOBALS['waiting_for'][$chatId] = $service;
    sendMessage($chatId, "👤 $prompt\n\nمثال: username");
}

// معالجة الخدمة
function processService($chatId, $service, $input) {
    $services = [
        'instagram_views' => [
            'url' => 'https://leofame.com/ar/free-instagram-views',
            'name' => 'مشاهدات انستقرام',
            'quantity' => 200
        ],
        'instagram_likes' => [
            'url' => 'https://leofame.com/ar/free-instagram-likes', 
            'name' => 'لايكات انستقرام',
            'quantity' => null
        ],
        'instagram_saves' => [
            'url' => 'https://leofame.com/ar/free-instagram-saves',
            'name' => 'حفظ انستقرام', 
            'quantity' => 30
        ],
        'instagram_story' => [
            'url' => 'https://leofame.com/ar/free-instagram-story-views',
            'name' => 'مشاهدات ستوري',
            'quantity' => null
        ],
        'tiktok_likes' => [
            'url' => 'https://leofame.com/ar/free-tiktok-likes',
            'name' => 'لايكات تيك توك',
            'quantity' => 100
        ],
        'tiktok_views' => [
            'url' => 'https://leofame.com/ar/free-tiktok-views',
            'name' => 'مشاهدات تيك توك',
            'quantity' => null
        ]
    ];
    
    if (!isset($services[$service])) {
        sendMessage($chatId, "❌ خدمة غير موجودة");
        return;
    }
    
    $serviceInfo = $services[$service];
    sendMessage($chatId, "⏳ جاري معالجة طلب {$serviceInfo['name']}...");
    
    // تحضير الرابط
    if ($service == 'instagram_story') {
        $targetUrl = $serviceInfo['url'] . '?username=' . urlencode($input);
    } else {
        $targetUrl = $serviceInfo['url'] . '?url=' . urlencode($input);
    }
    
    if ($serviceInfo['quantity']) {
        $targetUrl .= '&quantity=' . $serviceInfo['quantity'];
    }
    
    // جلب الصفحة وإرسال الطلب
    $result = sendRequest($targetUrl, $input, $serviceInfo);
    
    if ($result['success']) {
        $message = "✅ <b>تم بنجاح!</b>\n\n";
        $message .= "📊 الخدمة: {$serviceInfo['name']}\n";
        $message .= "🔗 الرابط: " . substr($input, 0, 50) . "...\n";
        if ($serviceInfo['quantity']) {
            $message .= "📈 الكمية: +{$serviceInfo['quantity']}\n";
        }
        $message .= "\n⏱️ سيتم التنفيذ خلال 5-30 دقيقة\n";
        $message .= "📢 @IDLEXB";
        sendMessage($chatId, $message);
    } else {
        sendMessage($chatId, "❌ <b>فشل التنفيذ</b>\n\n" . $result['message'] . "\n\n⚠️ قد يكون الرابط مستخدم سابقاً خلال 24 ساعة");
    }
}

// إرسال طلب HTTP
function sendRequest($url, $input, $serviceInfo) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        return ['success' => true, 'message' => 'تم الإرسال بنجاح'];
    } else {
        return ['success' => false, 'message' => 'حدث خطأ في الاتصال بالخدمة'];
    }
}
?>
