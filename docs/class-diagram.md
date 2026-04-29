# Class Diagram - SiMArsip

Diagram ini menggambarkan struktur class pada Sistem Informasi Manajemen Arsip, meliputi atribut data dan method yang dimiliki setiap class beserta relasi antar class.

```mermaid
classDiagram
    class Users {
        <<Entity>>
        - id : int
        - nama : varchar
        - email : varchar
        - password : varchar
        - role : enum
        - status : enum
        - created_at : timestamp
        - updated_at : timestamp
        + login()
        + register()
        + logout()
        + approve()
        + reject()
    }

    class Documents {
        <<Entity>>
        - id : int
        - judul : varchar
        - deskripsi : text
        - file_path : varchar
        - file_name : varchar
        - file_size : int
        - file_type : varchar
        - category_id : int
        - uploaded_by : int
        - nominal : decimal
        - pihak_terkait : varchar
        - tanggal_transaksi : date
        - deleted_at : timestamp
        - created_at : timestamp
        + upload()
        + edit()
        + delete()
        + restore()
        + download()
        + forceDelete()
    }

    class Categories {
        <<Entity>>
        - id : int
        - nama : varchar
        - deskripsi : text
        - created_at : timestamp
        - updated_at : timestamp
        + getAll()
        + findById()
    }

    class ActivityLogs {
        <<Entity>>
        - id : bigint
        - user_id : int
        - action : varchar
        - description : text
        - ip_address : varchar
        - created_at : timestamp
        + log()
        + getAll()
        + getByUser()
        + countAll()
        + countByUser()
    }

    class LoginAttempts {
        <<Entity>>
        - id : bigint
        - email : varchar
        - ip_address : varchar
        - attempted_at : timestamp
        + recordAttempt()
        + getRecentAttempts()
        + clearAttempts()
    }

    Users "1" -- "N" Documents : uploads
    Users "1" -- "N" ActivityLogs : performs
    Users "1" -- "N" LoginAttempts : records
    Categories "1" -- "N" Documents : categorizes
```

---

### PlantUML: Class Diagram

```plantuml
@startuml
title Class Diagram - SiMArsip

skinparam class {
    BackgroundColor White
    BorderColor Black
    ArrowColor Black
    FontName Arial
    HeaderBackgroundColor LightGray
}

skinparam linetype ortho

class Users {
    **Attributes (Data)**
    --
    - id : int
    - nama : varchar
    - email : varchar
    - password : varchar
    - role : enum (admin, pegawai)
    - status : enum (pending, approved)
    - created_at : timestamp
    - updated_at : timestamp
    ==
    **Methods (Functions)**
    --
    + login()
    + register()
    + logout()
    + approve()
    + reject()
}

class Documents {
    **Attributes (Data)**
    --
    - id : int
    - judul : varchar
    - deskripsi : text
    - file_path : varchar
    - file_name : varchar
    - file_size : int
    - file_type : varchar
    - category_id : int (FK)
    - uploaded_by : int (FK)
    - nominal : decimal
    - pihak_terkait : varchar
    - tanggal_transaksi : date
    - deleted_at : timestamp
    - created_at : timestamp
    ==
    **Methods (Functions)**
    --
    + upload()
    + edit()
    + delete()
    + restore()
    + download()
    + forceDelete()
}

class Categories {
    **Attributes (Data)**
    --
    - id : int
    - nama : varchar
    - deskripsi : text
    - created_at : timestamp
    - updated_at : timestamp
    ==
    **Methods (Functions)**
    --
    + getAll()
    + findById()
}

class ActivityLogs {
    **Attributes (Data)**
    --
    - id : bigint
    - user_id : int (FK)
    - action : varchar
    - description : text
    - ip_address : varchar
    - created_at : timestamp
    ==
    **Methods (Functions)**
    --
    + log()
    + getAll()
    + getByUser()
    + countAll()
    + countByUser()
}

class LoginAttempts {
    **Attributes (Data)**
    --
    - id : bigint
    - email : varchar
    - ip_address : varchar
    - attempted_at : timestamp
    ==
    **Methods (Functions)**
    --
    + recordAttempt()
    + getRecentAttempts()
    + clearAttempts()
}

Users "1" -- "N" Documents : uploads
Users "1" -- "N" ActivityLogs : performs
Users "1" -- "N" LoginAttempts : records
Categories "1" -- "N" Documents : categorizes

@enduml
```
