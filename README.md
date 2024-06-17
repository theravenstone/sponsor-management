# Sponsorenverwaltung

## Aufgabenstellung

### Block 1: Entwicklung einer Website zur Verwaltung von Sponsoren

Wir benötigen für ein Projekt eine Administrationswebseite, bei der zulässige Benutzer Sponsoren anlegen, editieren und auch sperren/archivieren können.

Daten, die bei der Erfassung der Sponsoren relevant sind:

- Interne Kundennummer (wird vom Mitarbeiter vergeben, eine Kundennummer kann mehreren Sponsoren zugeordnet werden, nachträgliche Änderungen sind nicht möglich)
- Firme
- Name, Vorname des Ansprechpartners
- Straße, PLZ, Ort
- Land (Auswahl momentan beschränkt auf DE und AT)
- USt-ID (hier sollte eine Validierung nach den Standards abhängig vom Land erfolgen, eine Onlineprüfung ist nicht notwendig)
- Telefonnummer des Ansprechpartners
- E-Mail des Ansprechpartners
- E-Mail für Rechnungen
- Voraussichtliches monatliches Budget in Euro
- Upload eines Logos

Bitte erstelle ein Datenbankmodell für diese Daten. Berücksichtige ebenso eine Benutzertabelle, Benutzer werden allerdings nur in der Datenbank gepflegt - die Tabelle muss jedoch für den Login zur Webseite genutzt werden. Dieser soll aktuellen Standards entsprechen (Passwörter in Datenbank, Passwort vergessen-Funktion).

Für die Pflege der Sponsoren soll zudem in der Datenbank ein Journal geführt werden: Dort muss nachvollzogen werden, wann welcher Nutzer welchen Sponsor angelegt oder verändert hat. Die Änderungen selbst müssen dabei nicht protokolliert werden.

Auf der Webseite sollen nur die Zeitstempel der Anlage und der letzten Änderung angezeigt werden.

Für die Übersicht der bestehenden Sponsoren soll in der Tabelle die interne Kundennummer, der Name des Unternehmens, der Ansprechpartner sowie das Logo angezeigt werden. Die Übersicht soll dabei paginierbar sein, die Seitengröße soll über eine Konfigurationsdatei einstellbar sein - eine Anpassung an die Größe des Viewports soll vorerst nicht erfolgen.

In der Übersicht soll zudem eine Suche nach Kundennummer, Unternehmensname und Ansprechpartner möglich sein. Dabei soll es sich nur um ein singuläres Suchfeld handeln.

Bestehende Sponsoren müssen über ein Attribut sperrbar sein, zudem müssen Sponsoren auch archivierbar sein. Archivierte Sponsoren werden in der Tabelle nicht mehr angezeigt und können auch nicht editiert werden. Dies muss unter allen Umständen verhindert werden.

### Block 2: API

Stelle für oben entworfene Webseite eine API zur Verfügung, bei der entweder alle Sponsoren (unter Berücksichtigung einer Paginiert) als JSON mit allen Eckdaten zurückgegeben werden. Zudem soll es möglich sein, über eine interne Kundennummer alle entsprechenden Sponsoren zu ermitteln.

Zeige auf, wie sich eine solche API gegen unbefugte Zugriffe schützen lässt - diese Absicherung muss jedoch noch nicht implementiert werden.

- API-Schlüssel, OAuth, JWT (JSON Web Token) oder IP-Whitelisting
- Eingehenden Daten validieren, um SQL-Injektionen und andere Sicherheitsprobleme zu vermeiden. Dies kann durch Prepared Statements und Parameterbindung erfolgen.
- API-Aufrufe protokollieren und die Nutzung überwachen, um ungewöhnliche oder verdächtige Aktivitäten frühzeitig zu erkennen.

## Datenbankstruktur

### Users
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Sponsors
```sql
CREATE TABLE sponsors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_number VARCHAR(50) NOT NULL,
    firm_name VARCHAR(255) NOT NULL,
    contact_last_name VARCHAR(255) NOT NULL,
    contact_first_name VARCHAR(255) NOT NULL,
    street VARCHAR(255) NOT NULL,
    plz VARCHAR(10) NOT NULL,
    city VARCHAR(255) NOT NULL,
    country ENUM('DE', 'AT') NOT NULL,
    ust_id VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    email_contact VARCHAR(255),
    email_billing VARCHAR(255),
    budget INT,
    logo_path VARCHAR(255),
    archived BOOLEAN DEFAULT FALSE,
    locked BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### Logs
```sql
CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    sponsor_id INT NOT NULL,
    action VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_id FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_sponsor_id FOREIGN KEY (sponsor_id) REFERENCES sponsors(id)
);
```

## API

### Alle Sponsoren paginiert abrufen:
```bash
GET /api.php?page=1&perPage=10
```

Rückgabe
```json
{
    "total_pages": 3,
    "current_page": 1,
    "data": [ {...}, {...}, ... ]
}
```

### Sponsoren anhand der Kundennummer abrufen:
```bash
GET /api.php?customer_number=ABC123
```

Rückgabe
```json
[
    {
        "id": 1,
        "customer_number": "ABC123",
        "firm_name": "Beispiel GmbH",
        ...
    }
]
```