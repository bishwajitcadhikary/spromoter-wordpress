document.addEventListener('DOMContentLoaded', function () {
    let widget = document.createElement('script');
    widget.type = 'text/javascript';
    widget.async = true;
    widget.src = `//staticwp.spromoter.test/${spromoterSettings.app_id}/widget.js`;
    document.body.appendChild(widget);
});