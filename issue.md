# Implementasaun Módulu Relatóriu & Sistema Vizualizasaun Dadus (Analytics)

## 1. Deskrisaun Jerál
Dokumentu ida-ne'e mak planu tékniku (blueprint) atu dezenvolve fitúr oin rua ba sistema HRIS Simaucatar:
1. **Módulu Relatóriu (Reporting):** Fitúr atu filtra, haree, no halo esportasaun (PDF/Excel) ba dadus operasionál HRIS nian.
2. **Vizualizasaun Dadus (Gráfiku/Charts):** Aumenta elementu gráfiku interativu iha dashboard sira hodi fasilita análize ba dezempeñu no tendénsia dadus.

Dokumentu ne'e hakerek ho detallu (Pasu-tuir-Pasu) atu bele ezekuta direita husi Junior Developer ka AI Coding Agent. Lingua, variável, no baze de dadus iha instrusaun ne'e tuir padraun sistema nian ne'ebé uza lian Tetun.

---

## 2. Preparasaun & Instalasaun Dependency (Obrigatóriu!)

Molok komesa kódigu iha Controller ka View, tenke garante katak library suporta nian instala ona.

**A. Library Backend (Export PDF & Excel)**
Loke terminál iha root folder projetu nian (`simaucatar/`), no halai komandu ne'e:
```bash
composer require dompdf/dompdf
composer require phpoffice/phpspreadsheet
```

**B. Library Frontend (Gráfiku/Charts)**
Ita sei uza **ApexCharts**. La presiza `npm install`, tau de'it CDN ne'e iha file layout prinsipál frontend nian (baibain iha `app/Views/layouts/main.php` molok tag `</body>`):
```html
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
```

---

## PART A: DEZENVOLVIMENTU MÓDULU RELATÓRIU

### 1. Lista Relatóriu ne'ebé Sei Kria
*   **Relatóriu Funsionáriu:** Filtru (Departamentu, Pozisaun). Hatudu lista funsionáriu. Dadus: NID, Naran Kompletu, Departamentu, Pozisaun, Estadu, Data Hahu Servisu.
*   **Relatóriu Prezensa:** Filtru (Data Hahu - Data Remata, Departamentu). Hatudu rekapitulasaun: Total Prezente, Falta, Tardi, Lisensa.
*   **Relatóriu Saláriu:** Filtru (Fulan, Tinan). Hatudu rekapitulasaun: Saláriu Báziku, Total Subsídiu, Total Deskontu, Saláriu Líkuidu.
*   **Relatóriu Lisensa:** Filtru (Data Hahu - Data Remata, Estadu Lisensa). Hatudu dadus cuti/lisensa husi funsionáriu sira.
*   **Relatóriu Sansaun:** Filtru (Fulan/Tinan, Estadu Sansaun). Hatudu lista sansaun (Ativu, Konkluidu, Retira) no valor_total (potongan).

### 2. Konfigurasaun Routing (`app/Config/Routes.php`)
Aumenta blok route ne'e iha laran grup `administrador`:
```php
$routes->group('administrador/relatoriu', static function ($routes) {
    $routes->get('/', 'Relatoriu::index'); // Dashboard Relatóriu (Pájjina hili relatóriu)
    
    // Rota ba Tampilan (View)
    $routes->get('funsionariu', 'Relatoriu::funsionariu');
    $routes->get('prezensa', 'Relatoriu::prezensa');
    $routes->get('salariu', 'Relatoriu::salariu');
    $routes->get('lisensa', 'Relatoriu::lisensa');
    $routes->get('sansaun', 'Relatoriu::sansaun');
    
    // Rota ba Export (POST form submission)
    $routes->post('export/funsionariu', 'Relatoriu::exportFunsionariu');
    $routes->post('export/prezensa', 'Relatoriu::exportPrezensa');
    $routes->post('export/salariu', 'Relatoriu::exportSalariu');
    $routes->post('export/lisensa', 'Relatoriu::exportLisensa');
    $routes->post('export/sansaun', 'Relatoriu::exportSansaun');
});
```

### 3. Lójika Backend (Model & Controller)

**A. Model Relatóriu (`app/Models/RelatoriuModel.php`)**
Sujere tebes atu kria Model foun espesífiku ba relatóriu.
*Kria funsaun query ne'ebé simu paramétru filtru.* Ezemplu lójika query ba `getRekapPrezensa($data_hahu, $data_remata, $departamentu_id)`:
Tenke uza `SUM` no `IF` SQL atu sura total:
```sql
SELECT f.nid, f.naran_kompletu,
  SUM(IF(p.estadu_prezensa = 'Prezente', 1, 0)) as total_prezente,
  SUM(IF(p.estadu_prezensa = 'Falta', 1, 0)) as total_falta,
  SUM(IF(p.estadu_prezensa = 'Tardi', 1, 0)) as total_tardi,
  SUM(IF(p.estadu_prezensa = 'Lisensa', 1, 0)) as total_lisensa
FROM prezensa p
JOIN funsionariu f ON p.funsionariu_id = f.id
WHERE p.data_prezensa BETWEEN 'data_hahu' AND 'data_remata'
GROUP BY p.funsionariu_id
```

**B. Controller (`app/Controllers/Relatoriu.php`)**
*   **Method View (Ezemplu: `prezensa()`):** 
    Kaptura filtru GET (`$this->request->getGet('data_hahu')`), bolu funsaun husi Model, haruka dadus ba view `pages/administrador/relatoriu/prezensa.php`.
*   **Method Export (Ezemplu: `exportPrezensa()`):**
    Verifika `$this->request->getPost('export_type')`.
    *   Se `pdf`: Karga dadus, manda ba view HTML simples (ezemplu, `print_prezensa.php`), tuirmai load ba Dompdf -> `stream("Relatoriu_Prezensa.pdf")`.
    *   Se `excel`: Inisia `$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet()`, tau header ba koluna sira, looping dadus atu prienxe liña, kria header HTTP atu halo download file `.xlsx`.

### 4. User Interface (Views ba Relatóriu)
Ba file view idaidak nian (ezemplu `prezensa.php`), tenke asegura estrutura hanesan ne'e:
1.  **Form Filtru (Leten):** Uza method `GET`. Iha input data (data_hahu, data_remata) no dropdown (departamentu_id). Botoun "Filtra Relatóriu".
2.  **Botoun Export (Klaran):** Botoun "Exporta PDF" no "Exporta Excel". Botoun sira ne'e sei trigger form seluk (method `POST`) ba rota `/export` ne'ebé lori paramétru filtru ne'ebé ativu (bele uza `input type="hidden"`).
3.  **Tabela Dadus (Okos):** Render loop dadus PHP (foreach) iha ne'e.

---

## PART B: IMPLEMENTASAUN VIZUALIZASAUN DADUS (GRÁFIKU/CHARTS)

Objektivu: Konverte dadus númeru ba vizuál atu fasilita análize dezempeñu.

### 1. Dashboard Administrador (`app/Views/pages/administrador/dashboard.php`)
Aumenta 2 Gráfiku (Chart) Prinsipál:

**A. Gráfiku Tendénsia Prezensa Mensál (Line Chart)**
*   **Fatin:** Parte leten iha dashboard (Luan 100%).
*   **Dadus Controller:** Sura total 'Prezente', 'Falta', 'Tardi' loroloron ba loron 30 ikus nian. Haruka hanesan JSON array (data) no JSON array (valór).
*   **Lójika Frontend (ApexCharts):** Kria gráfiku liña (line chart) ho Eixu X = Loron, Eixu Y = Total Funsionáriu. Tau kór Matak (Prezente), Mean (Falta), Kinur (Tardi).

**B. Gráfiku Kompozisaun Departamentu (Donut Chart)**
*   **Fatin:** Parte okos ka sorin.
*   **Dadus Controller:** `SELECT departamentu_id, COUNT(id) FROM funsionariu GROUP BY departamentu_id` (Junta ho tabela departamentu atu hetan naran).
*   **Lójika Frontend:** Hatudu Donut chart atu haree proporsaun funsionáriu iha kada departamentu.

### 2. Dashboard Funsionáriu (`app/Views/pages/funsionariu/dashboard.php`)
Kada funsionáriu bele haree ninia dezempeñu rasik.

**A. Gráfiku Dezempeñu Prezensa Pessoál (Pie Chart)**
*   **Fatin:** Parte sorin loos ka klaran.
*   **Dadus Controller:** Foti total (COUNT) Prezente, Falta, Tardi, Lisensa bazeia ba ID Funsionáriu ne'ebé login ba fulan/tinan atual.
*   **Lójika Frontend:** Render Pie Chart ho label persentajen. Ezemplu: "Prezente 80%, Falta 10%, Tardi 10%".

### 3. Vizualizasaun Adisionál iha Módulu Relatóriu (Bonus Level)
Bainhira Administrador filtra dadus (ezemplu: Relatóriu Prezensa ba fulan Jullu):
*   Aumenta **Bar Chart** ida iha tabela relatóriu nia leten.
*   Chart ne'e rezume (SUM) koluna sira ne'ebé iha tabela laran. (Ezemplu: Bar 1 = Total Prezente hotu iha fulan ne'e, Bar 2 = Total Falta hotu, nsst.).
*   *Implementasaun:* Dadus ba chart ne'e bele foti direita husi array PHP ne'ebé uza hodi halo loop ba tabela, konverte ba JSON (`json_encode()`), tuirmai hatama ba script Javascript ApexCharts iha view refere.

---

## 5. Padraun Kódigu & Gía ba Developer / AI Agent
1. **Seguransa:** Garante form export POST hotu iha protesaun husi *Cross-Site Request Forgery* (Uza fitúr CSRF husi Codeigniter 4).
2. **Dezempeñu Baze de Dadus:** Halo agregasaun (kálkulu `SUM`, `COUNT`) direitamente iha nivel Database (Query husi Model), **LABELE** halo looping ba rihun dadus husi Controller/View de'it atu sura total. Uza Query Builder CI4 ho loloos.
3. **Dezain UI:** Gráfiku (Charts) tenke *Responsive* (tuir medida monitor/hp). ApexCharts iha ona konfigurasaun responsive by default, asegura de'it container div uza grid Bootstrap ne'ebé loos (ezemplu, `col-md-12`, `col-lg-8`).
4. **Error Handling PDF/Excel:** Se dadus ne'ebé filtra mak mamuk (0 liña), no administrador hanehan botoun Export PDF/Excel, tenke mosu alert *Flashdata* (Error) "Dadus laiha, la bele halo esportasaun", duké kria file PDF ne'ebé mamuk ka aat.

---
**Estadu Dokumentu:** Prontu Ezekuta. 
**Prioridade:** Aas.
**Orden Ezekusaun Sujere:** Part A (Backend/Reports) uluk -> Part B (Vizualizasaun Dadus Frontend).
