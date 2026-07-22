# Dokumentu Rekizitu Tékniku (TRD) & Development Issue
**Títulu Projetu:** Sistema Jestaun Funsionáriu Postu Administrativu Maucatar
**Objetivu:** Aplikasaun HRIS (Human Resource Information System) hodi jere dadus funsionáriu, prezensa loroloron, pedidu lisensa (cuti/izin), pagamentu saláriu (payroll), avizu, no sansaun disiplinar.
**Targetu Dezenvolvedór:** Junior Developer / AI Agent. (Sistema tenke kria integra ba Template CodeIgniter ne'ebé iha hela folder ida ne'e).

---

## 1. TEKNOLOJIA (Tech Stack)
- **Framework Báziku:** **CodeIgniter** (Integrasaun diretu ho template ne'ebé eziste ona iha workspace ne'e. La presiza setup framework foun ka starter kit seluk).
- **Baze de Dadus (Database):** MySQL (Uza CI Query Builder / CI Models hodi halo operasaun CRUD).
- **Dezain (Styling):** Uza CSS/Template ne'ebé eziste ona iha projeto ne'e (Tailwind/Bootstrap).
- **Kompónente Ekstra:** SweetAlert2 (Ba notifikasaun), DataTables (Tabela ho paginasaun), DomPDF/TCPDF ka MPDF (Ba export Resibu PDF).

---

## 2. BAZE DE DADUS (Database Schema)
*Atenasaun ba Developer: Naran tabela no koluna hotu tenke uza lian Tetun. Kria SQL Schema/Migrations tuir lista iha kraik.*

### 2.1. Tabela `papel` (Roles)
- `id` (Primary Key, INT, Auto Increment)
- `naran_papel` (Varchar 50, Unique) -> Valór: 'administrador', 'funsionariu'

### 2.2. Tabela `utilizador` (Users)
- `id` (Primary Key, INT, Auto Increment)
- `naran_utilizador` (Varchar 100, Unique, Required) -> Username ba login.
- `xave_secreta` (Varchar 255, Hashed, Required) -> Password (uza `password_hash()` iha PHP/CI).
- `papel_id` (Foreign Key -> `papel.id`)
- `estadu_kontu` (Enum: 'Ativu', 'Inativu', Default: 'Ativu')
- `created_at`, `updated_at` (Datetime)

### 2.3. Tabela Dadus Báziku (Master Data)
**Tabela `departamentu`**
- `id` (PK, INT)
- `naran_departamentu` (Varchar 100, Required)

**Tabela `pozisaun` (Jabatan/Position)**
- `id` (PK, INT)
- `naran_pozisaun` (Varchar 100, Required)
- `salariu_baziku` (Decimal 10,2, Required) -> Vensimentu báziku ba pozisaun ne'e.

**Tabela `kategoria` (Golongan/Grade)**
- `id` (PK, INT)
- `naran_kategoria` (Varchar 50, Required)

### 2.4. Tabela `funsionariu` (Employee Data)
- `id` (PK, INT)
- `utilizador_id` (Foreign Key -> `utilizador.id`, Nullable, Unique)
- `nid` (Varchar 50, Unique, Required) -> Númeru Identifikasaun (NID).
- `naran_kompletu` (Varchar 150, Required)
- `seksu` (Enum: 'Mane', 'Feto', Required)
- `fatin_moris` (Varchar 100, Required)
- `data_moris` (Date, Required)
- `hela_fatin` (Text, Required)
- `nu_telefone` (Varchar 20, Required)
- `estadu_sivil` (Enum: 'Solteiru', 'Kaben Nain', 'Divorsiadu', Required)
- `departamentu_id` (FK -> `departamentu.id`, Required)
- `pozisaun_id` (FK -> `pozisaun.id`, Required)
- `kategoria_id` (FK -> `kategoria.id`, Required)
- `data_hahu_servisu` (Date, Required)
- `foto_perfil` (Varchar 255, Nullable)
- `created_at`, `updated_at`

### 2.5. Tabela `prezensa` (Attendance)
- `id` (PK, INT)
- `funsionariu_id` (FK -> `funsionariu.id`, Required)
- `data_prezensa` (Date, Required)
- `oras_tama` (Time, Nullable) -> Clock in.
- `oras_sai` (Time, Nullable) -> Clock out.
- `estadu_prezensa` (Enum: 'Prezente', 'Loron Sorin', 'Falta', 'Lisensa', Required)
- `foto_tama` (Varchar 255, Nullable)
- `kordenada` (Varchar 100, Nullable)
- `created_at`, `updated_at`

### 2.6. Tabela `lisensa` (Leave/Permit Requests)
- `id` (PK, INT)
- `funsionariu_id` (FK -> `funsionariu.id`, Required)
- `tipu_lisensa` (Enum: 'Moras', 'Anual', 'Maternidade', 'Lutu', 'Seluk', Required)
- `data_hahu` (Date, Required)
- `data_remata` (Date, Required)
- `razaun` (Text, Required)
- `dokumentu_suporta` (Varchar 255, Nullable)
- `estadu_lisensa` (Enum: 'Pendente', 'Aprovadu', 'Rezeitadu', Default: 'Pendente')
- `komentariu_admin` (Text, Nullable)
- `created_at`, `updated_at`

### 2.7. Tabela `salariu` & `salariu_detallu` (Payroll)
**Tabela `salariu`**
- `id` (PK, INT)
- `funsionariu_id` (FK -> `funsionariu.id`, Required)
- `fulan` (Int 1-12, Required)
- `tinan` (Year, Required)
- `salariu_baziku` (Decimal 10,2, Required)
- `total_subsidiu` (Decimal 10,2, Required, Default: 0)
- `total_deskontu` (Decimal 10,2, Required, Default: 0)
- `salariu_liquidu` (Decimal 10,2, Required) -> Gaji Bersih
- `estadu_pagamentu` (Enum: 'Seidauk Selu', 'Selu Ona', Default: 'Seidauk Selu')
- `data_pagamentu` (Date, Nullable)
- `created_at`, `updated_at`

**Tabela `salariu_detallu`**
- `id` (PK)
- `salariu_id` (FK -> `salariu.id`, On Delete Cascade)
- `naran_komponente` (Varchar 100)
- `valór` (Decimal 10,2)
- `tipu` (Enum: 'Subsidiu', 'Deskontu')

### 2.8. Tabela `avizu` & `sansaun` (Fitur Kompletu HR)
**Tabela `avizu` (Announcements/Pengumuman)**
- `id` (PK)
- `titulu` (Varchar 255, Required)
- `konteudu` (Text, Required)
- `data_publikasaun` (Date, Required)
- `created_at`, `updated_at`

**Tabela `sansaun` (Disciplinary/Teguran)**
- `id` (PK)
- `funsionariu_id` (FK -> `funsionariu.id`, Required)
- `tipu_sansaun` (Enum: 'Avisu Lisan', 'Avisu Eskritu 1', 'Avisu Eskritu 2', 'Suspensaun', Required)
- `motivu` (Text, Required)
- `data_sansaun` (Date, Required)
- `created_at`, `updated_at`

---

## 3. PÁJINA, FORMULÁRIU & VALIDASAUN CRUD (CodeIgniter Standard)

### 3.1. Pájina Autentikasaun (`/login`)
- **Controller/Route:** `Login::index` no `Login::process`
- **Form:** `naran_utilizador` (Text), `xave_secreta` (Password).
- **Lójika CI:** Verifika login no salva ba `session()->set()`. Salva `utilizador_id`, `papel_id`, no `funsionariu_id` (se iha). Redirect ba `/administrador/dashboard` ka `/funsionariu/dashboard` hodi uza `redirect()->to()`.

### 3.2. PORTÁL ADMINISTRADÓR

#### A. Dashboard (`/administrador/dashboard`)
- Hatudu estatístika ho query husi CI Models. Hatudu Avizu ikus no listagem Sansaun.

#### B. Jestaun Dadus Báziku (`/administrador/departamentu`, `/administrador/pozisaun`, `/administrador/kategoria`)
- CRUD padraun uza CI Validation Service (`$validation->run()`).

#### C. Jestaun Funsionáriu (`/administrador/funsionariu`)
- **Kria Foun:** Kompleta form `funsionariu`. Kria kontu automátiku: insert uluk ba tabela `utilizador`, foti Insert ID (`$db->insertID()`), foin insert ba tabela `funsionariu`.
- **Upload File:** Uza library CI ba Upload hodi move file foto_perfil.
- **Hamos:** Uza Soft Delete. Keta hamos loloos, altera de'it koluna `estadu_kontu` ba 'Inativu' iha tabela `utilizador`.

#### D. Jestaun Prezensa (`/administrador/prezensa`)
- Lista prezensa. Admin bele halo *override* uza form manual (Update/Insert model Prezensa).

#### E. Jestaun Lisensa (Aprovasaun Pedidu) (`/administrador/lisensa`)
- Iha pájina ne'e tenke iha **Tab Menu** 4:
  1. **Hotu (All)**
  2. **Pendente (Pending)**
  3. **Aprovadu (Approved)**
  4. **Rezeitadu (Rejected)**
- Kuandu Aprovadu, modelu CI sei insert automátiku loron hotu-hotu husi `data_hahu` to'o `data_remata` ba tabela `prezensa` ho estadu 'Lisensa'.

#### F. Jestaun Saláriu (`/administrador/salariu`)
- **Lójika CodeIgniter:** Kria Controller ba kalkulasaun. Query ba tabel `prezensa` baseia ba `funsionariu_id` no fulan. Hitung valór -> insert ba tabel `salariu` & `salariu_detallu`. Print Resibu PDF uza library kompativel CI.

#### G. Avizu & Sansaun (`/administrador/avizu`, `/administrador/sansaun`)
- CRUD padraun ba tabela rua ne'e uza CI Controller & View.

---

### 3.3. PORTÁL FUNSIONÁRIU (SELF-SERVICE)

#### A. Dashboard Funsionáriu (`/funsionariu/dashboard`)
- Query Avizu ikus husi tabela `avizu` atu hatudu iha Dashboard. Hatudu estatístika prezensa fulan atuál.

#### B. Prezensa (Clock In / Clock Out) (`/funsionariu/prezensa`)
- Botão boot ba **TAMA** no **SAI**.
- Uza lójika CodeIgniter atu *disable* botão se `session()->get('funsionariu_id')` iha ona prezensa ba loron ohin (`date('Y-m-d')`). Labele clock in ba loron aban.

#### C. Ha'u-nia Perfil (`/funsionariu/perfil`)
- Form Update ba fields balun de'it. Form troka *password* uza CI Validation rule: `matches[xave_foun]`.

#### D. Pedidu Lisensa (`/funsionariu/lisensa`)
- **Tab Menu Estadu:** "Hotu", "Pendente", "Aprovadu", "Rezeitadu".
- **Upload File:** Upload `dokumentu_suporta` uza CI File Upload.
- Labele hamos/edita pedidu ne'ebé la'os 'Pendente' ona.

#### E. Resibu Saláriu (Payslip) (`/funsionariu/salariu`)
- **Seguransa CI:** Model Query **TENKE** uza klausa `$builder->where('funsionariu_id', session()->get('funsionariu_id'))` atu proteje privasidade!

---

## 4. PADRAUN UI/UX & BEHAVIOR SISTEMA (CODEIGNITER)
1. **Notifikasaun (Alertas):** Uza `session()->setFlashdata('success', 'Mensajen...')` iha Controller, no implementa SweetAlert2 iha views hodi kaer flashdata ne'e.
2. **Error Handling Form:** Uza Validation CodeIgniter hodi hatudu erro iha kraik input form (ex: `$validation->getError('campo')`). Hatama fali dadus uluk ba form uza funsaun `set_value('campo')`.
3. **Paginasaun Tabela:** Uza DataTables (client-side ka server-side CI) ba tabela hotu hodi suporta limit 10-15 dadus por pájina.
4. **Guards / Filters (CI4) ka Middleware/Hooks (CI3):** 
   - Kria *Filters* atu blokeia rotas/URL `/administrador/*` ba user ne'ebé la'os papel 'administrador'.
   - Hanesan mós ba URL `/funsionariu/*`, tenke blokeia se papel laloos.
5. **Formatasaun Jeral:**
   - Moeda (Osan): Dolar Amerikanu (USD / $). Uza funsaun `number_format()` iha PHP.
   - Data & Oras: Format loron lokál Timor-Leste `DD-MM-YYYY HH:mm` (uza funsaun `date()` iha PHP).
