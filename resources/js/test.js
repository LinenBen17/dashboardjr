console.log("AAA");

window.addEventListener('print-payroll', event => {
    if (!event.detail?.url) return;

    window.open(event.detail.url, '_blank');
});