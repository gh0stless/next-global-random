<?php
/** @var array $_ */
style('globalrandom', 'style');
?>
<div id="globalrandom-wrapper">
    <iframe id="globalrandom-frame"
            src="<?php p($_['embedUrl']); ?>"
            title="GLOBAL RANDOM"
            allow="autoplay; encrypted-media; clipboard-write"
            allowfullscreen>
    </iframe>
</div>
