# Heise-Bewerbungsaufgaben Teil 1: News-Artikel-Webservice (Backend)

## Einrichten der Entwicklungsumgebung
Die Entwicklungsumgebung besteht aus einem LEMP-Stack in Form von Docker-Containern und kann gestartet werden über
`docker-compose up`. Der Stack besteht aus folgenden Diensten:

- nginx: Der Webserver bezieht seine config beim Buildvorgang aus der `docker/nginx/nginx.conf`. Diese leitet alle eingehenden Anfragen, welche nicht 
    auf eine explizite php-Datei gehen, auf die index.php und somit den Einstiegspunkt der Applikation weiter
- php-fpm: Als Interpreter wird ein php-fpm der Version 8.0 verwendet. Beim Buildvorgang werden alle Abhängigkeiten automatisch über composer installiert
- MariaDB: Als relationale Datenbank wird MariaDB verwendet. Die Datenbank, User und Passwort werden über die Environment-Variablen in der `docker-compose.yml` automatisch angelegt.
    Beim ersten Start des Containers werden alle sql-Dateien in `data/sql` ausgeführt und so alle benötigten Tabellen erstellt

Zur besseren Verwaltung der Datenbank steht zusätzlich ein phpmyadmin-Container zur Verfügung, welcher über Port `8081` erreichbar ist

## Verwendetes Framework
Als Framework wird Slim 4 verwendet. Slim ist, wie der Name bereits sagt, ein schlankes PHP-Framework, welches einfache Mittel zum Routing und Behandeln von
http-Requests bereitstellt. Die Routen sind in der `app/routes.php` konfiguriert und verweisen auf einen Handler als Callable. An diesen Handler wird der Request
dann vom Framework weitergeleitet. Der Handler stellt somit den Einstiegspunkt in die Geschäftslogik für die entsprechende Route dar.

Dependency-Injection wird durch die Bibliothek php-di realisiert. Die Konfiguration zum Auflösen der Abhängigkeiten befindet sich in der Klasse `ApplicationConfig`.
Allerdings kann php-di die meisten Abhängigkeiten durch Reflection-Klassen automatisch auflösen, sodass in den meisten Fällen weder manuelle Konfiguration noch das Erstellen
einer Factory-Klasse notwendig sind.

## Codestruktur
Der Code der Applikation befindet sich im Ordner `src` und ist dort aufgeteilt in `Handler`, `MySQL`, `Repository` und `Value`.
In dem Ordner `Handler` befinden sich die im obigen Abschnitt beschriebenen Handler-Klassen.
Im Ordner `MySQL` sind Klassen zum Erstellen der Verbindung zur Datenbank.
Unter `Repository` werden Klassen eingeordnet, die nach dem [Repository-Pattern](https://de.wikipedia.org/wiki/Repository_(Entwurfsmuster)) die Zugriffe
auf die Datenbank kapseln.
`Value`-Klassen dienen der [Kapselung Domänenspezifischer Werte](https://dev.to/ianrodrigues/writing-value-objects-in-php-4acg) innerhalb der Applikation (z.B. des Titels).
Die Klassen im Ordner `Entity` stellen Entities, also Werte beinhaltende Objekte mit einem einzigartigen Identifier dar. Die Klasse `NewsArticle` dient konkret zur Repräsentation
einer Zeile in der Datenbank.

Die Konfiguration für Routen und Middleware befindet sich im Verzeichnis `app`.

## Unit-Tests
Das Projekt wird mit PHPUnit getestet. Die Unit-Tests befinden sich im Verzeichnis `tests`, welches genau so strukturiert ist wie das
`src`-Verzeichnis.
Die Tests können mit dem Befehl `docker-compose run --rm php-web composer test --migrate-configuration` ausgeführt werden.
