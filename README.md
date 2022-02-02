# php-rest-api-framework #
Mikro framework pozwalający na tworzenie aplikacji REST API. 

## Konfiguracja ##
Plik "app.ini"

## Przykładowy endpoint ##
/test/test

## Middleware ##
Dostępne middleware:
* GUIDv4
* ErrorHandling
* CORS
* JSONParser
* QueryStringParser
* Routing
* Sanitizing
* Validating

## Walidacja ##
Dostępne typy walidacji:
* base64
* minimum
* maximum
* integer
* json
* required
* GUUIDv4
* email
* ISOCountryCode

### TODO ###
* opcjonalny, oddzielny zestaw middleware dla endpointów
