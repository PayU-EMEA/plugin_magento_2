# Moduł PayU dla Magento 2 w wersji >2.0.6, 2.1, 2.2
``Moduł jest wydawany na licencji GPL.``

**Jeżeli masz jakiekolwiek pytania lub chcesz zgłosić błąd zapraszamy do kontaktu z naszym wsparciem pod adresem: tech@payu.pl.**

* Jeżeli używasz Magneto w wersji 1.x proszę skorzystać z [pluginu dla wersji 1.x][ext0]

## Spis treści

1. [Cechy](#cechy)
1. [Wymagania](#wymagania)
1. [Instalacja](#instalacja)
1. [Aktualizacja](#aktualizacja)
1. [Konfiguracja](#konfiguracja)
    * [Parametry](#parametry)
1. [Informacje o cechach](#informacje-o-cechach)
    * [Kolejność metod płatności](#kolejność-metod-płatności)
    * [Ponowienie płatności](#ponowienie-płatności)
    * [Zapisywanie kart](#zapisywanie-kart)
    * [Przewalutowanie](#przewalutowanie)


## Cechy
Moduł płatności PayU dodaje do Magento 2 opcję płatności PayU. Moduł współpracuje z Magento 2 w wersjach: >2.0.6, 2.1, 2.2

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

#### Przy użyciu Composer
`composer require payu/magento2-payment-gateway`

#### Kopiując pliki na serwer
1. Pobierz najnowszą wersję moduł z [repozytorium GitHub][ext4]
1. Rozpakuj pobrany plik
1. Połącz się z serwerem ftp i skopiuj rozpakowaną zawartość do katalogu `app/code/PayU/PaymentGateway` swojego sklepu Magento 2. Jeżeli nie ma takiego katalogu utwórz go.

Po instalacji przy użyciu Composer lub kopiując pliki z poziomu konsoli uruchom:
   * php bin/magento module:enable PayU_PaymentGateway
   * php bin/magento setup:upgrade
   * php bin/magento setup:di:compile
   * php bin/magento setup:static-content:deploy

## Aktualizacja
Aktualizując plugin z wersji starszej niż 1.2.0 należy po aktualizacji pluginu ponownie podać i zapisać konfigurację POS-ów.

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
| Kolejność metod płatności | Określa kolejnośc wyświetlanych metod płatności (dostępne tylko dla `Płatność PayU`) [więcej informacji](#kolejność-metod-płatności). |

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
W celu ustalenia kolejności wyświetlanych ikon matod płatności należy podać symbole metod płatności oddzielając je przecinkiem. [Lista metod płatności][ext6]. 

### Ponowienie płatności
Aby użyć tej opcji, należy również odpowiednio skonfigurować POSa w PayU i wyłączyć automatycznie odbieranie płatności (domyślnie auto-odbiór jest włączony).
W tym celu należy zalogować się do panelu PayU, wejść do zakładki "Płatności elektroniczne", następnie wybrać "Moje sklepy" i punkt płatności na danym sklepie.
Opcja "Automatyczny odbiór płatności" znajduje się na samym dole, pod listą metod płatności.

Ponowienie płatności umożliwia zakładanie wielu płatności w PayU do jednego zamówienia w Magento. Wtyczka automatycznie odbierze pierwszą udaną płatność, a pozostałe zostaną anulowane.
Ponowienie płatności z punktu widzenia kupującego jest możliwe poprzez listę zamówień w Magento (pojawi się tam link "Zapłać ponownie"). 
Kupujący automatycznie otrzyma również wiadomość e-mail z takim linkiem. 
Tym samym kupujący otrzymuje możliwość skutecznego opłacenia zamówienia, nawet jeśli pierwsza płatność była nieudana (np. brak środków na karcie, problemy z logowaniem do banku itp.). 

### Zapisywanie kart
Zapisywanie kart pozwala zalogowanym użytkownikom zapamiętać kartę na poczet przyszłych płatności.
Każda zapisana karta jest "tokenizowana", przy czym Magento w żaden sposób nie przetwarza pełnych danych karty (podawane są one na wlanym widgecie hostowanym przez PayU),
ani nie zapisuje w swojej bazie tokenów kartowych (przed użyciem, aktualne tokeny dla danego użytkownika są zawsze pobierane z PayU).

W celu prawidłowego działania usługi konieczna jest dodatkowa konfiguracja w PayU, polegająca na umożliwieniu tworzenia i pobierania tokenów.
Dodatkowo, można również ustalić zasady uwierzytelniania płatności zapisaną kartą (domyślnie każda płatność zapisaną karta wymaga podania kodu CVV i 
uwierzytelnieniu przez 3DS, ale można np. ustalić próg kwoty transakcji dla jakiego nie będzie to konieczne).

Kupujący może zapisać kartę podczas płatności, korzystając z opcji "Użyj i zapisz" na widgecie PayU podczas podawania danych karty.
Każda zapisywana karta podlega silnemu uwierzytelnieniu przy pierwszej płatności (CVV i 3DS).
Zapisana karta będzie pokazywać się po wybraniu płatności kartą przez PayU za zamówienie i jest widoczna w koncie użytkownika 
(zakładka "Moje zapisane karty"), gdzie jest również dostępna opcja jej usunięcia.   

### Przewalutowanie
Przewalutowanie, inaczej Multi-Currency Pricing (MCP), daje możliwość obciążania kart użytkowników w walucie innej niż waluta rozliczeniowa z PayU. Przykładowo, można obciażyć kartę w EUR, 
ale otrzymać PLN od PayU.
Przewalutowanie opiera się o funkcjonalność Magento, która dla sklepu pozwala dla "store-view" zdefiniować "display currency" różną od "base currency".
Ta opcja jest wygodniejsza dla kupującego niż DCC (Dynamic Currency Conversion), gdyż cena w walucie jego karty pokazana jest na poszczególnych produktach 
i pozwala łatwiej podjąć decyzję o zakupie (w przypadku DCC kwota w walucie znana jest dopiero po rozpoczęciu płatności).
W celu uruchomienia tej usługi należy:
* uzyskać parametr mcpPartnerId z PayU (pozwala pobierać tabele kursowe z PayU z odpowiednimi parami walutowymi),
* skonfigurować cykliczne pobieranie tabel kursowych z PayU w Magento.
W celu uruchomienia i konfiguracji usługi należy skontaktować się z opiekunem handlowym w PayU.


<!--external links:-->
[ext0]: https://github.com/PayU/plugin_magento_160
[ext1]: https://secure.payu.com/boarding/?pk_campaign=Plugin-Github&pk_kwd=Magento2#/form
[ext2]: http://php.net/manual/en/book.curl.php
[ext3]: http://php.net/manual/en/book.hash.php
[ext4]: https://github.com/PayU/plugin_magento_2/releases/latest
[ext5]: https://secure.snd.payu.com/boarding/?pk_campaign=Plugin-Github&pk_kwd=Magento2#/form
[ext6]: http://developers.payu.com/pl/overview.html#paymethods

<!--images:-->
[img0]: readme_images/methods.png
