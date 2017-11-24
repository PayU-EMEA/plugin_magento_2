# Moduł PayU dla Magento 2 w wersji >2.0.6, 2.1, 2.2
``Moduł jest wydawany na licencji GPL.``

**Jeżeli masz jakiekolwiek pytania lub chcesz zgłosić błąd zapraszamy do kontaktu z naszym wsparciem pod adresem: tech@payu.pl.**

* Jeżeli używasz Magneto w wersji 1.x proszę skorzystać z [pluginu dla wersji 1.x][ext0]

## Spis treści

1. [Cechy](#cechy)
1. [Wymagania](#wymagania)
1. [Instalacja](#instalacja)
1. [Konfiguracja](#konfiguracja)
    * [Parametry](#parametry)
1. [Informacje o cechach](#informacje-o-cechach)
    * [Kolejność metod płatności](#kolejnosc-metod-platnosci)
    * [Ponowienie płatności](#ponowienie-płatności)
    * [Zapisywanie kart](#zapisywanie-kart)
    * [Przewalutowanie](#przewalutowanie)


## Cechy
Moduł płatności PayU dodaje do Magento 2 opcję płatności PayU.

Możliwe są następujące operacje:
  * Utworzenie płatności w sytemie PayU
  * Automatyczne odbieranie powiadomień i zmianę statusów zamówienia
  * Odebranie lub odrzucenie płatności (w przypadku wyłączonego autoodbioru)
  * Wyświetlenie metod płatności i wybranie metody na stronie podsumowania zamówienia
  * Płatność kartą bezpośrednio na stronie podsumowania zamówienia
  * Zapisanie karty i płatność zapisaną kartą
  * Ponowienie płatności
  * Utworzenie zwrotu online (pełnego lub częściowego)

Moduł dodaje dwie metody płatności:

![methods][img0]
  * **Płatność PayU** - wybór metody płatności i przekierowanie do banku lub formatkę kartową
  * **Płatność kartą** - wpisanie numeru karty bezpośrednio na stronie sklepu i płatność kartą

## Wymagania

**Ważne:** Moduł ta działa tylko z punktem płatności typu `REST API` (Checkout), jeżeli nie posiadasz jeszcze konta w systemie PayU [**zarejestruj się w systemie produkcyjnym**][ext1] lub [**zarejestruj się w systemie sandbox**][ext5]

* Wersja PHP zgodna z wymaganiami zainstalowanej wersji Magento 2
* Rozszerzenia PHP: [cURL][ext2] i [hash][ext3].

## Instalacja

1. Pobierz najnowszą wersję moduł z [repozytorium GitHub][ext4]
1. Rozpakuj pobrany plik
1. Połącz się z serwerem ftp i skopiuj katalogi `app` i `lib` z rozpakowanego pliku do katalogu głównego swojego sklepu Magento 2
1. Z poziomu konsoli uruchom:
   * php bin/magento module:enable PayU_PaymentGateway
   * php bin/magento setup:upgrade
   * php bin/magento setup:di:compile
   * php bin/magento setup:static-content:deploy

## Konfiguracja

1. Przejdź do strony administracyjnej swojego sklepu Magento 2 [http://adres-sklepu/admin_xxx].
1. Przejdź do  **Stores** > **Configuration**.
1. Na stronie **Configuration** w menu po lewej stronie w sekcji **Sales** wybierz **Payment Methods**.
1. Na liście dostępnych metod płatności należy wybrać **PayU** lub **PayU - Cards** w celu konfiguracji parametrów wtyczki.
1. Po zmanie paramettrów naciśnij przycisk `Save config`.

### Parametry

#### Główne parametry

| Parameter | Opis |
|---------|-----------|
| Czy włączyć wtyczkę? | Określa czy metoda płatności będzie dostępna w sklepie na liście płatności. |
| Tryb Sandbox | Określa czy płatności będą realizowane na środowisku testowym (sandbox) PayU. |
| Kolejność metod płatności | Określa kolejnośc wyświetlanych metod płatności (dostępne tylko dla `Płatność PayU`) [więcej informacji](#kolejnosc-metod-platnosci). |

#### Parametry punktu płatności (POS)

| Parameter | Opis |
|---------|-----------|
| Id punktu płatności| Identyfikator POS-a z systemu PayU |
| Drugi klucz MD5 | Drugi klucz MD5 z systemu PayU |
| OAuth - client_id | client_id dla protokołu OAuth z systemu PayU |
| OAuth - client_secret | client_secret for OAuth z systemu PayU |

#### Parametry punktu płatności (POS) - Tryb testowy (Sandbox)
Dostępne gdy parametr `Tryb testowy (Sandbox)` jest ustawiony na `Tak`.

| Parameter | Opis |
|---------|-----------|
| Id punktu płatności| Identyfikator POS-a z systemu PayU |
| Drugi klucz MD5 | Drugi klucz MD5 z systemu PayU |
| OAuth - client_id | client_id dla protokołu OAuth z systemu PayU |
| OAuth - client_secret | client_secret for OAuth z systemu PayU |

#### Inne parametry

| Parameter | Opis |
|---------|-----------|
| Czy uaktywnić ponowienie płatności? | [więcej informacji](#ponowienie-płatności) |
| Czy uaktywnić zapisywanie kart? | Dostępne tylko dla `Płatność kartą` [więcej informacji](#zapisywanie-kart) |
| Czy uaktywnić moduł przewalutowania? | Dostępne tylko dla `Płatność kartą` [więcej informacji](#przewalutowanie) |


## Informacje o cechach

### Kolejność metod płatności
W celu ustalenia kolejności wyświetlanych ikno matod płatności podawaj symbole metod płatności oddzielając je przecinkiem. [Lista metod płatności][ext6]. 

### Ponowienie płatności

### Zapisywanie kart

### Przewalutowanie


<!--external links:-->
[ext0]: https://github.com/PayU/plugin_magento_160
[ext1]: https://secure.payu.com/boarding/#/form&pk_campaign=Plugin-Github&pk_kwd=Magento2
[ext2]: http://php.net/manual/en/book.curl.php
[ext3]: http://php.net/manual/en/book.hash.php
[ext4]: https://github.com/PayU/plugin_magento/releases/latest
[ext5]: https://secure.snd.payu.com/boarding/#/form&pk_campaign=Plugin-Github&pk_kwd=Magento2
[ext6]: http://developers.payu.com/pl/overview.html#paymethods

<!--images:-->
[img0]: readme_images/methods.png
