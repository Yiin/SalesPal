# Projekto įsirašymas

## Reikalavimai prieš įsirašant

1. Composer (https://getcomposer.org/download/)
2. Node.JS v6.* (https://nodejs.org/en/)
3. PHP7
4. MySQL 5.3+
5. Git (https://git-scm.com/)

### Notes

git, composer ir node/npm executives turi būti pridėti į PATH.

## Steps

1. Atsidaryti konsolę (rekomenduoju http://cmder.net/ jei windows OS, nes defaultinis cmd.exe sucks)
2. Nueiti iki folderio kuriame nori laikyti projekto folderį (pvz cd C:/Users/{Name}/Projects)
3. `git clone https://github.com/Yiin-/SalesPal.git`
4. `composer install`
5. `npm i`
6. `npm i -g gulp bower`
7. `bower install`
8. `mkdir storage/sessions storage/views`
9. `cp .env.example .env`
10. Atsidaryti MySQL, sukurti naują duomenų bazę su norimu pavadinimu (pvz "salespal")
10. Atsidaryti .env failą su kokiu nors text editoriumi.
11. Įrašyti teisingus mysql duomenis bei sukurtos duomenų bazės pavadinimą.
12. Išsaugoti ir uždaryti .env failą.
13. `php artisan migrate` arba paprašyti manęs .sql failo su duomenų bazės kopija.
14. `gulp watch`
15. Naujoje konsolėje tame pačiame folderyje `php artisan serve`
16. Jei kas nors neveikia parašyti man.
