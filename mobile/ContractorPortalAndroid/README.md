# Contractor Portal Android

تطبيق Android بسيط لفتح صفحات المتعهد (`profile/support/marketing`) مباشرة من الموقع.

## المميزات
- إدخال رابط صفحة المتعهد مرة واحدة.
- فتح المحتوى داخل WebView (تحديثات الموقع تظهر مباشرة).
- سحب لتحديث الصفحة (Pull to Refresh).
- زر تسجيل خروج يمسح جلسة WebView (Cookies + Storage).
- حفظ رابط المتعهد محليًا على نفس الجهاز.

## التشغيل
1. افتح المجلد `mobile/ContractorPortalAndroid` في Android Studio.
2. دع Android Studio يعمل Sync للمشروع.
3. شغّل التطبيق على جهاز/محاكي Android.

## إخراج ملف APK للتثبيت
1. من Android Studio اختر:
	- Build > Build APK(s) (لـ debug)
	- أو Build > Generate Signed Bundle / APK (لـ release)
2. بعد انتهاء البناء، شغّل السكربت التالي من داخل مجلد المشروع:

```powershell
powershell -ExecutionPolicy Bypass -File .\publish-apk.ps1
```

3. السكربت ينسخ أحدث APK متاح إلى:
	- `public/downloads/contractor-portal-latest.apk`

بعد النسخ، رابط التحميل في الموقع سيقوم بتنزيل APK مباشرة.

## ملاحظة الربط مع الموقع
- التطبيق يعتمد على صفحات الموقع الحية، لذلك أي تغيير في الموقع يظهر داخل التطبيق فورًا.
- تسجيل الخروج يمسح بيانات الجلسة المحلية في WebView.
