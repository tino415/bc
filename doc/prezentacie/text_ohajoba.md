# Text ku prezentácií na obhajobu

## Úvodný slajd

Dobrý deň,

moje meno je Martin Černák a témou moje bakalárskej práce bolo 
dynamické odporúčanie pod vedením pána profesora Návrata.
Dúfam že vás moja práca zuajme.

## Prečo táto téma

Hlavným zmyslom informatyki, ako z jej názvu vyplíva je 
spracovanie a distribúcia informácií. V súčasnej dobe naša civilizácia
vyprodukuje neuveriteľné množstvo informácií

Jedným z problémov je že tieto informácie sú zväčša neužitočné, je preto
potrebné oddelovať relevantné a irelevantné informácie.

Tak isto relevancia informácií priamo závisí od jedinca ktorému chceme
informácie poskytnúť, každý má iné záujmi. Ktoré sa dynamicky menia
v čase.

## Existujúce riešenia

Existuje niekoľko riešení na odporúčanie, ktoré len zbežne opíšem.

Napríklad Filtrovanie na základe obsahu sa porovnáva to čo používateľ
vydel v minulosti s ostatnými dokumentmi.

Kolaboratívne filtrovanie je asi najrozšírenejší prístup, 
tento prístup sa zvykne hlavne implementovať spolu s explicitným zbieraňím
spätnej väzby, kedy hľadám používateľov ktorí dali podobné hodnotenia ako aktuálny
používateľ a odporučím mu to čo sa páčilo im.

Zameranie tejto práce boly dynamické profili, pri tomto smere som sa primárne 
stretol s troma prístupmi:

- Agrgácia histórie, konkrétne algoritmus Pclicks ktorý agreguje navštívené dokumenty
- Bayesová sieť, kedy sa zostrojí bayesová sieť kde jej koreňmi sú dokuemnty ktoré 
    sú napojené na vrstvu pojmov, na ktoré sa následne dynamicky pripája používateľov profil
    prípadne vyhľadávací reťazec
- Polčas rozpadu bol prístup pri ktorom sa využíval vzorec polšasu rozpadu ktorý bral 
    dva parametre, jeden reprezentujúci silu aktuálneho záujmu a druhý reprezentujúci
    rýchlosť jeho rozkladu

## Doména

Prácu som sa rozhodol riešiť v doméne hudobných dokumentov, teda dokumentov
ktoré pomáhajú hudobníkom reprodukovať hudobné dielo. Mohol by som uviesť napríklad
noty, ale akurát k tým som sa nejak moc nedostal, spracovával som hlavne
- texty
- taby a
- akordy.

Pripravil som si príklad tabov, je to špecializovaný formát pre gitary,
kde čiary reprezentujú struny na gitare a čísla sú pozície začínajúce nulov,
, číslované od hlavy (tam kde sú kolíky, nejak naznač).

Ďaľší príklad je oakordovaný text, kde sú do textu doplnené akordy.

## Platforma

Zvolil som prostredie webovskej aplikácie implementovanej v štandardných webových jazykoch a frameworkoch.
Čo sa týka backendu som zvolil Yii2 php framework, správu verzíí som robil pomocov git-u a závyslosti
knižníc pomocov nástroja composer.

Aplikácie je schopná spolupracovať či už z MySQL alebo PostgreSQL databázov. Celkom vtipná príhoda
môžem povedať neskôr.

## Ukladanie dát

Keďže sa mi nepodarilo datový model nejak zmenšiť aby ból čítateľný, tak som to poňal abstraktne.

Dokument je reprezentovaný množinov značiek, alebo inak povedané slóv ktoré reprezentujú jeho obsah.

Používateľ je reprezentovaný históriou, teda usporiadaným zoznamom značiek, ktoré sa nachádzali 
v dokumentoch ktoré navštívil.

## Získavanie dát

Dáta som získaval parsovaním zo stránky supermusic.sk. z tejto stránky som získal
dokumenty, tak isto z ich názvu a interpréta som spracovával nejaké značky.

Tieto značky som doplnil značkamy ktoré som získal z webovskej služby last.fm,
táto služba už funguje niekoľko rokov, jej princípom je, že umožňuje jej používateľom
pridávať značky k hudobným dielam.

## Štruktúra aplikácie

Na tomto obrázku môžeme vydieť štruktúru navrhovanej aplikácie. Čo som ešte nespomenul 
je vyhľadávacií modul, aplikácia poskytuje aj možnosť vyhľadávania dokumentov.

## Process zíkavania dát

Na obrázku môžeme vydieť spôsob, akým postupuje aplikácia keď zíkava nové dokumenty.

Najskôr sa vyhľadávajú interpréti kôli konzistencií databázy.

Aby sa viac násobne nestiahli tie isté dokumenty tak najskôr sa porovnáva zoznam 
dokumentov z databázov.

Generovanie značiek z vlastnosti dokumentu.

Následne sa pre daný dokument doplnia značky z last.fm pomocou REST api. Nakoniec sa značky ováhujú.

## Generovanie značiek z vlastnosí dokumentu

Medzi značky sa pridá názov dokumentu názov interpréta typ dokumentu slová z názvu dokumentu
slová z názvu interpreta a last.fm značky. Slovách z názvu dokumentu a názvu interpréta sa 
aplikujú tzv. stopwords. To sú slová ktoré nenesú význam a používam pevne daný zoznam 
stopwords, je to jeden z projektov google ktorý si kladie za účela takéto slová zozbierať.

Používam kombináciu slovenských, českých a anglických stopwords.

## Váhovanie značiek

Ďalej vám poviem niečo ku váhovaniu značiek.

Značky sa váhovali podľa nasledujúceho vzorca, kde w_d je sila d-tej zančky pre aktuálny dokument.
`prečítaj slajd`

Vzorec sa v podstate skladá z troch častí:

Ako prvá je základná časť, ktorá určuje váhu pojmu.
V strede je normalizačný faktor, ktorého úlohou je zabezpečiť že ak je počet unikátnych značiek v dokumente kratší
ako priemerný počet unikátnych značiek, relevancia dokumentu stúpa.
Na konci je inverzná frekvencia, ktorá zabezpečuje že menej časté značky majú vyššiu relevanciu.

## Referenčné algoritmy

Podobnosť dokumentov, tento algoritmus vracia najpodobnejší dokument na základe porovnania 
značiek a ich váh z ostatnými dokumentamy. Tento spôsob porovnávania je zavyslí na tom
aby bol aktulne zobrazený nejaký dokument.

Agregácia znamená len že -- prečítaj slide

## 
