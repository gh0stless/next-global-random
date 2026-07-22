<?php
/** @var array $_ */
style('globalrandom', 'style');
script('globalrandom', 'main');
?>
<div id="globalrandom-wrapper"
     data-app-src="<?php p($_['appSrc']); ?>"
     data-de-src="<?php p($_['deSrc']); ?>"
     data-en-src="<?php p($_['enSrc']); ?>">
    <div id="globalrandom-nav">
        <button type="button" class="globalrandom-nav-btn active" data-target="app">GLOBAL RANDOM</button>
        <button type="button" class="globalrandom-nav-btn" data-target="de">Beschreibung</button>
        <button type="button" class="globalrandom-nav-btn" data-target="en">Description</button>
    </div>
    <iframe id="globalrandom-frame"
            src="<?php p($_['appSrc']); ?>"
            title="GLOBAL RANDOM — Democracy of Sound"
            allow="autoplay; fullscreen"
            allowfullscreen>
    </iframe>
</div>
