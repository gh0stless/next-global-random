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
     * bieten allowInlineScript()), die Original-Datei nutzt aber Inline-<script>
     * und onclick-Attribute. Der App-Ordner wird von Apache direkt als statische
     * Datei ausgeliefert (an PHP/CSP vorbei), daher der direkte linkTo()-Link.
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index(): TemplateResponse {
        $embedUrl = $this->urlGenerator->linkTo($this->appName, 'global-random.html');
        return new TemplateResponse($this->appName, 'index', ['embedUrl' => $embedUrl]);
    }
}
