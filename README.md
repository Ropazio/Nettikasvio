# Nettikasvio
Nettikasvio on tarkoitettu lajintunnistukseen ja se sisältää yleisimmät kasvit Suomen kauniista luonnosta.

# Näin lisäät Nettikasinon servulle

1. Korvaa `Nettikasvio/public/index.php`:n import-polku seuraavalla: `../../Nettikasvio_app/bootstrap.php`.
2. Siirrä `Nettikasvio/public`-kansio servulle kansion `/public_html` sisälle.
3. Siirrä `Nettikasvio/public`-kansion sisältö servun kansioon `public_html/Nettikasvio` ja ylikirjoita.
    * Huom! Myös `.htaccess`!
4. Siirrä `Nettikasvio/Nettikasvio_app` servulle roottiin `/`.