# Weather Information Project

Ushbu loyiha foydalanuvchilarga istalgan joydagi ob-havo ma'lumotlarini olish imkonini beradi. Ob-havo ma'lumotlari `OpenWeatherMap API` orqali olinadi va foydalanuvchiga joriy ob-havo holati haqida batafsil ma'lumot taqdim etiladi.

## Talablar

Loyihani to'g'ri ishlatish uchun quyidagi talablar bajarilishi kerak:

- **PHP 7.4** yoki undan yuqori versiyasi
- **Bootstrap** va **Font Awesome** kutubxonalari uchun internetga ulanilgan holat
- OpenWeatherMap API-dan olingan haqiqiy API kaliti

## Loyihani o'rnatish

1. **OpenWeatherMap API kaliti olish:** 
   - [OpenWeatherMap](https://home.openweathermap.org/users/sign_up) saytida ro'yxatdan o'ting va API kalitingizni oling.
   - API kalitni loyihadagi PHP faylda o'zgartiring: `appid` qiymatini o'zingizning API kalitingizga almashtiring.

2. **Kutubxonalarni ulash:** 
   - Bootstrap va Font Awesome kutubxonalari loyihangizda `<head>` tagida CDN orqali ulangan. Loyiha ishlashi uchun internetga ulanishingiz kerak.

## Foydalanish

1. **Joylashuvni kiriting:**
   - Veb sahifadagi formaga kiriting joy nomini yozing, masalan: `London`, `Tashkent`.
   
2. **Ob-havo ma'lumotlarini ko'rish:**
   - "Get Weather" tugmasini bosing, va sahifa siz kiritgan joy bo'yicha quyidagi ma'lumotlarni ko'rsatadi:
     - Joylashuv
     - Harorat
     - Ob-havo holati (quyoshli, bulutli, yomg'irli va boshqalar)
     - Namlik
     - Shamol tezligi
     - Bosim

3. **Xatolik holati:**
   - Agar kiritilgan joy uchun ob-havo ma'lumotlari topilmasa, xabar qizil rangda ko'rsatiladi.

## Tuzilishi

- **HTML va CSS:** Bootstrap va Font Awesome yordamida interfeys yaratildi.
- **PHP va OpenWeatherMap API:** Ma'lumotlar olish va foydalanuvchiga ko'rsatish uchun foydalaniladi.

---

**Eslatma:** Ushbu README fayl loyiha ishga tushirilishidan oldin API kalitining to'g'ri kiritilganligiga ishonch hosil qilishingizni eslatib turadi.
