# Falcon Core – WordPress šablona

Firemní web Falcon Core (mytí fasád, opláštění a prosklení dronem).
Statické PHP + ACF bloky, styly na Tailwindu v4. Žádný JavaScriptový framework,
za běhu nejsou potřeba `node_modules`.

## Požadavky

- WordPress 6.4+, PHP 8.0+
- **ACF PRO** (nebo zdarma **Secure Custom Fields**) – bez něj se bloky nevykreslí
- **Rank Math** – SEO (šablona funguje i bez něj, viz kapitola SEO)
- Node.js jen pro build CSS

## Build CSS

Zkompilované CSS je součástí repozitáře, build je potřeba jen při úpravě stylů.

```bash
npm install
npm run build   # produkční build (minifikovaný) + varianta pro editor
npm run dev     # watch při vývoji
```

| Soubor | K čemu |
|---|---|
| `assets/src/main.css` | **zdroj** – tokeny v `@theme`, komponenty v `@layer components` |
| `assets/css/main.css` | web (generované, needitovat ručně) |
| `assets/css/editor.css` | editor – totéž bez `@layer` (generované) |

Proč dvě varianty: Tailwind v4 dává styly do kaskádových vrstev. Styly editoru
WordPressu žádnou vrstvu nemají a nezařazené CSS vždy přebije to ve vrstvě –
v náhledu bloků by tak vyhrály nadpisy, odkazy a seznamy z editoru.
`build/flatten-layers.js` obaly `@layer` odstraní a náhled vypadá stejně jako web.

## Struktura

```
inc/
  setup.php       nastavení šablony, enqueue, úklid <head>
  helpers.php     ikony, logo, firemní údaje, shortcode [fc_firma]
  acf.php         stránka „Nastavení firmy“, pomocníci pro pole
  fields.php      definice všech polí (v kódu, ne klikáním)
  blocks.php      registrace bloků ze složky /blocks
  widgets.php     čtyři sloupce patičky + firemní widgety
  lead-form.php   zpracování poptávky, CPT „Poptávky“, e-mail
  seo.php         popisek, canonical, OG, Twitter, JSON-LD, sitemap
  analytics.php   Google Tag Manager a konverze do dataLayeru
  maintenance.php režim „Připravujeme“ (503 + noindex)
blocks/<blok>/    block.json + render.php
template-parts/   opakované kusy markupu (viz níže)
```

## Template parts

Markup, který se opakuje, je na jednom místě. Bloky ho vkládají přes
`get_template_part()`, takže se stejná věc nikde nepíše dvakrát.

| Část | Kdo ji používá |
|---|---|
| `section-head.php` | všech 8 bloků se sekcí – nadtitulek, H2, perex (pole si čte z bloku sama) |
| `card-service.php` | blok Služby – karta s ikonou, popisem a odkazem |
| `card-feature.php` | blok Karty s ikonami |
| `checklist.php` | blok Text + obrázek – seznam s fajfkami (v textu i v kartě) |
| `page-head.php` | `page.php` a `index.php` – záhlaví stránky bez hera |
| `coming-soon.php` | režim „Připravujeme“ |

## Mezery sekcí

Odsazení sekcí drží čtyři tokeny v `assets/src/main.css`, ne hodnoty u jednotlivých
bloků. Díky tomu mají všechny stránky stejný rytmus:

| Token | K čemu |
|---|---|
| `--spacing-section` | svislé odsazení každé sekce včetně pruhu s výzvou k akci |
| `--spacing-head` / `--spacing-head-end` | záhlaví podstránky (hero i `fc-pagehead`) |
| `--spacing-head-gap` | mezera mezi záhlavím a první sekcí pod ním |

## Bloky (kategorie „Falcon Core“ v editoru)

| Blok | Použití |
|---|---|
| Hero | úvod stránky s H1 (varianta úvodní / podstránka) |
| Čísla / výhody | klíčové hodnoty s vysvětlením |
| Služby – karty | karty služeb s ikonou a odkazem |
| Srovnání | tabulka dron vs. plošina (na mobilu karty) |
| Postup | číslované kroky |
| Text + obrázek | text s fotkou, bez fotky s kartou bodů |
| Karty s ikonami | kvalifikace, bezpečnost, metoda |
| Časté dotazy | rozbalovací FAQ (plní se i do schema.org FAQPage) |
| Výzva k akci | pruh s tlačítkem a telefonem |
| Kontakt + formulář | poptávkový formulář + kontakty + firemní údaje |

Pole bloků jsou v `inc/fields.php`, vykreslení v `blocks/<blok>/render.php`,
styly v `assets/src/main.css`.

## Patička

Patička se skládá ze čtyř widgetových oblastí (**Vzhled → Widgety**):

1. Patička 1 – značka (logo, popis, pojištění)
2. Patička 2 – navigace (hlavní menu)
3. Patička 3 – kontakt (telefon, e-mail, sídlo)
4. Patička 4 – firemní údaje (obchodní firma, IČO, DIČ, zápis)

Čtyři firemní widgety berou data z **Nastavení firmy**, takže se telefon ani IČO
nepíše nikde dvakrát. Při aktivaci šablony se do prázdných oblastí doplní samy.
Pod sloupci je řádek s právními odkazy (menu „Menu v patičce“), pod ním podpis
autora webu a úplně dole copyright klienta (rok se doplňuje automaticky).

## Nastavení po instalaci

1. Nainstalovat a aktivovat ACF PRO / Secure Custom Fields.
2. **Nastavení firmy** (levé menu): IČO, DIČ, sídlo, telefon, e-mail, pojištění.
3. Nastavení → Trvalé odkazy → „Název příspěvku“.
4. Vzhled → Přizpůsobit → Identita webu → Logo (do hlavičky, na světlý podklad).
   Světlá varianta do patičky je v šabloně (`assets/img/falcon-core-logo-light.png`).
5. Doporučeno: plugin pro SMTP (např. WP Mail SMTP) – jinak maily z formuláře
   padají do spamu nebo se neodešlou vůbec. Poptávky se i tak vždy ukládají
   do menu **Poptávky** a u každé je vidět, jestli se e-mail podařilo odeslat.
6. Zveřejnění: Nastavení firmy → „Web je veřejný“. Do té doby nepřihlášení
   vidí stránku „Připravujeme“ (HTTP 503, noindex).

## SEO

Web běží na **Rank Mathu**. Šablona umí SEO i bez něj (viz `inc/seo.php`), ale
jakmile je nějaký SEO plugin aktivní, vlastní meta tagy se vypnou, aby nebyly
dvakrát – viz `fc_seo_plugin()` / `fc_seo_active()`.

### Proč je tam i tak kód k SEO

Rank Math neumí přečíst obsah ACF bloků – texty stránek jsou v komentářích bloků,
ne v `post_content` jako text. Bez pomoci by všechny stránky zůstaly **bez popisku**.
Šablona mu proto dodává:

| Co | Jak |
|---|---|
| Popisek stránky | filtr `rank_math/frontend/description` → `fc_meta_description()` (text prvního bloku, u běžných stránek první odstavec) |
| Firemní údaje ve schématu | filtr `rank_math/json_ld` → `fc_organization_data()` (IČO, DIČ, telefon, e-mail, sídlo z Nastavení firmy) |
| Časté dotazy | `FAQPage` z bloků `falcon/faq` – Rank Math o nich sám neví |
| Úklid | ze stránek se odstraní entity `Article` a `Person` (firemní stránka není článek) |

Ruční popisek vyplněný v Rank Mathu má vždy přednost.

### Nastavení Rank Mathu, které se nesmí „opravit“

- **Typy obsahu → Stránky → Schéma: `Article`.** Vypadá to divně, ale je to
  správně: Rank Math jen při zapnutém schématu poskládá celý graf
  (Organization + WebSite + WebPage + BreadcrumbList). Samotnou entitu `Article`
  z něj pak odstraní šablona. Když se přepne na „None“, **stránky zůstanou
  úplně bez strukturovaných dat**.
- **Knowledge Graph: Company, název „Falcon Core“**, logo z Identity webu.
- **Drobečky zapnuté** – kvůli `BreadcrumbList` ve výsledcích vyhledávání.
- Úvodní stránka má v Rank Mathu titulek `%sitename% %sep% %sitedesc%`,
  aby nesla „Falcon Core - Mytí fasád a oken dronem“ místo „Úvod“.

Mapa webu je Rank Mathova: `/sitemap_index.xml` (odkazuje na ni `robots.txt`).

## Měření konverzí (Google Tag Manager)

Kontejner GTM vkládá `inc/analytics.php`. ID se vyplňuje v **Nastavení firmy → Měření**;
dokud je pole prázdné, web načítá zástupné `GTM-XXXXXXX` a měření nic nesbírá.
Po dodání skutečného kontejneru stačí ID přepsat v administraci, v kódu se nic nemění.

Do `dataLayer` posílá web tři události, zbytek (GA4, Ads, …) se nastavuje v GTM:

| Událost | Kdy | Parametry |
|---|---|---|
| `generate_lead` | po úspěšném odeslání poptávky | `form_name`, `page_path` |
| `phone_click` | kliknutí na telefonní odkaz | `link_area`, `link_url` |
| `email_click` | kliknutí na e-mailový odkaz | `link_area`, `link_url` |

`link_area` říká, odkud se kliklo: `hlavicka`, `hero`, `vyzva`, `kontakt`, `paticka`
nebo `obsah`.

## Před zveřejněním

Prohledat obsah stránek na `[DOPLNIT`, `[OVĚŘIT`, `[KONCEPT` – to jsou místa,
kde zatím chybí fakta (hlavně Zásady zpracování osobních údajů).
