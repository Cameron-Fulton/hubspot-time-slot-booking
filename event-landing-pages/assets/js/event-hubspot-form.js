/**
 * Event Landing Pages — HubSpot Form Embed Loader
 *
 * Reads configuration from the global `elpEventConfig` object
 * injected via wp_localize_script().
 *
 * Expected elpEventConfig properties:
 *   portalId - HubSpot portal ID
 *   formId   - HubSpot form GUID
 */
(function () {
  'use strict';

  var config = window.elpEventConfig || {};
  if (!config.portalId || !config.formId) return;

  var target = document.getElementById('elpHubspotFormTarget');
  if (!target) return;

  // Load the HubSpot Forms SDK.
  var script = document.createElement('script');
  script.src = 'https://js.hsforms.net/forms/embed/v2.js';
  script.charset = 'utf-8';
  script.onload = function () {
    if (window.hbspt && window.hbspt.forms) {
      window.hbspt.forms.create({
        region: 'na1',
        portalId: config.portalId,
        formId: config.formId,
        target: '#elpHubspotFormTarget',
      });
    }
  };
  document.head.appendChild(script);
})();
