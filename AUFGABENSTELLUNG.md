## Block 1: Entwicklung einer Website zur Verwaltung von Sponsoren

Wir benötigen für ein Projekt eine Administrationswebseite, bei der zulässige Benutzer Sponsoren anlegen, editieren und auch sperren/archivieren können.

Daten, die bei der Erfassung der Sponsoren relevant sind:

- Interne Kundennummer (wird vom Mitarbeiter vergeben, eine Kundennummer kann mehreren Sponsoren zugeordnet werden, nachträgliche Änderungen sind nicht möglich)
- Firme
- Name, Vorname des Ansprechpartners
- Straße, PLZ, Ort
- Land (Auswahl momentan beschränkt auf DE und AT)
- USt-ID (hier sollte eine Validierung nach den Standards abhängig vom Land erfolgen, eine Onlineprüfung ist nicht notwendig)
- Telefonnummer des Ansprechpartners
- eMail des Ansprechpartners
- eMail für Rechnungen
- Voraussichtliches monatliches Budget in Euro
- Upload eines Logos

Bitte erstelle ein Datenbankmodell für diese Daten. Berücksichtige ebenso eine Benutzertabelle, Benutzer werden allerdings nur in der Datenbank gepflegt - die Tabelle muss jedoch für den Login zur Webseite genutzt werden. Dieser soll aktuellen Standards entsprechen (Passwörter in Datenbank, Passwort vergessen-Funktion).

Für die Pflege der Sponsoren soll zudem in der Datenbank ein Journal geführt werden: Dort muss nachvollzogen werden, wann welcher Nutzer welchen Sponsor angelegt oder verändert hat. Die Änderungen selbst müssen dabei nicht protokolliert werden.

Auf der Webseite sollen nur die Zeitstempel der Anlage und der letzten Änderung angezeigt werden.

Für die Übersicht der bestehenden Sponsoren soll in der Tabelle die interne Kundennummer, der Name des Unternehmens, der Ansprechpartner sowie das Logo angezeigt werden. Die Übersicht soll dabei paginierbar sein, die Seitengröße soll über eine Konfigurationsdatei einstellbar sein - eine Anpassung an die Größe des Viewports soll vorerst nicht erfolgen.

In der Übersicht soll zudem eine Suche nach Kundennummer, Unternehmensname und Ansprechpartner möglich sein. Dabei soll es sich nur um ein singuläres Suchfeld handeln.

Bestehende Sponsoren müssen über ein Attribut sperrbar sein, zudem müssen Sponsoren auch archivierbar sein. Archivierte Sponsoren werden in der Tabelle nicht mehr angezeigt und können auch nicht editiert werden. Dies muss unter allen Umständen verhindert werden.

## Block 2: API

Stelle für oben entworfene Webseite eine API zur Verfügung, bei der entweder alle Sponsoren (unter Berücksichtigung einer Paginiert) als JSON mit allen Eckdaten zurückgegeben werden. Zudem soll es möglich sein, über eine interne Kundennummer alle entsprechenden Sponsoren zu ermitteln.

Zeige auf, wie sich eine solche API gegen unbefugte Zugriffe schützen lässt - diese Absicherung muss jedoch noch nicht implementiert werden.

- API-Schlüssel, OAuth, JWT (JSON Web Token) oder IP-Whitelisting
- Eingehenden Daten validieren, um SQL-Injektionen und andere Sicherheitsprobleme zu vermeiden. Dies kann durch Prepared Statements und Parameterbindung erfolgen.
- API-Aufrufe protokollieren und die Nutzung überwachen, um ungewöhnliche oder verdächtige Aktivitäten frühzeitig zu erkennen.