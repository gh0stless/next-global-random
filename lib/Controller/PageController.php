<?php

namespace OCA\GlobalRandom\Controller;

use OCP\AppFramework\Controller;
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
     * Nextcloud-Seite mit Vollbild-Iframe auf die statische global-random.html.
     *
     * global-random.html wird bewusst NICHT über einen eigenen Controller/Route
     * ausgeliefert: Nextclouds CSP-Middleware erlaubt in dieser Version keine
     * Inline-Skripte (weder ContentSecurityPolicy noch EmptyContentSecurityPolicy
     * bieten allowInlineScript() - mehrfach nachgewiesen, siehe Git-Historie).
     * Die Original-Datei nutzt aber Inline-<script> und onclick-Attribute.
     * Der App-Ordner wird von Apache direkt als statische Datei ausgeliefert
     * (an PHP/CSP vorbei, per curl bestätigt: 200 OK, kein CSP-Header),
     * daher der direkte linkTo()-Link statt einer eigenen Route.
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(): TemplateResponse {
        return new TemplateResponse($this->appName, 'index', [
            'appSrc' => $this->urlGenerator->linkTo($this->appName, 'global-random.html'),
        ]);
    }
}
