<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فحص Reverb Real-Time</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/8.3.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
</head>
<body>

    <div style="text-align: center; margin-top: 50px; font-family: sans-serif;">
        <h1 style="color: #2d3748;">📡 صفحة فحص البث اللحظي (Reverb)</h1>
        <p style="color: #4a5568; font-size: 18px;">افتح الـ **Console (F12)** وراقب المخرجات عند إرسال طلب جديد!</p>
        
        <div id="status" style="display: inline-block; padding: 10px 20px; background: #feebc8; color: #c05621; border-radius: 5px; font-weight: bold;">
            جاري الاتصال بسيرفر Reverb...
        </div>
    </div>
    <script>
        // 1. إعداد واجهة Laravel Echo بالقيم الصريحة لسيرفرك الحالي
        window.Echo = new window.Echo({
            broadcaster: 'pusher',
            key: 'hfauysjmov3blta8zfql', // الـ Key الصريح لتجنب مشاكل الـ Blade والـ Cache
            wsHost: "bcknd.sea-go.org",  // الدومين الفعلي الحالي للسيرفر بتاعك
            wsPort: 443,
            wssPort: 443,
            forceTLS: true,
            enabledTransports: ['ws', 'wss'],
            cluster: 'mt1'
        });

        const statusDiv = document.getElementById('status');

        // تحديث واجهة الصفحة عند نجاح الاتصال
        window.Echo.connector.pusher.connection.bind('connected', function() {
            statusDiv.style.background = '#c6f6d5';
            statusDiv.style.color = '#22543d';
            statusDiv.innerText = '🟢 متصل بنجاح وسيرفر Reverb مستعد!';
            console.log('✅ Connected to Reverb Successfully!');
        });

        // مراقبة أخطاء الاتصال
        window.Echo.connector.pusher.connection.bind('error', function(err) {
            statusDiv.style.background = '#fed7d7';
            statusDiv.style.color = '#9b2c2c';
            statusDiv.innerText = '🔴 فشل الاتصال بسيرفر Reverb!';
            console.error('❌ Reverb Connection Error:', err);
        });

        // 2. الاستماع للقناة العامّة الإدارية
        window.Echo.channel('userNotification_') 
            .listen('.UserNotificationEvent', (data) => { 
                console.log('🎯 وصّلت نوتيفيكيشن جديدة لايف يا معلم!!');
                console.log('📦 Object Data:', data);
                alert('تنبيه جديد: ' + (data.notification || 'تم استقبال بيانات بنجاح!'));
            });
    </script>
</body>
</html>