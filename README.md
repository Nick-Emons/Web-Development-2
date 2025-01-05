# League of Legends Champion Management

Deze applicatie is een platform voor het beheren van League of Legends-champions en het personaliseren van favorieten door gebruikers. Het biedt zowel een gebruikersinterface als een admin dashboard om de gebruikerservaring te beheren.

## Link naar website


## Beschikbare accounts
E-mail:     admin@account.com
Wachtwoord: Test123

E-mail:     user@account.com
Wachtwoord: Test123

## Functionaliteiten
1. **Champion management**
- Champions kunnen worden geïmporteerd vanuit een externe API.
- Gebruikers kunnen champions bekijken, en hun gedetailleerde gegevens ophalen. Denk hierbij aan champion beschrijvingen, skins en abilities.
- De champion list kan ook door een filter kleiner gemaakt worden. Op deze manier kan je via de naam van de champion, snel de champion binnen de lijst vinden.
    - Het integreren van externe API's weerspiegelt praktische scenario's waarin gegevens van externe services worden opgehaald en verwerkt.
    - Maakt gebruik van CRUD functionaliteit. Dit maakt het beheer flexibel en gebruiksvriendelijk.

2. **Favorieten functionaliteit**
- Gebruikers kunnen champions aan hun favorieten lijst toevoegen of deze weer verwijderen.
- Favorieten worden per gebruiker opgeslagen, zodat ze gepersonaliseerd zijn. Hierdoor kan elke gebruiker zijn eigen favorieten bepalen.
- "Show Favorites" button toegevoegd, zodat de gebruiker overzichtelijk kan zien, welke favorieten er in zijn of haar lijst zitten. 
    - Gebruikers verwachten gepersonaliseerde ervaringen in moderne applicaties, zoals het opslaan van voorkeursitems.
    - Het koppelen van data aan specifieke gebruikers versterkt de relevantie van de applicatie.

3. **Admin functionaliteit**
- Gebruikers met de 'admin' role kunnen het admin center openen (soort CMS), waarbij zij alle bestaande gebruikers kunnen zien. Daarbij kunnen zij gegevens van deze gebruikers aanpassen, zoals de naam of e-mail en de favorieten per gebruiker bekijken.
- Het CMS heeft vrijwel geen opmaak, om het puur functioneel, maar wel duidelijk te houden.

4. **Authenticatie functionaliteit**
- Er wordt gebruik gemaakt van JWT-tokens om gebruikers in te kunnen loggen, te registreren en tokens te kunnen refreshen.
- Zonder JWT-token, kom je de applicatie niet in en krijg je een foutmelding
- De tokens worden opgeslagen in een LocalStorage tijdens een sessie. Hiermee wordt binnen het CMS ook gekeken, naar welke gebruiker ingelogd is. Dit zorgt ervoor dat de gebruiker zijn eigen role niet aan kan passen. 

5. **Database beheer**
- Maakt gebruik van een relationele database (MySQL) voor het opslaan van gebruikers, champions en favorieten.
- Migraties en seeders worden gebruikt om de database dynamisch op te zetten en aan te vullen.
    - Migraties en seeders zorgen ervoor dat het project eenvoudig geïnstalleerd kan worden in verschillende omgevingen.

6. **Externe API-Integratie**
- De applicatie haalt data over champions op via een externe League of Legends API.
- Verzamelde data wordt in de database opgeslagen en lokaal toegankelijk gemaakt. Tijdens het ophalen van de data, kijkt het systeem eerst of er al data in de database beschikbaar is, zodat er geen onnodige calls naar de externe api gemaakt hoeven te worden. Elke 'eerste' call van de ingelogde sessie, wordt de call wel naar de externe API gemaakt, zodat de data up to date blijft.

7. **Beveiliging**
- Wachtwoorden worden gehast in de database neergezet voor veilige opslag.
- Gevoelige routes zijn beveiligd met middelware die JWT-tokens valideert.
- Externe API-aanroepen worden alleen gedaan als dat nodig is, door het opslaan van de data. 
    

## Hoe de code is gestructureerd:

- **app/**: Bevat de backend logica, zoals controllers, modellen, services en middleware.
- **frontend/src/components**: De frontend-templates worden hier bewaard.
- **routes/api.php**: Hier worden alle routes van de applicatie gedefinieerd.
- **database/migrations/**: Hier vind je de migraties voor het opzetten van de database. (Let op: De database is al ingesteld en gevuld op de gehoste server.)

## CSS Framework
Binnen deze applicatie is er geen gebruik gemaakt van een specifiek CSS framework. Het bestaat uit Custom css, wat door de hele applicatie terugkomt. Hierdoor is een eigen thema ontstaan, die bij het thema League of Legends past.

De applicatie is responsive. Dit zorgt ervoor dat de applicatie zich zo aanpast, dat het voor verschillende schermgroottes nog steeds gebruikersvriendelijk blijft.

## Frontend architecture
### Routing
In de frontend is de routing en state management goed geïntegreerd om de verschillende delen van de applicatie te beheren. De routing is opgezet met **Vue Router**, terwijl de state wordt beheerd met behulp van Vue's reactive data en localStorage voor persistente gegevens.

De Vue Router zorgt voor het navigeren tussen de verschillende pagina's binnen de applicatie. De router is te vinden in de **src/router/index.js** en bestaat uit routes zoals de lijst van champions, de detailpagina van een champion, login, registratie en het admin center.

De routing is beveiligd door middel van route guards, waarbij gecontroleerd wordt of een gebruiker geauthenticeerd is voordat toegang wordt verleend tot bepaalde pagina's (zoals het admin-gedeelte).

In de routes is er een voorwaardelijke controle of een gebruiker een admin is, voordat toegang wordt gegeven tot het admin center. Als de gebruiker geen admin is, wordt hij doorgestuurd naar de homepage. Het beveiligingsmechanisme zorgt ervoor dat ongeautoriseerde toegang wordt voorkomen.

### State Management
De state binnen de frontend-applicatie wordt beheerd door reactive data in Vue-componenten, zoals in **src/components/ChampionList.vue**. De state wordt eenvoudig beheerd door het gebruik van componentdata, die automatisch wordt geüpdatet wanneer de gebruiker interactie heeft met de applicatie.

Favorites worden lokaal opgeslagen in de localStorage, wat ervoor zorgt dat de favoriete champions van de gebruiker tussen sessies bewaard blijven. Als er een geldige JWT-token is, wordt daarnaast de favorietenlijst van de gebruiker opgehaald van de backend via de API.

### API Communicatie
De communicatie tussen de frontend en de backend gebeurt via Axios, een HTTP-client die in **src/utils/axios.js** is geconfigureerd. De configuratie zorgt ervoor dat bij iedere API-aanroep de JWT-token wordt meegestuurd in de request headers, zodat beveiligde endpoints kunnen worden benaderd. Dit wordt mogelijk gemaakt door request interceptors in Axios, die automatisch de token toevoegen aan de headers van elke request.

Wanneer een request naar de backend wordt gedaan, bijvoorbeeld om de lijst van champions op te halen, gaat dit via de **app/Http/Controllers/ChampionController.php** in Laravel. Deze controller handelt het ophalen van champions uit de database of via een externe API af. De communicatie met de backend gebeurt via routes die gedefinieerd zijn in **routes/api.php**. Wanneer een gebruiker interactie heeft met de app, bijvoorbeeld door een champion als favoriet te markeren, worden de juiste API-endpoints aangesproken voor het toevoegen of verwijderen van favorieten. Deze API-aanroepen worden in de Vue-componenten verwerkt, bijvoorbeeld in de **src/components/ChampionList.vue**.

### Bestanden die Routing en State Management Beheren:
**src/router/index.js** - Beheert de routing van de applicatie.
**src/components/ChampionList.vue** - Beheert de lijst van champions en toont de favorieten.
**src/utils/axios.js** - Beheert de configuratie van Axios voor API-aanroepen.
**routes/api.php** (Laravel) - Definieert de routes voor de API in de backend, zoals het ophalen van champions en het beheren van favorieten.

De communicatie tussen de frontend en de backend gebeurt via deze gedefinieerde routes en gebruik van de Axios-client, terwijl de routing en state management in Vue.js zelf wordt afgehandeld door Vue Router en reactive data.

## Rest API
### Filtering
De API ondersteunt filtering door het mogelijk te maken om specifieke data op te halen, bijvoorbeeld via een ID. Bij het ophalen van gegevens van een champion kan een ID worden meegegeven in de API-aanroep om enkel de informatie van die specifieke champion te verkrijgen. Dit zorgt ervoor dat de server alleen relevante gegevens terugstuurt. Dit is onder andere terug te vinden binnen de volgende files: **app/Http/Controllers/ChampionController.php** en **app/Http/Services/ChampionService.php**. Kijk hierbij naar de **getChampion** en **getChampionDetailFromApi** functies. Binnen het **ChampionList** component kan er gebruikt worden gemaakt van filtering op de champion list. Door in de zoekbalk een deel van een naam in te vullen, zul je alle champions krijgen, die aan de zoekterm voldoen. Kijk in **src/components/ChampionList.vue** naar de volgende computed function: **filteredChampions**. Dit stuurt de <input> aan op code line 20 om op champions te kunnen filteren.

### Error handling
Bij elke API aanroep in de service laag, worden error messages gegeven, mits de API aanroep fout gaat. De errors worden dan in de frontend weergegeven, zoals bij het invullen van verkeerde inloggegevens. **app/Http/Services/AuthService** laat dit in de **login** functie goed zien. De error message wordt aangeroepen op code line 55 binnen de **src/components/Login.vue** file.

### Pagination
Ik heb binnen de applicatie geen gebruik gemaakt van pagination, omdat ik vind dat de champion lijst door middel van filteren en favorieten toevoegen/verwijderen nog steeds overzichtelijk is en uit eigen ervaring weet ik dat er niet zomaar 20 nieuwe champions bij komen, waardoor de pagina mogelijk te vol wordt. Een nieuwe champion komt geleidelijk over een tijd van maanden. 

 In de frontend kan de zoekfunctie, die in Vue is geïmplementeerd, worden beschouwd als een client-side filtering. Deze zoekfunctie filtert de lijst van champions op basis van de zoekterm die de gebruiker invoert.

## Authentication
De applicatie maakt gebruik van Role Based Access Control. Bij het registreren van een gebruiker wordt er een rol toegewezen. Als er nog geen gebruiker in de database aanwezig is, wordt de eerste gebruiker in dit geval automatisch Admin. De rest krijg de User role. Gebruikers met de role Admin hebben een eigen CMS, waarbij zij alle geregistreerde gebruikers kunnen zien. Hierbij kunnen zij andere gebruikers hun role aanpassen naar Admin of terug naar User. De ingelogde user kan zijn eigen Admin role niet aanpassen. Door middel van middleware zijn specifieke routes beperkt op basis van een role. Op deze manier hebben gebruikers met de Admin role, meer mogelijkheden binnen de applicatie, dan gebruikers met de standaard User role. Binnen de **register** functie binnen de **app/Http/Services/AuthService** file, kan worden gezien dat de role wordt toegewezen. In de **app/Models/User** file kan je zien dat **role** een eigenschap is van een user. In de **frontend/src/router/index.js** kan je zien dat de /admin route de eigenschap **requiresAdmin** heeft. Verder in dezelfde file zie je hoe dit gecontroleerd wordt.

## SQL-creatie script
-- Create the database (adjust the database name as needed)
CREATE DATABASE IF NOT EXISTS `league_of_legends` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the newly created database
USE `league_of_legends`;

-- Create 'users' table
CREATE TABLE `users` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL
);

-- Create 'champions' table
CREATE TABLE `champions` (
    `id` VARCHAR(255) PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `blurb` TEXT NOT NULL,
    `image` VARCHAR(255) NOT NULL,
    `lore` TEXT DEFAULT NULL,
    `tags` JSON DEFAULT NULL,
    `info` JSON DEFAULT NULL,
    `stats` JSON DEFAULT NULL,
    `spells` JSON DEFAULT NULL,
    `passive` JSON DEFAULT NULL,
    `skins` JSON DEFAULT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL
);

-- Create 'favorites' table
CREATE TABLE `favorites` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `champion_id` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY `unique_favorite` (`user_id`, `champion_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`champion_id`) REFERENCES `champions`(`id`) ON DELETE CASCADE
);