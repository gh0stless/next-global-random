<?php

namespace OCA\GlobalRandom\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataDisplayResponse;
use OCP\AppFramework\Http\EmptyContentSecurityPolicy;
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
     * Nextcloud-Seite mit Vollbild-Iframe, der auf globalRandom() zeigt.
     *
     * @NoAdminRequired
     */
    public function index(): TemplateResponse {
        $embedUrl = $this->urlGenerator->linkToRoute('globalrandom.page.globalRandom');
        return new TemplateResponse($this->appName, 'index', ['embedUrl' => $embedUrl]);
    }

    /**
     * Liefert global-random.html unverändert und roh aus, mit einer eigenen,
     * bewusst geöffneten CSP (die Original-Datei nutzt Inline-Skripte und lädt
     * von mehreren externen Diensten). Betrifft NUR diese Route, nicht den Rest
     * von Nextcloud.
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function globalRandom(): DataDisplayResponse {
        $path = __DIR__ . '/../../global-random.html';
        $html = file_get_contents($path);

        $response = new DataDisplayResponse($html, 200, [
            'Content-Type' => 'text/html; charset=utf-8',
        ]);
        $response->addHeader('X-Frame-Options', 'SAMEORIGIN');

        $csp = new EmptyContentSecurityPolicy();
        $csp->allowInlineScript(true);
        $csp->allowInlineStyle(true);
        $csp->allowEvalScript(true);

        // Skripte: Spotify iFrame-API + Twemoji via jsDelivr
        $csp->addAllowedScriptDomain('https://open.spotify.com');
        $csp->addAllowedScriptDomain('https://cdn.jsdelivr.net');

        // Eingebetteter Spotify-Player
        $csp->addAllowedFrameDomain('https://open.spotify.com');

        // Datenabfragen: MusicBrainz, Wikipedia/Wikidata (alle Sprach-Subdomains),
        // MyMemory-Übersetzung, Open-Meteo
        $csp->addAllowedConnectDomain('https://musicbrainz.org');
        $csp->addAllowedConnectDomain('https://*.musicbrainz.org');
        $csp->addAllowedConnectDomain('https://*.wikipedia.org');
        $csp->addAllowedConnectDomain('https://www.wikidata.org');
        $csp->addAllowedConnectDomain('https://query.wikidata.org');
        $csp->addAllowedConnectDomain('https://mymemory.translated.net');
        $csp->addAllowedConnectDomain('https://api.mymemory.translated.net');
        $csp->addAllowedConnectDomain('https://api.open-meteo.com');
        $csp->addAllowedConnectDomain('https://open.spotify.com');

        // Bilder (Wikipedia/Wikidata-Thumbnails, Spotify-Cover, u.a.) + eingebettete data:-URIs
        $csp->addAllowedImageDomain('*');
        $csp->addAllowedImageDomain('data:');

        // Eingebetteter Base64-Font (C64-WOFF2) und eingebettete Base64-Audiosamples
        $csp->addAllowedFontDomain('data:');
        $csp->addAllowedMediaDomain('data:');

        $response->setContentSecurityPolicy($csp);

        return $response;
    }
}
