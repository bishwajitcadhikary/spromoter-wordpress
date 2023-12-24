(function() {
    let widget = document.createElement('script');
    widget.type = 'text/javascript';
    widget.async = true;
    widget.src = `//staticwp.spromoter.test/${spromoterSettings.app_id}/widget.js`;
    let script = document.getElementsByTagName('script')[0];
    script.parentNode.insertBefore(widget, script);
})();