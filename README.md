# Sponsorenverwaltung

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