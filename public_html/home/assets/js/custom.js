function updateUrl(type, value) {
    let url = new URL(window.location.href);
    url.searchParams.set(type, value);
    window.history.pushState({}, "", url);
}