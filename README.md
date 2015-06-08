
QUIQQER Continuous Integration
========

Dokumentationsgenerierung und Continuous Integration für dein PHP Projekt.


Packetname:

    quiqqer/quiqqerci


Features
--------

- Documentationsgenerierung (phpdox)
- Continuous Integration
- PHPUnit
- Copy/Paste Detector
- Static code analysis (PHPDepend)
- Code Metriken


Installation
------------

Bevor Sie das Paket installieren müssen folgende Voraussetzungen gegeben sein:

Voraussetzungen:

- php5-xsl 
- php5-xdebug 
- graphviz 
- php5-intl
- system() shell_exec() muss erlaubt sein
- git

Installation via APT (Ubuntu/Debian):

```bash
sudo apt-get install php5-xsl php5-xdebug graphviz php5-intl libpcre3-dev php-pear
```

Der Paketname ist: quiqqer/quiqqerci

*Hinweis*
Wenn Xdebug auf der CLI nicht verfügbar ist, dann muss dies via PECL werden. :-/

```bash
sudo apt-get install php5-dev
sudo pecl install xdebug

sudo ln -s /etc/php5/mods-available/xdebug.ini /etc/php5/apache2/conf.d/20-xdebug.ini
sudo ln -s /etc/php5/mods-available/xdebug.ini /etc/php5/cli/conf.d/20-xdebug.ini
```

Aktualisierung
------------

Die Installation und die Aktualisierung sollten über die Konsole / Bash ausgeführt werden.
Das Aktualisierung aller Abhängigkeiten kann seine Zeit dauern und bei den meisten Webserver werden somit Timeouts geworfen die das Akutlaiiseren abbrechen.


Mitwirken
----------

- Issue Tracker: https://dev.quiqqer.com/quiqqer/quiqqer-ci/issues
- Source Code: https://dev.quiqqer.com/quiqqer/quiqqer-ci/


Support
-------

Falls Sie ein Fehler gefunden haben oder Verbesserungen wünschen,
Dann können Sie gerne an support@pcsg.de eine E-Mail schreiben.


License
-------


QUIQQER-CI übr die Konsole verwenden
--------

### Projekt anlegen

```
sudo -u www-data php quiqqer.php --username= --password= --tool=quiqqer:quiqqerci --add=git@***.git
```

### Test für Projekt verfügbar machen

Zum Beispiel: phpdox

```
sudo -u www-data php quiqqer.php --username= --password= --tool=quiqqer:quiqqerci-project --ci-project=0 --enable=phpdox
```
