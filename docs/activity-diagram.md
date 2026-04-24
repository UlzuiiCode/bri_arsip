# Activity Diagram - SiMArsip

## 1. Activity Diagram: Login

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

### PlantUML: Login

```plantuml
@startuml
title Activity Diagram - Login SiMArsip

start

:Pengguna Membuka Halaman Login;
:Pengguna Mengisi Email dan Password;

if (Input Kosong?) then (Ya)
  :Tampilkan Error\n"Email dan Password Wajib Diisi";
  stop
else (Tidak)
endif

if (Rate Limiting: Sudah >= 5 Percobaan?) then (Ya)
  if (Masa Lockout 15 Menit Masih Berlaku?) then (Ya)
    :Tampilkan Error\n"Akun Terkunci Sementara";
    stop
  else (Tidak)
  endif
endif

:Sistem Memverifikasi Email dan Password;

if (Email & Password Valid?) then (Tidak)
  :Catat Percobaan Login Gagal;
  :Tampilkan Error\n"Email atau Password Salah";
  stop
else (Ya)
endif

if (Status Akun?) then (Pending)
  :Tampilkan Error\n"Akun Belum Disetujui Admin";
  stop
else (Approved)
endif

:Hapus Catatan Percobaan Login;
:Regenerasi Session ID;
:Simpan Data Sesi Pengguna;
:Catat Log Aktivitas Login;
:Redirect ke Dashboard;

stop

@enduml
```

---

## 2. Activity Diagram: Registrasi

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

### PlantUML: Registrasi

```plantuml
@startuml
title Activity Diagram - Registrasi SiMArsip

start

:Pengguna Membuka Halaman Registrasi;
:Pengguna Mengisi Form\n(Nama, Email, Password, Konfirmasi Password);

if (Nama Kosong?) then (Ya)
  :Tampilkan Error Validasi;
  stop
else (Tidak)
endif

if (Email Valid?) then (Tidak)
  :Tampilkan Error Validasi;
  stop
else (Ya)
endif

if (Password >= 8 Karakter?) then (Tidak)
  :Tampilkan Error Validasi;
  stop
else (Ya)
endif

if (Password = Konfirmasi Password?) then (Tidak)
  :Tampilkan Error Validasi;
  stop
else (Ya)
endif

if (Email Sudah Terdaftar?) then (Ya)
  :Tampilkan Error Validasi;
  stop
else (Tidak)
endif

:Simpan Data Pengguna Baru\n(Status: Pending, Role: Pegawai);
:Catat Log Aktivitas Registrasi;
:Tampilkan Pesan Sukses\n"Silakan Tunggu Persetujuan Admin";
:Redirect ke Halaman Login;

stop

@enduml
```

---

## 3. Activity Diagram: Melihat Log Aktivitas (Admin)

```mermaid
flowchart TD
    Start([Mulai]) --> C1[Admin Membuka Menu Log Aktivitas]
    C1 --> C2[Sistem Mengecek Role Pengguna]
    C2 --> C3{Role = Admin?}

    C3 -->|Tidak| C4[Tampilkan Pesan Error:\nAkses Ditolak]
    C4 --> End([Selesai])

    C3 -->|Ya| C5[Sistem Mengambil Semua Data Log\ndari Database]
    C5 --> C6[Sistem Menyusun Data dengan Pagination]
    C6 --> C7[Tampilkan Tabel Log Aktivitas Seluruh Pengguna]
    C7 --> C8{Admin Menggunakan Filter?}

    C8 -->|Ya| C9[Sistem Memfilter Log\nberdasarkan Parameter]
    C9 --> C7

    C8 -->|Tidak| End([Selesai])
```

### PlantUML: Melihat Log Aktivitas (Admin)

```plantuml
@startuml
title Activity Diagram - Melihat Log Aktivitas (Admin)

start

:Admin Membuka Menu Log Aktivitas;
:Sistem Mengecek Role Pengguna;

if (Role = Admin?) then (Tidak)
  :Tampilkan Pesan Error\n"Akses Ditolak";
  stop
else (Ya)
endif

:Sistem Mengambil Semua Data Log dari Database;
:Sistem Menyusun Data dengan Pagination;
:Tampilkan Tabel Log Aktivitas Seluruh Pengguna;

if (Admin Menggunakan Filter?) then (Ya)
  :Sistem Memfilter Log berdasarkan Parameter;
  :Tampilkan Hasil Filter;
else (Tidak)
endif

stop

@enduml
```

---

## 4. Activity Diagram: Melihat Log Aktivitas (Pegawai)

```mermaid
flowchart TD
    Start([Mulai]) --> D1[Pegawai Membuka Dashboard]
    D1 --> D2[Sistem Mengecek Sesi Login]
    D2 --> D3{Sesi Valid?}

    D3 -->|Tidak| D4[Redirect ke Halaman Login]
    D4 --> End([Selesai])

    D3 -->|Ya| D5[Sistem Mengambil Log Aktivitas\nMilik Pegawai Sendiri\nberdasarkan user_id]
    D5 --> D6[Sistem Membatasi tampilan\nmaks. 8 Log Terbaru]
    D6 --> D7[Tampilkan Riwayat Aktivitas\ndi Bagian Recent Activity Dashboard]
    D7 --> End([Selesai])
```

### PlantUML: Melihat Log Aktivitas (Pegawai)

```plantuml
@startuml
title Activity Diagram - Melihat Log Aktivitas (Pegawai)

start

:Pegawai Membuka Dashboard;
:Sistem Mengecek Sesi Login;

if (Sesi Valid?) then (Tidak)
  :Redirect ke Halaman Login;
  stop
else (Ya)
endif

:Sistem Mengambil Log Aktivitas\nMilik Pegawai Sendiri (berdasarkan user_id);
:Sistem Membatasi Tampilan Maks. 8 Log Terbaru;
:Tampilkan Riwayat Aktivitas\ndi Bagian Recent Activity Dashboard;

stop

@enduml
```
