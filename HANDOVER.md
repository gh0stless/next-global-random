# HANDOVER — next-global-random (Nextcloud app)

**Stand:** 31.07.2026. Dieses Dokument ersetzt die frühere Kette `HANDOVER-next-global-random.md` (lag im `global-random`-Repo, gehörte aber hierher) und `HANDOVER-nextcloud-app-to-vscode.md`/`...-to-vscode2.md` (Duplikat) — alle drei gelöscht, Inhalt hier konsolidiert. Beschreibt den **aktuellen Stand**, nicht die Chronologie. Details zu einzelnen Fixes stehen bei Bedarf in der Git-Historie.

**Verhältnis zum Hauptprojekt:** eigenständiges Spinoff, eigenes Repo (`github.com/gh0stless/next-global-random`, jetzt öffentlich). Die eingebettete `global-random.html` bleibt **unangetastet** — nur eingerahmt, nie bearbeitet. Siehe [`global-random`](https://github.com/gh0stless/global-random) für das Hauptprojekt und dessen eigenes `HANDOVER.md`.

---

## 1. Projekt in einem Satz

Eine Nextcloud-App (`globalrandom`, App-ID), die die unveränderte GLOBAL-RANDOM-Installation per Iframe in Andreas' eigene, passwortgeschützte Nextcloud-Instanz einbindet. Läuft **bereits produktiv** auf dem echten System (nicht nur in einer isolierten Testumgebung — siehe Abschnitt 5). Ein KI-gestütztes IT-Security-News-Ticker-Feature ist als spätere, eigene Ausbaustufe angedacht, aber noch nicht begonnen (Abschnitt 6).

---

## 2. Zielsystem

- Andreas' echte Nextcloud-Instanz: Docker-Container `nextcloud`, läuft auf einem [privates NAS-System]-Server (`[privater Server]`).
- `appinfo/info.xml`: `<nextcloud min-version="28" max-version="34" />`, `<php min-version="8.2" max-version="8.5" />` (bewusst großzügiger als ein früher erwogenes eng gesetztes 34-34 — Stand jetzt: der aktuelle `info.xml`-Wert, siehe Datei für die verbindliche Version).
- Aktuelle App-Version: siehe `<version>` in `appinfo/info.xml` (Cache-Buster bei jeder sichtbaren Änderung, mehrere Version-Bumps in der Historie ausschließlich dafür).
- Eigene Kontakt-E-Mail für die Nextcloud-Kopie (`6933630@pyur.net`), bewusst getrennt von der Webspace-Version (`info@crazy-midi.de`) — zwei unabhängige MyMemory-Kontingente.

---

## 3. Architektur (aktueller, tatsächlicher Stand)

**Route:** nur noch eine einzige Route, `page#index` auf `/`. `PageController::index()` liefert eine `TemplateResponse` mit einer Iframe-Vollbild-Hülle; die `src` zeigt über `IURLGenerator::linkTo()` direkt auf die App-Web-URL von `global-random.html`.

**Wichtige, hart erarbeitete Erkenntnis (mehrfach hin- und her-revertiert, siehe Git-Historie):** Diese Nextcloud-Version bietet in ihrer CSP-API **kein** `allowInlineScript()` — weder über `ContentSecurityPolicy` noch `EmptyContentSecurityPolicy`. `global-random.html` nutzt aber durchgehend Inline-`<script>` und `onclick`-Attribute. Der ursprünglich geplante Weg — eigener Controller mit eigens geöffneter CSP (siehe die alte, jetzt gelöschte Domain-Liste) — **funktioniert in dieser NC-Version nicht** und wurde verworfen. Stattdessen: `global-random.html`, `beschreibung.html` und `description.html` liegen einfach im App-Ordner und werden von Apache **direkt als statische Datei ausgeliefert**, komplett an PHP/CSP vorbei (per `curl` bestätigt: 200 OK, kein CSP-Header). Kein eigener Controller, keine eigene Route für diese Dateien nötig — `linkTo()` reicht.

**CSS (`css/style.css`):** bewusste Design-Entscheidung, Nextclouds eigene "schwebende Karte" (Rand + abgerundete Ecken um `#content`) **nicht** zu entfernen — passt zum GLOBAL-RANDOM-Terminal-Look, kein cross-browser Vollbild-Gefrickel nötig. Diverse per Browser-Inspector vermessene Pixel-genaue Fixes (Scrollleisten-Lücke, Header-Kontrast-Variable, Wrapper-Höhe) sind in der Git-Historie einzeln dokumentiert.

---

## 4. Sync-Workflow (unverändert gegenüber dem Hauptprojekt)

Änderungen entstehen im kanonischen `global-random`-Repo und werden manuell mit "Sync: ..."-Commits hierher übertragen. `global-random.html`, `beschreibung.html`, `description.html` sind aktuell byte-identisch mit der kanonischen Quelle.

---

## 5. Wie es hierher kam (kurz, für Kontext — Details in der Git-Historie)

Der ursprüngliche Plan (siehe gelöschte alte Handover-Kette) war: erst eine isolierte, wegwerfbare Docker-Testumgebung bauen, nie direkt am Produktivsystem testen. Tatsächlich passiert ist: die App wurde direkt gegen die echte `[privater Server]`-Instanz iteriert (sichtbar an der langen Kette von "Fix:"/"Revert"/"Version-Bump"-Commits — CSS-Layout, CSP-Verhalten, Header-Kontrast, CSRF-Annotationen, jeweils per Cache-Buster-Version-Bump verifiziert). Läuft mittlerweile stabil. Für künftige, größere strukturelle Änderungen (nicht nur CSS/Copy) bleibt "erst isoliert testen" trotzdem der sicherere Standardweg, nicht automatisch wieder direkt am Live-System.

---

## 6. Rechtliche Einordnung (bereits erledigt, weiterhin gültig)

Relevant, falls das Projekt je über "nur für mich und Freunde" hinauswächst:

- **Rundfunklizenz (Medienstaatsvertrag):** Pflicht ab durchschnittlich 20.000 gleichzeitigen Nutzern über 6 Monate, und/oder bei festem Sendeplan.
- **GEMA/GVL:** keine Bagatellgrenze, aber bei geschlossenem, passwortgeschütztem Nextcloud-Kreis vermutlich nicht einschlägig (nicht 100% wasserdicht ohne Anwalt).
- **Framing/Embedding (BestWater/EuGH 2014, bestätigt durch BGH):** Einbetten von Spotifys offiziellem IFrame-API ist keine eigene "öffentliche Wiedergabe" — Lizenzpflicht liegt bei Spotify.
- **heise-RSS-Nutzungsbedingungen** (relevant erst für Abschnitt 7): Titel+Teaser+Link frei nutzbar, kein Volltext-Reproduzieren — passt zum ohnehin geplanten "destillierte Zusammenfassung, kein 1:1-Zitat"-Ansatz.
- **Fazit unverändert:** für die aktuelle Machbarkeitsstudie (geschlossener Kreis, kein Sendeplan) rechtlich nichts Kritisches im Weg. Bei echtem Ausbau: IT-Anwalt für verbindliche Einschätzung.

---

## 7. Für später: News-Ticker-Feature (Konzept, noch nicht begonnen)

**Ziel:** zur vollen Stunde/täglich automatisiert IT-Security-Lage zusammenfassen, optional per TTS vorlesbar.

**Quelle:** `https://www.heise.de/security/rss/alert-news-atom.xml` (verifiziert), optional Golem Security, BSI/CERT-Bund WID-Feed, NVD/CVE.

**Pipeline (Konzept):** Cron → RSS abrufen → Volltext pro Meldung laden → gebündelt an Claude-API zur Zusammenfassung → Text/TTS speichern → in der App anzeigen/abspielbar machen.

**Architektur-Empfehlung:** Nextcloud-**ExApp** (eigener Prozess/Container, beliebige Sprache, HTTP via AppAPI/Reverse-Tunnel) statt klassischer PHP-App — Standardweg für KI-lastige NC-Apps (`context_chat`, `llm2`, `stt_whisper2` sind Beispiele), vermeidet native Abhängigkeiten im PHP-Server.

**TTS-Optionen (offen):** Browser-`SpeechSynthesis` (einfach, kostenlos) vs. Server-seitig (ElevenLabs/Piper, bessere Stimme, mehr Aufwand — würde in die ExApp passen).

**Bewusst verworfen:** politische Ausrichtungswahl (links/rechts/konservativ) — widerspricht der Zufalls-/Anti-Kurations-These von GLOBAL RANDOM, zusätzlich regulatorisch heikler.

---

## 8. Prinzipien

- **Root Cause statt Symptom-Patch.**
- **Vor jedem Build kurz nachfragen**, ob noch was dazukommt.
- **GLOBAL RANDOM selbst bleibt unangetastet** — Änderungen nur an der Hülle (Routen, Controller, Templates, CSS), nie an `global-random.html` selbst außer per Sync von der kanonischen Quelle.
- **Nextcloud-API-Annahmen vor Umsetzung verifizieren, nicht aus Doku/Erinnerung annehmen** — die CSP-`allowInlineScript()`-Frage (Abschnitt 3) wurde genau deshalb zweimal falsch angenommen, bevor der tatsächliche statische Ausliefer-Weg stand.
- **Version-Bump als Cache-Buster** bei jeder sichtbaren CSS/Copy-Änderung, sonst zeigt Nextcloud den alten Stand weiter an.

---

*Ende Handover.*
