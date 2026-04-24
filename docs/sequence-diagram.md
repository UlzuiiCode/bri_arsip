# Sequence Diagram - SiMArsip

## 1. Sequence Diagram: Login

```mermaid
sequenceDiagram
    actor P as Pengguna
    participant V as View Login
    participant AC as AuthController
    participant UM as UserModel
    participant LM as ActivityLogModel
    participant DB as Database

    P->>V: Mengakses Halaman Login
    V-->>P: Menampilkan Form Login

    P->>V: Mengisi Email & Password
    V->>AC: processLogin(email, password)
    
    AC->>AC: Validasi CSRF Token
    alt CSRF Token Tidak Valid
        AC-->>V: Redirect + Error "Request tidak valid"
        V-->>P: Tampilkan Pesan Error
    end

    AC->>AC: Validasi Input Kosong
    alt Input Kosong
        AC-->>V: Redirect + Error "Email dan Password wajib diisi"
        V-->>P: Tampilkan Pesan Error
    end

    AC->>UM: getRecentAttempts(email, ip, 15)
    UM->>DB: SELECT COUNT(*) FROM login_attempts
    DB-->>UM: Jumlah Percobaan
    UM-->>AC: attempts

    alt Percobaan >= 5
        AC->>UM: getLastAttemptTime(email, ip)
        UM->>DB: SELECT attempted_at FROM login_attempts
        DB-->>UM: Waktu Terakhir
        UM-->>AC: lastAttemptTime
        alt Masih Dalam Masa Lockout 15 Menit
            AC-->>V: Redirect + Error "Terlalu banyak percobaan"
            V-->>P: Tampilkan Pesan Error + Countdown
        end
    end

    AC->>UM: findByEmail(email)
    UM->>DB: SELECT * FROM users WHERE email = ?
    DB-->>UM: Data User
    UM-->>AC: user

    AC->>AC: password_verify(password, hash)

    alt Email/Password Tidak Valid
        AC->>UM: recordLoginAttempt(email, ip)
        UM->>DB: INSERT INTO login_attempts
        AC->>DB: INSERT INTO activity_logs (LOGIN_FAILED)
        AC-->>V: Redirect + Error "Email atau Password salah"
        V-->>P: Tampilkan Pesan Error
    end

    alt Status Akun = Pending
        AC-->>V: Redirect + Error "Akun belum disetujui"
        V-->>P: Tampilkan Pesan Error
    end

    AC->>UM: clearLoginAttempts(email, ip)
    UM->>DB: DELETE FROM login_attempts
    AC->>UM: purgeOldAttempts()
    UM->>DB: DELETE FROM login_attempts (expired)

    AC->>AC: session_regenerate_id()
    AC->>AC: Simpan Data Sesi (user_id, nama, role, foto)

    AC->>LM: log(user_id, "LOGIN", deskripsi)
    LM->>DB: INSERT INTO activity_logs
    DB-->>LM: OK

    AC-->>V: Redirect ke Dashboard
    V-->>P: Tampilkan Dashboard
```

### PlantUML: Login

```plantuml
@startuml
title Sequence Diagram - Login SiMArsip

actor Pengguna as P
participant "View Login" as V
participant "AuthController" as AC
participant "UserModel" as UM
participant "ActivityLogModel" as LM
database "Database" as DB

P -> V: Mengakses Halaman Login
V --> P: Menampilkan Form Login

P -> V: Mengisi Email & Password
V -> AC: processLogin(email, password)

AC -> AC: Validasi CSRF Token & Input

AC -> UM: getRecentAttempts(email, ip, 15)
UM -> DB: SELECT COUNT(*) FROM login_attempts
DB --> UM: Jumlah Percobaan
UM --> AC: attempts

alt Percobaan >= 5 & Masih Lockout
    AC --> V: Error "Terlalu banyak percobaan"
    V --> P: Tampilkan Pesan Error
end

AC -> UM: findByEmail(email)
UM -> DB: SELECT * FROM users WHERE email = ?
DB --> UM: Data User
UM --> AC: user

AC -> AC: password_verify(password, hash)

alt Email/Password Tidak Valid
    AC -> UM: recordLoginAttempt(email, ip)
    UM -> DB: INSERT INTO login_attempts
    AC -> DB: INSERT INTO activity_logs (LOGIN_FAILED)
    AC --> V: Error "Email atau Password salah"
    V --> P: Tampilkan Pesan Error
end

alt Status Akun = Pending
    AC --> V: Error "Akun belum disetujui admin"
    V --> P: Tampilkan Pesan Error
end

AC -> UM: clearLoginAttempts(email, ip)
UM -> DB: DELETE FROM login_attempts
AC -> AC: session_regenerate_id()
AC -> AC: Simpan Sesi Pengguna

AC -> LM: log(user_id, "LOGIN", deskripsi)
LM -> DB: INSERT INTO activity_logs
DB --> LM: OK

AC --> V: Redirect ke Dashboard
V --> P: Tampilkan Dashboard

@enduml
```

---

## 2. Sequence Diagram: Registrasi

```mermaid
sequenceDiagram
    actor P as Pengguna
    participant V as View Register
    participant AC as AuthController
    participant UM as UserModel
    participant LM as ActivityLogModel
    participant DB as Database

    P->>V: Mengakses Halaman Registrasi
    V-->>P: Menampilkan Form Registrasi

    P->>V: Mengisi Nama, Email, Password, Konfirmasi Password
    V->>AC: processRegister()

    AC->>AC: Validasi CSRF Token
    alt CSRF Token Tidak Valid
        AC-->>V: Redirect + Error "Request tidak valid"
        V-->>P: Tampilkan Pesan Error
    end

    AC->>AC: Sanitasi Input (nama, email, password)

    AC->>AC: Validasi Input
    Note right of AC: - Nama tidak boleh kosong<br/>- Email harus valid<br/>- Password minimal 8 karakter<br/>- Konfirmasi password harus cocok

    alt Validasi Gagal
        AC-->>V: Redirect + Error Validasi
        V-->>P: Tampilkan Pesan Error
    end

    AC->>UM: emailExists(email)
    UM->>DB: SELECT COUNT(*) FROM users WHERE email = ?
    DB-->>UM: Count
    UM-->>AC: true/false

    alt Email Sudah Terdaftar
        AC-->>V: Redirect + Error "Email sudah terdaftar"
        V-->>P: Tampilkan Pesan Error
    end

    AC->>UM: create(nama, email, password, "pegawai")
    UM->>UM: password_hash(password, BCRYPT)
    UM->>DB: INSERT INTO users (status = 'pending')
    DB-->>UM: user_id
    UM-->>AC: userId

    AC->>LM: log(userId, "REGISTER", deskripsi)
    LM->>DB: INSERT INTO activity_logs
    DB-->>LM: OK

    AC-->>V: Redirect ke Login + Pesan Sukses
    V-->>P: Tampilkan Pesan "Akun Berhasil Dibuat,<br/>Silakan Tunggu Persetujuan Admin"
```

### PlantUML: Registrasi

```plantuml
@startuml
title Sequence Diagram - Registrasi SiMArsip

actor Pengguna as P
participant "View Register" as V
participant "AuthController" as AC
participant "UserModel" as UM
participant "ActivityLogModel" as LM
database "Database" as DB

P -> V: Mengakses Halaman Registrasi
V --> P: Menampilkan Form Registrasi

P -> V: Mengisi Nama, Email, Password, Konfirmasi Password
V -> AC: processRegister()

AC -> AC: Validasi CSRF Token & Sanitasi Input

AC -> AC: Validasi Input\n(Nama, Email, Password, Konfirmasi)

alt Validasi Gagal
    AC --> V: Error Validasi
    V --> P: Tampilkan Pesan Error
end

AC -> UM: emailExists(email)
UM -> DB: SELECT COUNT(*) FROM users WHERE email = ?
DB --> UM: Count
UM --> AC: true/false

alt Email Sudah Terdaftar
    AC --> V: Error "Email sudah terdaftar"
    V --> P: Tampilkan Pesan Error
end

AC -> UM: create(nama, email, password, "pegawai")
UM -> UM: password_hash(password, BCRYPT)
UM -> DB: INSERT INTO users\n(status = 'pending', role = 'pegawai')
DB --> UM: user_id
UM --> AC: userId

AC -> LM: log(userId, "REGISTER", deskripsi)
LM -> DB: INSERT INTO activity_logs
DB --> LM: OK

AC --> V: Redirect ke Login + Flash Success
V --> P: "Akun Berhasil Dibuat,\nSilakan Tunggu Persetujuan Admin"

@enduml
```

---

## 3. Sequence Diagram: Proses Admin Menyetujui Akun Pegawai Baru

```mermaid
sequenceDiagram
    actor A as Admin
    participant V as View Pending
    participant UC as UserController
    participant UM as UserModel
    participant LM as ActivityLogModel
    participant DB as Database

    A->>V: Mengakses Halaman Persetujuan Akun
    V->>UC: pending()
    UC->>UC: requireAdmin()
    
    UC->>UM: getPending()
    UM->>DB: SELECT * FROM users WHERE status = 'pending'
    DB-->>UM: Daftar Pengguna Pending
    UM-->>UC: pendingUsers[]

    UC-->>V: Tampilkan Daftar Akun Pending
    V-->>A: Menampilkan Daftar Akun Pending

    alt Admin Menyetujui Akun
        A->>V: Klik Tombol "Setujui"
        V->>UC: approve(id)
        UC->>UC: requireAdmin()

        UC->>UM: findById(id)
        UM->>DB: SELECT * FROM users WHERE id = ?
        DB-->>UM: Data User
        UM-->>UC: user

        alt User Tidak Ditemukan
            UC-->>V: Redirect + Error "Pengguna tidak ditemukan"
            V-->>A: Tampilkan Pesan Error
        end

        UC->>UM: approve(id)
        UM->>DB: UPDATE users SET status = 'approved' WHERE id = ?
        DB-->>UM: OK
        UM-->>UC: true

        UC->>LM: log(admin_id, "APPROVE_USER", deskripsi)
        LM->>DB: INSERT INTO activity_logs
        DB-->>LM: OK

        UC-->>V: Redirect + Flash Success
        V-->>A: Tampilkan "Akun Berhasil Disetujui!"
    end

    alt Admin Menolak Akun
        A->>V: Klik Tombol "Tolak"
        V->>UC: reject(id)
        UC->>UC: requireAdmin()

        UC->>UM: findById(id)
        UM->>DB: SELECT * FROM users WHERE id = ?
        DB-->>UM: Data User
        UM-->>UC: user

        UC->>UM: delete(id)
        UM->>DB: DELETE FROM users WHERE id = ?
        DB-->>UM: OK

        UC->>LM: log(admin_id, "REJECT_USER", deskripsi)
        LM->>DB: INSERT INTO activity_logs
        DB-->>LM: OK

        UC-->>V: Redirect + Flash Success
        V-->>A: Tampilkan "Akun Berhasil Ditolak"
    end
```

### PlantUML: Proses Admin Menyetujui Akun

```plantuml
@startuml
title Sequence Diagram - Admin Menyetujui Akun Pegawai Baru

actor Admin as A
participant "View Pending" as V
participant "UserController" as UC
participant "UserModel" as UM
participant "ActivityLogModel" as LM
database "Database" as DB

A -> V: Mengakses Halaman Persetujuan Akun
V -> UC: pending()
UC -> UC: requireAdmin()

UC -> UM: getPending()
UM -> DB: SELECT * FROM users\nWHERE status = 'pending'
DB --> UM: Daftar Pengguna Pending
UM --> UC: pendingUsers[]

UC --> V: Render Daftar Akun Pending
V --> A: Menampilkan Daftar Akun Pending

== Menyetujui Akun ==

A -> V: Klik Tombol "Setujui"
V -> UC: approve(id)
UC -> UC: requireAdmin()

UC -> UM: findById(id)
UM -> DB: SELECT * FROM users WHERE id = ?
DB --> UM: Data User
UM --> UC: user

UC -> UM: approve(id)
UM -> DB: UPDATE users\nSET status = 'approved'
DB --> UM: OK

UC -> LM: log(admin_id, "APPROVE_USER", deskripsi)
LM -> DB: INSERT INTO activity_logs
DB --> LM: OK

UC --> V: Redirect + Flash Success
V --> A: "Akun Berhasil Disetujui!"

== Menolak Akun ==

A -> V: Klik Tombol "Tolak"
V -> UC: reject(id)
UC -> UC: requireAdmin()

UC -> UM: findById(id)
UM -> DB: SELECT * FROM users WHERE id = ?
DB --> UM: Data User
UM --> UC: user

UC -> UM: delete(id)
UM -> DB: DELETE FROM users WHERE id = ?
DB --> UM: OK

UC -> LM: log(admin_id, "REJECT_USER", deskripsi)
LM -> DB: INSERT INTO activity_logs
DB --> LM: OK

UC --> V: Redirect + Flash Success
V --> A: "Akun Berhasil Ditolak"

@enduml
```

---

## 4. Sequence Diagram: Pengelolaan Sampah (Delete & Restore)

```mermaid
sequenceDiagram
    actor U as Pengguna
    participant V as View Dokumen
    participant VT as View Trash
    participant DC as DocumentController
    participant DM as DocumentModel
    participant LM as ActivityLogModel
    participant DB as Database

    Note over U,DB: === Soft Delete (Pindah ke Sampah) ===

    U->>V: Melihat Daftar Dokumen
    U->>V: Klik Tombol "Hapus" pada Dokumen
    V->>DC: delete(id)

    DC->>DM: findById(id)
    DM->>DB: SELECT * FROM documents WHERE id = ?
    DB-->>DM: Data Dokumen
    DM-->>DC: document

    alt Dokumen Ditemukan & Belum Dihapus
        DC->>DM: softDelete(id)
        DM->>DB: UPDATE documents SET deleted_at = NOW() WHERE id = ?
        DB-->>DM: OK

        DC->>LM: log(user_id, "DELETE_DOCUMENT", deskripsi)
        LM->>DB: INSERT INTO activity_logs
        DB-->>LM: OK
    end

    DC-->>V: Redirect + Flash "Dokumen dipindahkan ke sampah"
    V-->>U: Tampilkan Pesan Sukses

    Note over U,DB: === Restore dari Sampah ===

    U->>VT: Mengakses Halaman Tempat Sampah
    VT->>DC: trash()
    DC->>DM: getTrashed()
    DM->>DB: SELECT * FROM documents WHERE deleted_at IS NOT NULL
    DB-->>DM: Daftar Dokumen Terhapus
    DM-->>DC: trashedDocuments[]
    DC-->>VT: Render Daftar Sampah
    VT-->>U: Menampilkan Daftar Dokumen di Sampah

    U->>VT: Klik Tombol "Pulihkan" pada Dokumen
    VT->>DC: restore(id)

    DC->>DM: findById(id)
    DM->>DB: SELECT * FROM documents WHERE id = ?
    DB-->>DM: Data Dokumen
    DM-->>DC: document

    alt Dokumen Ditemukan & Sudah di Sampah
        DC->>DM: restore(id)
        DM->>DB: UPDATE documents SET deleted_at = NULL WHERE id = ?
        DB-->>DM: OK

        DC->>LM: log(user_id, "RESTORE_DOCUMENT", deskripsi)
        LM->>DB: INSERT INTO activity_logs
        DB-->>LM: OK
    end

    DC-->>VT: Redirect + Flash "Dokumen berhasil dipulihkan"
    VT-->>U: Tampilkan Pesan Sukses

    Note over U,DB: === Hapus Permanen ===

    U->>VT: Klik Tombol "Hapus Permanen"
    VT->>DC: forceDelete(id)

    DC->>DM: findById(id)
    DM->>DB: SELECT * FROM documents WHERE id = ?
    DB-->>DM: Data Dokumen
    DM-->>DC: document

    alt Dokumen Ditemukan
        DC->>DC: Hapus File Fisik dari Server
        DC->>DM: forceDelete(id)
        DM->>DB: DELETE FROM documents WHERE id = ?
        DB-->>DM: OK

        DC->>LM: log(user_id, "FORCE_DELETE_DOCUMENT", deskripsi)
        LM->>DB: INSERT INTO activity_logs
        DB-->>LM: OK
    end

    DC-->>VT: Redirect + Flash "Dokumen dihapus permanen"
    VT-->>U: Tampilkan Pesan Sukses
```

### PlantUML: Pengelolaan Sampah

```plantuml
@startuml
title Sequence Diagram - Pengelolaan Sampah (Delete & Restore)

actor Pengguna as U
participant "View Dokumen" as V
participant "View Trash" as VT
participant "DocumentController" as DC
participant "DocumentModel" as DM
participant "ActivityLogModel" as LM
database "Database" as DB

== Soft Delete (Pindah ke Sampah) ==

U -> V: Klik Tombol "Hapus" pada Dokumen
V -> DC: delete(id)

DC -> DM: findById(id)
DM -> DB: SELECT * FROM documents WHERE id = ?
DB --> DM: Data Dokumen
DM --> DC: document

DC -> DM: softDelete(id)
DM -> DB: UPDATE documents\nSET deleted_at = NOW()
DB --> DM: OK

DC -> LM: log(user_id, "DELETE_DOCUMENT", deskripsi)
LM -> DB: INSERT INTO activity_logs
DB --> LM: OK

DC --> V: Redirect + Flash Success
V --> U: "Dokumen dipindahkan ke sampah"

== Melihat Sampah ==

U -> VT: Mengakses Halaman Tempat Sampah
VT -> DC: trash()

DC -> DM: getTrashed()
DM -> DB: SELECT * FROM documents\nWHERE deleted_at IS NOT NULL
DB --> DM: Daftar Dokumen Terhapus
DM --> DC: trashedDocuments[]

DC --> VT: Render Daftar Sampah
VT --> U: Menampilkan Daftar Dokumen di Sampah

== Restore dari Sampah ==

U -> VT: Klik Tombol "Pulihkan"
VT -> DC: restore(id)

DC -> DM: findById(id)
DM -> DB: SELECT * FROM documents WHERE id = ?
DB --> DM: Data Dokumen
DM --> DC: document

DC -> DM: restore(id)
DM -> DB: UPDATE documents\nSET deleted_at = NULL
DB --> DM: OK

DC -> LM: log(user_id, "RESTORE_DOCUMENT", deskripsi)
LM -> DB: INSERT INTO activity_logs
DB --> LM: OK

DC --> VT: Redirect + Flash Success
VT --> U: "Dokumen berhasil dipulihkan"

== Hapus Permanen ==

U -> VT: Klik Tombol "Hapus Permanen"
VT -> DC: forceDelete(id)

DC -> DM: findById(id)
DM -> DB: SELECT * FROM documents WHERE id = ?
DB --> DM: Data Dokumen
DM --> DC: document

DC -> DC: Hapus File Fisik dari Server
DC -> DM: forceDelete(id)
DM -> DB: DELETE FROM documents WHERE id = ?
DB --> DM: OK

DC -> LM: log(user_id, "FORCE_DELETE_DOCUMENT", deskripsi)
LM -> DB: INSERT INTO activity_logs
DB --> LM: OK

DC --> VT: Redirect + Flash Success
VT --> U: "Dokumen dihapus secara permanen"

@enduml
```

---

## 5. Sequence Diagram: Melihat Log Aktivitas (Admin)
*Halaman Khusus Manajemen Log*

```mermaid
sequenceDiagram
    actor A as Admin
    participant V as View Activity Log
    participant ALC as ActivityLogController
    participant LM as ActivityLogModel
    participant DB as Database

    A->>V: Mengakses Menu Log Aktivitas
    V->>ALC: index()

    ALC->>ALC: requireAdmin()
    
    ALC->>LM: getAll(perPage, offset)
    LM->>DB: SELECT * FROM activity_logs ...
    DB-->>LM: Data Log Semua User
    LM-->>ALC: logs[]

    ALC->>LM: countAll()
    LM->>DB: SELECT COUNT(*) ...
    DB-->>LM: Total Log
    LM-->>ALC: total

@enduml
```

---

## 6. Sequence Diagram: Upload Dokumen

```mermaid
sequenceDiagram
    actor U as User
    participant V as Upload View
    participant DC as DocumentController
    participant DM as Document Model
    participant DB as Database

    U->>V: Isi form (Judul, Kategori, dst) & Klik Upload
    V->>DC: POST /documents/store
    
    DC->>DC: Validasi File (Tipe & Size)
    
    alt Valid
        DC->>DM: save(metadata kategori_id, path)
        DM->>DB: INSERT INTO documents
        DB-->>DM: OK
        DM-->>DC: Success
        DC-->>V: Redirect ke Daftar Dokumen
        V-->>U: Tampilkan Notifikasi Berhasil
    else Invalid
        DC-->>V: Kembalikan dengan Pesan Error
        V-->>U: Tampilkan Alert Gagal
    end
```

### PlantUML: Upload Dokumen

```plantuml
@startuml
title Sequence Diagram - Upload Dokumen SiMArsip

actor User as U
participant "Upload View" as V
participant "DocumentController" as DC
participant "Document Model" as DM
database "Database" as DB

U -> V: Isi form (Judul, Kategori, dst) & Klik Upload
V -> DC: POST /documents/store

DC -> DC: Validasi File (Tipe & Size)

alt Valid
    DC -> DM: save(metadata kategori_id, path)
    DM -> DB: INSERT INTO documents
    DB --> DM: OK
    DM --> DC: Success
    DC --> V: Redirect ke Daftar Dokumen
    V --> U: Tampilkan Notifikasi Berhasil
else Invalid
    DC --> V: Kembalikan dengan Pesan Error
    V --> U: Tampilkan Alert Gagal
end

@enduml
```

