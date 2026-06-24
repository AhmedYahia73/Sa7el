<!DOCTYPE html>
<html lang="ar" dir="rtl">
  <style>
    /* إعدادات الخلفية والخطوط */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Cairo', sans-serif;
}

body {
    background: linear-gradient(135deg, #f5f7fa 0%, #e4ecf7 100%);
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 20px;
}

/* البطاقة الرئيسية */
.card-container {
    background-color: #ffffff;
    padding: 40px 30px;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.05);
    width: 100%;
    max-width: 440px;
    text-align: center;
}

/* أيقونة البريد */
.icon-box {
    width: 75px;
    height: 75px;
    background-color: #eff6ff;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0 auto 24px auto;
}

.icon-box svg {
    width: 35px;
    height: 35px;
    color: #3b82f6;
}

/* النصوص العلوية */
h2 {
    color: #1e293b;
    font-size: 24px;
    margin-bottom: 12px;
    font-weight: 700;
}

.description {
    color: #64748b;
    font-size: 14px;
    line-height: 1.6;
    margin-bottom: 30px;
}

/* صندوق عرض الرمز */
.code-display-box {
    background-color: #f8fafc;
    border: 2px dashed #cbd5e1;
    border-radius: 14px;
    padding: 16px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    direction: ltr; /* لعرض الرمز الإنجليزي بشكل صحيح من اليسار لليمين */
}

/* النص الخاص بالرمز */
#verification-code {
    font-size: 32px;
    font-weight: 800;
    letter-spacing: 6px;
    color: #1e293b;
}

/* زر النسخ */
.btn-copy {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    padding: 8px 14px;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}

.btn-copy:hover {
    background-color: #f1f5f9;
    border-color: #cbd5e1;
}

.btn-copy svg {
    width: 16px;
    height: 16px;
    color: #3b82f6;
}

.btn-copy span {
    font-size: 13px;
    font-weight: 600;
    color: #3b82f6;
    font-family: 'Cairo', sans-serif;
}

/* نص العداد المؤقت */
.timer-text {
    font-size: 13px;
    color: #94a3b8;
    margin-bottom: 30px;
}

.countdown {
    color: #ef4444;
    font-weight: 600;
}

/* زر العودة للرئيسية */
.btn-back {
    display: block;
    text-decoration: none;
    background-color: #3b82f6;
    color: #ffffff;
    padding: 14px;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 600;
    transition: background-color 0.2s ease;
}

.btn-back:hover {
    background-color: #2563eb;
}
  </style>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رمز استعادة الحساب</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="card-container">
        <div class="icon-box">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
            </svg>
        </div>

        <h2>تم إرسال رمز التحقق</h2>
        <p class="description">يرجى التحقق من بريدك الإلكتروني. لقد أرسلنا لك رمزاً سرياً مؤقتاً لتتمكن من إعادة تعيين كلمة المرور.</p>

        <div class="code-display-box">
            <span id="verification-code">{{$code}}</span>
            <button class="btn-copy" onclick="copyCode()" title="نسخ الرمز">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" id="copy-icon">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376A8.965 8.965 0 0 0 12 12.75a8.965 8.965 0 0 0-3.75 1.5m7.5 3H4.5m10.5 0a3.75 3.75 0 1 1-7.5 0M3 10.5h18" />
                </svg>
                <span id="copy-text">نسخ</span>
            </button>
        </div>

        <p class="timer-text">ينتهي مفعول هذا الرمز خلال <span class="countdown">10:00</span> دقائق</p>

        <a href="/login" class="btn-back">العودة لصفحة تسجيل الدخول</a>
    </div>

    <script>
        function copyCode() {
            const codeText = document.getElementById("verification-code").innerText;
            navigator.clipboard.writeText(codeText).then(() => {
                const copyText = document.getElementById("copy-text");
                copyText.innerText = "تم النسخ!";
                copyText.style.color = "#10b981"; // تغيير اللون للأخضر عند النسخ
                
                setTimeout(() => {
                    copyText.innerText = "نسخ";
                    copyText.style.color = "#3b82f6";
                }, 2000);
            });
        }
    </script>
</body>
</html>