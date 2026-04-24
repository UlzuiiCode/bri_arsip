# System Design Documentation - Archive Management System (bri_arsip)

Dokumen ini memberikan gambaran komprehensif tentang arsitektur dan desain **Sistem Manajemen Arsip**. Diagram ini merepresentasikan sistem dalam bentuk yang lengkap secara fungsionalitas dan teknis.

---

## 1. Use Case Diagram
Menggambarkan fungsi sistem dan interaksi aktor (**Administrator** & **Pegawai**).

```mermaid
useCaseDiagram
    actor "Administrator" as Admin
    actor "Pegawai" as Pegawai
    
    package "Sistem Manajemen Arsip" {
        usecase "Login / Logout" as UC1
        usecase "Register Akun" as UC2
        usecase "Upload Dokumen" as UC4
        usecase "Cari & Lihat Dokumen" as UC5
        usecase "Edit / Hapus Dokumen" as UC6
        usecase "Approve / Blokir User" as UC8
        usecase "Kelola Sampah (Restore/Hapus)" as UC9
        usecase "Lihat Activity Logs" as UC10
    }
    
    Pegawai --> UC1
    Pegawai --> UC2
    Pegawai --> UC4
    Pegawai --> UC5
    Pegawai --> UC6
    Pegawai --> UC10
    
    Admin --> UC1
    Admin --> UC4
    Admin --> UC5
    Admin --> UC6
    Admin --> UC8
    Admin --> UC9
    Admin --> UC10
```

---

## 2. Activity Diagram

### A. Login
Menjelaskan alur masuk ke sistem dengan validasi keamanan dan rate limiting.

```mermaid
flowchart TD
    Start([Mulai]) --> A1[Pengguna Membuka Halaman Login]
    A1 --> A2[Pengguna Mengisi Email dan Password]
    A2 --> A3{Validasi Input Kosong?}
    
    A3 -->|Ya| A4[Tampilkan Pesan Error:\nEmail dan Password Wajib Diisi]
    A4 --> A2
    
    A3 -->|Tidak| A5{Cek Rate Limiting:\nSudah Melebihi 5 Percobaan?}
    
    A5 -->|Ya| A6{Masa Lockout\n15 Menit Masih Berlaku?}
    A6 -->|Ya| A7[Tampilkan Pesan Error:\nAkun Terkunci Sementara]
    A7 --> A2
    A6 -->|Tidak| A8[Lanjut Verifikasi]
    
    A5 -->|Tidak| A8
    
    A8 --> A9{Verifikasi\nEmail & Password}
    
    A9 -->|Gagal| A10[Catat Percobaan Login Gagal]
    A10 --> A11[Tampilkan Pesan Error:\nEmail atau Password Salah]
    A11 --> A2
    
    A9 -->|Berhasil| A12{Cek Status Akun}
    
    A12 -->|Pending| A13[Tampilkan Pesan Error:\nAkun Belum Disetujui Admin]
    A13 --> A2
    
    A12 -->|Approved| A14[Hapus Catatan Percobaan Login]
    A14 --> A15[Regenerasi Session ID]
    A15 --> A16[Simpan Data Sesi Pengguna]
    A16 --> A17[Catat Log Aktivitas Login]
    A17 --> A18[Redirect ke Dashboard]
    A18 --> End([Selesai])
```

### B. Registrasi
Alur pendaftaran akun baru bagi pegawai.

```mermaid
flowchart TD
    Start([Mulai]) --> B1[Pengguna Membuka Halaman Registrasi]
    B1 --> B2[Pengguna Mengisi Form:\nNama, Email, Password, Konfirmasi Password]
    B2 --> B3{Validasi Input}
    
    B3 --> B4{Nama Kosong?}
    B4 -->|Ya| B_err[Tampilkan Pesan Error Validasi]
    
    B4 -->|Tidak| B5{Email Valid?}
    B5 -->|Tidak| B_err
    
    B5 -->|Ya| B6{Password >= 8 Karakter?}
    B6 -->|Tidak| B_err
    
    B6 -->|Ya| B7{Password = Konfirmasi Password?}
    B7 -->|Tidak| B_err
    
    B7 -->|Ya| B8{Email Sudah Terdaftar?}
    B8 -->|Ya| B_err
    
    B_err --> B2
    
    B8 -->|Tidak| B9[Sistem Menyimpan Data Pengguna Baru\ndengan Status: Pending]
    B9 --> B10[Catat Log Aktivitas Registrasi]
    B10 --> B11[Tampilkan Pesan Sukses:\nAkun Berhasil Dibuat,\nSilakan Tunggu Persetujuan Admin]
    B11 --> B12[Redirect ke Halaman Login]
    B12 --> End([Selesai])
```

### C. Melihat Log Aktivitas (Admin)
Admin mengakses halaman khusus log dengan semua data seluruh pengguna.

```mermaid
flowchart TD
    Start([Mulai]) --> C1[Admin Membuka Menu Log Aktivitas]
    C1 --> C2[Sistem Mengecek Role Pengguna]
    C2 --> C3{Role = Admin?}

    C3 -->|Tidak| C4[Tampilkan Pesan Error: Akses Ditolak]
    C4 --> End1([Selesai])

    C3 -->|Ya| C5[Sistem Mengambil Semua Data Log dari Database]
    C5 --> C6[Sistem Menyusun Data dengan Pagination]
    C6 --> C7[Tampilkan Tabel Log Aktivitas Seluruh Pengguna]
    C7 --> C8{Admin Menggunakan Filter?}

    C8 -->|Ya| C9[Sistem Memfilter Log berdasarkan Parameter]
    C9 --> C7

    C8 -->|Tidak| End2([Selesai])
```

### D. Melihat Log Aktivitas (Pegawai)
Pegawai hanya melihat aktivitasnya sendiri di bagian Recent Activity pada Dashboard.

```mermaid
flowchart TD
    Start([Mulai]) --> D1[Pegawai Membuka Dashboard]
    D1 --> D2[Sistem Mengecek Sesi Login]
    D2 --> D3{Sesi Valid?}

    D3 -->|Tidak| D4[Redirect ke Halaman Login]
    D4 --> End3([Selesai])

    D3 -->|Ya| D5[Sistem Mengambil Log Aktivitas Milik Pegawai Sendiri berdasarkan user_id]
    D5 --> D6[Sistem Membatasi Tampilan Maks. 8 Log Terbaru]
    D6 --> D7[Tampilkan Riwayat Aktivitas di Recent Activity Dashboard]
    D7 --> End4([Selesai])
```

---

## 3. Class Diagram
Struktur MVC (Model-View-Controller) sistem.

```mermaid
classDiagram
    class AuthController {
        +login()
        +register()
        +logout()
    }
    class DocumentController {
        +index()
        +create()
        +store()
        +edit()
        +update()
        +delete()
        +trash()
        +restore()
    }
    class UserController {
        +index()
        +pending()
        +approve()
        +reject()
        +delete()
    }
    class CategoryModel {
        +getAll()
        +findById()
    }
    class User {
        +id: int
        +nama: string
        +email: string
        +role: enum
        +status: enum
        +foto: string
    }
    class Document {
        +id: int
        +judul: string
        +file_path: string
        +category_id: int
        +nominal: decimal
        +pihak_terkait: string
        +tanggal_transaksi: date
        +deleted_at: timestamp
    }
    class Category {
        +id: int
        +nama: string
        +deskripsi: string
        +created_at: timestamp
        +updated_at: timestamp
    }
    
    AuthController ..> User
    DocumentController ..> Document
    DocumentController ..> CategoryModel
    UserController ..> User
    Document "*" -- "1" User : uploaded by
    Category "1" -- "*" Document : categorizes
```

---

## 4. Sequence Diagram

### A. Login
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
    
    AC->>UM: getRecentAttempts(email, ip, 15)
    UM-->>AC: attempts
    alt Percobaan >= 5 & Lockout
        AC-->>V: Error "Terlalu banyak percobaan"
    end

    AC->>UM: findByEmail(email)
    UM-->>AC: user

    alt Email/Password Tidak Valid
        AC->>UM: recordLoginAttempt(email, ip)
        AC-->>V: Error "Email atau Password salah"
    end

    alt Status Akun = Pending
        AC-->>V: Error "Akun belum disetujui"
    end

    AC->>UM: clearLoginAttempts(email, ip)
    AC->>AC: session_regenerate_id()
    AC->>LM: log(user_id, "LOGIN")
    AC-->>V: Redirect ke Dashboard
```

### B. Registrasi
```mermaid
sequenceDiagram
    actor P as Pengguna
    participant V as View Register
    participant AC as AuthController
    participant UM as UserModel
    participant LM as ActivityLogModel

    P->>V: Mengisi Nama, Email, Password, Konfirmasi
    V->>AC: processRegister()
    AC->>AC: Validasi Input
    AC->>UM: emailExists(email)
    UM-->>AC: false
    AC->>UM: create(nama, email, password, "pegawai")
    AC->>LM: log(userId, "REGISTER")
    AC-->>V: Redirect Login + Success Message
```

### C. Admin Menyetujui Akun Pegawai
```mermaid
sequenceDiagram
    actor A as Admin
    participant V as View Pending
    participant UC as UserController
    participant UM as UserModel
    participant LM as ActivityLogModel

    A->>V: Klik Tombol "Setujui"
    V->>UC: approve(id)
    UC->>UM: approve(id)
    UC->>LM: log(admin_id, "APPROVE_USER")
    UC-->>V: Redirect + Berhasil
```

### D. Pengelolaan Sampah (Delete & Restore)
```mermaid
sequenceDiagram
    actor U as Pengguna
    participant V as View Dokumen
    participant VT as View Trash
    participant DC as DocumentController
    participant DM as DocumentModel
    participant LM as ActivityLogModel

    U->>V: Klik Hapus
    V->>DC: delete(id)
    DC->>DM: softDelete(id)
    DC->>LM: log(user_id, "DELETE_DOCUMENT")
    
    U->>VT: Klik Pulihkan
    VT->>DC: restore(id)
    DC->>DM: restore(id)
    DC->>LM: log(user_id, "RESTORE_DOCUMENT")
```

### E. Melihat Log Aktivitas

**1. Skenario Admin (Halaman Khusus)**
```mermaid
sequenceDiagram
    actor A as Admin
    participant V as View Log Aktivitas
    participant ALC as ActivityLogController
    participant LM as ActivityLogModel

    A->>V: Mengakses Menu Log Aktivitas
    V->>ALC: index()
    ALC->>ALC: requireAdmin()
    ALC->>LM: getAll(limit, offset)
    LM-->>ALC: logs[]
    ALC-->>V: Tampilkan Tabel Log Lengkap
```

**2. Skenario Pegawai (Halaman Dashboard)**
```mermaid
sequenceDiagram
    actor P as Pegawai
    participant V as View Dashboard
    participant DC as DashboardController
    participant LM as ActivityLogModel

    P->>V: Mengakses Dashboard
    V->>DC: index()
    DC->>LM: getByUser(userId, 8)
    LM-->>DC: recentLogs[]
    DC-->>V: Tampilkan Dashboard (Recent Activity)
```

---

## 5. Program Flowchart
Alur logika eksekusi aplikasi dari `index.php`.

```mermaid
graph TD
    A[Start: index.php] --> B{Login Aktif?}
    B -- Tidak --> C[Halaman Login/Register]
    B -- Ya --> D[Dashboard]
    
    C --> E[Proses Autentikasi]
    E -- Gagal --> C
    E -- Berhasil & Approved --> D
    
    D --> H[Navigasi Menu]
    H --> I[Kelola Dokumen]
    H --> K[Kelola Pengguna - Admin]
    H --> L[Lihat Log - Admin/Pegawai]
    H --> M[Logout]
    
    M --> N[Hapus Session]
    N --> C
```

---

## 6. Entity Relationship Diagram (ERD)
Struktur database tabel-tabel utama sesuai `schema.sql`.

```mermaid
erDiagram
    USERS ||--o{ DOCUMENTS : "uploads (uploaded_by)"
    USERS ||--o{ ACTIVITY_LOGS : "performs (user_id)"
    USERS ||--o{ LOGIN_ATTEMPTS : "records (email)"
    CATEGORIES ||--o{ DOCUMENTS : "groups (category_id)"

    USERS {
        int id PK
        varchar nama
        varchar email
        varchar foto_profil "nullable"
        varchar password "bcrypt hash"
        enum role "admin | pegawai"
        enum status "pending | approved"
        timestamp created_at
        timestamp updated_at
    }

    CATEGORIES {
        int id PK
        varchar nama
        text deskripsi "nullable"
        timestamp created_at
        timestamp updated_at
    }

    DOCUMENTS {
        int id PK
        varchar judul
        text deskripsi "nullable"
        varchar file_path "nama file di server"
        varchar file_name "nama asli dari user"
        int file_size "ukuran dalam bytes"
        varchar file_type "MIME type"
        int category_id FK "nullable"
        int uploaded_by FK "nullable"
        decimal nominal "nullable"
        varchar pihak_terkait "nullable"
        date tanggal_transaksi "nullable"
        timestamp deleted_at "soft delete, NULL = aktif"
        timestamp created_at
        timestamp updated_at
    }

    ACTIVITY_LOGS {
        bigint id PK
        int user_id FK "nullable jika user dihapus"
        varchar action "LOGIN, UPLOAD_DOCUMENT, dst."
        text description "nullable"
        varchar ip_address "IPv4 atau IPv6"
        timestamp created_at
    }

    LOGIN_ATTEMPTS {
        bigint id PK
        varchar email
        varchar ip_address
        timestamp attempted_at
    }
```

---

## 7. Data Flow Diagram (DFD) Level 0

```mermaid
graph TD
    P[Pegawai]
    A[Administrator]
    S((0.0 <br/> Sistem Informasi <br/> Manajemen Arsip))

    P -->|Data Login| S
    P -->|Data Dokumen| S
    P -->|Request Download/Export| S
    S -->|Info Login Status| P
    S -->|Daftar/Preview Dokumen| P
    S -->|File Dokumen / CSV| P

    A -->|Konfirmasi Pendaftaran| S
    A -->|Manajemen User| S
    S -->|Log Aktivitas| A
    S -->|Laporan Data User| A
```

---

## 8. Data Flow Diagram (DFD) Level 1

```mermaid
graph TD
    PE[Pegawai]
    AD[Administrator]

    P1((1.0 <br/> Autentikasi))
    P2((2.0 <br/> Manajemen <br/> Dokumen))
    P3((3.0 <br/> Manajemen <br/> Trash))
    P4((4.0 <br/> Manajemen <br/> User))
    P5((5.0 <br/> Logging & <br/> Pelaporan))

    D1[(D1: Users)]
    D2[(D2: Documents)]
    D3[(D3: Activity Logs)]

    PE -->|Cek Kredensial| P1
    AD -->|Cek Kredensial| P1
    P1 <-->|Data User| D1
    P1 -->|Status Login| PE
    P1 -->|Status Login| AD

    PE -->|Upload/Edit Dokumen| P2
    P2 <-->|Simpan/Ambil Dokumen| D2
    P2 -->|Log Upload/Update| P5
    P2 -->|File/Daftar Dokumen| PE

    PE -->|Hapus/Restore Dokumen| P3
    P3 <-->|Update Status deleted_at| D2
    P3 -->|Log Hapus/Restore| P5

    AD -->|Approve/Hapus User| P4
    P4 <-->|Update Status Approval| D1
    P4 -->|Log Manajemen User| P5

    P5 -->|Simpan Log| D3
    D3 -->|Ambil Data Log| P5
    D2 -->|Ambil Data Export| P5
    P5 -->|Log Aktivitas| AD
    P5 -->|Export CSV| PE
```
