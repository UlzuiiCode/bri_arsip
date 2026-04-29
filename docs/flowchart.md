# Flowchart Sistem - SiMArsip

Dokumen ini berisi bagan alir (flowchart) utama yang menggambarkan logika perjalanan pengguna dari awal membuka aplikasi, memilih menu di Dashboard, hingga keluar dari sistem.

---

## Flowchart Program

Sesuai dengan format yang diinginkan, berikut adalah flowchart yang digabungkan menjadi satu kesatuan utuh (Mulai $\rightarrow$ Login $\rightarrow$ 3 Pilihan Menu $\rightarrow$ Logout).

```mermaid
graph TD
    A([Mulai]) --> B[Akses Halaman Login]
    B --> C[/Input email & password/]
    C --> D{Login<br>berhasil?}
    D -- Tidak --> C
    D -- Ya --> E[Dashboard]
    
    E --> F{Pilih Menu}
    
    %% Kolom Kiri: Upload
    F -->|Upload Dokumen| G[Upload Dokumen]
    G --> G1[Isi Form & Pilih File]
    G1 --> G2[Simpan ke Database & Server]
    G2 --> L{Logout?}
    
    %% Kolom Tengah: Arsip
    F -->|Arsip Dokumen| H[Arsip Dokumen]
    H --> H1{Pilih aksi}
    
    H1 -- Cari --> H2[/Input keyword/]
    H2 --> H3[Tampilkan Data]
    H3 --> H1
    
    H1 -- Lihat --> H4[Preview Dokumen]
    H4 --> H1
    
    H1 -- Unduh --> H5[File diunduh ke perangkat]
    H5 --> H1
    
    H1 -- Edit --> H6[Tampil form edit dokumen]
    H6 --> H7[Simpan tetap di halaman arsip]
    H7 --> H1
    
    H1 -- Hapus --> H8[Hapus Dokumen]
    H8 --> L
    
    %% Kolom 3: Log Aktivitas
    F -->|Log Aktivitas| O[Log Aktivitas]
    O --> O1[Lihat Riwayat Tindakan<br>& Statistik]
    O1 --> L
    
    %% Kolom 4: Sampah & User (Admin)
    F -->|Sampah & User| I[Sampah & User]
    I --> I1[Kelola Sampah pulihkan/hapus permanen<br>& Kelola persetujuan User]
    I1 --> L
    
    %% Bawah: Logout
    L -- Tidak --> E
    L -- Ya --> M[Kembali ke Halaman Login]
    M --> N([Selesai])
```

### Penjelasan Singkat
1. **Mulai & Login**: Pengguna mengakses halaman login dan memasukkan email/password. Jika salah, kembali input. Jika benar, masuk ke Dashboard.
2. **Pilih Menu**: Dari Dashboard, alur terpecah menjadi 3 cabang menu utama:
   - **Upload Dokumen**: Untuk mengunggah dokumen baru.
   - **Arsip Dokumen**: Mengelola dokumen yang ada dengan opsi berulang (*Cari, Lihat, Unduh, Edit*) yang akan kembali ke "Pilih aksi". Memilih opsi *Hapus* akan menyelesaikan aksi di menu ini.
   - **Sampah & User**: (Khusus Admin) untuk mengelola data terhapus dan akun pegawai.
3. **Logout**: Setelah melakukan aksi, sistem menanyakan apakah ingin Logout. Jika "Tidak", kembali ke Dashboard. Jika "Ya", sesi berakhir dan selesai.
