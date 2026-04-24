# Data Flow Diagram (DFD) - SiMArsip

Dokumen ini menjelaskan aliran data dalam Sistem Informasi Manajemen Arsip (SiMArsip).

## DFD Level 0 (Context Diagram)

DFD Level 0 atau Diagram Konteks memberikan gambaran besar interaksi sistem dengan entitas luar.

```mermaid
graph TD
    %% Entities
    P[Pegawai]
    A[Administrator]

    %% System
    S((0.0 <br/> Sistem Informasi <br/> Manajemen Arsip))

    %% Data Flows - Pegawai
    P -->|Data Login| S
    P -->|Data Dokumen| S
    P -->|Request Download/Export| S
    S -->|Info Login Status| P
    S -->|Daftar/Preview Dokumen| P
    S -->|File Dokumen / CSV| P

    %% Data Flows - Admin
    A -->|Konfirmasi Pendaftaran| S
    A -->|Manajemen User| S
    S -->|Log Aktivitas| A
    S -->|Laporan Data User| A
```

---

## DFD Level 1

DFD Level 1 merinci proses utama yang terjadi di dalam sistem dan interaksinya dengan penyimpanan data (Data Store).

```mermaid
graph TD
    %% Entities
    PE[Pegawai]
    AD[Administrator]

    %% Processes
    P1((1.0 <br/> Autentikasi))
    P2((2.0 <br/> Manajemen <br/> Dokumen))
    P3((3.0 <br/> Manajemen <br/> Trash))
    P4((4.0 <br/> Manajemen <br/> User))
    P5((5.0 <br/> Logging & <br/> Pelaporan))

    %% Data Stores
    D1[(D1: Users)]
    D2[(D2: Documents)]
    D3[(D3: Activity Logs)]

    %% Process 1.0: Autentikasi
    PE -->|Cek Kredensial| P1
    AD -->|Cek Kredensial| P1
    P1 <-->|Data User| D1
    P1 -->|Status Login| PE
    P1 -->|Status Login| AD

    %% Process 2.0: Manajemen Dokumen
    PE -->|Upload/Edit Dokumen| P2
    P2 <-->|Simpan/Ambil Dokumen| D2
    P2 -->|Log Upload/Update| P5
    P2 -->|File/Daftar Dokumen| PE

    %% Process 3.0: Manajemen Trash (Soft Delete)
    PE -->|Hapus/Restore Dokumen| P3
    P3 <-->|Update Status deleted_at| D2
    P3 -->|Log Hapus/Restore| P5

    %% Process 4.0: Manajemen User
    AD -->|Approve/Hapus User| P4
    P4 <-->|Update Status Approval| D1
    P4 -->|Log Manajemen User| P5

    %% Process 5.0: Logging & Pelaporan
    P5 -->|Simpan Log| D3
    D3 -->|Ambil Data Log| P5
    D2 -->|Ambil Data Export| P5
    P5 -->|Log Aktivitas| AD
    P5 -->|Export CSV| PE
```

## Penjelasan Singkat

1.  **D1: Users**: Menyimpan data akun pegawai, termasuk status approval oleh admin.
2.  **D2: Documents**: Menyimpan metadata dokumen (judul, deskripsi, nominal, dll) dan path file fisik.
3.  **D3: Activity Logs**: Mencatat setiap aksi penting (Login, Upload, Edit, Delete) untuk keperluan audit.
4.  **Soft Delete**: Proses hapus dokumen (P3) tidak langsung menghapus dari database, melainkan memperbarui kolom `deleted_at`.
