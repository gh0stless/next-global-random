<?php

declare(strict_types=1);

namespace OCA\GlobalRandom\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\AppFramework\Http\DataDisplayResponse;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCP\IURLGenerator;

class PageController extends Controller {

    private IURLGenerator $urlGenerator;

    public function __construct(string $appName, IRequest $request, IURLGenerator $urlGenerator) {
        parent::__construct($appName, $request);
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Gerahmte Nextcloud-Seite: Vollbild-Iframe, der auf globalRandom() zeigt.
     */
    public function index(): TemplateResponse {
        return new TemplateResponse($this->appName, 'index', [
            'iframeSrc' => $this->urlGenerator->linkToRoute($this->appName . '.page.globalRandom'),
        ]);
    }

    /**
     * Liefert global-random.html unverändert roh aus.
     *
     * CSP ist bewusst nur für DIESE Route geöffnet (nicht global für Nextcloud),
     * weil das Werk auf seinem regulären Webspace ohnehin frei mit diesen
     * Diensten spricht — hier wird dieselbe Offenheit eins zu eins nachgebildet,
     * nicht verschärft und nicht künstlich erweitert.
     *
     * WICHTIG fürs Docker-Testing: Browser-Konsole beobachten. CSP-Verstöße
     * erscheinen dort explizit ("Refused to connect/load ... because it
     * violates the following Content Security Policy directive") — falls
     * doch ein Dienst blockt, der hier fehlt, sofort sichtbar, nicht als
     * mysteriöser stiller Bug.
     */
    public function globalRandom(): DataDisplayResponse {
        return $this->serveRawHtml('global-random.html');
    }

    /**
     * Von global-random.html verlinkte Werkbeschreibungen (EN/DE). Werden aus
     * demselben Grund über eine eigene, offene CSP-Route ausgeliefert wie
     * global-random.html selbst: beide Seiten bringen eigene Inline-<script>/
     * <style>-Blöcke mit, die über die normale Nextcloud-CSP sonst blockiert
     * würden.
     */
    public function description(): DataDisplayResponse {
        return $this->serveRawHtml('description.html');
    }

    public function beschreibung(): DataDisplayResponse {
        return $this->serveRawHtml('beschreibung.html');
    }

    private function serveRawHtml(string $filename): DataDisplayResponse {
        $path = __DIR__ . '/../../' . $filename;
        $html = file_get_contents($path);

        if ($html === false) {
            return new DataDisplayResponse(
                $filename . ' konnte nicht gelesen werden.',
                500,
                ['Content-Type' => 'text/plain; charset=utf-8']
            );
        }

        $response = new DataDisplayResponse(
            $html,
            200,
            ['Content-Type' => 'text/html; charset=utf-8']
        );

        $response->setContentSecurityPolicy($this->buildOpenCsp());

        return $response;
    }

    private function buildOpenCsp(): ContentSecurityPolicy {
        $csp = new ContentSecurityPolicy();
        $csp->allowInlineScript(true);
        $csp->allowInlineStyle(true);

        // Spotify IFrame API + eingebettete Player
        $csp->addAllowedScriptDomain('open.spotify.com');
        $csp->addAllowedScriptDomain('sdk.scdn.co');
        $csp->addAllowedFrameDomain('open.spotify.com');
        $csp->addAllowedConnectDomain('open.spotify.com');
        $csp->addAllowedConnectDomain('api.spotify.com');

        // MusicBrainz
        $csp->addAllowedConnectDomain('musicbrainz.org');

        // Wikipedia (alle Sprachsubdomains) + Wikidata/SPARQL
        $csp->addAllowedConnectDomain('*.wikipedia.org');
        $csp->addAllowedConnectDomain('www.wikidata.org');
        $csp->addAllowedConnectDomain('query.wikidata.org');

        // MyMemory Translation API
        $csp->addAllowedConnectDomain('api.mymemory.translated.net');

        // Open-Meteo
        $csp->addAllowedConnectDomain('api.open-meteo.com');

        // Google Fonts (falls im Hauptfile referenziert, C64-Font selbst ist base64-inline)
        $csp->addAllowedStyleDomain('fonts.googleapis.com');
        $csp->addAllowedFontDomain('fonts.gstatic.com');
        $csp->addAllowedFontDomain('data:');

        // Twemoji via jsDelivr
        $csp->addAllowedScriptDomain('cdn.jsdelivr.net');
        $csp->addAllowedImageDomain('cdn.jsdelivr.net');
        $csp->addAllowedImageDomain('data:');
        $csp->addAllowedImageDomain('blob:');

        return $csp;
    }
}
