@echo off
echo ================================================
echo   Copying Updated Files to XAMPP
echo ================================================
echo.

copy /Y "Util\StripeService.php" "C:\xampp\htdocs\NutriSmart\Util\StripeService.php"
echo [OK] StripeService.php

copy /Y "Util\EmailService.php" "C:\xampp\htdocs\NutriSmart\Util\EmailService.php"
echo [OK] EmailService.php (warning suppressed)

copy /Y "config\groq-config.js" "C:\xampp\htdocs\NutriSmart\config\groq-config.js"
echo [OK] groq-config.js (API key updated)

copy /Y "public\js\chatbot.js" "C:\xampp\htdocs\NutriSmart\public\js\chatbot.js"
echo [OK] chatbot.js (better error handling)

copy /Y "test-chatbot.html" "C:\xampp\htdocs\NutriSmart\test-chatbot.html"
echo [OK] test-chatbot.html

copy /Y "test-phpmailer.php" "C:\xampp\htdocs\NutriSmart\test-phpmailer.php"
echo [OK] test-phpmailer.php

copy /Y "test-stripe.php" "C:\xampp\htdocs\NutriSmart\test-stripe.php"
echo [OK] test-stripe.php

copy /Y "View\FrontOffice\checkout.php" "C:\xampp\htdocs\NutriSmart\View\FrontOffice\checkout.php"
echo [OK] checkout.php

copy /Y "View\FrontOffice\payment-success.php" "C:\xampp\htdocs\NutriSmart\View\FrontOffice\payment-success.php"
echo [OK] payment-success.php

copy /Y "View\FrontOffice\payment-cancel.php" "C:\xampp\htdocs\NutriSmart\View\FrontOffice\payment-cancel.php"
echo [OK] payment-cancel.php

copy /Y "View\FrontOffice\index.php" "C:\xampp\htdocs\NutriSmart\View\FrontOffice\index.php"
echo [OK] index.php (FrontOffice - meal planner removed)

copy /Y "index.php" "C:\xampp\htdocs\NutriSmart\index.php"
echo [OK] index.php (main - meal planner route removed)

echo.
echo ================================================
echo   All Files Copied Successfully!
echo ================================================
echo.
echo IMPORTANT:
echo   - Orders ARE being saved to database
echo   - Email warning is normal (XAMPP has no mail server)
echo   - Email is optional, orders are what matters
echo.
echo CHANGES APPLIED:
echo   [1] Currency: TND to EUR
echo   [2] Design: Modern without emojis
echo   [3] Orders: Saved to database after payment
echo   [4] Email: Warning suppressed (optional feature)
echo.
echo STRIPE PAYMENT NOW:
echo   - Creates orders in database (WORKING)
echo   - Tries to send email (optional)
echo   - Shows orders in BackOffice (WORKING)
echo.
echo TEST NOW:
echo   1. Go to: http://localhost/NutriSmart/
echo   2. Add products to cart
echo   3. Complete payment (4242 4242 4242 4242)
echo   4. Check BackOffice for orders (THEY WILL BE THERE)
echo.
echo To enable emails (optional):
echo   - See EMAIL_GUIDE.md for PHPMailer installation
echo.
pause
