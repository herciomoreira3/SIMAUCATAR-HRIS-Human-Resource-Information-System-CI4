# Planu Integrasaun, Migrasaun Dadus (Excel), no Mockup Sistema Tomak (HRIS Simaucatar)

## 1. Deskrisaun Jerál
Dokumentu ne'e mak matadalan (blueprint) super-detalladu atu halo importasaun dadus husi file Excel `DADUS FUNSIONARIO MAUCATAR.xlsx` ba baze de dadus HRIS Simaucatar, no **seeding/mocking ba Módulu Hotu-Hotu** (Prezensa, Lisensa, Sansaun, Saláriu, no Avizu). 

Objektivu husi prosesu ne'e mak atu **koko (testing) komprehensivu** ba Dashboard no Módulu Relatóriu ne'ebé foin dezenvolve. Bainhira script ne'e halai, sistema tomak sei nakonu ho dadus simulasaun ne'ebé loloos (realístiku) ba fulan ida kotuk.

---

## 2. Preparasaun (Prerequisites)
1.  **File Excel:** Garante katak file `DADUS FUNSIONARIO MAUCATAR.xlsx` tau ona iha laran projetu (ezemplu: `public/uploads/`).
2.  **Library:** Presiza library `phpoffice/phpspreadsheet` atu lee file Excel. Sujere kria Controller foun naran `Migrasi.php` ka script Command line atu prosesa lógika sira iha okos ne'e.

---

## 3. Pasu-tuir-Pasu Ezekusaun (Gía ba Developer / AI Agent)

### FASE 1: Lee no Normaliza Dadus Excel
1.  **Hamoos Anomalia (Data Cleaning):** 
    *   Se iha liña Excel ne'ebé mamuk ka iha string la lójika (ezemplu: naran funsionáriu nakonu ho númeru ka simbulu, NID mamuk), **skip** liña ne'e.
2.  **Prienxe Dadus ne'ebé Kurang:**
    *   Genera dadus *dummy* ba koluna ne'ebé mamuk iha Excel.
    *   `seksu`: Foti random 'M' ka 'F'.
    *   `data_moris`: Jera random tinan 1980 - 1995.
    *   `data_hahu_servisu`: Jera random husi tinan 2020 - 2023.
    *   `estadu_sivil`: Set default 'Solteiro' ka random.
    *   `nu_telefone`: Set random '7' + 7 díjitu.

### FASE 2: Inserte Dadus Master (Departamentu, Pozisaun, Kategoria)
*Foreign Key* rule: Master data tenke iha uluk molok hatama funsionáriu.
1.  Foti naran departamentu husi Excel. Se laiha iha tabela `departamentu`, `INSERT` no foti nia `ID`.
2.  Halo prosesu hanesan ba tabela `pozisaun` no tabela `kategoria`.

### FASE 3: Kria Akountu no Inserte Funsionáriu
1.  **Kria Utilizador:** Jera username (bazeia ba NID) ho password default (ezemplu: `password_hash('123456', PASSWORD_DEFAULT)`). `INSERT` ba tabela `utilizador` ho `papel_id = 2`.
2.  **Kria Funsionáriu:** Hatama dadus husi Fase 1 no Fase 2 ba tabela `funsionariu` (`nid`, `naran_kompletu`, `departamentu_id`, `pozisaun_id`, `kategoria_id`, `utilizador_id`, dsst.). Save hotu sira nia `id` atu uza iha Fase oin.

### FASE 4: Jera Dadus Mockup Absénsia (Prezensa) ba Fulan 1
Targetu: Fulan kotuk tomak (Loron 1 to'o Loron 30/31).
1.  Loop loron hotu iha fulan kotuk.
2.  Ba kada loron, loop ba kada `funsionariu_id` ne'ebé foin kria.
3.  **Finde-Semana:** Skip (la kria absénsia) se loron refere mak Sabadu ka Domingu.
4.  **Lógika Estadu (Random):**
    *   **85% Probabilidade:** `estadu_prezensa` = 'Prezente' (Set `oras_tama` foti random entre 07:30 to'o 08:30, `oras_sai` foti random entre 16:30 to'o 17:30).
    *   **10% Probabilidade:** `estadu_prezensa` = 'Falta' (Oras mamuk).
    *   **5% Probabilidade:** `estadu_prezensa` = 'Lisensa' (Oras mamuk).
    *(ATENSAUN: Keta hatama estadu 'Tardi'! Agora uza 'Loron Sorin').*

### FASE 5: Jera Dadus Mockup Lisensa (Cuti)
Targetu: Kria pedidu lisensa ba funsionáriu sira ne'ebé hetan status absénsia 'Lisensa' iha Fase 4.
1.  Foti random 20% husi total funsionáriu.
2.  `INSERT` ba tabela `lisensa`:
    *   `tipu_lisensa`: Random ('Moras', 'Feriadu', 'Seluk').
    *   `data_hahu` no `data_remata`: Random loron balun iha fulan kotuk.
    *   `razaun`: String dummy (Ezemplu: "Moras isin manas", "Asuntu familia").
    *   `estadu_lisensa`: Random ('Aprovadu' 70%, 'Pendente' 20%, 'Rejeitadu' 10%).

### FASE 6: Jera Dadus Mockup Sansaun
Targetu: Hatudu katak Módulu Sansaun funsiona.
1.  **Tipu Sansaun:** Se tabela `tipu_sansaun` mamuk, insert dummy data 3: "Atraso Frequente", "Insubordinasaun", "Falta la fó hatene".
2.  **Sansaun:** Foti random 10% husi funsionáriu.
3.  `INSERT` ba tabela `sansaun`:
    *   `data_sansaun`: Random loron iha fulan kotuk.
    *   `tipu_sansaun_id`: Foti husi pontu 1.
    *   `valor_total`: Random dolar husi $5 to'o $50.
    *   `valor_pagadu`: Random ($0 ka valor hanesan ho valor_total).
    *   `estadu_sansaun`: Bazeia ba pagamentu (se selu ona = 'Konkluidu', se seidauk = 'Ativu').

### FASE 7: Jera Dadus Mockup Saláriu
Targetu: Jera slip gaji ba fulan kotuk bazeia ba dadus absénsia no sansaun.
1.  Loop ba kada funsionáriu.
2.  Sura total absénsia 'Falta' no total sansaun 'Ativu' iha fulan kotuk nian.
3.  `INSERT` ba tabela `salariu`:
    *   `fulan`: Fulan kotuk (angka 1-12).
    *   `tinan`: Tinan atuál.
    *   `salariu_baziku`: Set default $200 ka random tuir `kategoria_id`.
    *   `total_subsidiu`: Set random $20 - $50.
    *   `total_deskontu`: (Total 'Falta' * $5) + (Valor Sansaun).
    *   `salariu_liquidu`: `salariu_baziku` + `total_subsidiu` - `total_deskontu`.
    *   `data_pagamentu`: Loron ikus husi fulan kotuk.

### FASE 8: Jera Dadus Mockup Avizu (Announcements)
Targetu: Fasilita dashboard lógika.
1.  `INSERT` ba tabela `avizu` 3 ka 4 rekord.
2.  `titulu`: "Enkontru Fulan Fulan", "Avisu Feriadu", dsst.
3.  `konteudu`: Text lorem ipsum badak.
4.  `data_publikasaun`: Random data iha fulan atuál no fulan kotuk.

---

## 4. Kriteria Susesu (Acceptance Criteria)
Bainhira script migrasaun no mocking ne'e halai tiha (la iha *error/exception*):
1. Menus hotu-hotu (Funsionáriu, Prezensa, Lisensa, Saláriu, Sansaun, Relatóriu) iha sistema hatudu dadus ne'ebé relasionadu ba fulan kotuk.
2. **Dashboard Administrador:** Gráfiku line chart atuál hatudu flutuasaun prezensa no falta. Donut chart departamentu nakunu. Tabela avizu no sansaun nakunu ho dadus dummy.
3. **Dashboard Funsionáriu:** Pie Chart prezensa individu hatudu persentajen loloos bazeia ba mock data, no avizu admin mosu loloos.
4. **Relatóriu (Export PDF & Excel):** Bele koko fila-fila relatóriu hotu-hotu ho filter tempu (fulan kotuk) no la iha resultadu *empty set*.
