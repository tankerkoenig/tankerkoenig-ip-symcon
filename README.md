# Echtzeit-SpritpreiseScript auf IP-Symcon Smarthome-Software

## API unter Open-Data-Lizenz
Tankerkönig.de betreibt unter https://creativecommons.tankerkoenig.de ein freies API für Echtzeit-Spritpreise an deutschen Tankstellen. Aktuelle Spritpreise können über die IP-Symcon-Smarthome-Lösung angezeigt werden.

Das Tankerkönig-API steht unter einer Open-Data-Lizenz, es ist lediglich ein API-Key nötig, der unter https://creativecommons.tankerkoenig.de#register abgerufen (und im Script eingetragen) werden muss.

## Script aus dem Symcon-Forum
Das Script stammt aus dem Symcon-Forum und wird hier mit Erlaubnis des Autors veröffentlicht.

## History
Siehe symcon-Forum: https://www.symcon.de/forum/threads/28346-Tankerkoenig-de-%28Spritpreise-mit-Umkreissuche-oder-Detailabfrage%29

v1.4 > Fehler bei der Datenabfrage werden jetzt abgefangen, keine Liste von Folgefehlern mehr, Fehlerausgabe in Log + Meldungen Fenster
v1.5 > Umkreissuche erweitert um Ausgabe der Tankstelle mit dem niedrigsten Preis (wenn mehrere gleich günstig sind, dann wird die mit der kleinsten Distanz ausgegeben)
v1.6 > Bei der Tankstelle mit dem günstigsten Preis wird jetzt mit überprüft, ob die Tankstelle überhaupt geöffnet hat, ansonsten wird eine geöffnete Alternative (nächst höherer Preis/Entfernung) ausgegeben. Zusätzlich werden noch die Öffnungszeiten und Geöffnet/Geschlossen in Variablen ausgegeben.


Links
- https://www.symcon.de/forum/threads/28346-Tankerkoenig-de-(Spritpreise-mit-Umkreissuche-oder-Detailabfrage)
- https://creativecommons.tankerkoenig.de#register
- http://www.tankerkoenig.de
- http://blog.tankerkoenig.de
