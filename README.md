Projekat "Veb aplikacija za online aukcije" je kompletna full-stack veb aplikacija koja omogućava korisnicima da učestvuju u online licitacijama. Aplikacija je izgrađena koristeći tehnologije Laravel i React, gde Laravel služi kao robustan backend API, a React kao dinamičan i interaktivan frontend.
Aplikacija je dizajnirana da bude intuitivna i jednostavna za korišćenje, sa jasnim razdvajanjem uloga između neulogovanih, ulogovanih korisnika i administratora. Glavni cilj je bio kreiranje platforme koja automatizuje ceo životni ciklus aukcije, od njenog kreiranja do uspešnog završetka i prenosa sredstava.

Aplikacija nudi širok spektar funkcionalnosti koje pokrivaju sve aspekte jedne aukcijske platforme:

1. Registracija, prijava i odjava korisnika.
2. Mogućnost filtriranja aukcija po statusu (predstojeća, aktivna, završena).
3. Mogućnost sortiranja aukcija po početnoj ceni (rastuće ili opadajuće).
4. Mogućnost pretrage aukcija po kategoriji proizvoda.
5. Paginacija za lakše pregledanje velikog broja aukcija.
6. Mogućnost kreiranja, ažuriranja i brisanja aukcija.
7. Ulogovani korisnici mogu da postavljaju ponude na aktivnim aukcijama.
8. Po završetku aukcije, automatski prenos sredstava sa računa pobednika na račun kreatora.
9. Sistem notifikacija obaveštava korisnike o ishodu aukcije (da li su pobedili, ili je aukcija završena bez ponuda).
10. Ulogovani korisnici mogu da pregledaju i ažuriraju svoje lične podatke (adresa, broj telefona, stanje na računu).
11. Konverzija valuta - korisnici mogu da vide procenjenu vrednost početne i trenutne cene u EUR ili USD, koristeći eksterni API.
12. Korisnici sa ulogom administratora imaju mogućnost da obrišu bilo koju aukciju na platformi.

Preduslovi za pokretanje aplikacije na lokalnoj mašini su instalirani: Git, PHP (verzija 8.1 ili novija), Composer, Node.js i npm, kao i lokalni server za bazu podataka (npr. MySQL)

Da bi se preuzeo projekat, potrebno je klonirati repozitorijum na lokalnu mašinu. Projekat se sastoji od dva zasebna foldera: online-aukcije za backend i online-aukcije-front za frontend.

Treba instalirati composer u laravel folderu.

cd online-aukcije

composer install

Na backendu treba podesiti konekciju sa lokalnom bazom podataka(ime baze, korisničko ime i lozinka) u .env fajlu.

DB_CONNECTION=mysql

DB_HOST=127.0.0.1

DB_PORT=3306

DB_DATABASE=online-aukcije

DB_USERNAME=root

DB_PASSWORD=

Generisanje ključa i migracije i povezivanje storage-a

Bash

php artisan key:generate

php artisan migrate

php artisan storage:link

Podešavanje Frontenda (React), instaliranje zavisnosti

cd online-aukcije-front

npm install



Za potpunu funkcionalnost, potrebno je pokrenuti tri odvojena procesa u tri terminala.

Terminal 1: Pokretanje Laravel servera, u direktorijumu online-aukcije:

php artisan serve

Server će se pokrenuti, obično na adresi http://localhost:8000.

Terminal 2: Pokretanje Laravel Queue Worker-a, u direktorijumu online-aukcije:

php artisan queue:work

Ovaj proces je neophodan za izvršavanje automatskih poslova (StartAuctionJob i EndAuctionJob). Bez njega, statusi aukcija se neće automatski menjati.

Terminal 3: Pokretanje React aplikacije u direktorijumu online-aukcije-front:

npm start

React aplikacija će se pokrenuti, obično na adresi http://localhost:3000
