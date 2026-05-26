# 🏛️ SIMAUCATAR — Sistema Jestaun Funsionáriu Postu Administrativu Maucatar

> **HRIS (Human Resource Information System)** ba jestaun funsionáriu iha Postu Administrativu Maucatar, Timor-Leste.
> Dezenvolve uza **CodeIgniter 4** · **PHP 8.4** · **Bootstrap 5** · **MySQL**

---

## 📋 Deskrisaun Jerál

**SIMAUCATAR** mak aplikasaun web HRIS (Human Resource Information System) ne'ebé dezaina atu jere atividades rekursu umanu iha Postu Administrativu Maucatar. Sistema ida-ne'e fasilita jestaun funsionáriu, prezensa loroloron, pedidu lisensa, pagamentu saláriu, avizu ofisiál, no sansaun disiplinar hotu iha plataforma ida.

---

## ✨ Fitúr Prinsipál

### 👥 1. Jestaun Funsionáriu
- **Rejistrasaun Funsionáriu Foun** — Hatama dadus pesoál kompletu funsionáriu: NID, naran, seksu, fatin moris, data moris, hela fatin, telefone, no estadu sivil.
- **Organiza ho Departamentu, Pozisaun & Kategoria** — Kada funsionáriu liga ba departamentu, pozisaun, no kategoria/golongan.
- **Upload Foto Perfil** — Suporta karga foto profil funsionáriu.
- **Kria Akun Login Automátiku** — Bainhira rejista funsionáriu foun, sistema kria akun utilizador (username & password) automátikamente.
- **Atualiza & Hamos Dadus** — Administradór bele halo edisaun no eliminasaun dadus funsionáriu.

---

### 🗓️ 2. Jestaun Prezensa (Absénsia)
- **Clock In & Clock Out** — Funsionáriu bele rejista tama (clock in) no sai (clock out) uza sistema online.
- **Konfigurasaun Oras Absénsia** — Administradór bele define oras loke/taka ba Tama no Sai, inklui toleránsia minutu atrazu.
- **Deteisaun Estadu Automátiku** — Sistema hatene automátiku se funsionáriu 'Prezente', 'Tardi', 'Falta', ka 'Lisensa'.
- **Suporta Fim-de-Semana** — Administradór bele ativa/desativa absénsia loron Sábadu no Domingu.
- **Haree Istória Prezensa** — Funsionáriu no Administradór bele haree rejistus prezensa tomak.

---

### 🏖️ 3. Jestaun Lisensa (Cuti/Izin)
- **Pedidu Lisensa Online** — Funsionáriu bele submete pedidu lisensa (Moras, Anual, Maternidade, Lutu, Seluk) direitamente husi sistema.
- **Upload Dokumentu Suporta** — Funsionáriu bele karga dokumentu suporta (sertifikadu moras, etc.).
- **Validasaun Data Inteligente** — Sistema labele simu pedidu lisensa ba loron ne'ebé funsionáriu prezente ona.
- **Aprovasaun ba Administradór** — Administradór bele **Aprova** ka **Rejeita** pedidu ho komentáriu.
- **Atualiza Prezensa Automátiku** — Bainhira lisensa aprova, sistema insere rekord 'Lisensa' automátiku ba tabela prezensa ba loron hotu-hotu ne'ebé tama iha períodu lisensa.
- **Filtragem por Estadu** — Lista lisensa bele filtra: Hotu, Pendente, Aprovadu, Rezeitadu.

---

### 💰 4. Jestaun Saláriu (Payroll)
- **Kalkulasaun Saláriu Automátiku** — Sistema kalkula saláriu bazeia ba pozisaun funsionáriu + total subsídiu - total deskontu/sansaun.
- **Jestaun Subsídiu** — Administradór bele define tipu subsídiu no valór padraun nian (ex: Subsídiu Transporte, Subsídiu Refeisaun).
- **Integrasaun Sansaun** — Potongan husi sansaun aktiva kalkulasaun automátiku no dedus husi saláriu líkuidu.
- **Rejistus Detallu (Payslip)** — Sistema guarda detállu komponente saláriu (subsidiu no deskontu) ba kada pagamentu.
- **Verifika Duplikasaun** — Sistema bele deteita se saláriu ba fulan/tinan ne'e prosesa ona.
- **Haree Resibu Saláriu** — Funsionáriu bele haree resibu saláriu nian rasik de'it (protesaun privacidade garantida).

---

### 📢 5. Jestaun Avizu (Anúnsiu)
- **Publika Avizu** — Administradór bele publika avizu/anúnsiu ne'ebé funsionáriu hotu bele haree.
- **Tempu Expirasaun** — Administradór bele define data no oras remata ba avizu sira.
- **Avizu Automátiku** — Sistema publika avizu automátiku bainhira iha mudansa konfigurasaun prezensa ka bainhira sansaun fo/retira.

---

### ⚖️ 6. Jestaun Sansaun Disiplinar
- **Kria Tipu Sansaun** — Administradór define tipu sansaun ho kategoria: *Korta Saláriu*, *Hatun Pozisaun*, ka *Seluk*.
- **Fo Sansaun ba Funsionáriu** — Rekorda motivu, data sansaun, no valór potongan.
- **Hatun Pozisaun (Demoção)** — Sistema bele halo demoção automátiku — muda pozisaun funsionáriu ba nivel kraik.
- **Retira Sansaun** — Administradór bele kansela sansaun; se sansaun "Hatun Pozisaun", pozisaun funsionáriu fila fali automátiku.
- **Jera Sansaun Absénsia Automátiku** — Ba funsionáriu ho falta ≥ 3 loron iha fulan ida, sistema bele jera sansaun potongan saláriu automátiku (0.9% × saláriu baziku ba kada 3 falta).
- **Rekord Pagamentu Sansaun** — Kada prosesamentu saláriu rastreia pagamentu sansaun aktiva sira.

---

### 📊 7. Módulu Relatóriu & Vizualizasaun Dadus
- **Relatóriu Funsionáriu** — Lista funsionáriu ho filtru Departamentu & Pozisaun. Hatudu: NID, Naran, Departamentu, Pozisaun, Kategoria.
- **Relatóriu Prezensa** — Rekapitulasaun prezensa ho filtru data range & departamentu. Hatudu: Total Prezente, Falta, Tardi, Lisensa.
- **Relatóriu Saláriu** — Sumáriu pagamentu saláriu ho filtru fulan/tinan. Hatudu: Saláriu Báziku, Subsídiu, Deskontu, Líkuidu.
- **Relatóriu Lisensa** — Lista pedidu lisensa ho filtru data range & estadu lisensa.
- **Relatóriu Sansaun** — Lista sansaun ho filtru fulan/tinan & estadu sansaun.
- **Exporta PDF** — Relatóriu hotu bele esporta hanesan file PDF (uza DomPDF).
- **Exporta CSV** — Relatóriu hotu bele esporta hanesan file CSV/Excel.

---

### 📈 8. Dashboard & Gráfiku Interativu
- **Dashboard Administradór:**
  - 📉 Gráfiku Tendénsia Prezensa (Line Chart) — hatudu Prezente vs Falta ba loron 15 ikus.
  - 🍩 Gráfiku Kompozisaun Departamentu (Donut Chart) — hatudu proporsaun funsionáriu ba kada departamentu.
  - Estatístika kard: Total Funsionáriu, Total Prezensa Ohin, Pedidu Lisensa Pendente.
- **Dashboard Funsionáriu:**
  - 🥧 Gráfiku Dezempeñu Prezensa Pesoál (Pie Chart) — hatudu persentajen Prezente, Falta, Lisensa nian.
  - Hatudu avizu/anúnsiu ikus husi administrasaun.

---

### ⚙️ 9. Konfigurasaun Sistema & Jestão Utilizadór
- **Jestaun Papel (Role)** — Define papel utilizadór: `administrador` no `funsionariu`.
- **Jestaun Utilizadór** — CRUD ba akun utilizadór sistema nian.
- **Kontrólu Aksesu Menu** — Administradór bele define menu sira ne'ebé papel ida-idak bele haree ka la bele.
- **Jestão Menu Dinámiku** — Menu foun bele aumenta no sistema kria automátiku controller file no view file foun.

---

### 🔐 10. Autentikasaun & Seguransa
- **Login Sistema** — Uza username no password ho hash seguru (`password_hash()`).
- **Kontrólu Sesaun** — Uza CodeIgniter Session ba jestaun sesaun utilizadór.
- **Filtru Papel** — URL `/administrador/*` blokeadu ba utilizadór ne'ebé papel la'os administrador; hanesan mós URL `/funsionariu/*`.
- **Protesaun CSRF** — Form hotu-hotu ho eksportasaun iha protesaun husi atake *Cross-Site Request Forgery*.
- **Privacidade Saláriu** — Funsionáriu de'it bele haree resibu saláriu nian rasik, la bele haree resibu ema seluk nian.

---

## 🖥️ Rekizitu Servidor

| Rekizitu | Versaun Minimál |
|---|---|
| PHP | 8.1+ (rekomendu 8.4) |
| CodeIgniter | 4.6.1 |
| MySQL | 5.7+ / MariaDB 10.4+ |
| Bootstrap | 5.x |

**Extensaun PHP ne'ebé presiza:**
- `curl`, `fileinfo`, `gd`, `intl`, `mbstring`, `mysqlnd`, `openssl`, `json`, `xml`

---

## 📦 Libraria Eksternu

| Libraria | Finalidade |
|---|---|
| `dompdf/dompdf` | Jera & esporta relatóriu PDF |
| `phpoffice/phpspreadsheet` | Jera & esporta relatóriu CSV/Excel |
| `ApexCharts` (CDN) | Gráfiku interativu iha dashboard |
| `SweetAlert2` | Notifikasaun/alertas ne'ebé bonitu |
| `DataTables` | Tabela ho paginasaun interativu |

---

## 🚀 Instalasaun & Konfigura

### Pasu 1 — Kopia Projetu
```bash
git clone <url-repositoriu>
cd simaucatar
```

### Pasu 2 — Instala Dependénsia
```bash
composer install
```

### Pasu 3 — Konfigura Ambiente
```bash
# Kopia file env ba .env
cp env .env
```
Edita file `.env`, konfigura:
```ini
app.baseURL = 'http://localhost/simaucatar/public/'
database.default.hostname = localhost
database.default.database = simaucatar_db
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
```

### Pasu 4 — Prepara Baze de Dadus
```bash
# Kria baze de dadus foun
php spark db:create

# Halai migrasyaun tabela
php spark migrate

# Hatama dadus inisiál (utilizadór default)
php spark db:seed Users

# Jera kodigifikasaun nia xave
php spark key:generate
```

### Pasu 5 — Hahu Servidor
```bash
php spark serve
```
Sistema bele asesu iha: **http://localhost:8080**

---

## 🗂️ Estrutura Projetu

```
simaucatar/
├── app/
│   ├── Controllers/
│   │   ├── Administrador.php   # Lójika hotu ba portál administradór
│   │   ├── Funsionariu.php     # Lójika hotu ba portál funsionáriu
│   │   ├── Relatoriu.php       # Módulu relatóriu & esportasaun
│   │   ├── Auth.php            # Login & logout
│   │   └── Settings.php        # Konfigurasaun utilizadór & menu
│   ├── Models/
│   │   ├── ApplicationModel.php  # Model jerál ba operasaun CRUD
│   │   └── RelatoriuModel.php    # Query espesializadu ba relatóriu
│   ├── Views/
│   │   ├── layouts/            # Template layout prinsipál
│   │   ├── pages/
│   │   │   ├── administrador/  # View ba portál administradór
│   │   │   └── funsionariu/    # View ba portál funsionáriu
│   │   └── components/         # Komponente UI ne'ebé reutilizável
│   ├── Config/                 # Konfigurasaun aplikasaun & routing
│   ├── Database/               # Migrasyaun & seeder
│   └── Filters/                # Guard/filter ba protesaun rota
├── public/                     # Entry point & assets públiku
│   └── uploads/                # Foto perfil & dokumentu karga nian
├── composer.json
└── README.md
```

---

## 👤 Papel Utilizadór (User Roles)

| Papel | Asesu |
|---|---|
| **Administradór** | Asesu tomak ba jestaun funsionáriu, prezensa, lisensa, saláriu, avizu, sansaun, relatóriu, no konfigurasaun |
| **Funsionáriu** | Asesu limitadu: haree dashboard rasik, clock in/out, pedidu lisensa, haree resibu saláriu, no perfil |

---

## 💡 Nota Tékniku

- **Moeda**: Dolar Amerikanu (USD / $), formatadu ho `number_format()` PHP.
- **Formatu Data**: `DD-MM-YYYY HH:mm` tuir formatu lokál Timor-Leste.
- **Seguransa BD**: Query agregasaun (`SUM`, `COUNT`) halo iha nivel database, la iha PHP loop, atu garantia dezempeñu di'ak.
- **Transasaun DB**: Operasaun krítiku (prosesamentu saláriu, retira sansaun) uza `transBegin/transCommit/transRollback` atu garantia konsisténsia dadus.

---

## 📜 Lisensa

Projetu ida-ne'e lisensa ho [MIT License](LICENSE).

---

<div align="center">
  <p>Dezenvolve ho ❤️ ba Postu Administrativu Maucatar, Timor-Leste 🇹🇱</p>
</div>
