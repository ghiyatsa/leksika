# Leksika — Pengecekan Orisinalitas Judul Skripsi

**Leksika** adalah sistem berbasis web untuk mendeteksi tingkat kemiripan antar judul skripsi menggunakan algoritma _hybrid TF-IDF Cosine Similarity_ dan _Jaccard Similarity_. Dikembangkan sebagai bagian dari penelitian di Program Studi Teknik Informatika, Universitas Malikussaleh.

---

## Latar Belakang

Pertumbuhan jumlah judul skripsi di lingkungan akademik meningkatkan risiko kemiripan antar judul yang dapat mengarah pada duplikasi atau plagiarisme. Diperlukan suatu sistem yang mampu mengukur tingkat kemiripan secara kuantitatif dan objektif menggunakan pendekatan _text mining_ dan perhitungan similaritas teks.

## Algoritma

Sistem menerapkan tiga metode perhitungan similaritas yang dikombinasikan secara hybrid:

### 1. Pra-Pemrosesan Teks

Sebelum perhitungan similaritas, teks melalui lima tahap pra-pemrosesan:

1. **Case folding** — konversi seluruh karakter menjadi huruf kecil (*lowercase*)
2. **Cleansing** — penghapusan tanda baca, angka, dan karakter khusus
3. **Tokenisasi** — pemisahan teks menjadi token/kata individual
4. **Stopword removal** — penghapusan kata umum tidak bermakna menggunakan Sastrawi
5. **Stemming** — pengembalian kata ke bentuk dasarnya menggunakan Sastrawi

### 2. TF-IDF (*Term Frequency — Inverse Document Frequency*)

TF-IDF adalah metode pembobotan yang merepresentasikan setiap dokumen sebagai vektor dalam ruang _terms_.

**Definisi:**

$$TF(t, d) = \frac{\mathrm{count}(t, d)}{\mathrm{total\_terms}(d)}$$

$$IDF(t) = \log\frac{N}{\mathrm{df}(t)}$$

$$TF\text{-}IDF(t, d) = TF(t, d) \times IDF(t)$$

Dimana:
- $N$ = jumlah total dokumen dalam korpus
- $df(t)$ = jumlah dokumen yang mengandung term $t$

### 3. Cosine Similarity

Similaritas kosinus mengukur kemiripan antara dua vektor TF-IDF berdasarkan sudut antar vektor dalam ruang _n_-dimensi.

**Definisi:**

$$\mathrm{Cosine}(A, B) = \frac{A \cdot B}{\|A\| \times \|B\|}$$

Nilai berkisar antara 0 (tidak mirip) hingga 1 (identik). Metode ini menangkap kemiripan semantik berdasarkan distribusi kata dalam dokumen.

### 4. Jaccard Similarity

Similaritas Jaccard mengukur kemiripan berdasarkan irisan dan gabungan dua himpunan token.

**Definisi:**

$$\mathrm{Jaccard}(A, B) = \frac{|A \cap B|}{|A \cup B|}$$

Metode ini menangkap kemiripan leksikal berdasarkan kata-kata yang sama-sama muncul di kedua dokumen.

### 5. Skor Hybrid

Skor akhir merupakan kombinasi linear dari Cosine Similarity dan Jaccard Similarity:

$$\mathrm{Hybrid} = w_1 \times \mathrm{Cosine} + w_2 \times \mathrm{Jaccard}$$

Bobot $w_1$ dan $w_2$ dapat dikonfigurasi oleh administrator melalui panel pengaturan threshold. Nilai default: $w_1 = 0{,}60$ (cosine), $w_2 = 0{,}40$ (jaccard).

## Klasifikasi Hasil

Berdasarkan skor hybrid, sistem mengklasifikasikan hasil ke dalam tiga kategori:

| Kategori | Rentang Hybrid | Interpretasi |
|----------|---------------|--------------|
| **Aman** | < $t_r$ | Judul cukup berbeda dan aman digunakan |
| **Perlu Ditinjau** | $t_r$ – $t_s$ | Terdapat kemiripan yang perlu ditinjau |
| **Sangat Mirip** | $\geq t_s$ | Judul memiliki kemiripan tinggi, disarankan revisi |

Dimana $t_r$ adalah _review threshold_ (default 0,40) dan $t_s$ adalah _similar threshold_ (default 0,75). Kedua ambang batas dapat disesuaikan oleh administrator.

## Arsitektur Sistem

```
Masukan (Judul + Kata Kunci)
             │
             ▼
┌───────────────────────────┐
│   TextPreprocessor        │
│   • Case folding          │
│   • Cleansing             │
│   • Stopword removal      │
│   • Stemming              │
│   • Tokenisasi            │
└────────────┬──────────────┘
             │
             ▼
┌──────────────────────────┐
│   SimilarityCalculator   │
│                          │
│   ┌──────────────────┐   │
│   │  TF-IDF Vectors  │   │
│   │  (seluruh korpus)│   │
│   └────────┬─────────┘   │
│            │             │
│   ┌────────▼─────────┐   │
│   │ Cosine Similarity│   │
│   └────────┬─────────┘   │
│            │             │
│   ┌────────▼─────────┐   │
│   │Jaccard Similarity│   │
│   └────────┬─────────┘   │
│            │             │
│   ┌────────▼─────────┐   │
│   │ Hybrid Score     │   │
│   │ w1×Cosine +      │   │
│   │ w2×Jaccard       │   │
│   └────────┬─────────┘   │
└────────────┬─────────────┘
             │
             ▼
┌──────────────────────────┐
│   Peringkat & Klasifikasi│
│   Top-5 hasil dengan     │
│   kategori: Aman /       │
│   Perlu Ditinjau /       │
│   Sangat Mirip           │
└──────────────────────────┘
```

## Dataset

Dataset judul skripsi yang digunakan dalam sistem merupakan data riil dari mahasiswa Program Studi Teknik Informatika Universitas Malikussaleh, mencakup 126 judul skripsi dari berbagai kategori topik, antara lain:

- Kecerdasan Buatan
- Pengembangan Web
- Data Mining
- Sistem Informasi
- Internet of Things
- Jaringan Komputer
- Mobile
- Keamanan Informasi

## Teknologi

| Komponen | Teknologi |
|----------|-----------|
| Bahasa Pemrograman | PHP 8.2 |
| Framework | CodeIgniter 4.7 |
| Basis Data | MySQL 8 / MariaDB |
| Autentikasi | Firebase Authentication |
| Text Mining | Sastrawi (stemming Bahasa Indonesia) |
| Server | FrankenPHP / Nginx + PHP-FPM |

## Struktur Database

```
users ──1:N── similarity_checks ──1:N── similarity_check_details ──N:1── thesis
                                                                           │
                                                           students ──1:1──┘
                                                                           │
                                                   topic_categories ──1:N──┘
```

## Lisensi

Proyek ini dilisensikan di bawah **MIT License**.

---

<p align="center">
  Dikembangkan untuk Program Studi Teknik Informatika<br>
  Universitas Malikussaleh
</p>
