# Echtzeit-SpritpreiseScript auf IP-Symcon Smarthome-Software

## Aktuell gibt es keinen Code für dieses System.

Falls jemand etwas beitragen will: sehr gerne. Der Code sollte den Nutzungsbedingungen genügen:

Falls jemand etwas beitragen will: sehr gerne. Der Code sollte den Nutzungsbedingungen genügen:

- Keine Verwendung von Detailabfrage (detail.php) für Preis-Updates. Statt dessen Listenabfrage (list.php), oder - besser - prices.php (siehe API-Doku https://creativecommons.tankerkoenig.de)

- Intervall 10 Minuten + plus ein paar Sekunden, zur Vermeidung von Lastspitzen

- Auswertung des ok-Flags der Antwort:
  - Anzeige der Fehlermeldung, falls ok == false
  - Keine weiteren Abfragen, wenn ok == false

- Anzeige des Offen-Status

- keine Veröffentlichung von API-Keys - statt dessen den Hinweis wie man einen Key beantragt


Code bitte an info@tankerkoenig.de
