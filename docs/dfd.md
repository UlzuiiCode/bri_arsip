# Data Flow Diagram (DFD) - SiMArsip

Dokumen ini menjelaskan aliran data dalam Sistem Informasi Manajemen Arsip.

---

## DFD Level 0 (Context Diagram)

```mermaid
graph TD
    %% Entities
    P[Pegawai]
    A[Administrator]
    
    %% System
    S(("0.0<br>Sistem Informasi<br>Manajemen Arsip"))
    
    %% Flows Pegawai
    P -->|"Data Login, Data Dokumen"| S
    S -->|"Status Login, Daftar/File Dokumen"| P
    
    %% Flows Admin
    A -->|"Data Login, Kelola Dokumen, Kelola User"| S
    S -->|"Status Login, Daftar Dokumen, Log Aktivitas"| A
```

---

## DFD Level 1

Mengikuti struktur asli web aplikasi SiMArsip, terdapat 6 proses utama dan 4 Data Store (`users`, `documents`, `categories`, `activity_logs`). Berikut adalah DFD Level 1 yang lebih akurat:

```mermaid
graph LR
    %% Entities (Kiri)
    P["Pengguna<br>(Admin & Pegawai)"]

    %% Processes (Tengah)
    P1(("1.0<br>Autentikasi"))
    P2(("2.0<br>Dashboard"))
    P3(("3.0<br>Manajemen<br>Dokumen"))
    P4(("4.0<br>Manajemen<br>Sampah"))
    P5(("5.0<br>Manajemen<br>User"))
    P6(("6.0<br>Log Aktivitas"))

    %% Data Stores (Kanan)
    D1[(D1: Data Users)]
    D2[(D2: Data Documents)]
    D3[(D3: Data Categories)]
    D4[(D4: Log Aktivitas)]

    %% 1.0 Autentikasi
    P <-->|"Data Login / Status"| P1
    P1 <-->|"Cek User"| D1
    P1 -->|"Catat Login"| D4

    %% 2.0 Dashboard
    P <-->|"Request / Tampilan Statistik"| P2
    D2 -->|"Hitung Total & Kategori"| P2
    D1 -->|"Hitung User"| P2

    %% 3.0 Manajemen Dokumen
    P <-->|"Upload, Edit, Hapus (Soft) / File"| P3
    D3 -->|"Daftar Kategori"| P3
    P3 <-->|"Simpan / Ambil Dokumen"| D2
    P3 -->|"Catat Aktivitas Dokumen"| D4

    %% 4.0 Manajemen Sampah (Khusus Admin)
    P <-->|"Restore, Hapus Permanen / Status"| P4
    P4 <-->|"Update / Hapus"| D2
    P4 -->|"Catat Aktivitas Sampah"| D4

    %% 5.0 Manajemen User (Khusus Admin)
    P <-->|"Approve, Reject / Status User"| P5
    P5 <-->|"Update Data"| D1
    P5 -->|"Catat Aktivitas User"| D4

    %% 6.0 Log Aktivitas (Riwayat Saya / Semua)
    P <-->|"Request / Tampilan Riwayat"| P6
    D4 -->|"Ambil Data Log"| P6
```

### PlantUML: DFD Level 1

```plantuml
@startuml
title DFD Level 1 - SiMArsip (Sesuai Web App)

skinparam rectangle {
    BackgroundColor White
    BorderColor Black
    RoundCorner 10
}
skinparam usecase {
    BackgroundColor White
    BorderColor Black
}
skinparam database {
    BackgroundColor White
    BorderColor Black
}

rectangle "Pengguna\n(Admin & Pegawai)" as P

usecase "1.0\nAutentikasi" as P1
usecase "2.0\nDashboard" as P2
usecase "3.0\nManajemen\nDokumen" as P3
usecase "4.0\nManajemen\nSampah" as P4
usecase "5.0\nManajemen\nUser" as P5
usecase "6.0\nLog Aktivitas" as P6

database "D1: Data Users" as D1
database "D2: Data Documents" as D2
database "D3: Data Categories" as D3
database "D4: Log Aktivitas" as D4

' 1.0
P -down-> P1 : Data Login
P1 -up-> P : Status
P1 <-right-> D1 : Cek User
P1 -right-> D4 : Catat Login

' 2.0
P -down-> P2 : Request
P2 -up-> P : Statistik
D2 -up-> P2 : Hitung Total
D1 -up-> P2 : Hitung User

' 3.0
P -down-> P3 : Upload/Edit/Hapus
P3 -up-> P : File
D3 -up-> P3 : Daftar Kategori
P3 <-right-> D2 : Simpan/Ambil Dokumen
P3 -right-> D4 : Catat Aktivitas

' 4.0
P -down-> P4 : Restore/Hapus Permanen
P4 -up-> P : Status
P4 <-right-> D2 : Update/Hapus
P4 -right-> D4 : Catat Aktivitas

' 5.0
P -down-> P5 : Approve/Reject
P5 -up-> P : Status
P5 <-right-> D1 : Update Data
P5 -right-> D4 : Catat Aktivitas

' 6.0
P -down-> P6 : Request
P6 -up-> P : Riwayat
D4 -up-> P6 : Ambil Data Log

@enduml
```
