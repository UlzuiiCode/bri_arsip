# Entity Relationship Diagram (ERD) - SiMArsip

Diagram ini menggambarkan struktur database dan relasi antar tabel pada Sistem Informasi Manajemen Arsip.

---

## ERD

```mermaid
erDiagram
    USERS ||--o{ DOCUMENTS : "uploads (uploaded_by)"
    USERS ||--o{ ACTIVITY_LOGS : "performs (user_id)"
    USERS ||--o{ LOGIN_ATTEMPTS : "records (email)"
    CATEGORIES ||--o{ DOCUMENTS : "categorizes (category_id)"

    USERS {
        int id PK "AUTO_INCREMENT"
        varchar nama "NOT NULL"
        varchar email "UNIQUE, NOT NULL"
        varchar password "NOT NULL, bcrypt hash"
        enum role "admin | pegawai"
        enum status "pending | approved"
        timestamp created_at "DEFAULT CURRENT_TIMESTAMP"
        timestamp updated_at "ON UPDATE CURRENT_TIMESTAMP"
    }

    CATEGORIES {
        int id PK "AUTO_INCREMENT"
        varchar nama "UNIQUE, NOT NULL"
        text deskripsi "nullable"
        timestamp created_at "DEFAULT CURRENT_TIMESTAMP"
        timestamp updated_at "ON UPDATE CURRENT_TIMESTAMP"
    }

    DOCUMENTS {
        int id PK "AUTO_INCREMENT"
        varchar judul "NOT NULL"
        text deskripsi "nullable"
        varchar file_path "NOT NULL, nama file di server"
        varchar file_name "NOT NULL, nama asli dari user"
        int file_size "nullable, ukuran dalam bytes"
        varchar file_type "nullable, MIME type"
        int category_id FK "nullable, ON DELETE SET NULL"
        int uploaded_by FK "nullable, ON DELETE SET NULL"
        decimal nominal "nullable, nilai transaksi"
        varchar pihak_terkait "nullable"
        date tanggal_transaksi "nullable"
        timestamp deleted_at "nullable, NULL = aktif"
        timestamp created_at "DEFAULT CURRENT_TIMESTAMP"
        timestamp updated_at "ON UPDATE CURRENT_TIMESTAMP"
    }

    ACTIVITY_LOGS {
        bigint id PK "AUTO_INCREMENT"
        int user_id FK "nullable, ON DELETE SET NULL"
        varchar action "NOT NULL (LOGIN, UPLOAD, dll)"
        text description "nullable"
        varchar ip_address "nullable, IPv4/IPv6"
        timestamp created_at "DEFAULT CURRENT_TIMESTAMP"
    }

    LOGIN_ATTEMPTS {
        bigint id PK "AUTO_INCREMENT"
        varchar email "NOT NULL"
        varchar ip_address "NOT NULL"
        timestamp attempted_at "DEFAULT CURRENT_TIMESTAMP"
    }
```

---

### PlantUML: ERD

```plantuml
@startuml
title Entity Relationship Diagram - SiMArsip

skinparam linetype ortho

entity "USERS" as users {
    * **id** : INT <<PK>>
    --
    * nama : VARCHAR(150)
    * email : VARCHAR(255) <<UNIQUE>>
    * password : VARCHAR(255)
    * role : ENUM('admin','pegawai')
    * status : ENUM('pending','approved')
    * created_at : TIMESTAMP
    * updated_at : TIMESTAMP
}

entity "CATEGORIES" as categories {
    * **id** : INT <<PK>>
    --
    * nama : VARCHAR(100) <<UNIQUE>>
    deskripsi : TEXT
    * created_at : TIMESTAMP
    * updated_at : TIMESTAMP
}

entity "DOCUMENTS" as documents {
    * **id** : INT <<PK>>
    --
    * judul : VARCHAR(255)
    deskripsi : TEXT
    * file_path : VARCHAR(500)
    * file_name : VARCHAR(255)
    file_size : INT
    file_type : VARCHAR(100)
    category_id : INT <<FK>>
    uploaded_by : INT <<FK>>
    nominal : DECIMAL(20,2)
    pihak_terkait : VARCHAR(255)
    deleted_at : TIMESTAMP
    * created_at : TIMESTAMP
    * updated_at : TIMESTAMP
}

entity "ACTIVITY_LOGS" as logs {
    * **id** : BIGINT <<PK>>
    --
    user_id : INT <<FK>>
    * action : VARCHAR(100)
    description : TEXT
    ip_address : VARCHAR(45)
    * created_at : TIMESTAMP
}

entity "LOGIN_ATTEMPTS" as attempts {
    * **id** : BIGINT <<PK>>
    --
    * email : VARCHAR(255)
    * ip_address : VARCHAR(45)
    * attempted_at : TIMESTAMP
}

users ||--o{ documents : "uploads\n(uploaded_by)"
users ||--o{ logs : "performs\n(user_id)"
users ||--o{ attempts : "records\n(email)"
categories ||--o{ documents : "categorizes\n(category_id)"

@enduml
```

---

## Penjelasan Relasi

| Relasi | Tipe | Keterangan |
|--------|------|------------|
| **USERS → DOCUMENTS** | One-to-Many | Satu user dapat mengunggah banyak dokumen. FK: `uploaded_by` → `users.id`. ON DELETE SET NULL. |
| **USERS → ACTIVITY_LOGS** | One-to-Many | Satu user dapat memiliki banyak catatan log aktivitas. FK: `user_id` → `users.id`. ON DELETE SET NULL. |
| **USERS → LOGIN_ATTEMPTS** | One-to-Many | Satu email user dapat memiliki banyak catatan percobaan login. Relasi berdasarkan kolom `email`. |
| **CATEGORIES → DOCUMENTS** | One-to-Many | Satu kategori dapat mengelompokkan banyak dokumen. FK: `category_id` → `categories.id`. ON DELETE SET NULL. |

## Catatan Teknis

- **Soft Delete**: Dokumen yang dihapus tidak langsung hilang dari database, melainkan kolom `deleted_at` diisi timestamp. Jika `NULL` berarti dokumen masih aktif.
- **Rate Limiting**: Tabel `login_attempts` mencatat setiap percobaan login gagal. Setelah 5 kali gagal dalam 15 menit, akun terkunci sementara.
- **Cascade**: Semua foreign key menggunakan `ON UPDATE CASCADE` dan `ON DELETE SET NULL` agar data tetap konsisten saat user atau kategori dihapus.
