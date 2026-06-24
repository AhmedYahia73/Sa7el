<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رمز استعادة الحساب</title>
</head>
<body style="margin:0;padding:0;background-color:#e4ecf7;font-family:Arial,sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#e4ecf7;padding:40px 20px;">
    <tr>
        <td align="center">

            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:440px;background-color:#ffffff;border-radius:20px;box-shadow:0 15px 35px rgba(0,0,0,0.08);overflow:hidden;">

                {{-- Header --}}
                <tr>
                    <td align="center" style="background-color:#3b82f6;padding:32px 30px 28px;">
                        <table cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td align="center" style="width:70px;height:70px;background-color:rgba(255,255,255,0.2);border-radius:50%;">
                                    <img src="https://img.icons8.com/ios-filled/50/ffffff/secured-letter.png" width="35" height="35" alt="email" style="display:block;margin:17px auto;" />
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- Body --}}
                <tr>
                    <td align="center" style="padding:36px 30px 20px;direction:rtl;">
                        <h2 style="margin:0 0 12px 0;color:#1e293b;font-size:22px;font-weight:700;">تم إرسال رمز التحقق</h2>
                        <p style="margin:0 0 28px 0;color:#64748b;font-size:14px;line-height:1.7;">
                            لقد أرسلنا لك رمزاً سرياً مؤقتاً لتتمكن من إعادة تعيين كلمة المرور.
                        </p>

                        {{-- Code Box --}}
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
                            <tr>
                                <td align="center" style="background-color:#f8fafc;border:2px dashed #cbd5e1;border-radius:14px;padding:20px;">
                                    <span style="font-size:36px;font-weight:800;letter-spacing:8px;color:#1e293b;direction:ltr;display:block;">{{ $code }}</span>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:0 0 28px 0;color:#94a3b8;font-size:13px;">
                            ينتهي مفعول هذا الرمز خلال <span style="color:#ef4444;font-weight:700;">10</span> دقائق
                        </p>
                    </td>
                </tr>

                {{-- Warning --}}
                <tr>
                    <td style="padding:0 30px 30px;direction:rtl;">
                        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#fff7ed;border-right:4px solid #f97316;border-radius:8px;padding:14px 16px;">
                            <tr>
                                <td style="color:#92400e;font-size:13px;line-height:1.6;padding:14px 16px;">
                                    ⚠️ إذا لم تطلب إعادة تعيين كلمة المرور، يرجى تجاهل هذا البريد.
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td align="center" style="background-color:#f8fafc;padding:18px 30px;border-top:1px solid #e2e8f0;">
                        <p style="margin:0;color:#94a3b8;font-size:12px;">Sea Go &copy; {{ date('Y') }} &mdash; جميع الحقوق محفوظة</p>
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
